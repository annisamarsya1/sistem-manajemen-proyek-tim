<h1 align="center">Sistem Manajemen Proyek Tim</h1>

<p align="center">
  <img src="./<img width="1600" height="805" alt="landing.png" src="https://github.com/user-attachments/assets/9ba98786-fac7-4a71-af6a-88ea2cb95756" />
 />
" alt="Landing Page TaskSync" width="100%">
</p>

<p align="center">
  <img src="./image.png" alt="Dashboard TaskSync" width="100%">
</p>

<p align="center">
Sistem Manajemen Proyek Tim (TaskSync) merupakan aplikasi berbasis web yang dikembangkan untuk membantu tim dalam mengelola proyek, membagi tugas, memantau progres pekerjaan, serta mencatat waktu kerja setiap anggota. Aplikasi ini dilengkapi dengan Dashboard, Project Studio, Kanban Board, Timesheet, dan User Management untuk memudahkan pengelolaan proyek serta meningkatkan koordinasi dan produktivitas tim.
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
