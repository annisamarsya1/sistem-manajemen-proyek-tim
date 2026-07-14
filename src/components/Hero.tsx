import React, { useState, useEffect } from 'react';
import { ArrowRight, Briefcase, CheckCircle2, Clock, TrendingUp } from 'lucide-react';
import { fetchData } from '../lib/api';

/**
 * Interface untuk data statistik yang ditampilkan di Hero section.
 */
interface HeroStats {
  active_projects: number;
  completed_projects: number;
  logged_hours_this_week: number;
}

/**
 * Komponen utility untuk membuat efek angka yang dianimasikan naik (count up).
 * Sangat berguna untuk memberikan efek dinamis pada tampilan statistik.
 */
function CountUp({ end, duration = 1200, decimals = 0 }: { end: number; duration?: number; decimals?: number }) {
  const [count, setCount] = useState(0);

  useEffect(() => {
    let startTime: number | null = null;
    const animate = (timestamp: number) => {
      if (!startTime) startTime = timestamp;
      const progress = timestamp - startTime;
      const eased = 1 - Math.pow(1 - Math.min(progress / duration, 1), 3);
      const current = eased * end;
      setCount(parseFloat(current.toFixed(decimals)));
      if (progress < duration) requestAnimationFrame(animate);
      else setCount(end);
    };
    requestAnimationFrame(animate);
  }, [end, duration, decimals]);

  return <span>{decimals > 0 ? count.toFixed(decimals) : count}</span>;
}

/**
 * Komponen Hero
 * 
 * Bagian paling atas dari Landing Page (above the fold) yang memuat Call to Action (CTA)
 * utama dan juga statistik interaktif proyek yang diambil dari backend.
 */
export default function Hero() {
  const [stats, setStats] = useState<HeroStats | null>(null);
  const [loadingStats, setLoadingStats] = useState(true);

  useEffect(() => {
    fetchData<any>('/public/stats')
      .then(data => {
        if (data && data.active_projects !== undefined) {
          setStats({
            active_projects: Number(data.active_projects) || 0,
            completed_projects: Number(data.completed_projects) || 0,
            logged_hours_this_week: parseFloat(data.logged_hours_this_week) || 0,
          });
        } else {
          setStats({ active_projects: 0, completed_projects: 0, logged_hours_this_week: 0 });
        }
      })
      .catch(() => {
        setStats({ active_projects: 0, completed_projects: 0, logged_hours_this_week: 0 });
      })
      .finally(() => setLoadingStats(false));
  }, []);

  const scrollToContact = (e: React.MouseEvent<HTMLAnchorElement>) => {
    e.preventDefault();
    const element = document.getElementById('kontak');
    if (element) {
      element.scrollIntoView({ behavior: 'smooth', block: 'start' });
      window.history.pushState(null, '', '#kontak');
    }
  };

  return (
    <div className="relative bg-[#060b13] overflow-hidden">
      {/* Background radial glow matching the dashboard's blue theme */}
      <div 
        id="hero-ambient-glow"
        className="absolute top-[25vh] left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] md:w-[900px] md:h-[900px] rounded-full bg-gradient-to-b from-blue-600/20 to-blue-950/5 blur-[120px] pointer-events-none z-0" 
      />

      <section id="hero" className="relative min-h-screen pt-32 pb-16 flex flex-col items-center justify-center z-10">
        <div className="relative max-w-7xl mx-auto px-6 md:px-12 text-center flex flex-col items-center">
        
        {/* Top Badge (matching active project stats theme) */}
        <div 
          id="hero-badge"
          className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-300 text-xs md:text-sm font-medium mb-8 animate-fade-in-up"
        >
          <span className="flex h-2 w-2 rounded-full bg-blue-400 animate-pulse" />
          <span>Sistem Manajemen Proyek Terintegrasi</span>
        </div>

        {/* Centered Headline with Serif & Sans-Serif Contrast */}
        <h1 
          id="hero-title"
          className="font-display text-4xl md:text-6xl lg:text-7xl font-extrabold tracking-tight text-white leading-[1.1] max-w-4xl mb-6"
        >
          Kelola <span className="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-blue-200">Proyek Tim</span> Anda dalam <span className="font-serif italic font-normal text-slate-100">Satu Platform</span>
        </h1>

        {/* Sub-headline / Value Prop */}
        <p 
          id="hero-description"
          className="text-slate-400 text-base md:text-xl max-w-2xl mb-10 leading-relaxed font-sans"
        >
          Gabungan manajemen tugas visual, pencatatan waktu kerja presisi, dan laporan produktivitas terotomatisasi dalam satu ekosistem handal.
        </p>

        {/* 2 CTA Buttons */}
        <div id="hero-actions" className="flex flex-col sm:flex-row items-center gap-4 mb-16">
          <a
            href="http://127.0.0.1:8000/login"
            className="w-full sm:w-auto px-8 py-3.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold shadow-lg shadow-blue-900/30 transition-all duration-200 flex items-center justify-center gap-2 group"
          >
            Masuk Dashboard
            <ArrowRight className="w-4 h-4 group-hover:translate-x-1 transition-transform" />
          </a>
          <a
            href="#kontak"
            onClick={scrollToContact}
            className="w-full sm:w-auto px-8 py-3.5 bg-transparent border border-slate-700 hover:bg-blue-950/20 text-white rounded-xl font-bold transition-all duration-200 flex items-center justify-center gap-1.5"
          >
            Hubungi Kami
          </a>
        </div>

        </div>
      </section>

      {/* 3 Dynamic Stats Cards - Second Section */}
      <section id="hero-stats" className="relative min-h-screen py-24 flex flex-col items-center justify-center z-10">
        <div className="relative max-w-7xl mx-auto px-6 md:px-12 w-full flex flex-col items-center">
          <div
            id="hero-previews"
            className="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-7xl w-full relative z-10 px-4"
          >
          {/* Left Card: Total Completed Projects */}
          <div
            id="hero-card-completed"
            className="bg-[#0b1325]/60 border border-blue-950/50 rounded-2xl p-8 text-left shadow-2xl transform md:-rotate-2 hover:rotate-0 hover:scale-105 transition-all duration-300 flex flex-col backdrop-blur-sm"
          >
            <div className="flex items-center gap-2 mb-6">
              <div className="p-2.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20">
                <CheckCircle2 className="w-5 h-5 text-emerald-400" />
              </div>
              <span className="text-sm font-bold uppercase tracking-wider text-emerald-400">Proyek Selesai</span>
            </div>
            <div className="flex-1 flex flex-col justify-center">
              {loadingStats ? (
                <div className="h-20 w-40 bg-blue-950/40 rounded-xl animate-pulse" id="completed-skeleton" />
              ) : (
                <div className="font-display text-7xl lg:text-8xl font-extrabold text-white tracking-tight leading-none" id="completed-projects-num">
                  <CountUp end={stats?.completed_projects ?? 0} />
                </div>
              )}
              <p className="text-xs text-emerald-400/80 mt-4 font-mono uppercase tracking-wider">sepanjang masa</p>
            </div>
            <div className="mt-4 pt-4 border-t border-blue-950/40 flex items-center gap-2">
              <TrendingUp className="w-3.5 h-3.5 text-emerald-400/60" />
              <span className="text-[11px] text-slate-500">Total proyek terselesaikan</span>
            </div>
          </div>

          {/* Center Card (Featured): Active Projects */}
          <div
            id="hero-card-active"
            className="bg-gradient-to-b from-[#0d1832] to-[#060b13] border-2 border-blue-500/50 rounded-2xl p-8 text-left shadow-2xl md:scale-110 transform md:-translate-y-4 transition-all duration-300 flex flex-col relative overflow-hidden"
          >
            <div className="absolute top-0 left-1/2 -translate-x-1/2 w-48 h-32 bg-blue-500/15 rounded-full blur-3xl pointer-events-none" />
            <div className="flex items-center justify-between mb-6 relative z-10">
              <div className="flex items-center gap-2">
                <div className="p-2.5 rounded-xl bg-blue-500/20 border border-blue-500/40">
                  <Briefcase className="w-5 h-5 text-blue-400" />
                </div>
                <span className="text-sm font-bold uppercase tracking-wider text-blue-400">Proyek Aktif</span>
              </div>
              <span className="flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-500/15 text-blue-300 text-xs font-semibold border border-blue-500/25">
                <span className="h-2 w-2 rounded-full bg-blue-400 animate-ping" />
                Live
              </span>
            </div>
            <div className="flex-1 flex flex-col justify-center relative z-10">
              {loadingStats ? (
                <div className="h-24 w-48 bg-blue-950/40 rounded-xl animate-pulse" id="active-skeleton" />
              ) : (
                <div className="font-display text-8xl lg:text-9xl font-extrabold text-white tracking-tight leading-none" id="active-projects-num">
                  <CountUp end={stats?.active_projects ?? 0} />
                </div>
              )}
              <p className="text-xs text-blue-400/80 mt-4 font-mono uppercase tracking-wider">sedang dikerjakan</p>
            </div>
            <div className="mt-4 pt-4 border-t border-blue-950/50 flex items-center gap-2 relative z-10">
              <span className="w-2 h-2 rounded-full bg-blue-400 animate-pulse" />
              <span className="text-[11px] text-slate-400">Diperbarui secara realtime</span>
            </div>
          </div>

          {/* Right Card: Logged Hours This Week */}
          <div
            id="hero-card-hours"
            className="bg-[#0b1325]/60 border border-blue-950/50 rounded-2xl p-8 text-left shadow-2xl transform md:rotate-2 hover:rotate-0 hover:scale-105 transition-all duration-300 flex flex-col backdrop-blur-sm"
          >
            <div className="flex items-center gap-2 mb-6">
              <div className="p-2.5 rounded-xl bg-blue-600/10 border border-blue-600/20">
                <Clock className="w-5 h-5 text-blue-600" />
              </div>
              <span className="text-sm font-bold uppercase tracking-wider text-blue-600">Jam Kerja</span>
            </div>
            <div className="flex-1 flex flex-col justify-center">
              {loadingStats ? (
                <div className="h-20 w-40 bg-blue-950/40 rounded-xl animate-pulse" id="hours-skeleton" />
              ) : (
                <div className="font-display text-7xl lg:text-8xl font-extrabold text-white tracking-tight leading-none" id="logged-hours-num">
                  <CountUp end={stats?.logged_hours_this_week ?? 0} decimals={1} />
                  <span className="text-3xl lg:text-4xl text-slate-400 font-bold ml-1">h</span>
                </div>
              )}
              <p className="text-xs text-blue-600/80 mt-4 font-mono uppercase tracking-wider">minggu ini</p>
            </div>
            <div className="mt-4 pt-4 border-t border-blue-950/40 flex items-center gap-2">
              <Clock className="w-3.5 h-3.5 text-blue-600/60" />
              <span className="text-[11px] text-slate-500">Total jam kerja tercatat</span>
            </div>
          </div>
          </div>
        </div>
      </section>
    </div>
  );
}
