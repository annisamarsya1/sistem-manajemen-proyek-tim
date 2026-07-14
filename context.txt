# Project Context

## Project Name & Purpose
- **Name**: TaskSync Landing Page (Proyek Tim)
- **Purpose**: Landing page publik untuk aplikasi manajemen proyek tim. Aplikasi ini merupakan React SPA (Single Page Application) mandiri yang dirancang untuk berkoordinasi dengan backend terpisah berbasis Laravel + Livewire yang menangani logika aplikasi utama dan dashboard.

## React Stack
- **Framework / Build Tool**: React 19 (v19.0.1) dengan Vite (v6.2.3)
- **Language**: TypeScript (~5.8.2)
- **Routing**: React Router v7 (`react-router-dom` v7.18.1)
- **Styling**: Tailwind CSS v4 (`@tailwindcss/vite`)
- **Animation / Graphics**: Motion (Framer Motion v12.23.24) dan Three.js (v0.183.2)
- **Icons**: Lucide React
- **State Management**: Hanya state komponen lokal (misalnya, `useState`). Tidak ada library state management global (seperti Redux atau Zustand) yang digunakan.
- **Data Fetching**: Custom wrapper yang menggunakan `fetch` API bawaan (`src/lib/api.ts`). Tidak ada TanStack Query atau Axios yang digunakan dalam basis kode (meskipun Axios disebutkan di `README`).
- **Testing**: Tidak ada library testing yang diinstal.

## Authentication & Authorization
- **Auth Mechanism**: Otentikasi ditangani sepenuhnya oleh backend Laravel yang terpisah menggunakan Laravel Sanctum (berbasis Cookie).
- **Integration**: Utilitas `fetchData` di `src/lib/api.ts` dikonfigurasi dengan `credentials: 'include'` untuk secara otomatis mengirim dan menerima cookie Sanctum di seluruh subdomain/port.
- **Route Protection**: Tidak ada protected routes atau role-based access control pada aplikasi React ini. Aplikasi ini berfungsi sebagai halaman publik, dan logika login pada akhirnya akan mengarahkan ulang (*redirect*) pengguna ke backend Laravel (`VITE_APP_URL`).

## Project Structure
Proyek ini menggunakan struktur datar (*flat structure*) sederhana yang khas untuk situs landing page tunggal:
- `src/`
  - `components/`: Komponen UI (contoh: `Hero`, `ContactForm`, `Navbar`, `FluidSection`, `ProjectStats`).
  - `pages/`: Berisi komponen level halaman (hanya ada `LandingPage.tsx`).
  - `lib/`: Utilitas dan layanan inti (contoh: `api.ts` untuk pengambilan data).
  - `assets/`: Aset visual statis.

## Routing Summary
- **Main Routes**: Dikelola melalui `BrowserRouter` di `src/App.tsx`.
- **Pages**: Hanya ada satu route utama (`/`) yang me-render `<LandingPage />`.
- **Fallback**: Wildcard route (`*`) mengarahkan ulang path yang tidak dikenal kembali ke `<LandingPage />`.
- **Navigation**: Menggunakan navigasi berbasis hash untuk menggulir (*scroll*) ke berbagai section (contoh: `#about`, `#fitur`, `#kontak`), yang ditangani secara manual di komponen `Navbar` dan `LandingPage`.

## Data & API Summary
- **API Client**: Terdapat custom API wrapper (`fetchData`) di `src/lib/api.ts` yang menstandarisasi request headers, JSON parsing, penanganan error, dan penyertaan cookie Sanctum.
- **Endpoints Used**:
  - `POST /contact`: Menangani pengiriman form kontak.
  - `GET /public/stats`: Mengambil statistik proyek secara real-time untuk ditampilkan di landing page.
- **Environment Variables**:
  - `VITE_API_URL`: URL API backend Laravel.
  - `VITE_APP_URL`: URL Aplikasi Utama Laravel (untuk redirect ke dashboard).
- **Database**: Tidak ada skema database, ORM, atau model di frontend. Semuanya sepenuhnya bergantung pada endpoint backend.

## Core Features
- **Hero Section**: Titik masuk visual utama.
- **Fluid Reveal Section**: Section dengan visual intensif yang menggunakan Three.js dan custom shader (`fluidShaders.ts`), yang dimuat menggunakan teknik lazy loading untuk mengoptimalkan performa.
- **Feature Showcase**: Menyoroti fitur inti aplikasi.
- **Project Stats**: Menampilkan metrik dinamis yang diambil dari backend.
- **Contact Inquiries**: Form kontak yang berfungsi penuh dengan validasi di sisi klien (*client-side*) yang mengirim pesan langsung ke backend Laravel.

## Testing Summary
- **Tests Available**: Tidak ada.
- **Testing Tools**: Tidak ada testing framework (seperti Jest, Vitest, Cypress, Playwright) yang ada dalam setup proyek atau `package.json`.

## Build & Deployment
- **Scripts**: 
  - `dev`: Menjalankan Vite development server.
  - `build`: Melakukan proses build aset statis ke direktori `dist/`.
  - `preview`: Menampilkan preview hasil build production secara lokal.
  - `lint`: Menjalankan pengecekan tipe data TypeScript (`tsc --noEmit`).
- **Deployment**: Tidak ada konfigurasi deployment (contoh: Docker, GitHub Actions, Vercel, Netlify) di dalam repository. Hasil build (`dist/`) dimaksudkan untuk disajikan secara statis.

## Anything Unusual or Worth Noting
- **Axios Discrepancy**: Berkas `README.md` menyebutkan Axios sebagai HTTP client, tetapi `package.json` tidak menyertakannya, dan `src/lib/api.ts` malah menggunakan `fetch` API bawaan.
- **React Router Usage**: `react-router-dom` diinstal dan digunakan untuk membungkus (*wrap*) aplikasi, tetapi karena ini adalah situs landing page tunggal, kegunaannya lebih banyak untuk menangani route fallbacks dan mengelola status URL, sementara navigasi sebenarnya sangat bergantung pada navigasi scroll berbasis hash.
- **Heavy Visuals**: Proyek ini menyertakan Three.js untuk fluid shader section. Section ini secara eksplisit menggunakan lazy load (`Suspense`) di `LandingPage.tsx` untuk mencegahnya memblokir pemuatan awal halaman, tetapi mungkin masih akan terasa berat jika dijalankan pada perangkat berspesifikasi rendah.
- **Missing Linter**: Tidak ada konfigurasi ESLint (`eslint.config.js` atau `.eslintrc`), melainkan hanya mengandalkan pengecekan kompilasi TypeScript untuk proses linting.

## Generated At
2026-07-03
