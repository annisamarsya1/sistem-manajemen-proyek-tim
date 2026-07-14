import React from 'react';
import { CalendarRange, Sparkles, Send } from 'lucide-react';

/**
 * Komponen PricingComingSoon
 * 
 * Seksi placeholder untuk paket langganan yang mengarahkan pengguna
 * untuk menghubungi tim terkait demo khusus (karena fitur self-service masih dikembangkan).
 */
export default function PricingComingSoon() {
  // Fungsi utilitas kecil untuk scroll ke bagian kontak
  const scrollToContact = (e: React.MouseEvent<HTMLAnchorElement>) => {
    e.preventDefault();
    const element = document.getElementById('kontak');
    if (element) {
      element.scrollIntoView({ behavior: 'smooth', block: 'start' });
      window.history.pushState(null, '', '#kontak');
    }
  };

  return (
    <section id="pricing" className="py-24 bg-[#060b13] border-t border-blue-950/40 relative overflow-hidden">
      {/* Decorative gradient sphere in the background */}
      <div className="absolute -bottom-1/3 left-1/3 w-[500px] h-[500px] rounded-full bg-blue-500/5 blur-[100px] pointer-events-none" />

      <div className="max-w-4xl mx-auto px-6 relative z-10 text-center">
        
        {/* Card container */}
        <div 
          className="relative bg-gradient-to-b from-[#0b1325]/40 to-[#060b13]/20 border border-blue-950/40 rounded-3xl p-8 md:p-16 overflow-hidden shadow-2xl group hover:border-blue-500/30 transition-all duration-300"
          id="pricing-container-card"
        >
          {/* Subtle light streak */}
          <div className="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-blue-500/20 to-transparent" />

          {/* Icon */}
          <div className="inline-flex p-3 rounded-2xl bg-blue-500/10 border border-blue-500/20 text-blue-400 mb-6">
            <CalendarRange className="w-8 h-8" />
          </div>

          <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/25 text-blue-300 text-[10px] font-bold uppercase tracking-wider mb-4">
            <Sparkles className="w-3.5 h-3.5 text-blue-400" />
            <span>Coming Soon</span>
          </div>

          <h2 
            className="font-display text-2xl md:text-4xl font-extrabold text-white mb-4"
            id="pricing-title"
          >
            Paket Langganan Segera Hadir
          </h2>

          <p 
            className="text-slate-400 text-sm md:text-base max-w-xl mx-auto mb-8 leading-relaxed"
            id="pricing-desc"
          >
            Sistem pembayaran mandiri dan paket langganan kami sedang dalam tahap pengembangan akhir. Sementara waktu, Anda dapat menghubungi tim kami langsung untuk demo khusus dan penawaran korporat terbaik.
          </p>

          <div className="flex justify-center" id="pricing-actions">
            <a
              href="#kontak"
              onClick={scrollToContact}
              className="px-8 py-3.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl shadow-lg shadow-blue-600/10 transition-all duration-200 flex items-center gap-2 group cursor-pointer"
            >
              Hubungi Tim Kami
              <Send className="w-4 h-4 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform" />
            </a>
          </div>
        </div>

      </div>
    </section>
  );
}
