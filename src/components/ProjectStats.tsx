import React, { useState, useEffect } from 'react';
import { fetchData } from '../lib/api';
import { Briefcase, CheckCircle2 } from 'lucide-react';

/**
 * Interface data statistik proyek.
 */
interface StatsData {
  active_projects: number;
  completed_projects: number;
}

/**
 * Komponen utility untuk membuat efek angka yang dianimasikan naik (count up).
 */
function CountUp({ end, duration = 1000 }: { end: number; duration?: number }) {
  const [count, setCount] = useState(0);

  useEffect(() => {
    let startTime: number | null = null;
    const startValue = 0;

    const animate = (timestamp: number) => {
      if (!startTime) startTime = timestamp;
      const progress = timestamp - startTime;
      const progressPercent = Math.min(progress / duration, 1);
      
      setCount(Math.floor(startValue + progressPercent * (end - startValue)));

      if (progress < duration) {
        requestAnimationFrame(animate);
      } else {
        setCount(end);
      }
    };

    requestAnimationFrame(animate);
  }, [end, duration]);

  return <span>{count}</span>;
}

/**
 * Komponen ProjectStats
 * 
 * Menampilkan statistik proyek (aktif vs selesai). Mengambil data secara live
 * dari endpoint /public/stats. Jika gagal, card ini akan disembunyikan sepenuhnya.
 */
export default function ProjectStats() {
  const [stats, setStats] = useState<StatsData | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(false);

  useEffect(() => {
    fetchData<any>('/public/stats')
      .then(data => {
        if (data && data.active_projects !== undefined && data.completed_projects !== undefined) {
          setStats({
            active_projects: Number(data.active_projects) || 0,
            completed_projects: Number(data.completed_projects) || 0
          });
        } else {
          // If response data format is incorrect, fall back to error
          setError(true);
        }
      })
      .catch(() => {
        setError(true);
      })
      .finally(() => {
        setLoading(false);
      });
  }, []);

  // Strict requirement: Hide section entirely on error
  if (error) {
    return null;
  }

  return (
    <div id="project-stats" className="w-full max-w-4xl mx-auto mt-4 relative z-10 px-4">
        
        {/* Statistics Board Container */}
        <div className="bg-[#0b1325]/50 border border-blue-950/40 rounded-3xl p-8 md:p-10 backdrop-blur-md shadow-2xl">
          <div className="text-center mb-10">
            <h3 className="font-display text-lg font-semibold text-slate-400 uppercase tracking-widest mb-2" id="stats-title">
              Progres Proyek Saat Ini
            </h3>
            <p className="text-sm text-slate-500 max-w-md mx-auto">
              Statistik pengerjaan proyek riil terintegrasi langsung dari database sistem manajemen kami.
            </p>
          </div>

          <div id="stats-values-container" className="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 divide-y md:divide-y-0 md:divide-x divide-blue-950/40">
            
            {/* Active Projects Card */}
            <div className="flex flex-col items-center text-center p-4 md:p-0" id="stats-active-container">
              <div className="p-3 rounded-2xl bg-blue-500/10 border border-blue-500/20 text-blue-400 mb-4 shadow-sm shadow-blue-500/5">
                <Briefcase className="w-6 h-6" />
              </div>
              <p className="text-sm font-medium text-slate-400 mb-2">Proyek Aktif Saat Ini</p>
              
              {loading ? (
                <div className="h-16 w-32 bg-blue-950/40 rounded-2xl animate-pulse my-1" id="active-pulse-skeleton" />
              ) : (
                <div className="text-4xl md:text-6xl font-extrabold font-display text-white tracking-tight" id="active-projects-num">
                  <CountUp end={stats?.active_projects || 0} />
                </div>
              )}
              <p className="text-[11px] text-blue-400/80 mt-2 font-mono uppercase tracking-wider">sedang berjalan</p>
            </div>

            {/* Completed Projects Card */}
            <div className="flex flex-col items-center text-center p-4 pt-8 md:p-0" id="stats-completed-container">
              <div className="p-3 rounded-2xl bg-blue-500/10 border border-blue-500/20 text-blue-400 mb-4 shadow-sm shadow-blue-500/5">
                <CheckCircle2 className="w-6 h-6" />
              </div>
              <p className="text-sm font-medium text-slate-400 mb-2">Proyek Selesai Dikirim</p>

              {loading ? (
                <div className="h-16 w-32 bg-blue-950/40 rounded-2xl animate-pulse my-1" id="completed-pulse-skeleton" />
              ) : (
                <div className="text-4xl md:text-6xl font-extrabold font-display text-white tracking-tight" id="completed-projects-num">
                  <CountUp end={stats?.completed_projects || 0} />
                </div>
              )}
              <p className="text-[11px] text-blue-400/80 mt-2 font-mono uppercase tracking-wider font-semibold">selesai 100%</p>
            </div>

          </div>
        </div>
    </div>
  );
}
