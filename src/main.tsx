import {StrictMode} from 'react';
import {createRoot} from 'react-dom/client';
import App from './App.tsx';
import './index.css';

// Mendapatkan elemen HTML root dan membuat root React DOM untuk merender aplikasi
createRoot(document.getElementById('root')!).render(
  // StrictMode digunakan untuk mendeteksi potensi masalah dalam aplikasi selama mode development
  <StrictMode>
    <App />
  </StrictMode>,
);
