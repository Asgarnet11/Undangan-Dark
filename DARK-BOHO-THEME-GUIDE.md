# Panduan Antigravity: Tema Undangan Digital "Dark Boho"

Dokumen ini adalah spesifikasi kerja untuk Antigravity IDE. Tujuannya: membangun satu tema undangan pernikahan digital baru bernama **Dark Boho** yang struktur dan fiturnya setara dengan template referensi (https://our-wedding.link/design/dark-boho), tetapi dengan aset, kode, dan tampilan yang orisinal.

Baca seluruh dokumen sebelum menulis kode. Kerjakan per fase (bagian 14) dan jangan lompat fase.

---

## 1. Aturan Utama (Wajib Dipatuhi)

1. **Jangan menyalin aset referensi.** Foto, ilustrasi, musik, dan teks dari situs referensi tidak boleh dipakai. Semua ilustrasi dibuat sendiri sebagai SVG.
2. **Setiap aset harus benar-benar dipakai.** Setiap file di `assets/themes/dark-boho/` harus dirujuk oleh kode. Di akhir tiap fase, buat tabel "aset - dipakai di komponen mana". Aset yang tidak terpakai dianggap kegagalan.
3. **Animasi wajib ada dan terlihat.** Tema tanpa animasi yang jelas dianggap belum selesai (lihat bagian 8).
4. **Font di-bundle lokal.** Dilarang memuat font dari CDN eksternal. Simpan file `.woff2` di dalam proyek.
5. **Ikuti konvensi proyek yang sudah ada.** Sebelum menulis apa pun, periksa struktur repo: framework, struktur tema yang sudah ada, cara tema didaftarkan, skema database, dan gaya penamaan. Tema baru harus mengikuti pola itu, bukan membuat arsitektur baru.
6. **Jangan membuat tampilan generik ala AI.** Hindari gradient ungu-biru, kartu putih dengan bayangan standar, font sistem polos, dan ikon dari library populer yang seragam. Tampilan harus punya karakter: gelap, hangat, organik, dan berkelas.
7. **Mobile-first.** Undangan dibuka hampir seluruhnya dari ponsel. Desain dari lebar 360 px ke atas; di desktop tampilkan kolom undangan di tengah (maks. sekitar 480 px) dengan latar dekoratif di kiri-kanan.
8. **Tanpa emoji** di UI, kode, komentar, maupun dokumentasi yang dihasilkan.
9. **Bahasa antarmuka default: Bahasa Indonesia**, konsisten, tanpa campuran Inggris acak. Semua teks harus berasal dari data undangan atau berkas bahasa, bukan hardcode tersebar.
10. **Bersihkan data demo.** Tidak boleh ada teks sisa developer (contoh: "asdf", "csdcsd"), typo, atau data lokasi yang saling bertentangan.

---

## 2. Gambaran Tema

| Aspek | Keputusan |
|---|---|
| Nama tema | Dark Boho |
| Suasana | Gelap, hangat, organik, tenang, sedikit mewah |
| Motif | Pampas grass, bunga kering, lengkungan (arch), matahari/bulan sabit, garis daun, tekstur kertas |
| Bahasa visual | Ilustrasi garis tipis (line art) dengan isian lembut, tekstur grain halus |
| Format | Satu halaman bergulir (single page), dibuka lewat cover |

Catatan: palet dan tipografi di bawah adalah usulan desain. Boleh disempurnakan selama suasana "dark boho" tetap terjaga.

---

## 3. Design Tokens

Simpan sebagai CSS custom properties pada scope tema (contoh: `.theme-dark-boho`). Semua warna harus lewat variabel agar preset warna bisa diganti.

```css
.theme-dark-boho {
  /* Latar */
  --db-bg:            #1d1d1b;  /* charcoal utama */
  --db-bg-elevated:   #262522;  /* kartu / panel */
  --db-bg-deep:       #151513;  /* section kontras */

  /* Teks */
  --db-ink:           #efe6d8;  /* krem hangat */
  --db-ink-soft:      #b9ad9a;
  --db-ink-faint:     #7d7466;

  /* Aksen boho */
  --db-terracotta:    #c0714f;
  --db-clay:          #a8563a;
  --db-rose:          #c99a8b;  /* dusty rose */
  --db-sage:          #8a9a7b;
  --db-sand:          #d8c3a0;
  --db-gold:          #c9a45c;  /* antique gold */

  /* Garis & efek */
  --db-line:          rgba(239, 230, 216, 0.14);
  --db-glow:          rgba(201, 164, 92, 0.25);
  --db-radius-arch:   999px 999px 24px 24px; /* bentuk lengkungan */

  /* Tipografi */
  --db-font-display:  "Cormorant Garamond", "Times New Roman", serif;
  --db-font-script:   "Allura", cursive;
  --db-font-body:     "Jost", system-ui, sans-serif;

  /* Gerak */
  --db-ease:          cubic-bezier(.22, .61, .36, 1);
  --db-dur-fast:      240ms;
  --db-dur-base:      700ms;
  --db-dur-slow:      1400ms;
}
```

### Preset warna (pilihan pengguna)

Sediakan minimal 4 preset yang hanya mengganti variabel aksen:

| Preset | Aksen utama | Aksen sekunder |
|---|---|---|
| Terracotta (default) | `--db-terracotta` | `--db-sand` |
| Sage | `--db-sage` | `--db-sand` |
| Dusty Rose | `--db-rose` | `--db-gold` |
| Gold | `--db-gold` | `--db-terracotta` |

### Tipografi

- Display (judul, nama mempelai): Cormorant Garamond, bobot 500-600, huruf kapital dengan letter-spacing lebar untuk label kecil.
- Script (aksen nama/ornamen kata): Allura atau font script sejenis, dipakai hemat.
- Body: Jost, bobot 300-400.
- Unduh file `.woff2` (subset Latin), simpan di `assets/themes/dark-boho/fonts/`, deklarasikan dengan `@font-face` dan `font-display: swap`. Preload dua font yang dipakai di cover.

---

## 4. Aset yang Harus Dibuat (SVG Orisinal)

Buat semua aset sebagai SVG yang bersih dan ringan, simpan di `assets/themes/dark-boho/svg/`. Gunakan `currentColor` atau variabel CSS untuk warna agar mengikuti preset.

| File | Fungsi | Dipakai di |
|---|---|---|
| `pampas-left.svg`, `pampas-right.svg` | Rumpun pampas grass di sisi cover | Cover, penutup |
| `arch-frame.svg` | Bingkai lengkung untuk foto mempelai | Section mempelai |
| `sun-moon.svg` | Ornamen matahari/bulan | Pembuka, pemisah |
| `leaf-branch.svg` | Ranting daun garis tipis | Pemisah section |
| `dried-flower-cluster.svg` | Bunga kering | Acara, hadiah |
| `divider-boho.svg` | Garis pemisah ornamen | Antar section |
| `monogram-frame.svg` | Bingkai monogram inisial | Cover, navigasi |
| `grain.svg` (atau `grain.png` kecil) | Tekstur noise halus | Overlay seluruh halaman |
| `bg-pattern.svg` | Pola latar desktop | Sisi kiri-kanan di desktop |

Aturan aset:
- Tiap SVG di bawah 30 KB. Optimalkan dengan SVGO.
- Beri `aria-hidden="true"` pada ornamen dekoratif.
- Pecah elemen yang akan dianimasikan menjadi grup `<g id="...">` agar bisa digerakkan terpisah (contoh: helai pampas yang bergoyang).

---

## 5. Struktur Halaman dan Spesifikasi Section

Urutan scroll. Semua teks diambil dari data undangan (bagian 7).

### 5.1 Cover
- Tampilkan "Undangan Pernikahan", nama panggilan mempelai, tanggal, dan nama tamu (jika aktif).
- Nama tamu diambil dari parameter `?to=KODE` (cari tamu berdasarkan kode; jika tidak ditemukan, tampilkan "Tamu Undangan").
- Tombol **Buka Undangan**. Saat diklik: mulai musik, jalankan animasi pembuka, lalu aktifkan scroll (halaman terkunci sebelum dibuka).
- Variasi gaya cover (pilihan pengguna): `default`, `top` (teks di atas), `space-between` (teks terpisah atas-bawah), `new` (komposisi lengkung di tengah).
- Opsi: foto cover atau ilustrasi saja, warna overlay, opacity overlay, warna teks.

### 5.2 Hitung Mundur dan Save the Date
- Countdown hari, jam, menit, detik menuju waktu akad (atau acara pertama), dihitung dari zona waktu acara (`Asia/Jakarta` secara default, bisa WITA/WIT).
- Jika waktu sudah lewat, tampilkan teks "Acara telah berlangsung" alih-alih angka nol.
- Tombol **Simpan ke Kalender** (lihat 5.7).

### 5.3 Ayat / Kata Pembuka
- Teks Arab, terjemahan, dan sumber. Semua field bisa diedit.
- Contoh default (QS. Ar-Rum: 21; pastikan diverifikasi oleh pemilik undangan sebelum dipakai):

  > وَمِنْ آيَاتِهِ أَنْ خَلَقَ لَكُم مِّنْ أَنفُسِكُمْ أَزْوَاجًا لِّتَسْكُنُوا إِلَيْهَا وَجَعَلَ بَيْنَكُم مَّوَدَّةً وَرَحْمَةً ۚ إِنَّ فِي ذَٰلِكَ لَآيَاتٍ لِّقَوْمٍ يَتَفَكَّرُونَ
  >
  > "Dan di antara tanda-tanda (kebesaran)-Nya ialah Dia menciptakan pasangan-pasangan untukmu dari jenismu sendiri, agar kamu cenderung dan merasa tenteram kepadanya, dan Dia menjadikan di antaramu rasa kasih dan sayang. Sungguh, pada yang demikian itu benar-benar terdapat tanda-tanda (kebesaran Allah) bagi kaum yang berpikir." (QS. Ar-Rum: 21)

- Tampilkan teks Arab dengan font yang mendukung harakat (mis. Amiri atau Scheherazade New, di-bundle lokal), `direction: rtl`, ukuran besar dan jarak baris longgar.
- Sediakan opsi sembunyikan section ini.

### 5.4 Profil Mempelai
- Dua blok (mempelai wanita dan pria) dengan foto di dalam bingkai lengkung (`arch-frame.svg`).
- Field: nama lengkap dan gelar, "putri/putra ke-", nama kedua orang tua, asal/domisili, akun Instagram (opsional).
- Urutan wanita-pria atau pria-wanita bisa ditukar lewat pengaturan.
- Foto memakai `object-fit: cover`, rasio tetap, dengan efek reveal (bagian 8).

### 5.5 Video (Opsional)
- Embed YouTube dengan **facade**: tampilkan thumbnail dan tombol play, muat iframe hanya saat diklik (hemat bandwidth).

### 5.6 Galeri
- Judul dan kutipan bisa diedit.
- Grid 2 kolom (mobile), maksimal 12 foto, `loading="lazy"`.
- Klik foto membuka lightbox dengan swipe kiri-kanan, tombol tutup, dan penutup lewat tombol Escape.

### 5.7 Acara (Akad dan Resepsi)
- Kartu terpisah untuk tiap acara: nama acara, hari-tanggal, jam mulai dan selesai (atau "selesai" jika tidak ada), nama tempat, alamat.
- Tombol **Lihat Lokasi** (Google Maps) dan **Simpan ke Kalender**.
- Link kalender **harus benar**:
  - Format Google Calendar: `https://calendar.google.com/calendar/render?action=TEMPLATE&text=...&dates=YYYYMMDDTHHMMSS/YYYYMMDDTHHMMSS&ctz=Asia/Jakarta&details=...&location=...`
  - Waktu mulai dan selesai berbeda dan sesuai jadwal sebenarnya.
  - Semua parameter di-`encodeURIComponent`.
  - Sediakan juga unduhan file `.ics` valid untuk Apple/Outlook.
- Nama tempat, teks alamat, dan tautan peta harus berasal dari **satu sumber data yang sama** agar tidak pernah saling bertentangan.
- Peta embed memakai `https://www.google.com/maps?q=<alamat ter-encode>&output=embed` dengan `loading="lazy"`. Jangan menaruh API key apa pun di kode klien.

### 5.8 RSVP
- Form: nama (terisi otomatis jika dari tautan tamu), pilihan hadir/tidak hadir, jumlah tamu (jika diizinkan), pesan opsional.
- Validasi sisi klien dan server, status loading dan sukses yang jelas.
- Satu tamu satu RSVP (boleh diperbarui). Terhubung ke tabel RSVP di panel admin.

### 5.9 Live Streaming (Opsional)
- Judul, catatan, dan tombol menuju tautan streaming. Tampil hanya jika tautan diisi dan section diaktifkan.

### 5.10 Hadiah / Amplop Digital
- Daftar rekening: nama bank, nomor rekening, atas nama, tombol **Salin** (Clipboard API, beri umpan balik "Tersalin").
- Alamat pengiriman kado (opsional).
- Catatan terima kasih bisa diedit.
- Posisi section bisa di dalam alur utama atau sebagai tombol melayang (opsi pengguna).

### 5.11 Ucapan dan Doa (Wishes)
- Form: nama, domisili (opsional), pesan.
- Daftar ucapan dengan paginasi atau "muat lebih banyak", urut terbaru.
- Sanitasi semua input (cegah XSS), batasi panjang pesan, dan beri rate limit.
- Opsi moderasi dari panel admin (sembunyikan/hapus ucapan).

### 5.12 Penutup
- Kalimat terima kasih, nama mempelai dengan gaya script, daftar "Turut Mengundang" (bisa kosong, dan disembunyikan jika kosong), dan hiasan pampas.

### 5.13 Navigasi Bawah (Sticky)
- Lima item: Mempelai, Galeri, monogram (kembali ke atas), Lokasi, Ucapan.
- Penanda section aktif memakai IntersectionObserver.
- Hormati safe area (`env(safe-area-inset-bottom)`).
- Tombol musik (putar/jeda) terpisah dan selalu terlihat setelah undangan dibuka.

---

## 6. Opsi Kustomisasi Tema

Semua opsi disimpan sebagai konfigurasi tema per undangan (JSON), lalu diterjemahkan menjadi atribut `data-*` dan CSS variable. Jangan membuat template terpisah untuk setiap variasi.

```json
{
  "theme": "dark-boho",
  "preset": "terracotta",
  "cover": {
    "style": "default",
    "usephoto": true,
    "overlayColor": "#1d1d1b",
    "overlayOpacity": 0.55,
    "textColor": "#efe6d8",
    "showGuestName": true
  },
  "layout": {
    "mapsAndRsvpBelowEvents": true,
    "giftInline": true,
    "illustrationMode": "illustrated",
    "brideFirst": true
  },
  "sections": {
    "verse": true,
    "video": false,
    "gallery": true,
    "livestream": false,
    "gift": true,
    "wishes": true
  }
}
```

Sediakan **panel pratinjau langsung** di admin: mengubah opsi langsung memperbarui tampilan tanpa muat ulang penuh.

---

## 7. Model Data Undangan

Sesuaikan penamaan dengan skema yang sudah ada di proyek. Minimal field berikut harus tersedia (jika belum ada, buat migrasi tambahan, jangan merusak data lama):

```
invitation
  slug, theme, theme_config (json), timezone
  bride_name, bride_nickname, bride_father, bride_mother, bride_order_label, bride_origin, bride_instagram, bride_photo
  groom_name, groom_nickname, groom_father, groom_mother, groom_order_label, groom_origin, groom_instagram, groom_photo
  verse_arabic, verse_translation, verse_source
  cover_photo, music_file, music_title
  video_url, livestream_url, livestream_note
  closing_text, thank_you_gift_note

event (banyak per undangan)
  invitation_id, title, starts_at, ends_at (nullable), venue_name, address, maps_query, sort_order

gallery_photo
  invitation_id, path, caption, sort_order

gift_account
  invitation_id, bank_name, account_number, account_name

gift_address
  invitation_id, label, address

co_inviter
  invitation_id, name, sort_order

guest
  invitation_id, name, code (unik), group, max_pax

rsvp
  guest_id / invitation_id, name, attending, pax, message, created_at

wish
  invitation_id, name, origin, message, is_visible, created_at
```

Aturan data:
- Waktu acara disimpan terstruktur (`starts_at`, `ends_at`), lalu semua tampilan teks dan link kalender diturunkan dari sana.
- Tampilkan hari dan tanggal dalam Bahasa Indonesia (contoh: "Senin, 21 Desember 2026").
- Jika mendukung bahasa lain, ambil teks dari berkas terjemahan, bukan hardcode.

---

## 8. Spesifikasi Animasi

Targetnya: terasa halus, mahal, dan organik. Gunakan `transform` dan `opacity` saja (hindari menganimasikan `top/left/width/height`).

### 8.1 Animasi Wajib

| No | Animasi | Detail |
|---|---|---|
| 1 | Pembukaan cover | Saat tombol diklik, dua panel lengkung (arch doors) membuka ke kiri-kanan dalam 1.2-1.6 dtk, nama mempelai naik memudar, lalu halaman bergulir otomatis ke section pertama |
| 2 | Pampas bergoyang | Helai pampas di cover dan penutup bergoyang pelan (rotasi 1-3 derajat, siklus 6-9 dtk, `transform-origin` di pangkal) |
| 3 | Partikel halus | Debu cahaya / serbuk bunga melayang sangat pelan di cover (maks. 20 elemen, CSS atau canvas ringan) |
| 4 | Scroll reveal | Setiap section masuk dengan fade + naik 24-40 px, bertahap (stagger 80-120 ms antar elemen) |
| 5 | Reveal teks | Judul section terungkap per baris atau per kata dengan masking |
| 6 | Foto mempelai | Foto dalam bingkai lengkung muncul dengan efek clip-path membuka dari bawah ke atas, ditambah parallax lembut |
| 7 | Garis pemisah | `divider-boho.svg` tergambar (stroke-dashoffset) saat masuk viewport |
| 8 | Countdown | Angka berganti dengan transisi flip/slide halus |
| 9 | Galeri | Foto muncul bertahap (stagger), lightbox membuka dengan scale + fade |
| 10 | Tombol | Hover/tap dengan glow emas lembut dan transisi 240 ms |
| 11 | Navigasi bawah | Item aktif bergeser dengan indikator yang bergerak mulus |

### 8.2 Aturan Implementasi
- Gunakan pustaka yang sudah ada di proyek. Jika belum ada, pilih salah satu: (a) GSAP + ScrollTrigger untuk animasi kompleks, atau (b) CSS + IntersectionObserver untuk versi ringan. Jangan memasang dua pustaka animasi sekaligus.
- Terapkan **`prefers-reduced-motion`**: jika aktif, matikan parallax, partikel, dan goyangan; sisakan fade sederhana.
- Jangan membuat animasi blocking: konten tetap terbaca jika JavaScript animasi gagal dimuat (progressive enhancement).
- Frame rate target 60 fps di ponsel menengah. Gunakan `will-change` seperlunya dan lepaskan setelah animasi selesai.
- Tahan animasi berat sampai undangan dibuka (jangan jalankan sebelum interaksi pertama).

---

## 9. Musik

- Berkas audio dari data undangan (`music_file`); sediakan satu musik default berlisensi bebas yang dipilih pemilik proyek.
- Mulai **hanya setelah** klik "Buka Undangan" (kebijakan autoplay browser).
- Tombol putar/jeda tetap terlihat; simpan preferensi mute selama sesi.
- Gunakan `preload="none"` sebelum dibuka agar hemat data.
- Jeda otomatis saat tab tidak aktif (opsional, `visibilitychange`).

---

## 10. Performa

Target (uji di profil jaringan 4G lambat):
- LCP di bawah 2,5 detik, CLS di bawah 0,1.
- Gambar dalam WebP/AVIF dengan `srcset`, `width/height` eksplisit, dan `loading="lazy"` untuk semua di bawah fold.
- Font: subset Latin, dua keluarga dipreload, sisanya dimuat sesuai kebutuhan.
- Iframe (YouTube, Maps) dimuat malas (facade / lazy).
- Total JavaScript tema di bawah 150 KB gzip, di luar pustaka animasi.
- Kompres semua aset gambar saat unggah (di sisi server atau saat build).

---

## 11. Aksesibilitas

- Kontras teks terhadap latar minimal WCAG AA (periksa teks `--db-ink-soft` di atas `--db-bg`).
- Semua gambar bermakna punya `alt`; ornamen dekoratif `aria-hidden="true"`.
- Fokus keyboard terlihat jelas, urutan tab logis, lightbox memerangkap fokus.
- Tombol punya label jelas; form memiliki `label` terkait dan pesan galat yang dapat dibaca pembaca layar.
- Teks Arab diberi atribut `lang="ar"` dan `dir="rtl"`.

---

## 12. Keamanan

- Validasi kode tamu di server; jangan membocorkan daftar tamu lewat respons API.
- Sanitasi dan escape semua input RSVP dan ucapan; gunakan perlindungan CSRF.
- Terapkan rate limit pada endpoint RSVP dan ucapan (per IP dan per kode tamu).
- Nomor rekening hanya ditampilkan setelah undangan dibuka; jangan mengirimkannya ke halaman yang tidak memerlukan.
- Tidak ada API key atau rahasia di kode klien.
- Unggahan file: batasi tipe dan ukuran, ganti nama file, simpan di luar direktori yang dapat dieksekusi.

---

## 13. Struktur Berkas yang Disarankan

Sesuaikan dengan struktur repo yang sudah ada. Contoh:

```
assets/themes/dark-boho/
  fonts/          (woff2 lokal)
  svg/            (aset bagian 4)
  img/            (tekstur, placeholder)
  audio/          (musik default berlisensi bebas)
  theme.css       (tokens + gaya)
  theme.js        (animasi, countdown, lightbox, musik)
  ASSET-USAGE.md  (tabel aset dan tempat pemakaiannya)
resources/views/themes/dark-boho/   (atau padanan di framework proyek)
  layout, cover, countdown, verse, couple, video, gallery,
  events, rsvp, livestream, gift, wishes, closing, bottom-nav
```

Daftarkan tema baru lewat mekanisme registrasi tema yang sudah ada (jangan mengubah tema watercolor atau tema lain yang sudah berjalan).

---

## 14. Rencana Kerja Bertahap

Selesaikan satu fase, jalankan, tunjukkan hasil, baru lanjut.

**Fase 0 - Audit**
- Pelajari struktur repo, sistem tema, skema database, dan cara halaman publik dirender.
- Keluarkan ringkasan singkat dan daftar perubahan yang akan dibuat. Jangan menulis kode sebelum ringkasan ini ada.

**Fase 1 - Fondasi**
- Daftarkan tema, buat layout dasar, tokens CSS, font lokal, grain overlay, dan kolom undangan responsif (mobile dan desktop).
- Migrasi/penambahan field data yang diperlukan.

**Fase 2 - Aset dan Cover**
- Buat semua SVG (bagian 4), bangun cover lengkap dengan animasi pembuka, pampas bergoyang, partikel, dan musik.
- Hasilkan `ASSET-USAGE.md`.

**Fase 3 - Section Konten**
- Countdown, ayat, mempelai, video facade, galeri dan lightbox, acara dengan link kalender dan `.ics` yang benar, navigasi bawah.

**Fase 4 - Interaksi dan Data**
- RSVP, ucapan, hadiah (salin rekening), live streaming, personalisasi nama tamu lewat `?to=KODE`.

**Fase 5 - Kustomisasi dan Admin**
- Opsi tema (bagian 6), preset warna, pratinjau langsung di panel admin, moderasi ucapan.

**Fase 6 - Animasi Lanjutan dan Polesan**
- Lengkapi seluruh animasi bagian 8.1, terapkan `prefers-reduced-motion`, optimasi performa dan aksesibilitas.

**Fase 7 - QA**
- Jalankan seluruh daftar periksa (bagian 15), perbaiki temuan, dan tulis ringkasan akhir.

---

## 15. Daftar Periksa Penerimaan (Acceptance Checklist)

Isi dengan bukti (tangkapan layar atau keterangan uji) sebelum menyatakan selesai.

**Visual dan aset**
- [ ] Palet dan tipografi sesuai bagian 3; font dimuat lokal.
- [ ] Semua SVG di bagian 4 ada dan dipakai; `ASSET-USAGE.md` lengkap.
- [ ] Tidak ada aset atau teks yang disalin dari situs referensi.
- [ ] Tampilan konsisten di 360, 390, 430 px dan desktop.

**Animasi**
- [ ] Ke-11 animasi di bagian 8.1 terlihat berjalan.
- [ ] `prefers-reduced-motion` mematikan gerak berat.
- [ ] Tidak ada patah-patah yang jelas saat scroll di ponsel menengah.

**Fungsi**
- [ ] Nama tamu muncul dari `?to=KODE`; kode tidak valid ditangani dengan baik.
- [ ] Musik hanya mulai setelah klik Buka Undangan; tombol putar/jeda berfungsi.
- [ ] Countdown akurat sesuai zona waktu; kondisi "sudah lewat" ditangani.
- [ ] Link Google Calendar valid (waktu mulai dan selesai berbeda dan benar); file `.ics` terunduh dan terbuka.
- [ ] Nama tempat, alamat, dan link peta selalu konsisten (satu sumber data).
- [ ] RSVP dan ucapan tersimpan, tampil di panel admin, dan tervalidasi.
- [ ] Salin nomor rekening berfungsi dengan umpan balik.
- [ ] Setiap opsi di bagian 6 benar-benar mengubah tampilan.

**Kualitas**
- [ ] Tidak ada teks sisa developer, typo, atau campuran bahasa yang tidak disengaja.
- [ ] Skor performa dan aksesibilitas memenuhi bagian 10 dan 11.
- [ ] Tidak ada API key di kode klien; input disanitasi; rate limit aktif.
- [ ] Tema lain yang sudah ada tetap berjalan normal (tidak ada regresi).

---

## 16. Prompt Pembuka yang Disarankan untuk Antigravity

Tempelkan teks berikut sebagai instruksi awal, lalu lampirkan dokumen ini:

```
Baca DARK-BOHO-THEME-GUIDE.md sampai habis. Mulai dari Fase 0: audit repo dan
berikan ringkasan temuan serta rencana perubahan, tanpa menulis kode dulu.
Setelah saya setujui, kerjakan satu fase per giliran dan hentikan di akhir
tiap fase untuk saya periksa. Patuhi Aturan Utama di bagian 1, terutama:
setiap aset harus benar-benar dipakai, animasi wajib terlihat, font di-bundle
lokal, dan jangan ubah tema yang sudah berjalan.
```
