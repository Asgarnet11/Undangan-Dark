# Daftar Penggunaan Aset: Bugis Heritage Theme

Dokumen ini mencatat seluruh aset statis yang tersimpan di dalam direktori `public/themes/bugis-heritage/`, fungsi masing-masing aset, dan lokasi penggunaannya di dalam kode sumber tema.
Seluruh aset mematuhi aturan ketat: **Zero CDN, Zero Emoji, dan 100% Digunakan**.

---

## 1. Tipografi Lokal (Font woff2)

Semua berkas font di-bundle secara lokal di `public/themes/bugis-heritage/fonts/` dengan format `.woff2` terkompresi tanpa dependensi ke CDN eksternal (Google Fonts, Bunny, dsb).

| Berkas Font | Ukuran | Format | Peran / Penggunaan | Referensi Kode |
|---|---|---|---|---|
| `cormorant-garamond-latin-500-normal.woff2` | ~16 KB | WOFF2 | Tipografi Display (Medium) untuk judul section | `fonts/fonts.css` |
| `cormorant-garamond-latin-600-normal.woff2` | ~17 KB | WOFF2 | Tipografi Display (SemiBold) untuk nama mempelai & cover | `fonts/fonts.css`, `<link rel="preload">` di `layout.blade.php` |
| `cormorant-garamond-latin-400-italic.woff2` | ~15 KB | WOFF2 | Tipografi Display (Italic) untuk kutipan & penekanan | `fonts/fonts.css` |
| `jost-latin-300-normal.woff2` | ~15 KB | WOFF2 | Tipografi Body teks halus | `fonts/fonts.css` |
| `jost-latin-400-normal.woff2` | ~15 KB | WOFF2 | Tipografi Body teks utama & label tombol | `fonts/fonts.css`, `<link rel="preload">` di `layout.blade.php` |
| `jost-latin-500-normal.woff2` | ~15 KB | WOFF2 | Tipografi Label kapital (kicker) berjarak | `fonts/fonts.css` |
| `amiri-arabic-400-normal.woff2` | ~25 KB | WOFF2 | Tipografi teks Arab (Ayat Al-Qur'an / doa) dengan harakat | `fonts/fonts.css` |
| `allura-latin-400-normal.woff2` | ~18 KB | WOFF2 | Tipografi aksen script/kaligrafi halus (& / inisial) | `fonts/fonts.css` |
| `noto-sans-buginese-400-normal.woff2` | 3.3 KB | WOFF2 | Aksara Lontara Bugis (rentang Unicode U+1A00 sampai U+1A1F) | `fonts/fonts.css` |
| `fonts.css` | 1.8 KB | CSS | Definisi `@font-face` lokal dengan `font-display: swap` | `layout.blade.php`, `expired.blade.php` |

---

## 2. Ilustrasi & Ornamen Vektor Orisinal (SVG)

Semua aset vektor tersimpan di `public/themes/bugis-heritage/svg/`. Dibuat orisinal, di bawah 30 KB, menggunakan variabel warna CSS (`currentColor`, `var(--bh-...)`), dan ramah pembaca layar (`aria-hidden="true"`).

| Nama Berkas | Ukuran | Fungsi | Lokasi Penggunaan |
|---|---|---|---|
| `walasuji-tile.svg` | < 2 KB | Ubin pola belah ketupat walasuji bersusun | `theme.css` (`.bh-pattern-walasuji`) |
| `walasuji-frame.svg` | < 3 KB | Bingkai belah ketupat geometris foto mempelai | `couple.blade.php` (`.bh-photo-frame--diamond`) |
| `lipa-check-tile.svg` | < 2 KB | Ubin pola kotak tekstur sarung sutra (lipa' sabbe) | `theme.css` (`.bh-pattern-lipa`) |
| `border-gold.svg` | < 3 KB | Garis tepi emas geometris kartu acara & penutup | `theme.css` (`.bh-card--gold-border`) |
| `corner-ornament.svg` | < 2 KB | Ornamen sudut geometris-floral cover & penutup | `cover.blade.php`, `closing.blade.php` (`.bh-corner-ornament`) |
| `rumah-panggung.svg` | < 4 KB | Siluet rumah panggung tradisional Bugis | `closing.blade.php` (`.bh-closing-silhouette`) |
| `pacci-leaf.svg` | < 3 KB | Daun pacar (inai) halus untuk dekorasi acara | Kartu acara Mappacci (Fase 4) |
| `divider-diamond.svg` | < 2 KB | Pembatas seksi belah ketupat horizontal | `verse.blade.php`, `couple.blade.php`, `countdown.blade.php`, `gallery.blade.php`, `events.blade.php`, `rsvp.blade.php`, `gift.blade.php`, `wishes.blade.php` |
| `monogram-badge.svg` | < 2 KB | Lencana inisial berbingkai belah ketupat | `cover.blade.php` (`.bh-cover-badge`), `bottom-nav.blade.php` (`.bh-nav-monogram`) |
| `grain-silk.svg` | < 2 KB | Tekstur butiran kain sutra halus | `theme.css` (`.bh-grain`), `layout.blade.php` |
| `bg-pattern.svg` | < 2 KB | Pola geometris latar belakang desktop | `theme.css` (`.bh-desktop-frame`), `layout.blade.php` |
| `glossary-icon.svg` | < 2 KB | Ikon buku / lentera glosarium istilah adat | Tombol Glosarium Adat / Mengenal Istilah (Fase 4) |

---

## 3. Berkas Media (Audio & Gambar)

| Nama Berkas | Format | Peran | Lisensi | Lokasi Penggunaan |
|---|---|---|---|---|
| `audio/heritage-instrumental.mp3` | MP3 | Musik latar instrumental khidmat dan berlisensi bebas | Creative Commons / Royalty-Free | Elemen `<audio id="bh-bgm">` di `layout.blade.php` |
| `img/og-preview.jpg` | JPEG | Cadangan pratinjau media sosial (Open Graph & Twitter Card) | Milik Sendiri / Proyek | Meta tag `og:image` & `twitter:image` di `layout.blade.php` |

---

## 4. Berkas Skrip & Gaya

| Nama Berkas | Peran | Lokasi Penggunaan |
|---|---|---|
| `theme.css` | Lembar gaya utama: design tokens, 4 preset warna, cover styles, couple frames, countdown, gallery, mobile-first 360-480 px | `<head>` di `layout.blade.php` & `expired.blade.php` |
| `theme.js` | Skrip tema: pembukaan gerbang cover, pemutar audio, hitung mundur, lightbox galeri, navigasi aktif, pencegahan freeze scroll | Akhir `<body>` di `layout.blade.php` |
