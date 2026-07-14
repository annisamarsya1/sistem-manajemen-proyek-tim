import React from 'react';
import { BrowserRouter, Routes, Route } from 'react-router-dom';
import LandingPage from './pages/LandingPage';

/**
 * Komponen Utama Aplikasi (App)
 * 
 * Mengatur routing dasar aplikasi menggunakan React Router.
 * Saat ini hanya memiliki satu rute utama yang mengarah ke LandingPage.
 */
export default function App() {
  return (
    // Membungkus aplikasi dengan BrowserRouter untuk mengaktifkan navigasi berbasis History API browser
    <BrowserRouter>
      {/* Routes digunakan untuk mendefinisikan daftar rute yang tersedia */}
      <Routes>
        {/* Rute utama: ketika path "/", render komponen LandingPage */}
        <Route path="/" element={<LandingPage />} />
        
        {/* Rute fallback: jika path tidak ditemukan (404), arahkan kembali ke LandingPage */}
        {/* Fallback route back to home */}
        <Route path="*" element={<LandingPage />} />
      </Routes>
    </BrowserRouter>
  );
}
