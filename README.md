Sistem Manajemen Proyek Tim

<p align="center">
  <img src="./<img width="1920" height="1080" alt="Screenshot 2026-11-01 194039" src="https://github.com/user-attachments/assets/fdf35f4d-9b7f-4189-82f5-305ffa5a5a17" />
" alt="Dashboard Sistem Manajemen Proyek Tim" width="100%">
</p>

<p align="center">
  Aplikasi web kolaborasi untuk manajemen proyek, tugas, dan pelacakan waktu kerja tim.
</p>

## Tech Stack
- Laravel (latest), Livewire 4, Alpine.js, MySQL, Tailwind CSS
- Export: maatwebsite/excel

## Cara Install
1. `composer install`
2. `cp .env.example .env` dan konfigurasi DB
3. `php artisan key:generate`
4. `php artisan migrate`
5. `php artisan db:seed` (akun awal)
6. `php artisan db:seed --class=DemoSeeder` (data demo, opsional)
7. `php artisan serve`

## Akun Demo
| Role | Email | Password |
|------|-------|----------|
| Admin | admin@proyektim.com | password |
| Project Manager | pm@proyektim.com | password |
| Employee | employee1@proyektim.com | password |

## Fitur
- Dashboard dengan analytics cards dan auto-refresh (60 detik)
- Kanban Board dengan drag-and-drop (Sortable.js)
- Time Log dengan validasi overlap
- Approve/Reject time log oleh PM
- Export CSV & Excel dengan filter
- User Management (Admin only)
