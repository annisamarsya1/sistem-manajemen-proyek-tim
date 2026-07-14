import React from 'react';

/**
 * Props untuk menyesuaikan ukuran dan class tambahan komponen LoadingSpinner.
 */
interface LoadingSpinnerProps {
  size?: 'sm' | 'md' | 'lg';
  className?: string;
}

/**
 * Komponen LoadingSpinner
 * 
 * Menampilkan indikator loading berputar (spinner) dengan ukuran yang dapat disesuaikan.
 * Biasa digunakan saat menunggu response API (misal: submit form).
 */
export default function LoadingSpinner({ size = 'md', className = '' }: LoadingSpinnerProps) {
  // Kamus ukuran tailwind CSS
  const sizeClasses = {
    sm: 'w-4 h-4 border-2',
    md: 'w-8 h-8 border-3',
    lg: 'w-12 h-12 border-4',
  };

  return (
    <div className={`flex items-center justify-center ${className}`} id="loading-spinner-container">
      <div
        id="loading-spinner-circle"
        className={`${sizeClasses[size]} border-gray-700 border-t-blue-500 rounded-full animate-spin`}
        role="status"
        aria-label="loading"
      />
    </div>
  );
}
