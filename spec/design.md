# design.md — ProyekTim (gaya visual Minecloud)

Mengadaptasi bahasa desain **Minecloud** (light, clean SaaS, aksen biru,
kartu putih rounded, shadow lembut) ke web **ProyekTim** yang sudah ada.
Struktur & fitur ProyekTim tidak berubah — hanya style-nya.
Stack: **Laravel + Livewire + Tailwind CSS**. Theme: **Light**.

---

## 1. Prinsip Adaptasi (dari Minecloud)

- Dari dark → **light theme**: kanvas abu muda + kartu putih.
- Satu **aksen biru dominan** untuk aksi primer, nav aktif, link.
- Hierarki lewat warna & bobot teks, bukan garis tebal.
- Kartu ber-radius besar + **shadow lembut** (bukan border tebal).
- Warna semantik (hijau/merah) tetap dipakai untuk status, tapi
  dalam bentuk **soft pill** ala Minecloud (bg tint + teks pekat).
- Banyak whitespace, font **Inter**.

## 2. Color Tokens

| Token          | Hex     | Penggunaan                          |
| -------------- | ------- | ----------------------------------- |
| bg-canvas      | #EBEDF0 | Background halaman (luar kartu)     |
| bg-surface     | #FFFFFF | Sidebar, kartu, tabel               |
| bg-subtle      | #F5F7FA | Hover row, input, dropdown          |
| border         | #E7EAEF | Border kartu & pemisah tipis        |
| text-primary   | #1A1D29 | Judul, angka utama, nama            |
| text-secondary | #6B7280 | Label, meta, sub-teks               |
| text-muted     | #9AA3B2 | Placeholder, empty state            |
| primary        | #2F6BFF | Tombol primer, nav aktif, link      |
| primary-hover  | #2559D8 | Hover tombol primer                 |
| primary-soft   | #EAF0FF | Bg nav aktif, chip/filter aktif     |
| success        | #16A34A | Teks Approved / Met Target          |
| success-soft   | #E7F6EC | Bg pill Approved / kartu Met        |
| danger         | #DC2626 | Teks Rejected / Under Target        |
| danger-soft    | #FDECEC | Bg pill Rejected / kartu Under      |

> Export Excel yang tadinya hijau solid → tetap boleh hijau (success) sebagai
> secondary action, atau ubah jadi ghost agar aksen tunggal biru lebih dominan.

## 3. Tipografi (Inter)

| Style   | Size / Line | Weight | Penggunaan               |
| ------- | ----------- | ------ | ------------------------ |
| Stat    | 24–28 / 32  | 700    | Angka kartu ("40 jam")   |
| Heading | 18 / 26     | 600    | "Dashboard Overview"     |
| Section | 15 / 22     | 600    | "Daftar Time Logs"       |
| Body    | 14 / 20     | 400–500| Isi tabel, teks umum     |
| Label   | 12 / 16     | 500    | Header kolom, nav item   |
| Caption | 12 / 16     | 400    | Meta, timestamp          |

Header kolom tabel: uppercase, tracking-wide, text-secondary.

## 4. Spacing, Radius, Shadow (kunci gaya Minecloud)

- Spacing base 4px: 4 · 8 · 12 · 16 · 20 · 24 · 32.
- Padding kartu: 20–24px. Gap stat card: 16px. Row tabel: py 12–14px.
- Radius: sm 8px (badge/input) · md 12px (tombol) · lg 16px (kartu) · xl 20–24px (kontainer).
- Shadow lembut (bukan border tebal):
  - shadow-card:  0 1px 3px rgba(16,24,40,.06), 0 1px 2px rgba(16,24,40,.04)
  - shadow-panel: 0 8px 24px rgba(16,24,40,.08)

## 5. Layout (tetap sama, restyle)

```

┌──────────┬──────────────────────────────────────────────┐

│ Sidebar  │  Topbar: "Dashboard"                          │

│ ~230px   ├──────────────────────────────────────────────┤

│ putih    │  Header + actions (Export CSV/Excel, Add Log) │

│          │  Stat cards (grid 4)                          │

│ nav      │  Aktivitas Hari Ini (snapshot)                │

│ + admin  │  Daftar Time Logs (filters + table)           │

└──────────┴──────────────────────────────────────────────┘

```

- Kanvas abu muda; sidebar & semua kartu putih ber-radius + shadow-card.
- Stat cards: grid 4 (2 di md, 1 di sm).

## 6. Komponen (mapping ke gaya Minecloud)

**Sidebar** — bg putih, border kanan tipis. Logo kotak biru radius-md + "ProyekTim".
Nav item ikon+label, padding 10px 12px, radius-md.
Aktif: bg **primary-soft** + teks/ikon **primary**. Default: text-secondary,
hover bg-subtle. Grup "ADMINISTRASI" uppercase text-muted.
Footer: avatar bulat + nama/role + Logout.

**Stat Card** — bg putih, radius-lg, shadow-card, padding 20–24px.
Label text-secondary di atas; angka besar (Stat). Untuk Met/Under bisa tambah
aksen: kartu Met pakai teks success, Under pakai teks danger (opsional bg soft tipis).

**Buttons**
| Variant | Style                                                       |
| ------- | ----------------------------------------------------------- |
| Primary | bg primary, teks putih, radius-md, ikon +, hover primary-hover (Add Time Log) |
| Success | bg success, teks putih (Export Excel) — atau jadikan ghost  |
| Ghost   | bg putih + border, text-primary, hover bg-subtle (Export CSV)|

**Filter Bar** — date input + dropdown (Semua Proyek/Status): bg-subtle atau putih
+ border, radius-md, teks text-secondary. Chip filter aktif: primary-soft.

**Table (Time Logs)** — kontainer putih radius-xl + shadow-card.
Header uppercase Label muted; kolom NO · NAMA KARYAWAN · PROYEK/TUGAS · WAKTU ·
DURASI · CATATAN · STATUS · AKSI. Nama font-medium; proyek 2 baris (judul + sub muted).
Hover row bg-subtle. Pemisah row border tipis.

**Status Badge (soft pill ala Minecloud)** — pill radius-full + dot:
- Approved → bg success-soft, teks success
- Rejected → bg danger-soft, teks danger
- (Pending opsional → bg primary-soft, teks primary)

**Empty State** — teks italic text-muted ("Tidak ada aktivitas tercatat hari ini.").

## 7. Iconography

Heroicons/Lucide (blade-heroicons), line/duotone, stroke ~1.5px.
Nav 18–20px, inline 16px. Aksen ikon = primary bila kontekstual.

## 8. Tailwind Config

```

// tailwind.config.js

module.exports = {

content: [

'./resources//*.blade.php',

'./app/Livewire//*.php',

],

theme: {

extend: {

colors: {

canvas: '#EBEDF0',

surface: '#FFFFFF',

subtle: '#F5F7FA',

border: '#E7EAEF',

primary: { DEFAULT: '#2F6BFF', hover: '#2559D8', soft: '#EAF0FF' },

ink: { DEFAULT: '#1A1D29', secondary: '#6B7280', muted: '#9AA3B2' },

success: { DEFAULT: '#16A34A', soft: '#E7F6EC' },

danger:  { DEFAULT: '#DC2626', soft: '#FDECEC' },

},

borderRadius: { sm: '8px', md: '12px', lg: '16px', xl: '24px' },

boxShadow: {

card: '0 1px 3px rgba(16,24,40,0.06), 0 1px 2px rgba(16,24,40,0.04)',

panel: '0 8px 24px rgba(16,24,40,0.08)',

},

fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui'] },

},

},

}

```

## 9. Contoh Komponen Blade

```

{{-- x-stat-card --}}

@props(['label','value','tone' => 'default'])

@php $tone = ['default'=>'text-ink','success'=>'text-success','danger'=>'text-danger'][$tone]; @endphp

<div class="bg-surface rounded-lg shadow-card border border-border p-5">

<p class="text-xs font-medium text-ink-secondary">{{25}}</p>

<p class="mt-2 text-2xl font-bold {{26}}">{{27}}</p>

</div>

```

```

{{-- x-status-badge --}}

@props(['status'])

@php

$map = [

'approved' => 'bg-success-soft text-success',

'rejected' => 'bg-danger-soft text-danger',

'pending'  => 'bg-primary-soft text-primary',

];

@endphp

{{29}}

</span>

```

## 10. Struktur Blade / Livewire (tetap)

```

resources/views/

├── layouts/app.blade.php          # sidebar putih + topbar shell

├── components/

│   ├── sidebar.blade.php

│   ├── stat-card.blade.php

│   ├── status-badge.blade.php

│   └── button.blade.php           # primary | success | ghost

└── livewire/

├── dashboard.blade.php

├── time-logs-table.blade.php  # filter + tabel

└── time-log-form.blade.php    # modal Add Time Log

```

Filter pakai `wire:model.live`; setelah simpan `$this->dispatch('log-created')`,
`TimeLogsTable` refresh via `#[On('log-created')]`.
```

Inti perubahannya: mengambil **token & pola visual Minecloud** (kanvas abu, kartu putih, aksen biru `#2F6BFF`, shadow lembut, radius besar, soft pill) lalu memetakannya ke **komponen ProyekTim yang sudah ada** (sidebar, stat cards, filter, tabel time logs, status Approved/Rejected). Struktur dan fitur tetap; hanya tampilannya yang diadaptasi.