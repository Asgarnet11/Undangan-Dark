# Pemetaan Penggunaan Aset Tema Dark Boho

Dokumen ini mencatat seluruh berkas aset di direktori `public/themes/dark-boho/` dan komponen kode yang merujuknya.

## Fase 1 - Fase 6 - Fondasi, Cover, Konten, Interaktivitas, Admin, & Animasi Lanjutan

| Berkas Aset | Kategori | Dipakai di Komponen / Berkas |
|---|---|---|
| `fonts/fonts.css` | Stylesheet Font Lokal | `resources/views/themes/dark-boho/layout.blade.php` |
| `fonts/cormorant-garamond-latin-400-italic.woff2` | Font Display (Italic) | `public/themes/dark-boho/fonts/fonts.css` |
| `fonts/cormorant-garamond-latin-500-normal.woff2` | Font Display (500) | `public/themes/dark-boho/fonts/fonts.css` |
| `fonts/cormorant-garamond-latin-600-normal.woff2` | Font Display (600) | `public/themes/dark-boho/fonts/fonts.css` & `<link rel="preload">` di `layout.blade.php` |
| `fonts/allura-latin-400-normal.woff2` | Font Script | `public/themes/dark-boho/fonts/fonts.css` |
| `fonts/jost-latin-300-normal.woff2` | Font Body (300) | `public/themes/dark-boho/fonts/fonts.css` |
| `fonts/jost-latin-400-normal.woff2` | Font Body (400) | `public/themes/dark-boho/fonts/fonts.css` & `<link rel="preload">` di `layout.blade.php` |
| `fonts/jost-latin-500-normal.woff2` | Font Body (500) | `public/themes/dark-boho/fonts/fonts.css` |
| `fonts/amiri-arabic-400-normal.woff2` | Font Arab Harakat | `public/themes/dark-boho/fonts/fonts.css` |
| `svg/pampas-left.svg` | Ilustrasi Pampas Kiri | `resources/views/themes/dark-boho/partials/cover.blade.php`, `closing.blade.php` |
| `svg/pampas-right.svg` | Ilustrasi Pampas Kanan | `resources/views/themes/dark-boho/partials/cover.blade.php`, `closing.blade.php` |
| `svg/arch-frame.svg` | Bingkai Lengkung Mempelai | `resources/views/themes/dark-boho/partials/couple.blade.php` |
| `svg/sun-moon.svg` | Ornamen Matahari dan Bulan | `resources/views/themes/dark-boho/partials/cover.blade.php`, `verse.blade.php` |
| `svg/leaf-branch.svg` | Ranting Daun Garis Tipis | `resources/views/themes/dark-boho/partials/couple.blade.php`, `gallery.blade.php` |
| `svg/dried-flower-cluster.svg` | Rangkaian Bunga Kering | `resources/views/themes/dark-boho/partials/events.blade.php`, `gift-content.blade.php` |
| `svg/divider-boho.svg` | Pemisah Ornamen Antar Section (Stroke Draw) | `resources/views/themes/dark-boho/partials/countdown.blade.php`, `verse.blade.php`, `events.blade.php`, `closing.blade.php` |
| `svg/monogram-frame.svg` | Bingkai Monogram Inisial | `resources/views/themes/dark-boho/partials/cover.blade.php`, `bottom-nav.blade.php` |
| `svg/grain.svg` | Tekstur Noise Halus Halaman | `public/themes/dark-boho/theme.css` (`.db-grain`) |
| `svg/bg-pattern.svg` | Pola Latar Desktop | `public/themes/dark-boho/theme.css` (`.db-desktop-frame::before`, `.db-desktop-frame::after`) |
| `img/cover-boho.webp` | Latar Foto Default Cover | `resources/views/themes/dark-boho/partials/cover.blade.php` |
| `audio/boho-serenade.mp3` | Musik Latar Default Bebas Lisensi | `resources/views/themes/dark-boho/layout.blade.php` (`#db-bgm`) |
| `theme.css` | Token, Cover, Masking, Indikator Nav, & Reduced Motion | `resources/views/themes/dark-boho/layout.blade.php` |
| `theme.js` | Seluruh 11 Animasi GSAP + ScrollTrigger & Interaksi | `resources/views/themes/dark-boho/layout.blade.php` |
