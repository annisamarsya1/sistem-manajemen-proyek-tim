# Sistem Manajemen Proyek Tim

Aplikasi web kolaborasi untuk manajemen proyek, tugas, dan pelacakan waktu kerja tim.

## Tech Stack

- **Backend:** Laravel 13, PHP 8.5
- **Reactive UI:** Livewire 4, Alpine.js
- **Database:** MySQL
- **Styling:** Tailwind CSS v4
- **Drag & Drop:** Sortable.js
- **Export:** maatwebsite/excel

## Cara Install

```bash
# 1. Install dependensi PHP
composer install

# 2. Install dependensi Node.js
npm install

# 3. Salin file environment dan konfigurasi
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Konfigurasi koneksi database di .env
# DB_DATABASE=nama_database
# DB_USERNAME=user
# DB_PASSWORD=password

# 6. Jalankan migrasi
php artisan migrate

# 7. Seed akun awal (admin, PM, employee)
php artisan db:seed

# 8. (Opsional) Seed data demo untuk keperluan presentasi
php artisan db:seed --class=DemoSeeder

# 9. Build aset frontend
npm run build

# 10. Jalankan server development
php artisan serve
```

## Akun Demo

| Role            | Email                      | Password |
|-----------------|----------------------------|----------|
| Admin           | admin@proyektim.com        | password |
| Project Manager | pm@proyektim.com           | password |
| Employee 1      | employee1@proyektim.com    | password |
| Employee 2      | employee2@proyektim.com    | password |
| Employee 3      | employee3@proyektim.com    | password |

## Fitur

- **Dashboard** — analytics cards (logged hours, met/under target) dan tabel time logs semua karyawan dengan auto-refresh setiap 60 detik
- **Kanban Board** — tampilan papan tugas per kolom (Todo → In Progress → Review → Done) dengan drag-and-drop menggunakan Sortable.js
- **Time Log Form** — pencatatan jam kerja harian dengan validasi overlap otomatis
- **Approve / Reject Time Log** — PM dapat menyetujui atau menolak log kerja dari dashboard
- **Personal Timesheet** — ringkasan jam kerja pribadi per minggu dan bulan dengan filter rentang tanggal
- **Project Studio** — CRUD proyek dengan filter status dan prioritas (Admin/PM only)
- **Export CSV & Excel** — ekspor data time log sesuai filter aktif (Admin/PM only)
- **User Management** — CRUD pengguna, atur role, dan toggle aktif/nonaktif (Admin only)

## Role & Permission

| Fitur                        | Admin | Project Manager | Employee |
|------------------------------|:-----:|:---------------:|:--------:|
| Buat/edit/hapus proyek       | ✅    | ✅              | ❌       |
| Buat/edit/hapus tugas        | ✅    | ✅              | ❌       |
| Pindah status tugas (Kanban) | ✅    | ✅              | ✅ (milik sendiri) |
| Approve/Reject time log      | ✅    | ✅              | ❌       |
| Export laporan               | ✅    | ✅              | ❌       |
| Lihat semua time logs        | ✅    | ✅              | ❌ (hanya milik sendiri) |
| User Management              | ✅    | ❌              | ❌       |

## Struktur Direktori Utama

```
app/
  Livewire/          # Semua komponen Livewire (Dashboard, KanbanBoard, dll.)
  Models/            # Eloquent models (User, TeamProject, Task, TimeLog, dll.)
  Exports/           # Laravel Excel export classes
database/
  migrations/        # Skema database
  seeders/           # DatabaseSeeder (akun awal) & DemoSeeder (data demo)
resources/views/
  layouts/           # Layout utama (app.blade.php, auth.blade.php)
  livewire/          # View untuk setiap komponen Livewire
routes/
  web.php            # Semua route web
```
