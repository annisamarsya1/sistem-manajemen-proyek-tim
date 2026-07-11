# Product Requirements Document (PRD)
## Sistem Manajemen Proyek Tim

**Versi:** 1.0.0
**Tanggal:** 17 Juni 2026
**Status:** Draft
**Dibuat oleh:** Project Team

---

## Daftar Isi

1. [Ringkasan Eksekutif](#1-ringkasan-eksekutif)
2. [Tujuan & Sasaran Produk](#2-tujuan--sasaran-produk)
3. [Target Pengguna](#3-target-pengguna)
4. [Ruang Lingkup](#4-ruang-lingkup)
5. [Tech Stack](#5-tech-stack)
6. [Arsitektur Sistem](#6-arsitektur-sistem)
7. [Skema Database](#7-skema-database)
8. [Spesifikasi Fitur](#8-spesifikasi-fitur)
9. [Role & Permission Matrix](#9-role--permission-matrix)
10. [Alur Kerja (User Flow)](#10-alur-kerja-user-flow)
11. [Kriteria Keberhasilan](#11-kriteria-keberhasilan)
12. [Asumsi & Ketergantungan](#12-asumsi--ketergantungan)
13. [Risiko & Mitigasi](#13-risiko--mitigasi)
14. [Di Luar Ruang Lingkup (Out of Scope)](#14-di-luar-ruang-lingkup-out-of-scope)

---

## 1. Ringkasan Eksekutif

Sistem Manajemen Proyek Tim adalah aplikasi web kolaborasi yang memungkinkan tim mengelola proyek, tugas, dan waktu kerja dalam satu platform terintegrasi. Aplikasi ini dirancang untuk menjembatani kesenjangan antara perencanaan proyek oleh Project Manager dan eksekusi harian oleh anggota tim, dengan mekanisme validasi dan pelaporan yang transparan.

Produk ini terbagi menjadi dua komponen utama yang dibangun dan di-deploy secara terpisah:

- **Landing Page** — antarmuka publik berbasis React.js untuk akuisisi pengguna.
- **Aplikasi Internal (Dashboard & Workspace)** — sistem utama berbasis Laravel + Livewire 4 untuk seluruh operasional tim.

---

## 2. Tujuan & Sasaran Produk

### Tujuan Bisnis

- Menyediakan platform terpusat untuk manajemen proyek, tugas, dan pelacakan waktu kerja tim.
- Menggantikan metode manual (spreadsheet, chat) dengan sistem yang terstruktur dan auditable.
- Menghasilkan laporan produktivitas tim yang dapat diekspor untuk keperluan administrasi.

### Sasaran Teknis

- Membangun sistem multi-role dengan kontrol akses berbasis middleware Laravel.
- Menyediakan pembaruan data real-time di dashboard tanpa full-page reload menggunakan Livewire polling.
- Memisahkan landing page publik dari sistem internal untuk performa dan keamanan yang optimal.

---

## 3. Target Pengguna

### 3.1 Administrator / Project Manager (PM)

**Deskripsi:** Pemimpin tim atau manajer yang bertanggung jawab atas perencanaan dan pemantauan proyek.

**Kebutuhan utama:**
- Membuat dan mengelola proyek serta tugas.
- Memantau produktivitas seluruh anggota tim secara real-time.
- Menyetujui atau menolak log waktu kerja karyawan.
- Mengekspor laporan produktivitas ke format Excel/CSV.

### 3.2 Employee / Team Member

**Deskripsi:** Anggota tim yang mengeksekusi tugas dan mencatat waktu kerja harian.

**Kebutuhan utama:**
- Melihat tugas yang ditugaskan kepada mereka.
- Memperbarui status tugas di Kanban Board.
- Mencatat jam kerja harian melalui Time Log Form.
- Melihat riwayat dan status persetujuan log kerja pribadi.

### 3.3 Calon Pengguna / Visitor (Landing Page)

**Deskripsi:** Individu atau perusahaan yang belum terdaftar dan sedang mengevaluasi produk.

**Kebutuhan utama:**
- Memahami fitur dan manfaat produk dengan cepat.
- Melihat informasi paket langganan.
- Menghubungi tim melalui contact form.
- Mendaftar akun atau login ke sistem.

---

## 4. Ruang Lingkup

### Termasuk dalam Scope

| No | Fitur | Komponen |
|----|-------|----------|
| 1 | Landing Page (Hero, Fitur Showcase, Pricing Coming Soon, Contact Form) | React.js |
| 2 | Autentikasi (Login, Logout) via Laravel Sanctum — tidak ada self-registration publik | Laravel + React |
| 3 | Dashboard Utama (Analytics Cards, Time Logs Table, Snapshot) | Laravel + Livewire |
| 4 | Manajemen Proyek CRUD | Laravel + Livewire |
| 5 | Manajemen Tugas CRUD + Kanban Board | Laravel + Livewire + Alpine.js |
| 6 | Komentar Tugas | Laravel + Livewire |
| 7 | Time Log Form & Personal Timesheet | Laravel + Livewire |
| 8 | Persetujuan Time Log (Approve/Reject) oleh PM | Laravel + Livewire |
| 9 | Ekspor laporan ke CSV / Excel | Laravel |
| 10 | Role & Permission Management (Admin/PM vs Employee) | Laravel Middleware |
| 11 | User Management — CRUD pengguna & penugasan role oleh Admin | Laravel + Livewire |

### Di Luar Scope

Lihat [Bagian 14](#14-di-luar-ruang-lingkup-out-of-scope).

---

## 5. Tech Stack

| Layer | Teknologi | Fungsi |
|-------|-----------|--------|
| Frontend Publik | React.js | Landing Page |
| Backend & Dashboard | Laravel (latest stable) | API, logika bisnis, rendering server-side |
| Reactive UI Internal | Livewire 4 | Komponen dinamis tanpa full-page reload |
| JavaScript Interaktivitas | Alpine.js | Drag-and-drop, toggle, interaksi ringan |
| Database | MySQL | Penyimpanan data relasional |
| Autentikasi | Laravel Sanctum | Token-based auth untuk SPA (React) + session auth untuk Livewire |
| Spreadsheet ringan | Sortable.js | Drag-and-drop Kanban (dibungkus Alpine.js) |
| Export | Laravel Excel / Spatie | Generate file .csv/.xlsx |

**Catatan implementasi:**
- Landing Page React.js di-deploy di domain utama (contoh: `proyektim.com`).
- Dashboard Laravel + Livewire di-deploy di subdomain (contoh: `app.proyektim.com`).
- Pemisahan ini bukan opsional — ini **wajib** untuk menghindari konflik routing dan memisahkan concerns secara bersih.

---

## 6. Arsitektur Sistem

```
┌─────────────────────────────────────────────────────────┐
│                   CLIENT (Browser)                      │
│                                                         │
│  ┌──────────────────────┐   ┌────────────────────────┐  │
│  │   React.js SPA       │   │  Laravel + Livewire    │  │
│  │   proyektim.com      │   │  app.proyektim.com     │  │
│  │                      │   │                        │  │
│  │  - Landing Page      │   │  - Dashboard           │  │
│  │  - Pricing           │   │  - Kanban Board        │  │
│  │  - Contact Form      │   │  - Time Logs           │  │
│  │  - Login/Register    │   │  - Project Management  │  │
│  └──────────┬───────────┘   └───────────┬────────────┘  │
└─────────────┼───────────────────────────┼───────────────┘
              │ HTTP (Sanctum Token)       │ HTTP (Session)
              ▼                           ▼
┌─────────────────────────────────────────────────────────┐
│               Laravel API Backend                       │
│                                                         │
│  - Auth (Sanctum)     - Role Middleware                 │
│  - RESTful Endpoints  - Business Logic                  │
│  - Export Engine      - Eloquent ORM                    │
└─────────────────────────────┬───────────────────────────┘
                              │
                              ▼
                    ┌─────────────────┐
                    │   MySQL DB      │
                    │   db_proyek     │
                    └─────────────────┘
```

---

## 7. Skema Database

### 7.1 Tabel Minimum (Wajib Ada)

#### `team_projects`

```sql
CREATE TABLE team_projects (
  id            INT PRIMARY KEY AUTO_INCREMENT,
  title         VARCHAR(200) NOT NULL,
  description   TEXT,
  client_name   VARCHAR(100),
  start_date    DATE,
  deadline      DATE NOT NULL,
  budget        DECIMAL(12,2) DEFAULT 0,
  priority      ENUM('low','medium','high','urgent') DEFAULT 'medium',
  status        ENUM('planning','active','on_hold','completed','cancelled') DEFAULT 'planning',
  created_by    INT,  -- FK ke users.id (direkomendasikan, bukan VARCHAR)
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### `tasks`

```sql
CREATE TABLE tasks (
  id               INT PRIMARY KEY AUTO_INCREMENT,
  project_id       INT NOT NULL,
  title            VARCHAR(200) NOT NULL,
  description      TEXT,
  assignee_id      INT,  -- FK ke users.id
  priority         ENUM('low','medium','high') DEFAULT 'medium',
  start_date       DATE,
  due_date         DATE,
  progress_percent DECIMAL(5,2) DEFAULT 0,
  status           ENUM('todo','in_progress','review','done') DEFAULT 'todo',
  completed_at     TIMESTAMP NULL,
  created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (project_id) REFERENCES team_projects(id) ON DELETE CASCADE
);
```

> **Catatan Kritis:** Skema minimum menggunakan `VARCHAR(100)` untuk `created_by` dan `assignee` — ini merupakan kelemahan desain. Kolom ini seharusnya berupa `INT` dengan Foreign Key ke tabel `users` agar referential integrity terjaga dan query relasional berfungsi benar. Perbaikan ini wajib dilakukan sebelum implementasi.

### 7.2 Tabel Tambahan (Direkomendasikan)

#### `users`

```sql
CREATE TABLE users (
  id         INT PRIMARY KEY AUTO_INCREMENT,
  name       VARCHAR(100) NOT NULL,
  email      VARCHAR(150) UNIQUE NOT NULL,
  password   VARCHAR(255) NOT NULL,
  role       ENUM('admin','project_manager','employee') DEFAULT 'employee',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### `time_logs`

```sql
CREATE TABLE time_logs (
  id             INT PRIMARY KEY AUTO_INCREMENT,
  user_id        INT NOT NULL,
  project_id     INT NOT NULL,
  task_id        INT,
  start_time     DATETIME NOT NULL,
  end_time       DATETIME NOT NULL,
  duration_hours DECIMAL(5,2) GENERATED ALWAYS AS
                   (TIMESTAMPDIFF(MINUTE, start_time, end_time) / 60) STORED,
  notes          TEXT,
  status         ENUM('pending','approved','rejected') DEFAULT 'pending',
  reviewed_by    INT,  -- FK ke users.id (PM yang approve/reject)
  reviewed_at    TIMESTAMP NULL,
  created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id)    REFERENCES users(id),
  FOREIGN KEY (project_id) REFERENCES team_projects(id),
  FOREIGN KEY (task_id)    REFERENCES tasks(id) ON DELETE SET NULL,
  FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
);
```

#### `task_comments`

```sql
CREATE TABLE task_comments (
  id         INT PRIMARY KEY AUTO_INCREMENT,
  task_id    INT NOT NULL,
  user_id    INT NOT NULL,
  comment    TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### 7.3 Diagram Relasi Entitas (ERD Ringkas)

```
users ─────────────┬──────────────────────────────────────────┐
  │ 1              │ 1                                         │ 1
  │                │                                           │
  │ N (created_by) │ N (assignee_id)                          │ N (user_id)
  ▼                ▼                                           ▼
team_projects ──► tasks ──────────────────────────────► time_logs
  │ 1               │ 1                                   ▲
  │                 │                                     │
  │ N (project_id)  │ N (task_id)                         │ N (project_id)
  └─────────────────┤                                     │
                    │                                team_projects
                    │ 1
                    │
                    │ N (task_id)
                    ▼
               task_comments ◄── users (user_id)
```

---

## 8. Spesifikasi Fitur

### 8.1 Modul A: Frontend Publik (React.js)

> **Scope:** Landing page saja. Dikerjakan dan di-deploy terpisah dari sistem internal.

---

#### F-A1: Hero Section

**Deskripsi:** Tampilan utama halaman depan yang memperkenalkan produk secara visual.

**Kriteria Penerimaan:**
- Menampilkan headline, sub-headline, dan value proposition produk.
- Terdapat dua tombol CTA: "Login" dan "Hubungi Kami".
- Tombol "Login" me-redirect ke `app.proyektim.com/login`.
- Tombol "Hubungi Kami" mengarahkan ke section Contact Form (F-A4).
- Tidak ada tombol "Mulai Gratis", "Daftar", atau "Register" — akun hanya dibuat oleh Admin.
- Responsif pada mobile, tablet, dan desktop.

---

#### F-A2: Fitur Showcase

**Deskripsi:** Penjelasan interaktif mengenai tiga fitur utama produk.

**Fitur yang ditampilkan:** Kanban Board, Time Tracker, Gantt Chart.

**Kriteria Penerimaan:**
- Setiap fitur memiliki ilustrasi/ikon, judul, dan deskripsi singkat.
- Animasi atau transisi halus saat pengguna scroll ke bagian ini.

---

#### F-A3: Pricing — Coming Soon Section

**Deskripsi:** Section pengganti halaman pricing, menginformasikan calon pengguna bahwa paket langganan belum tersedia dan mengarahkan mereka ke Contact Form.

**Kriteria Penerimaan:**
- Menampilkan teks yang jelas bahwa sistem pricing sedang dalam pengembangan (contoh: "Paket langganan segera hadir").
- Tidak menampilkan angka harga atau nama paket dalam bentuk apapun.
- Terdapat satu tombol CTA "Hubungi Kami" yang mengarahkan ke section Contact Form (F-A4) di halaman yang sama, atau ke halaman kontak terpisah.
- Tidak ada tombol "Pilih Paket", "Daftar", atau "Register" di section ini.

> **Alasan perubahan:** Menampilkan harga dan tombol "Pilih Paket" tanpa sistem billing yang berfungsi dan tanpa self-registration publik menciptakan ekspektasi yang tidak bisa dipenuhi. Section ini diganti dengan CTA yang jujur hingga billing diimplementasikan.

---

#### F-A4: Contact Form

**Deskripsi:** Formulir untuk calon klien menghubungi admin.

**Field:** Nama, Email, Pesan.

**Kriteria Penerimaan:**
- Validasi client-side untuk semua field wajib.
- Submit form mengirim data ke endpoint Laravel API.
- Menampilkan pesan sukses/gagal setelah submit.

---

#### F-A5: Authentication Gateway

**Deskripsi:** Halaman Login untuk pengguna yang sudah memiliki akun, terintegrasi dengan Laravel Sanctum.

**Kriteria Penerimaan:**
- Form Login: Email + Password, validasi error ditampilkan inline.
- Tidak ada form Register publik — akun hanya dibuat oleh Admin melalui User Management (F-E1).
- Setelah login berhasil, redirect ke `app.proyektim.com/dashboard`.
- Token Sanctum disimpan di memory (bukan localStorage) untuk keamanan.
- Terdapat pesan informatif bagi pengguna yang belum punya akun: "Belum punya akun? Hubungi administrator tim Anda." dengan link ke Contact Form (F-A4).

---

### 8.2 Modul B: Dashboard Utama (Laravel + Livewire)

---

#### F-B1: Analytics Cards

**Deskripsi:** Empat kartu ringkasan di bagian atas dashboard.

| Kartu | Data yang Ditampilkan |
|-------|----------------------|
| Total Logged Hours | Akumulasi jam kerja seluruh tim (minggu/bulan berjalan) |
| Target Hours | Target jam kerja wajib vs jam kerja aktual |
| Met Target | Jumlah anggota tim yang memenuhi target harian |
| Under Target | Jumlah anggota tim yang di bawah target harian |

**Kriteria Penerimaan:**
- Data diperbarui otomatis menggunakan `wire:poll` setiap 60 detik.
- PM melihat data seluruh tim; Employee melihat data pribadi saja.

---

#### F-B2: Today's Snapshot

**Deskripsi:** Panel pemantauan cepat anggota tim yang aktif hari ini.

**Kriteria Penerimaan:**
- Menampilkan nama anggota tim, proyek yang sedang dikerjakan, dan durasi kerja hari ini.
- Hanya menampilkan anggota yang memiliki time log dengan tanggal hari ini.
- Hanya dapat diakses oleh role Admin/PM.

---

#### F-B3: All Employee Time Logs Table

**Deskripsi:** Tabel terpusat riwayat seluruh log waktu kerja tim.

**Kolom tabel:** Nama Karyawan, Proyek, Tugas, Waktu Mulai, Waktu Selesai, Durasi, Catatan, Status (Pending/Approved/Rejected), Aksi.

**Kriteria Penerimaan:**
- PM dapat melihat semua log; Employee hanya melihat log milik sendiri.
- Kolom "Aksi" menampilkan tombol Approve/Reject hanya untuk PM dan log berstatus Pending.
- Tabel mendukung pagination (minimal 20 baris per halaman).

---

#### F-B4: Quick Filter & Search

**Deskripsi:** Filter instan pada Time Logs Table.

**Filter yang tersedia:** Rentang tanggal, nama proyek, status (Pending/Approved/Rejected).

**Kriteria Penerimaan:**
- Menggunakan `wire:model` Livewire — hasil filter tampil tanpa tombol "Submit".
- Filter dapat dikombinasikan (contoh: filter proyek + status secara bersamaan).

---

#### F-B5: Export Engine

**Deskripsi:** Ekspor data time log ke format file yang dapat dibaca di luar aplikasi.

**Format yang didukung:** `.csv`, `.xlsx`

**Kriteria Penerimaan:**
- Tombol Export hanya tersedia untuk role Admin/PM.
- File yang diekspor mengikuti filter yang sedang aktif (bukan seluruh data).
- Nama file mencantumkan tanggal ekspor (contoh: `time_logs_2026-06-17.xlsx`).

---

### 8.3 Modul C: Manajemen Proyek & Tugas

---

#### F-C1: Project CRUD (Project Studio)

**Deskripsi:** Pembuatan, pengeditan, penghapusan, dan penayangan daftar proyek.

**Field proyek:** Judul, Deskripsi, Nama Klien, Anggaran, Tanggal Mulai, Deadline, Prioritas, Status.

**Kriteria Penerimaan:**
- Hanya Admin/PM yang dapat membuat, mengedit, dan menghapus proyek.
- Validasi server-side untuk field wajib (Judul, Deadline).
- Penghapusan proyek menghapus semua tugas terkait secara cascade (sesuai FK `ON DELETE CASCADE`).
- Daftar proyek dapat difilter berdasarkan Status dan Prioritas.

---

#### F-C2: Task CRUD + Kanban Board

**Deskripsi:** Papan visual interaktif untuk mengelola dan memindahkan status tugas.

**Kolom Kanban:** Todo → In Progress → Review → Done

**Kriteria Penerimaan:**
- PM dapat membuat tugas baru dan menunjuk assignee dari daftar pengguna terdaftar.
- Employee dapat memindahkan tugas miliknya antar kolom dengan drag-and-drop (Sortable.js + Alpine.js).
- Pemindahan kartu tugas memperbarui kolom `status` di database secara real-time via Livewire.
- PM dapat memindahkan tugas milik siapa saja.
- Setiap kartu tugas menampilkan: judul, assignee, due date, dan indikator prioritas.

---

#### F-C3: Task Detail & Komentar

**Deskripsi:** Halaman detail tugas dengan fitur diskusi tim.

**Kriteria Penerimaan:**
- Klik kartu tugas membuka modal atau halaman detail.
- Detail menampilkan semua field tugas beserta daftar komentar secara kronologis.
- Semua anggota yang terlibat di proyek dapat menambahkan komentar.
- Komentar tidak dapat diedit atau dihapus setelah dikirim (untuk auditability).

---

### 8.4 Modul D: Pelacakan Waktu (Time Sheets)

---

#### F-D1: Time Log Form (+ Add Time Log)

**Deskripsi:** Modal pop-up untuk mencatat jam kerja harian.

**Field:** Proyek (dropdown), Tugas (dropdown, di-filter berdasarkan proyek yang dipilih), Jam Mulai, Jam Selesai, Catatan.

**Kriteria Penerimaan:**
- Durasi jam kerja dihitung otomatis dari selisih Jam Selesai dan Jam Mulai.
- Validasi: Jam Selesai tidak boleh lebih awal dari Jam Mulai.
- Validasi: Tidak boleh ada log yang tumpang tindih (overlapping) untuk pengguna yang sama.
- Log yang baru disubmit langsung muncul di tabel Time Logs (status: Pending).

---

#### F-D2: Personal Timesheet Tracker

**Deskripsi:** Halaman khusus karyawan untuk memantau produktivitas pribadi.

**Kriteria Penerimaan:**
- Menampilkan ringkasan total jam kerja per minggu dan per bulan.
- Menampilkan daftar log pribadi beserta status persetujuan masing-masing.
- Hanya menampilkan data milik pengguna yang sedang login.

---

### 8.5 Modul E: Manajemen Pengguna (User Management)

---

#### F-E1: User CRUD oleh Admin

**Deskripsi:** Admin membuat, mengedit, dan menonaktifkan akun pengguna serta menetapkan role masing-masing.

**Field pengguna:** Nama Lengkap, Email, Password (di-generate atau diisi manual), Role (Admin / Project Manager / Employee).

**Kriteria Penerimaan:**
- Hanya pengguna dengan role Admin yang dapat mengakses halaman ini — dilindungi middleware.
- Admin dapat membuat akun baru dengan role yang ditentukan saat pembuatan.
- Admin dapat mengubah role pengguna yang sudah ada.
- Admin dapat menonaktifkan akun (soft delete atau flag `is_active`) tanpa menghapus data historis (time log, komentar tetap ada).
- Email harus unik; sistem menolak duplikat dengan pesan error yang jelas.
- Tidak ada endpoint publik untuk self-registration yang menghasilkan akun aktif.

**Catatan implementasi:** Tombol "Register" di landing page React.js **tidak boleh** memanggil endpoint pembuatan akun langsung. Arahkan ke halaman statis berisi instruksi untuk menghubungi Admin, atau ke Contact Form (F-A4).

---

## 9. Role & Permission Matrix

| Fitur / Aksi | Admin / PM | Employee |
|---|:---:|:---:|
| Melihat semua proyek | ✅ | ❌ (hanya yang ditugaskan) |
| Membuat proyek baru | ✅ | ❌ |
| Mengedit/hapus proyek | ✅ | ❌ |
| Melihat semua tugas | ✅ | ❌ (hanya milik sendiri) |
| Membuat & menugaskan tugas | ✅ | ❌ |
| Memindahkan status tugas (Kanban) | ✅ (semua tugas) | ✅ (tugas milik sendiri) |
| Menambahkan komentar tugas | ✅ | ✅ |
| Membuat time log | ✅ | ✅ (milik sendiri) |
| Approve / Reject time log | ✅ | ❌ |
| Melihat semua time log | ✅ | ❌ (hanya milik sendiri) |
| Ekspor laporan CSV/Excel | ✅ | ❌ |
| Melihat Today's Snapshot | ✅ | ❌ |
| Melihat dashboard analytics (semua tim) | ✅ | ❌ (data pribadi saja) |
| Membuat akun pengguna baru | ✅ (Admin saja) | ❌ |
| Mengedit data & role pengguna | ✅ (Admin saja) | ❌ |
| Menonaktifkan / menghapus pengguna | ✅ (Admin saja) | ❌ |

---

## 10. Alur Kerja (User Flow)

### Alur 1: Perencanaan Proyek (PM)

```
PM Login
  └─► Dashboard
        └─► Project Studio → Buat Proyek Baru
              └─► Isi detail proyek (judul, klien, deadline, prioritas)
                    └─► Tambahkan Tugas ke proyek
                          └─► Tentukan assignee, due date, prioritas per tugas
                                └─► Tugas muncul di Kanban Board Employee
```

### Alur 2: Eksekusi Tugas (Employee)

```
Employee Login
  └─► Dashboard (tampilan personal)
        └─► Kanban Board
              └─► Lihat tugas dengan status "Todo"
                    └─► Drag kartu → kolom "In Progress"
                          └─► [Opsional] Tambah komentar di detail tugas
                                └─► Selesai → Drag kartu → kolom "Done"
```

### Alur 3: Pencatatan & Validasi Waktu Kerja

```
Employee
  └─► Klik "+ Add Time Log"
        └─► Pilih proyek & tugas → isi jam mulai & selesai → tulis catatan
              └─► Submit → Log muncul di tabel (status: Pending)
                    └─► PM melihat log di Dashboard (Today's Snapshot & Tabel)
                          └─► PM klik "Approve" atau "Reject"
                                ├─► Approved → Jam terhitung di metrik produktivitas
                                └─► Rejected → Status berubah, Employee dapat melihat
```

### Alur 4: Ekspor Laporan (PM)

```
PM
  └─► Dashboard → Time Logs Table
        └─► Set filter (rentang tanggal, proyek, status = Approved)
              └─► Klik "Export" → Pilih format (.csv / .xlsx)
                    └─► File diunduh ke komputer PM
```

---

## 11. Kriteria Keberhasilan

| Kriteria | Metrik Target |
|----------|---------------|
| Dashboard memuat dalam kondisi normal | < 3 detik |
| Pembaruan real-time via polling | Interval ≤ 60 detik |
| Semua operasi CRUD berhasil tersimpan | 100% (no silent failure) |
| Role-based access tidak dapat di-bypass | 0 akses tidak sah |
| Export file menghasilkan data yang akurat | Data sesuai filter aktif |
| Drag-and-drop Kanban memperbarui DB | Setiap perpindahan tersimpan |

---

## 12. Asumsi & Ketergantungan

**Asumsi:**

- Setiap pengguna memiliki satu role saja (tidak ada multi-role).
- Satu tugas hanya memiliki satu assignee (tidak ada co-assignee).
- Kalkulasi durasi `time_logs` menggunakan `GENERATED COLUMN` MySQL atau dihitung di layer aplikasi sebelum disimpan.
- Tidak ada self-registration publik. Hanya Admin yang dapat membuat akun pengguna baru dan menentukan role-nya (Admin, Project Manager, atau Employee). Tombol "Register" di landing page React diarahkan ke halaman informasi atau formulir permintaan akses — bukan ke form registrasi yang langsung membuat akun.

**Ketergantungan:**

- Server hosting yang mendukung dua subdomain dengan konfigurasi SSL terpisah.
- Versi PHP ≥ 8.2 untuk kompatibilitas Laravel terbaru.
- Node.js tersedia di environment build untuk kompilasi aset React.js.
- MySQL 8.0+ untuk mendukung `GENERATED COLUMNS`.

---

## 13. Risiko & Mitigasi

| Risiko | Probabilitas | Dampak | Mitigasi |
|--------|:---:|:---:|--------|
| Livewire polling terlalu agresif membebankan server | Medium | Medium | Gunakan interval polling minimal 30-60 detik; pertimbangkan Laravel Echo + WebSocket untuk produksi |
| Konflik CORS antara React SPA dan Laravel API | Medium | Tinggi | **Wajib dikonfigurasi sebelum memulai integrasi autentikasi.** Di `config/cors.php`: set `'allowed_origins' => ['https://proyektim.com']`, `'allowed_methods' => ['GET','POST','PUT','DELETE','OPTIONS']`, `'allowed_headers' => ['Content-Type','Authorization','Accept','X-Requested-With']`, `'supports_credentials' => true`. Di React, setiap request Axios/fetch harus menyertakan `withCredentials: true`. Jangan gunakan wildcard `'*'` pada `allowed_origins` — ini akan memblokir pengiriman cookie Sanctum. |
| Drag-and-drop Kanban gagal memperbarui status di DB | Low | Tinggi | Tambahkan optimistic UI + error handler; tampilkan notifikasi gagal jika request tidak berhasil |
| Ekspor file besar memakan memori server | Low | Medium | Gunakan Laravel Excel dengan chunk reading; batasi rentang ekspor maksimal 3 bulan |
| Overlapping time log tidak terdeteksi | Medium | Medium | Tambahkan validasi query di server-side, bukan hanya di client |

---

## 14. Di Luar Ruang Lingkup (Out of Scope)

Fitur-fitur berikut **tidak termasuk** dalam versi ini dan tidak boleh dikerjakan kecuali ada persetujuan eksplisit:

- Integrasi payment gateway (billing aktual, bukan simulasi).
- Gantt Chart interaktif dengan dependency antar tugas.
- Notifikasi email atau push notification.
- Mobile application (Android/iOS native).
- Integrasi third-party tools (Slack, Google Calendar, Jira, dll.).
- Multi-tenancy (satu instance untuk banyak perusahaan berbeda).
- Fitur chat/messaging real-time antar anggota tim.
- Sistem audit log perubahan data (activity log lengkap).
- Two-factor authentication (2FA).
- Fitur Gantt Chart (disebutkan di landing page sebagai showcase tetapi tidak diimplementasikan di backend pada versi ini).

> **Catatan:** Gantt Chart disebutkan dalam Fitur Showcase di landing page. Ini berarti ada ketidaksesuaian antara apa yang dipromosikan ke calon pengguna dan apa yang benar-benar tersedia. Ini perlu ditangani — baik dengan mengimplementasikan Gantt Chart (tambah scope), atau dengan menghapusnya dari landing page (kurangi klaim).

---

*Dokumen ini adalah living document dan dapat diperbarui seiring perkembangan proyek. Setiap perubahan scope harus didiskusikan dan disetujui sebelum implementasi dimulai.*
