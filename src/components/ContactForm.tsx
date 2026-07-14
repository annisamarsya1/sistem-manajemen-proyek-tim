import React, { useState } from 'react';
import { fetchData } from '../lib/api';
import { Send, CheckCircle2, AlertCircle } from 'lucide-react';
import LoadingSpinner from './LoadingSpinner';

/**
 * Interface untuk state form kontak.
 */
interface FormState {
  name: string;
  email: string;
  message: string;
}

/**
 * Interface untuk menyimpan pesan error validasi form kontak.
 */
interface FormErrors {
  name?: string;
  email?: string;
  message?: string;
}

/**
 * Komponen ContactForm
 * 
 * Menampilkan formulir kontak yang memungkinkan pengguna untuk mengirim pesan.
 * Mengelola state input, validasi sisi klien, dan mengirim data ke API backend.
 */
export default function ContactForm() {
  // State untuk menyimpan nilai input pengguna
  const [form, setForm] = useState<FormState>({ name: '', email: '', message: '' });
  // State untuk menyimpan pesan error jika validasi gagal
  const [errors, setErrors] = useState<FormErrors>({});
  // State untuk melacak status pengiriman (idle, submitting, success, error)
  const [status, setStatus] = useState<'idle' | 'submitting' | 'success' | 'error'>('idle');

  /**
   * Fungsi untuk memvalidasi input form sebelum dikirim.
   * Mengembalikan true jika valid, false jika ada error.
   */
  const validate = (): boolean => {
    const tempErrors: FormErrors = {};
    let isValid = true;

    // Name validation
    if (!form.name.trim()) {
      tempErrors.name = 'Nama lengkap wajib diisi.';
      isValid = false;
    } else if (form.name.length > 100) {
      tempErrors.name = 'Nama tidak boleh melebihi 100 karakter.';
      isValid = false;
    }

    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!form.email.trim()) {
      tempErrors.email = 'Alamat email wajib diisi.';
      isValid = false;
    } else if (!emailRegex.test(form.email)) {
      tempErrors.email = 'Format email tidak valid.';
      isValid = false;
    }

    // Message validation
    if (!form.message.trim()) {
      tempErrors.message = 'Pesan wajib diisi.';
      isValid = false;
    } else if (form.message.length > 2000) {
      tempErrors.message = 'Pesan tidak boleh melebihi 2000 karakter.';
      isValid = false;
    }

    setErrors(tempErrors);
    return isValid;
  };

  /**
   * Handler untuk setiap perubahan pada input atau textarea.
   * Memperbarui state form dan menghapus pesan error untuk field terkait.
   */
  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
    const { name, value } = e.target;
    setForm((prev) => ({ ...prev, [name]: value }));
    
    // Clear error inline as user types
    if (errors[name as keyof FormErrors]) {
      setErrors((prev) => ({ ...prev, [name]: undefined }));
    }
  };

  /**
   * Handler untuk mensubmit form.
   * Melakukan validasi, mengirim request POST ke endpoint /contact,
   * dan menangani perubahan status berdasarkan hasil response.
   */
  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault(); // Mencegah reload halaman secara default
    
    // Hentikan proses jika validasi gagal
    if (!validate()) return;

    setStatus('submitting'); // Set status loading
    try {
      await fetchData('/contact', {
        method: 'POST',
        data: form
      });
      setStatus('success');
      setForm({ name: '', email: '', message: '' });
      setErrors({});
    } catch (err) {
      setStatus('error');
    }
  };

  return (
    <section id="kontak" className="py-24 md:py-32 bg-[#060b13] border-t border-blue-950/40 relative overflow-hidden">
      {/* Dynamic ambient grid background */}
      <div className="absolute inset-0 bg-[linear-gradient(to_right,#80808006_1px,transparent_1px),linear-gradient(to_bottom,#80808006_1px,transparent_1px)] bg-[size:24px_24px] pointer-events-none" />

      <div className="max-w-6xl mx-auto px-6 relative z-10">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-start">
          
          {/* Info Side (4 columns) */}
          <div className="lg:col-span-5 text-left" id="kontak-info-column">
            <p className="text-xs font-bold uppercase tracking-widest text-blue-400 mb-3" id="kontak-badge">
              Kirim Pesan
            </p>
            <h2 
              className="font-display text-3xl md:text-5xl font-extrabold tracking-tight text-white mb-6 leading-tight"
              id="kontak-heading"
            >
              Hubungi Tim <span className="font-serif italic font-normal text-blue-200">Pengembang Kami</span>
            </h2>
            <p className="text-slate-400 text-sm md:text-base leading-relaxed mb-8">
              Punya pertanyaan mengenai implementasi backend Laravel, penyesuaian fitur Kanban Board, atau memerlukan akses uji coba sistem? Kirim pesan dan kami akan merespons dalam waktu 24 jam.
            </p>

            {/* Practical Contact Info */}
            <div className="flex flex-col gap-6" id="kontak-card-items">
              <div className="flex items-start gap-4">
                <div className="p-2.5 rounded-xl bg-[#0b1325] border border-blue-950/40 text-blue-400">
                  <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                  </svg>
                </div>
                <div>
                  <h4 className="text-sm font-semibold text-white">Email Resmi</h4>
                  <p className="text-xs text-slate-400 mt-0.5">info@proyektim.com</p>
                </div>
              </div>

              <div className="flex items-start gap-4">
                <div className="p-2.5 rounded-xl bg-[#0b1325] border border-blue-950/40 text-blue-400">
                  <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                </div>
                <div>
                  <h4 className="text-sm font-semibold text-white">Kantor Pusat</h4>
                  <p className="text-xs text-slate-400 mt-0.5">Jakarta, Indonesia</p>
                </div>
              </div>
            </div>
          </div>

          {/* Form Side (7 columns) */}
          <div className="lg:col-span-7 bg-[#0b1325]/40 border border-blue-950/40 rounded-3xl p-8 md:p-10 shadow-2xl relative" id="kontak-form-column">
            
            {/* Status Messages */}
            {status === 'success' && (
              <div 
                className="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/25 text-emerald-300 flex items-start gap-3 animate-fade-in-up"
                id="contact-status-success"
              >
                <CheckCircle2 className="w-5 h-5 text-emerald-400 shrink-0 mt-0.5" />
                <span className="text-xs md:text-sm">
                  Pesan Anda berhasil terkirim. Tim kami akan segera menghubungi Anda.
                </span>
              </div>
            )}

            {status === 'error' && (
              <div 
                className="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/25 text-red-300 flex items-start gap-3 animate-fade-in-up"
                id="contact-status-error"
              >
                <AlertCircle className="w-5 h-5 text-red-400 shrink-0 mt-0.5" />
                <span className="text-xs md:text-sm">
                  Gagal mengirim pesan. Silakan coba lagi.
                </span>
              </div>
            )}

            {/* The Form */}
            <form onSubmit={handleSubmit} className="flex flex-col gap-6" id="contact-form-tag">
              
              {/* Name Field */}
              <div className="flex flex-col gap-2">
                <label htmlFor="form-name" className="text-xs font-semibold text-slate-400 uppercase tracking-wider">
                  Nama Lengkap
                </label>
                <input
                  type="text"
                  id="form-name"
                  name="name"
                  value={form.name}
                  onChange={handleChange}
                  placeholder="Masukkan nama lengkap Anda"
                  maxLength={100}
                  disabled={status === 'submitting'}
                  className={`w-full px-4 py-3 rounded-xl bg-[#060b13] border ${
                    errors.name ? 'border-red-500/50 focus:border-red-500' : 'border-blue-950/40 focus:border-blue-500'
                  } text-white text-sm outline-none transition-all placeholder:text-slate-600 disabled:opacity-50`}
                />
                {errors.name && (
                  <span className="text-xs text-red-400 flex items-center gap-1 mt-0.5" id="error-name">
                    <AlertCircle className="w-3 h-3" /> {errors.name}
                  </span>
                )}
              </div>

              {/* Email Field */}
              <div className="flex flex-col gap-2">
                <label htmlFor="form-email" className="text-xs font-semibold text-slate-400 uppercase tracking-wider">
                  Alamat Email
                </label>
                <input
                  type="email"
                  id="form-email"
                  name="email"
                  value={form.email}
                  onChange={handleChange}
                  placeholder="nama@perusahaan.com"
                  disabled={status === 'submitting'}
                  className={`w-full px-4 py-3 rounded-xl bg-[#060b13] border ${
                    errors.email ? 'border-red-500/50 focus:border-red-500' : 'border-blue-950/40 focus:border-blue-500'
                  } text-white text-sm outline-none transition-all placeholder:text-slate-600 disabled:opacity-50`}
                />
                {errors.email && (
                  <span className="text-xs text-red-400 flex items-center gap-1 mt-0.5" id="error-email">
                    <AlertCircle className="w-3 h-3" /> {errors.email}
                  </span>
                )}
              </div>

              {/* Message Field */}
              <div className="flex flex-col gap-2">
                <div className="flex justify-between items-center">
                  <label htmlFor="form-message" className="text-xs font-semibold text-slate-400 uppercase tracking-wider">
                    Pesan Anda
                  </label>
                  <span className="text-[10px] text-slate-500">{form.message.length}/2000</span>
                </div>
                <textarea
                  id="form-message"
                  name="message"
                  value={form.message}
                  onChange={handleChange}
                  placeholder="Tuliskan detail kebutuhan atau pertanyaan Anda di sini..."
                  rows={5}
                  maxLength={2000}
                  disabled={status === 'submitting'}
                  className={`w-full px-4 py-3 rounded-xl bg-[#060b13] border ${
                    errors.message ? 'border-red-500/50 focus:border-red-500' : 'border-blue-950/40 focus:border-blue-500'
                  } text-white text-sm outline-none transition-all placeholder:text-slate-600 resize-none disabled:opacity-50`}
                />
                {errors.message && (
                  <span className="text-xs text-red-400 flex items-center gap-1 mt-0.5" id="error-message">
                    <AlertCircle className="w-3 h-3" /> {errors.message}
                  </span>
                )}
              </div>

              {/* Submit Button */}
              <button
                type="submit"
                id="form-submit-btn"
                disabled={status === 'submitting'}
                className="w-full mt-2 py-3.5 bg-blue-600 hover:bg-blue-500 disabled:bg-blue-700/60 text-white font-semibold rounded-xl shadow-lg shadow-blue-600/10 hover:shadow-blue-500/20 transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer"
              >
                {status === 'submitting' ? (
                  <>
                    <LoadingSpinner size="sm" />
                    <span>Mengirim...</span>
                  </>
                ) : (
                  <>
                    <span>Kirim Pesan</span>
                    <Send className="w-4 h-4" />
                  </>
                )}
              </button>

            </form>
          </div>

        </div>
      </div>
    </section>
  );
}
