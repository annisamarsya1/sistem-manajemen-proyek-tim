# Workflow & Hak Akses per Role (TaskSync)

Dokumen ini memetakan hak akses, batasan, serta alur kerja masing-masing role yang ada di dalam sistem.

## Ringkasan Role

Sistem menggunakan kolom `role` pada tabel `users` untuk menentukan hak akses. Terdapat 3 role utama:

| Role | Tanggung Jawab Utama | Akses Menu |
|---|---|---|
| **Admin** (`admin`) | Mengelola seluruh sistem, termasuk pengguna, proyek, dan persetujuan waktu. | Semua Menu (Dashboard, Project Studio, Kanban Board, Timesheet, User Management) |
| **Project Manager** (`project_manager`) | Mengelola proyek, menugaskan task, dan menyetujui log waktu karyawan. | Dashboard, Project Studio, Kanban Board, Timesheet |
| **Employee** (`employee`) | Mengerjakan tugas (task) dan mencatat waktu kerja (time log). | Dashboard, Kanban Board, Timesheet |

---

## 1. Admin (`admin`)

**Ringkasan:** Memiliki akses tak terbatas ke seluruh fitur dan pengaturan pengguna.
**Entry point:** `/login` $\rightarrow$ Redirect ke `/dashboard`

### Menu & Halaman yang Bisa Diakses

| Fitur | Route (URL) | Method/Controller | Middleware/Permission | Aksi yang bisa dilakukan (CRUD) |
|---|---|---|---|---|
| Dashboard | `/dashboard` | `App\Livewire\Dashboard` | `auth` | Read (analytics seluruh user), Approve/Reject log waktu. |
| Project Studio | `/projects` | `App\Livewire\ProjectStudio` | `admin_or_pm` | Create, Read, Update, Delete (semua proyek). |
| Kanban Board | `/tasks` | `App\Livewire\KanbanBoard` | `auth` | Create, Edit, Delete (semua tugas), Update status, Add comment. |
| Timesheet | `/timesheet` | `App\Livewire\PersonalTimesheet` | `auth` | Read (log waktu miliknya sendiri), Export PDF. |
| User Management | `/users` | `App\Livewire\UserManagement` | `admin_only` | Create, Read, Update user, Toggle active/inactive. |
| Tambah Log Waktu | (Modal Component) | `App\Livewire\TimeLogForm` | (View) | Create time log (untuk dirinya sendiri). |

**Batasan / Akses Ditolak:** Tidak ada.
**Ownership Scope:** Admin bisa melihat semua data proyek, task, dan timelog pending (di dashboard), namun di Personal Timesheet (`/timesheet`) admin hanya melihat catatan waktunya sendiri. Tidak bisa menonaktifkan akun sendiri.

---

## 2. Project Manager (`project_manager`)

**Ringkasan:** Fokus pada manajemen proyek tim dan pengawasan operasional tugas (task & waktu).
**Entry point:** `/login` $\rightarrow$ Redirect ke `/dashboard`

### Menu & Halaman yang Bisa Diakses

| Fitur | Route (URL) | Method/Controller | Middleware/Permission | Aksi yang bisa dilakukan (CRUD) |
|---|---|---|---|---|
| Dashboard | `/dashboard` | `App\Livewire\Dashboard` | `auth` | Read (analytics seluruh user), Approve/Reject log waktu. |
| Project Studio | `/projects` | `App\Livewire\ProjectStudio` | `admin_or_pm` | Create, Read, Update, Delete (semua proyek). |
| Kanban Board | `/tasks` | `App\Livewire\KanbanBoard` | `auth` | Create, Edit, Delete (semua tugas), Update status, Add comment. |
| Timesheet | `/timesheet` | `App\Livewire\PersonalTimesheet` | `auth` | Read (log waktu miliknya sendiri), Export PDF. |
| Tambah Log Waktu | (Modal Component) | `App\Livewire\TimeLogForm` | (View) | Create time log (untuk dirinya sendiri). |

**Batasan / Akses Ditolak:** 
- Diblokir dari route `/users` oleh middleware `EnsureIsAdmin`. (Redirect ke dashboard).

**Ownership Scope:** Sama seperti Admin dalam hal proyek dan task (dapat melihat dan mengubah semua). Di dashboard dapat memanajemen waktu (approval) untuk semua user.

---

## 3. Employee (`employee`)

**Ringkasan:** Pengguna biasa yang ditugaskan pada proyek tertentu.
**Entry point:** `/login` $\rightarrow$ Redirect ke `/dashboard`

### Menu & Halaman yang Bisa Diakses

| Fitur | Route (URL) | Method/Controller | Middleware/Permission | Aksi yang bisa dilakukan (CRUD) |
|---|---|---|---|---|
| Dashboard | `/dashboard` | `App\Livewire\Dashboard` | `auth` | Read (analytics milik sendiri), Export PDF ditolak (error). |
| Kanban Board | `/tasks` | `App\Livewire\KanbanBoard` | `auth` | Read (task & project di mana ia di-assign), Update status task miliknya, Add comment. |
| Timesheet | `/timesheet` | `App\Livewire\PersonalTimesheet` | `auth` | Read (log waktu miliknya sendiri), Export PDF. |
| Tambah Log Waktu | (Modal Component) | `App\Livewire\TimeLogForm` | (View) | Create time log (terikat user_id-nya). |

**Batasan / Akses Ditolak:** 
- Diblokir dari route `/users` dan `/projects` oleh middleware `admin_only` dan `admin_or_pm`.
- Tidak bisa menambah, mengedit detail (EditTask), atau menghapus (DeleteTask) task apa pun di Kanban Board (diblokir method `checkAdminOrPm()` di komponen Livewire).
- Tidak bisa Approve/Reject log waktu di dashboard.

**Ownership Scope (Penting):**
- **Dashboard:** Hanya melihat timelog miliknya sendiri (`where('user_id', Auth::id())`).
- **Kanban Board:** Hanya melihat daftar proyek tempat ia memiliki task (`where('assignee_id', Auth::id())`). Hanya bisa memindahkan status task (todo $\rightarrow$ in_progress dsb) apabila `assignee_id` task tersebut adalah dirinya.
- **TimeLogForm (Pencatatan Waktu):** Hanya bisa memilih tugas (Task) yang di-assign ke dirinya.

---

## Alur Kerja Utama

### Workflow Persetujuan Waktu (Employee $\rightarrow$ PM/Admin)

Alur ketika karyawan mencatat waktu hingga disetujui atau ditolak oleh manajer/admin.

```mermaid
flowchart TD
    A[Employee Login] --> B[Klik tombol 'Add Time Log']
    B --> C[Isi form TimeLogForm (Pilih Project & Task)]
    C --> D{Validasi}
    D -- Overlap / >12 Jam --> E[Error Validation]
    D -- Valid --> F[TimeLog tersimpan <br> status: pending]
    
    F --> G[PM / Admin Login]
    G --> H[Buka Halaman Dashboard]
    H --> I[Lihat daftar Log Waktu <br> pending]
    I --> J{Tindakan PM/Admin}
    
    J -- Approve --> K[Status menjadi 'approved']
    J -- Reject --> L[Status menjadi 'rejected']
    
    K --> M[Masuk hitungan analytics / Summary]
```

### Workflow Pengerjaan Task (PM $\rightarrow$ Employee)

1. **PM / Admin:** Masuk ke Project Studio $\rightarrow$ Buat Proyek baru.
2. **PM / Admin:** Pindah ke Kanban Board $\rightarrow$ Buat Tugas (Create Task), assign ke *Employee A*.
3. **Employee A:** Login, buka Kanban Board $\rightarrow$ Tugas muncul di kolom "Todo".
4. **Employee A:** Drag / Ubah status tugas ke "In Progress".
5. **Employee A:** Buka Modal 'Add Time Log', mencatat waktu pengerjaan untuk tugas tersebut.
6. **Employee A:** Selesai mengerjakan $\rightarrow$ Ubah status tugas ke "Done". (Sistem mencatat `completed_at`).

---

## ⚠️ Perlu diklarifikasi

- **Analytics Karyawan (Under Target):** Di dashboard, PM/Admin melihat jumlah karyawan yang *under target*. Saat ini dihitung dari `Total Active Users - Karyawan yang memenuhi target`, mungkin perlu penyesuaian (atau klarifikasi ke depannya) jika target diukur mingguan, bukan harian.
- **Export PDF Dashboard:** Fitur export PDF dari `/dashboard` memblokir akses Employee. Memang Employee memiliki halamannya sendiri `/timesheet` untuk mengekspor data waktunya sendiri, namun apakah Employee juga seharusnya diizinkan mengekspor daftar log waktu yang ia filter di dashboard? Saat ini Employee bisa melihat timelog di dashboard tapi akan error jika mencoba menekan Export PDF.
