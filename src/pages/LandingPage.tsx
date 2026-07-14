import React, { useEffect, Suspense, lazy } from 'react';
import Navbar from '../components/Navbar';
import Hero from '../components/Hero';

const FluidSection = lazy(() => import('../components/FluidSection'));
import FeatureShowcase from '../components/FeatureShowcase';
import PricingComingSoon from '../components/PricingComingSoon';
import ContactForm from '../components/ContactForm';
import Footer from '../components/Footer';

/**
 * Komponen Halaman Utama (Landing Page)
 * 
 * Berfungsi sebagai halaman beranda situs. 
 * Menyusun semua bagian (seksi) yang dibutuhkan dan mengelola logic scroll sederhana
 * ketika halaman di-load dengan atau tanpa anchor hash (misalnya #features).
 */
export default function LandingPage() {
  useEffect(() => {
    // Prevent browser from remembering previous scroll position on refresh
    if ('scrollRestoration' in history) {
      history.scrollRestoration = 'manual';
    }

    const hash = window.location.hash;
    if (hash) {
      setTimeout(() => {
        const id = hash.replace('#', '');
        const element = document.getElementById(id);
        if (element) {
          element.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      }, 300);
    } else {
      // If no hash in URL, always scroll to top (Hero section)
      window.scrollTo(0, 0);
    }
  }, []);

  return (
    <div className="relative min-h-screen bg-[#060b13] text-slate-100 overflow-x-hidden flex flex-col" id="landing-page-root">
      {/* Navigation bar */}
      <Navbar />

      <main className="flex-1">
        {/* Hero Section */}
        <Hero />

        {/* Fluid Reveal Section */}
        <Suspense fallback={<div className="w-full h-[800px] lg:h-[900px] bg-[#060b13] border-t border-blue-900/30"></div>}>
          <FluidSection />
        </Suspense>

        {/* Features Showcase Section */}
        <FeatureShowcase />

        {/* Pricing coming soon notification card */}
        <PricingComingSoon />

        {/* Contact inquiries & Support Section */}
        <ContactForm />
      </main>

      {/* Footer */}
      <Footer />
    </div>
  );
}
