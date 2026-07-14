# Landing Page — Sistem Manajemen Proyek Tim

Repository ini berisi kode sumber untuk landing page publik aplikasi **Proyek Tim**. Aplikasi ini dirancang menggunakan React (Vite) dan dikoordinasikan secara penuh dengan backend Laravel + Livewire yang berjalan terpisah di subdomain berbeda (`app.proyektim.com`).

## 🛠️ Tech Stack

- **Framework**: [React.js](https://react.dev/) v19 (di-bootstrap menggunakan Vite)
- **Routing**: [React Router](https://reactrouter.com/) v6 untuk navigasi SPA
- **HTTP Client**: [Axios](https://axios-http.com/) untuk pemanggilan API terintegrasi
- **Styling**: [Tailwind CSS](https://tailwindcss.com/) v4 dengan arsitektur modern
- **Animasi**: [Motion](https://motion.dev/) (Framer Motion) untuk transisi interaktif
- **Ikon**: [Lucide React](https://lucide.dev/) untuk visualisasi representatif

---

## 🚀 Panduan Setup & Instalasi

### 1. Kloning dan Instalasi Dependensi
Jalankan perintah berikut untuk mengunduh modul dependensi yang dibutuhkan:
```bash
npm install
```

### 2. Konfigurasi Environment Variables
Pastikan Anda memiliki berkas `.env` di direktori akar. Salin berkas `.env.example` atau gunakan nilai default berikut:

```env
# URL API backend Laravel
VITE_API_URL="http://localhost:8000/api"

# URL Utama Aplikasi Laravel (untuk Dashboard & Auth Cookie)
VITE_APP_URL="http://localhost:8000"
```

### 3. Menjalankan Server Pengembangan (Local Dev)
Untuk menjalankan aplikasi secara lokal dengan modul refresh instan, gunakan:
```bash
npm run dev
```
Aplikasi akan aktif di port **3000** (`http://localhost:3000`).

### 4. Kompilasi Produksi (Production Build)
Untuk mem-build landing page menjadi file statis siap sebar di direktori `dist/`, gunakan:
```bash
npm run build
```

---

## 🔑 Integrasi Backend Laravel (Penting!)

Aplikasi landing page ini bergantung penuh pada fungsionalitas backend Laravel yang berjalan terpisah. Berikut adalah catatan integrasi krusial:

1. **Laravel Sanctum Auth (Cookie-based)**:
   - Alur masuk pada halaman `/login` menggunakan otentikasi SPA berbasis cookie dari Laravel Sanctum.
   - **Urutan Alur Wajib**:
     1. Melakukan request `GET` ke `${VITE_APP_URL}/sanctum/csrf-cookie` untuk menginisialisasi cookie CSRF.
     2. Melakukan request `POST` ke `${VITE_API_URL}/login` dengan kredensial pengguna.
     3. Setelah sukses, browser akan melakukan redirect halaman penuh (`window.location.href`) ke `${VITE_APP_URL}/dashboard`.
   - Tidak ada token yang disimpan secara manual di `localStorage` atau `sessionStorage`.

2. **Project Stats Endpoint**:
   - Komponen statistik proyek (`ProjectStats.tsx`) mengambil data publik secara real-time dari `GET /public/stats`.
   - Format respons yang diharapkan dari backend:
     ```json
     { "active_projects": 12, "completed_projects": 34 }
     ```
   - **Mekanisme Toleransi**: Jika endpoint tersebut belum dikonfigurasi di Laravel atau mengembalikan galat, komponen statistik ini akan secara otomatis disembunyikan sepenuhnya dari pandangan pengunjung agar tidak merusak antarmuka landing page.

3. **Contact Inquiries**:
   - Formulir kontak mengirimkan data input (Nama, Email, Pesan) langsung ke endpoint `POST /contact` di backend Laravel.
   - Dilengkapi validasi client-side yang ramah pengguna.
