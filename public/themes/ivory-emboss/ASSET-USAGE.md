# Asset Usage - Ivory Emboss Theme

Catatan lengkap aset orisinal untuk tema **Ivory Emboss** (dibuat lewat generator prosedural botani melati berseed `tools/assets/generate/jasmine-generator.mjs` dan dirender melalui filter relief SVG ke WebP).

## 1. Font Lokal (`public/themes/ivory-emboss/fonts/`)

| Berkas | Sumber | Dipakai di |
|---|---|---|
| `montserrat-latin-300-normal.woff2` | `@fontsource/montserrat` (OFL-1.1) | Display judul mempelai |
| `montserrat-latin-400-normal.woff2` | `@fontsource/montserrat` (OFL-1.1) | Display heading |
| `montserrat-latin-500-normal.woff2` | `@fontsource/montserrat` (OFL-1.1) | Label & subjudul |
| `montserrat-latin-600-normal.woff2` | `@fontsource/montserrat` (OFL-1.1) | Kicker kapital & tombol pil |
| `work-sans-latin-400-normal.woff2` | `@fontsource/work-sans` (OFL-1.1) | Teks isi |
| `work-sans-latin-500-normal.woff2` | `@fontsource/work-sans` (OFL-1.1) | Penekanan teks isi |
| `amiri-arabic-400-normal.woff2` | `@fontsource/amiri` (OFL-1.1) | Teks Arab ayat/doa |
| `fonts.css` | Deklarasi `@font-face` lokal | Seluruh halaman (`layout.blade.php`) |

## 2. Ornamen Relief Melati (`public/themes/ivory-emboss/relief/`)

Semua aset tersedia dalam resolusi `1x` (`.webp`) dan `2x` (`@2x.webp`), cahaya konsisten dari kiri atas (`azimuth=225`, `elevation=52`).

| ID Aset | Seed | ViewBox | Dipakai di |
|---|---|---|---|
| `corner-tl` | `104729` | 600 x 600 | Cover (kiri atas), Penutup |
| `corner-tr` | `224737` | 600 x 600 | Cover (kanan atas) |
| `corner-bl` | `350377` | 600 x 600 | Cover (kiri bawah) |
| `corner-br` | `479909` | 600 x 600 | Cover (kanan bawah), Penutup |
| `edge-left-a` | `511213` | 360 x 900 | Hero, Kisah Cinta, Kado (sisi kiri) |
| `edge-left-b` | `619427` | 360 x 900 | Mempelai, Galeri (sisi kiri) |
| `edge-left-c` | `733189` | 360 x 900 | Acara, RSVP & Ucapan (sisi kiri) |
| `edge-right-a` | `821641` | 360 x 900 | Hero, Kisah Cinta (sisi kanan) |
| `edge-right-b` | `914327` | 360 x 900 | Mempelai, RSVP & Ucapan, Kado (sisi kanan) |
| `edge-right-c` | `1004591` | 360 x 900 | Hitung Mundur, Galeri (sisi kanan) |
| `wreath-oval` | `1129093` | 640 x 760 | Hero, Cover gaya `frame` |
| `emblem-melati` | `1245677` | 420 x 360 | Cover, Penutup |
| `flower-open` | `1357913` | 240 x 240 | Hitung Mundur, Pemisah, Kartu |
| `flower-half` | `1469201` | 240 x 240 | Pemisah, Kartu |
| `bud-cluster` | `1580447` | 240 x 240 | Kado, Kartu |
| `divider-sprig` | `1692119` | 640 x 120 | Mempelai, Galeri, Pemisah section |
| `arch-panel` | `1804333` | 720 x 960 | Mempelai, Kisah Cinta |
| `monogram-ring` | `1916557` | 360 x 360 | Cover, Navigasi bawah |
| `scallop-mask` | `2028773` | 160 x 160 | Kartu Hitung Mundur |
| `closing-emblem` | `2140991` | 520 x 460 | Penutup |

## 3. Tekstur & Efek (`public/themes/ivory-emboss/texture/`)

| Berkas | Dimensi | Fungsi |
|---|---|---|
| `paper-grain.webp` | 512 x 512 (seamless) | Latar tekstur kertas katun |
| `paper-fiber.webp` | 512 x 512 (seamless) | Lapisan serat kertas halus |
| `highlight-sweep.webp` | 512 x 256 | Pita sorotan animasi sapuan cahaya |
| `bg-desktop.webp` | 512 x 512 (seamless) | Pola ranting halus latar desktop kiri-kanan |
| `og-default.webp` | 1200 x 630 | Pratinjau Open Graph / WhatsApp bawaan |

## 4. Keputusan Teknis Fase 2 (QA & Kinerja)

- **Anggaran Ukuran:** Halaman pertama `260.61 KB` (batas `350 KB`), total seluruh ornamen `646.17 KB` (batas `900 KB`), total tekstur `72.16 KB` (batas `120 KB`).
- **Uji Kinerja `mix-blend-mode` (CPU throttle 4x di Chromium):** `normal` mencapai `62.6 fps` (`15.98 ms/frame`), `soft-light` mencapai `60.2 fps` (`16.61 ms/frame`). Pendekatan satu set peta relief dengan `mix-blend-mode: soft-light` (atau `normal` berkanal alfa) lolos target 60 fps dan dipakai untuk seluruh 4 preset kertas.
