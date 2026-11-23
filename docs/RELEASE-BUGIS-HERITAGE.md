# Rilis Resmi Tema Bugis Heritage (v1.0.0)

Dokumen ini merupakan catatan rilis resmi untuk tema undangan pernikahan digital adat Bugis bernama **Bugis Heritage**.

---

## 1. Ringkasan & Filosofi Desain

Tema **Bugis Heritage** dirancang untuk menghadirkan suasana pernikahan adat Bugis yang hangat, klasik, khidmat, berwibawa, dan bernuansa kain sutra serta emas antik.

### Prinsip Utama & Gerbang Keabsahan Budaya
1. **Tidak Ada Klaim Budaya Tanpa Verifikasi:** Tidak ada istilah, arti, atau makna filosofis yang dapat tampil di antarmuka publik sebelum ditinjau dan berstatus `diverifikasi` dengan `is_published = true`.
2. **Perlindungan Terhadap Privasi & Finansial:** Tidak menampilkan nominal atau rincian uang panai, uang belanja, mahar, maupun ritual privat keluarga ke ranah publik.
3. **Pemisahan Budaya:** Istilah adat Bugis tidak dicampuradukkan dengan istilah Makassar.
4. **Preservasi Gelar & Ejaan:** Gelar adat, kebangsawanan (seperti "Andi", "Puang"), serta gelar akademik ditampilkan persis tanpa normalisasi atau pemotongan. Tanda petik tunggal/apostrof (') pada ejaan dipertahankan.
5. **Zero CDN:** Seluruh berkas font, skrip, dan aset dimuat secara lokal tanpa dependensi CDN luar.
6. **Zero Emoji:** Bersih dari emoji pada UI, kode, komentar, dan dokumen.
7. **Bebas Pembekuan (Zero-Freeze):** Mekanisme pembukaan gerbang cover menggunakan kunci penyimpanan unik per jalur undangan dan penanganan pemulihan posisi gulir yang mulus.

---

## 2. Token Desain & 4 Preset Warna

Tema ini menggunakan variabel CSS (`.theme-bugis-heritage`) dengan sudut tegas berwibawa (`--bh-radius: 4px`) dan tipografi berlisensi terbuka:
- **Display:** Cormorant Garamond
- **Body:** Jost (bersih dan terstruktur)
- **Teks Arab:** Amiri (dengan harakat lengkap)
- **Aksara Lontara:** Noto Sans Buginese (Unicode Buginese U+1A00 sampai U+1A1F)

### 4 Preset Warna
1. **Marun & Emas (`marun-emas`, Bawaan):** Latar cokelat kehitaman hangat (`#1f1a17`), aksen emas antik (`#c9a45c`), aksen sekunder marun (`#6b2a2f`).
2. **Lumut & Emas (`lumut-emas`):** Latar gelap dengan aksen emas antik dan hijau lumut (`#3f5a3a`).
3. **Terong & Emas (`terong-emas`):** Latar gelap dengan aksen emas antik dan ungu terong (`#5b3a54`).
4. **Krem Sutra (`krem-sutra`):** Latar terang krem kain sutra (`#f3e6cf`) dengan kontras teks gelap terkalibrasi (`#2a1f1a`), aksen marun dan emas antik.

---

## 3. Inventaris Aset Orisinal

Semua aset tersimpan di `public/themes/bugis-heritage/`, tercatat di `ASSET-USAGE.md`, dan digunakan 100% pada kode tema:

### Tipografi Lokal (9 Berkas WOFF2)
- `cormorant-garamond-latin-500-normal.woff2`
- `cormorant-garamond-latin-600-normal.woff2`
- `cormorant-garamond-latin-400-italic.woff2`
- `jost-latin-300-normal.woff2`
- `jost-latin-400-normal.woff2`
- `jost-latin-500-normal.woff2`
- `amiri-arabic-400-normal.woff2`
- `allura-latin-400-normal.woff2`
- `noto-sans-buginese-400-normal.woff2`

### Vektor SVG Orisinal (12 Berkas SVG)
1. `walasuji-tile.svg` (Ubin pola geometris walasuji)
2. `walasuji-frame.svg` (Bingkai geometris belah ketupat foto mempelai)
3. `lipa-check-tile.svg` (Pola tekstur kotak sarung sutra lipa' sabbe)
4. `border-gold.svg` (Garis pembatas emas kartu acara dan penutup)
5. `corner-ornament.svg` (Ornamen sudut geometris pintu gerbang cover dan penutup)
6. `rumah-panggung.svg` (Siluet rumah panggung tradisional Bugis)
7. `pacci-leaf.svg` (Aksen daun pacar untuk prosesi Mappacci)
8. `divider-diamond.svg` (Pembatas horizontal belah ketupat antar-seksi)
9. `monogram-badge.svg` (Lencana inisial monogram mempelai)
10. `grain-silk.svg` (Tekstur butiran halus kain sutra)
11. `bg-pattern.svg` (Pola latar belakang desktop di luar batas tengah 480 px)
12. `glossary-icon.svg` (Ikon tombol glosarium istilah adat)

### Media Audio & Grafis
- `audio/heritage-instrumental.mp3` (Musik latar instrumental khidmat lisensi bebas)
- `img/og-preview.jpg` (Cadangan pratinjau media sosial Open Graph)

---

## 4. Sistem Manajemen Konten & Gerbang Verifikasi

Pengelolaan istilah dan prosesi adat disediakan melalui Filament Admin:
- **Katalog Jenis Acara (`/admin/adat-event-types`):** Mengelola daftar prosesi adat (misal: Mappacci, Mappasili, Mappettu Ada, dsb), alias ejaan, dan catatan variasi daerah.
- **Kamus Glosarium Adat (`/admin/adat-glossary-entries`):** Mengelola istilah budaya, aksara Lontara, dan arti dalam bahasa Indonesia.
- **Penyaring Otomatis:** Undangan publik hanya memuat entri dengan status `diverifikasi` dan `is_published = true`. Entri berstatus `draf` diberi lencana khusus di Filament dan tidak dapat dilihat tamu publik.

---

## 5. Fitur Interaktif & Pengalaman Tamu

- **Pintu Gerbang Tradisional (Cover):** Efek pembukaan gerbang dua daun pintu dengan ornamen sudut geometris emas dan animasi perlahan.
- **Varian Bingkai Foto Mempelai:** Pilihan bingkai belah ketupat (`diamond`), lengkung (`arch`), dan bulat (`round`).
- **Aksen Khusus Mappacci:** Deteksi otomatis prosesi Mappacci pada kartu acara untuk menyematkan ornamen daun pacar (`pacci-leaf`).
- **Unduh Kalender .ics & Google Calendar:** Setiap kartu acara dilengkapi tombol penambahan ke kalender gawai.
- **Modal Glosarium Adat:** Menampilkan istilah budaya terverifikasi dengan tipografi aksara Lontara Unicode dan padanan bacaan Latin.
- **Kado Cashless & QRIS Dinamis:** Penyalinan nomor rekening ke papan klip instan dengan pesan konfirmasi santun, dan tombol buka QRIS.
- **Buku Tamu Digital & QR Check-in:** Menyajikan kode QR unik per tamu untuk pemindaian saat resepsi.
- **Halaman Masa Berlaku Selesai (Expired View):** Tampilan purna-acara khusus Bugis Heritage dengan ucapan terima kasih bernuansa hangat.

---

## 6. Jaminan Kualitas & Pengujian (Quality Assurance)

Seluruh pengujian teknis telah diselesaikan dengan hasil 100% lulus:
- `tests/Feature/AdatVerificationGateTest.php`
- `tests/Feature/BugisHeritageFoundationTest.php`
- `tests/Feature/BugisHeritagePageStructureTest.php`
- `tests/Feature/BugisHeritageAdatEventsAndGlossaryTest.php`
- `tests/Feature/BugisHeritageSharedComponentsTest.php`
- `tests/Feature/BugisHeritageAnimationAndAccessibilityTest.php`
- `tests/Feature/BugisHeritageQualityAssuranceTest.php`

**Hasil Pengujian:** 94 pengujian passed, 894 assertions passed, 0 kegagalan, dan kompatibilitas tema sebelumnya (`dark-boho`, `sunny-meadow`, `minimalist-botanical`) tetap terjaga penuh.
