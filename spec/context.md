# Konteks Proyek

## Nama & Tujuan Proyek
**TaskSync Dashboard** (berdasarkan nama direktori dan variabel environment). Ini adalah sistem manajemen proyek dan sinkronisasi tugas yang dirancang untuk mengelola proyek tim, melacak tugas menggunakan papan Kanban, mencatat jam kerja karyawan (timesheet), serta menyediakan akses berbasis peran (role-based) untuk admin, manajer proyek, dan anggota tim.

## Sistem Autentikasi
- **Mekanisme**: Menggunakan autentikasi berbasis sesi bawaan Laravel (guard `web`) dengan provider `users` berbasis Eloquent untuk rute web. Selain itu, sistem menggunakan Laravel Sanctum (`auth:sanctum`) untuk autentikasi endpoint API.
- **Peran (Roles)**: Diimplementasikan melalui kolom `role` pada tabel `users`. Aplikasi secara khusus mengecek peran `admin` dan `pm` (Project Manager).
- **Middleware Kustom**:
  - `EnsureIsAdmin.php` (alias: `admin_only`): Membatasi akses khusus untuk pengguna dengan `role === 'admin'`.
  - `EnsureIsAdminOrPM.php` (alias: `admin_or_pm`): Membatasi akses untuk pengguna dengan `role === 'admin'` atau `role === 'pm'`.

## Stack Teknologi
- **Framework**: Laravel `^13.8` (Catatan: Tertulis di `composer.json`, kemungkinan salah ketik/typo untuk `11.8` jika melihat versi Laravel saat ini), PHP `^8.3`.
- **Frontend**: Livewire `^4.3` dikombinasikan dengan Tailwind CSS `^4.0.0` (dikompilasi melalui Vite). Frontend menggunakan pendekatan seperti TALL-stack (Tailwind, Alpine?, Laravel, Livewire) tanpa memisahkan framework JS seperti Vue atau React.
- **Database**: SQLite digunakan sebagai driver default pada `.env.example`.
- **Paket Tambahan**: `barryvdh/laravel-dompdf` (pembuatan PDF) dan `maatwebsite/excel` (ekspor Excel).

## Ringkasan Database
- `users`: Tabel utama yang menyimpan kredensial pengguna, `role`, dan boolean `is_active`.
- `team_projects`: Menyimpan proyek beserta atributnya seperti `budget`, `client_name`, `start_date`, `deadline`, `status` (planning, active, on_hold, completed, cancelled), `priority`, dan foreign key `created_by` ke tabel `users`.
- `tasks`: Merupakan bagian dari sebuah proyek (`project_id`). Terdiri dari `assignee_id` (pengguna), `title`, `description`, `priority`, `start_date`, `due_date`, `progress_percent`, `status` (todo, in_progress, review, done), dan `completed_at`.
- `time_logs`: Mencatat waktu pengerjaan oleh `user_id` pada `project_id` dan (opsional) `task_id`. Melacak `start_time`, `end_time`, `duration_hours`, beserta `status` persetujuan (pending, approved, rejected) dan siapa yang mereview (`reviewed_by`).
- `task_comments`: Menyimpan komentar yang dibuat oleh pengguna pada tugas-tugas tertentu.
- `personal_access_tokens`: Tabel bawaan Sanctum untuk token API.

## Fitur Utama
Berdasarkan rute dan komponen Livewire:
- **Dashboard**: Tampilan ringkasan utama (`\App\Livewire\Dashboard::class`), dapat diakses oleh semua pengguna yang sudah login.
- **Project Studio**: Antarmuka manajemen proyek (`\App\Livewire\ProjectStudio::class`), dibatasi khusus untuk Admin dan PM.
- **Kanban Board**: Papan visual manajemen tugas (`\App\Livewire\KanbanBoard::class`), dapat diakses oleh semua peran.
- **Personal Timesheet**: Antarmuka untuk mencatat jam kerja (`\App\Livewire\PersonalTimesheet::class`).
- **User Management**: Antarmuka khusus admin untuk mengelola pengguna (`\App\Livewire\UserManagement::class`).
- **Endpoint API**:
  - `/api/public/stats`: Mengembalikan data langsung (proyek aktif, proyek selesai, jam tercatat minggu ini) untuk halaman Landing Page publik.
  - `/api/contact`: Menerima pengiriman form kontak.
  - `/api/user`: Endpoint terproteksi untuk mengambil data pengguna yang terautentikasi.

## Hal-hal yang Tidak Biasa atau Perlu Diperhatikan
- **Routing Hanya dengan Livewire**: Aplikasi ini sepenuhnya bergantung pada komponen Livewire untuk routing frontend webnya. Tidak ada controller tradisional di dalam `app/Http/Controllers` selain `AuthController` dan base `Controller`.
- **Anomali Versi Laravel**: File `composer.json` mencantumkan `"laravel/framework": "^13.8"`, hal ini sangat tidak wajar dan kemungkinan besar adalah salah ketik (typo) oleh developer (karena lini masa versi Laravel saat ini belum mencapai v13).
- **Minim Pengujian**: Direktori `tests/Feature` dan `tests/Unit` saat ini tidak memiliki tes komprehensif untuk alur autentikasi atau endpoint API (hanya terdapat file bawaan `ExampleTest.php`).
- **API Publik untuk Landing Page**: Aplikasi dashboard utama secara eksplisit mengekspos data melalui API publik (`/api/public/stats`) untuk digunakan oleh eksternal Landing Page.

## Dibuat Pada
2026-07-03 (berdasarkan package.json)
