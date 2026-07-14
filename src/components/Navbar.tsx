import React, { useState, useEffect } from 'react';
import { Menu, X, Layers, ArrowRight } from 'lucide-react';

/**
 * Komponen Navbar
 * 
 * Bar navigasi utama untuk situs. Memiliki efek transparan transisi ke solid (blur) saat di-scroll.
 * Mendukung responsivitas dengan menu hamburger di layar kecil (mobile).
 */
export default function Navbar() {
  const [isOpen, setIsOpen] = useState(false); // State menu mobile terbuka/tertutup
  const [isScrolled, setIsScrolled] = useState(false); // State untuk mendeteksi scroll layar
  const [currentPath, setCurrentPath] = useState(window.location.pathname); // Melacak path saat ini

  // Effect untuk menangani event scroll dan perubahan path (SPA navigation)
  useEffect(() => {
    const handleScroll = () => {
      setIsScrolled(window.scrollY > 10);
    };

    const handlePathChange = () => {
      setCurrentPath(window.location.pathname);
    };

    window.addEventListener('scroll', handleScroll);
    // Track location changes in SPA
    window.addEventListener('popstate', handlePathChange);
    
    return () => {
      window.removeEventListener('scroll', handleScroll);
      window.removeEventListener('popstate', handlePathChange);
    };
  }, []);

  /**
   * Handler ketika link navigasi diklik.
   * Melakukan scroll mulus (smooth scroll) jika di halaman yang sama,
   * atau membiarkan navigasi default jika berpindah halaman.
   */
  const handleNavClick = (e: React.MouseEvent<HTMLAnchorElement>, targetId: string) => {
    const isLanding = window.location.pathname === '/';
    if (!isLanding) {
      // Let standard link navigation happen, it will reload or go to landing page with anchor
      return;
    }

    e.preventDefault();
    setIsOpen(false); // Menutup menu mobile saat link diklik
    const element = document.getElementById(targetId);
    if (element) {
      element.scrollIntoView({ behavior: 'smooth', block: 'start' });
      // Update URL hash without jumping
      window.history.pushState(null, '', `#${targetId}`);
    }
  };

  const isLanding = currentPath === '/';

  return (
    <nav
      id="main-navbar"
      className={`fixed top-0 left-0 right-0 z-50 transition-all duration-300 ${
        isScrolled
          ? 'bg-[#060b13]/85 backdrop-blur-md border-b border-blue-950/40 shadow-lg py-4'
          : 'bg-transparent py-6'
      }`}
    >
      <div className="max-w-7xl mx-auto px-6 md:px-12">
        <div className="flex items-center justify-between">
          {/* Logo */}
          <a
            href="/"
            className="flex items-center gap-2.5 font-display text-xl font-bold tracking-tight text-white group"
            id="nav-logo"
          >
            <div className="p-2 rounded-xl bg-blue-500/10 border border-blue-500/25 group-hover:bg-blue-500/20 group-hover:border-blue-500/40 transition-all">
              <Layers className="w-5 h-5 text-blue-400" />
            </div>
            <span>
              Task<span className="text-blue-400 font-normal">Sync</span>
            </span>
          </a>

          {/* Desktop Navigation */}
          <div className="hidden md:flex items-center gap-8" id="nav-desktop-menu">
            <a
              href={isLanding ? '#about' : '/#about'}
              onClick={(e) => handleNavClick(e, 'about')}
              className="text-sm font-medium text-slate-400 hover:text-white transition-colors"
            >
              About Us
            </a>
            <a
              href={isLanding ? '#fitur' : '/#fitur'}
              onClick={(e) => handleNavClick(e, 'fitur')}
              className="text-sm font-medium text-slate-400 hover:text-white transition-colors"
            >
              Fitur Utama
            </a>
            <a
              href={isLanding ? '#kontak' : '/#kontak'}
              onClick={(e) => handleNavClick(e, 'kontak')}
              className="text-sm font-medium text-slate-400 hover:text-white transition-colors"
            >
              Hubungi Kami
            </a>
            
          </div>

          {/* Mobile Menu Button */}
          <button
            onClick={() => setIsOpen(!isOpen)}
            className="md:hidden p-2 text-slate-400 hover:text-white focus:outline-none"
            aria-label="Toggle menu"
            id="nav-mobile-toggle"
          >
            {isOpen ? <X className="w-6 h-6" /> : <Menu className="w-6 h-6" />}
          </button>
        </div>
      </div>

      {/* Mobile Menu */}
      <div
        id="nav-mobile-menu"
        className={`md:hidden fixed inset-x-0 top-[73px] bg-[#060b13] border-b border-blue-950/40 transition-all duration-300 ease-in-out z-40 px-6 py-6 shadow-2xl ${
          isOpen ? 'opacity-100 translate-y-0 visible' : 'opacity-0 -translate-y-4 invisible pointer-events-none'
        }`}
      >
        <div className="flex flex-col gap-5">
          <a
            href={isLanding ? '#about' : '/#about'}
            onClick={(e) => handleNavClick(e, 'about')}
            className="text-base font-medium text-slate-400 hover:text-white transition-colors py-2"
          >
            About Us
          </a>
          <a
            href={isLanding ? '#fitur' : '/#fitur'}
            onClick={(e) => handleNavClick(e, 'fitur')}
            className="text-base font-medium text-slate-400 hover:text-white transition-colors py-2"
          >
            Fitur Utama
          </a>
          <a
            href={isLanding ? '#kontak' : '/#kontak'}
            onClick={(e) => handleNavClick(e, 'kontak')}
            className="text-base font-medium text-slate-400 hover:text-white transition-colors py-2"
          >
            Hubungi Kami
          </a>
        </div>
      </div>
    </nav>
  );
}
