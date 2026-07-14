import React from 'react';
import { Layers } from 'lucide-react';

/**
 * Komponen Footer
 * 
 * Bagian terbawah dari landing page yang menampilkan logo, slogan,
 * informasi hak cipta, dan tautan legal (Kebijakan Privasi, dll).
 */
export default function Footer() {
  // Dapatkan tahun saat ini secara dinamis untuk baris copyright
  const currentYear = new Date().getFullYear();

  return (
    <footer id="main-footer" className="bg-[#060b13] border-t border-blue-950/40 py-12 md:py-16 relative overflow-hidden">
      <div className="max-w-7xl mx-auto px-6 md:px-12 relative z-10">
        
        <div className="flex flex-col md:flex-row items-center justify-between gap-6 pb-8 border-b border-blue-950/30">
          
          {/* Logo */}
          <div className="flex items-center gap-2.5 font-display text-lg font-bold tracking-tight text-white" id="footer-logo">
            <div className="p-1.5 rounded-lg bg-blue-500/10 border border-blue-500/25">
              <Layers className="w-4 h-4 text-blue-400" />
            </div>
            <span>
              Proyek<span className="text-blue-400 font-normal">Tim</span>
            </span>
          </div>

          {/* Slogan */}
          <p className="text-xs text-slate-500 max-w-sm text-center md:text-right leading-relaxed">
            Sistem manajemen terintegrasi untuk mempercepat kolaborasi dan produktivitas tim Anda secara transparan dan akurat.
          </p>

        </div>

        {/* Bottom copyright */}
        <div className="flex flex-col md:flex-row items-center justify-between gap-4 pt-8 text-[11px] text-slate-600">
          <p id="footer-copyright">
            &copy; {currentYear} Proyek Tim. Seluruh Hak Cipta Dilindungi.
          </p>
          <div className="flex gap-6" id="footer-legal-links">
            <span className="hover:text-slate-400 transition-colors cursor-pointer">Kebijakan Privasi</span>
            <span className="hover:text-slate-400 transition-colors cursor-pointer">Syarat & Ketentuan</span>
          </div>
        </div>

      </div>
    </footer>
  );
}
