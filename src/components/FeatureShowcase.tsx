import React from 'react';
import { motion } from 'motion/react';
import { Clock, FileSpreadsheet, Kanban } from 'lucide-react';

/**
 * Komponen FeatureShowcase
 *
 * Menampilkan daftar fitur utama dari aplikasi (misal: Kanban Board, Pelacakan Waktu).
 * Menggunakan animasi scroll-triggered dari motion/react untuk memunculkan card fitur secara elegan.
 */
export default function FeatureShowcase() {
  // Data konfigurasi statis untuk masing-masing fitur yang akan dirender
  const features = [
    {
      icon: <Kanban className="w-8 h-8 text-blue-400" />,
      title: 'Kanban Board Interaktif',
      subtitle: 'Alur Kerja Visual Terstruktur',
      description:
        'Kelola alur kerja tugas tim dengan drag-and-drop intuitif antar kolom: Todo, In Progress, Review, dan Done. Pastikan setiap tugas terpantau progresnya tanpa hambatan.',
      color: 'blue',
      badge: 'Visual Tasking',
    },
    {
      icon: <Clock className="w-8 h-8 text-blue-400" />,
      title: 'Pelacakan Waktu Kerja',
      subtitle: 'Timesheet Akurat & Transparan',
      description:
        'Catat jam kerja harian per tugas dengan mudah. Dilengkapi dengan sistem alur persetujuan (approval) dari Project Manager untuk menjamin akurasi data produktivitas.',
      color: 'blue',
      badge: 'Time Management',
    },
    {
      icon: <FileSpreadsheet className="w-8 h-8 text-blue-400" />,
      title: 'Laporan Siap Ekspor',
      subtitle: 'Analisis Sekali Klik',
      description:
        'Unduh laporan produktivitas tim, pencatatan waktu kerja, dan status penyelesaian tugas ke format PDF kapan saja dibutuhkan untuk bahan evaluasi berkala.',
      color: 'blue',
      badge: 'Automated Reporting',
    },
  ];

  return (
    <section id="fitur" className="relative py-24 md:py-32 bg-[#060b13] border-t border-blue-950/40">
      {/* Decorative ambient background line */}
      <div className="absolute inset-0 bg-[radial-gradient(ellipse_at_bottom_right,rgba(59,130,246,0.05),transparent_50%)] pointer-events-none" />

      <div className="max-w-7xl mx-auto px-6 md:px-12 relative z-10">

        {/* Section Heading */}
        <div className="text-center max-w-3xl mx-auto mb-20">
          <p className="text-xs font-bold uppercase tracking-widest text-blue-400 mb-3" id="fitur-section-tag">
            Fitur Utama Aplikasi
          </p>
          <h2
            id="fitur-section-title"
            className="font-display text-3xl md:text-5xl font-extrabold tracking-tight text-white mb-6 leading-tight"
          >
            Segala Kebutuhan Manajemen Proyek,{' '}
            <span className="font-serif italic font-normal text-blue-200">
              Terpenuhi dalam Satu Tempat
            </span>
          </h2>
          <p className="text-slate-400 text-sm md:text-base leading-relaxed">
            Didesain khusus untuk meningkatkan transparansi, akurasi, dan efisiensi kolaborasi tim
            tanpa tambahan kompleksitas yang tidak perlu.
          </p>
        </div>

        {/* 3-Column Grid */}
        <div id="fitur-grid" className="grid grid-cols-1 md:grid-cols-3 gap-8">
          {features.map((feature, index) => (
            <motion.div
              key={index}
              id={`feature-card-${index}`}
              initial={{ opacity: 0, y: 30 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true, margin: '-100px' }}
              transition={{ duration: 0.6, delay: index * 0.15, ease: 'easeOut' }}
              className="group relative bg-[#0b1325]/40 border border-blue-950/40 hover:border-blue-500/50 rounded-2xl p-8 hover:shadow-2xl hover:shadow-blue-500/[0.02] hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between"
            >
              {/* Highlight gradient backglow on hover */}
              <div className="absolute inset-0 rounded-2xl bg-gradient-to-br from-white/[0.01] to-transparent pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity" />

              <div>
                {/* Icon Wrapper */}
                <div className="inline-flex p-3 rounded-xl bg-[#060b13] border border-blue-950/80 mb-6 group-hover:scale-110 transition-transform duration-300">
                  {feature.icon}
                </div>

                {/* Badge */}
                <div className="mb-3">
                  <span className="text-[10px] font-bold uppercase tracking-wider text-slate-500 bg-[#060b13] border border-blue-950/40 px-2.5 py-1 rounded">
                    {feature.badge}
                  </span>
                </div>

                {/* Titles */}
                <h3
                  className="font-display text-xl font-bold text-white mb-1 group-hover:text-blue-300 transition-colors"
                  id={`feature-title-${index}`}
                >
                  {feature.title}
                </h3>
                <p className="text-xs font-semibold text-blue-400 mb-4 uppercase tracking-wide">
                  {feature.subtitle}
                </p>

                {/* Description */}
                <p className="text-slate-400 text-sm leading-relaxed" id={`feature-desc-${index}`}>
                  {feature.description}
                </p>
              </div>

              {/* Bottom detail */}
              <div className="mt-8 pt-6 border-t border-blue-950/40 flex items-center justify-between text-xs text-slate-500 group-hover:text-blue-400 transition-colors">
                <span>Pelajari integrasi backend</span>
                <span className="p-1 rounded-full bg-[#060b13] border border-blue-950 group-hover:bg-blue-600 group-hover:border-blue-500 group-hover:text-white transition-all">
                  <svg className="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M9 5l7 7-7 7" />
                  </svg>
                </span>
              </div>
            </motion.div>
          ))}
        </div>

      </div>
    </section>
  );
}
