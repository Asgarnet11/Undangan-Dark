# Panduan Antigravity: Tema Undangan Adat Bugis "Bugis Heritage"

Dokumen ini adalah spesifikasi kerja untuk Antigravity IDE. Tujuannya: membangun tema undangan pernikahan digital bernuansa **adat Bugis** bernama **Bugis Heritage**, dengan aset, kode, dan tampilan yang orisinal.

Karena ini tema adat, dokumen ini punya satu prinsip yang mengalahkan semua yang lain:

> **Tidak boleh ada klaim budaya yang belum diverifikasi tampil di undangan.** Lebih baik tidak menampilkan sesuatu daripada menampilkannya salah.

Baca seluruh dokumen sebelum menulis kode. Kerjakan per fase (bagian 15). Fase 1 (Kamus Konten Adat dan gerbang verifikasi) wajib selesai sebelum fase visual.

**Prasyarat:** fitur tambahan panel (Kisah Cinta, Tempat Duduk, QRIS, Daftar Kado, penyempurnaan RSVP dan Acara) sebaiknya sudah selesai lebih dulu, lihat `FITUR-TAMBAHAN-PANEL-GUIDE.md`. Tema ini memakai komponen bersama dari fitur tersebut. Jika sebagian belum ada, tema tetap harus berjalan dan section tanpa data tidak ditampilkan.

---

## 1. Aturan Utama

### 1.1 Aturan Akurasi Budaya (Prioritas Tertinggi)

1. **Antigravity dilarang menambahkan fakta budaya dari pengetahuannya sendiri.** Istilah, ejaan, arti, urutan prosesi, busana, simbol, makna motif, dan aksara hanya boleh berasal dari **Kamus Konten Adat** (Lampiran A) atau dari data yang diisi pengguna. Jika Antigravity merasa perlu menambah istilah atau makna, ia **berhenti**, menuliskannya sebagai entri `BELUM-DIVERIFIKASI` di `VERIFIKASI-BUDAYA.md`, dan tidak menampilkannya di tema.
2. **Gerbang verifikasi.** Setiap entri di Kamus Konten Adat punya status: `draf`, `diverifikasi`, atau `ditolak`. Hanya entri `diverifikasi` yang boleh tampil di undangan publik. Entri `draf` hanya terlihat di panel admin dan pratinjau internal, dengan penanda jelas.
3. **Adat Bugis bervariasi antar daerah** (misalnya Bone, Wajo, Soppeng, Sidenreng, Pangkep, dan komunitas Bugis perantauan). Nama prosesi, urutan, dan kelengkapannya bisa berbeda. Karena itu:
   - Daftar acara bersifat **pilihan yang dapat diatur per undangan**, bukan urutan baku yang dipaksakan.
   - Teks bawaan harus **netral** dan tidak menyatakan "begini seharusnya".
   - Penjelasan istilah memakai kalimat hati-hati dan dapat diedit oleh pemilik undangan.
4. **Jangan mencampur istilah Bugis dan Makassar.** Keduanya berkerabat tetapi berbeda (contoh: prosesi mengantar mempelai pria disebut *mappenre botting* dalam Bugis dan *appanai leko lompo* dalam Makassar). Tema ini Bugis. Istilah Makassar tidak dipakai kecuali pemilik undangan sendiri yang memintanya dan sudah diverifikasi.
5. **Aksara Lontara hanya untuk teks yang sudah diverifikasi.** Dilarang membuat fitur transliterasi otomatis dari huruf Latin ke Lontara: aksara ini tidak menuliskan semua bunyi (misalnya vokal bawaan "a" tidak ditulis), sehingga konversi otomatis mudah salah. Teks Lontara tampil hanya jika pengguna mengisi pasangan teks Latin dan Lontara yang sudah ditandai `diverifikasi`.
6. **Jangan menyatakan makna warna.** Makna warna busana adat (misalnya pada baju bodo) berkaitan dengan status dan berbeda-beda menurut sumber dan daerah. Palet tema murni estetis. Tidak ada kalimat yang menjelaskan "warna ini berarti ...".
7. **Jangan menggambar orang berbusana adat** (siluet pengantin, ilustrasi pakaian) pada fase awal. Detail busana sangat mudah keliru. Foto asli klien diperbolehkan. Ilustrasi orang hanya boleh dibuat bila entri busana terkait sudah `diverifikasi` dan ada pengecek visual.
8. **Hormati gelar dan nama.** Gelar adat dan kebangsawanan (misalnya "Andi", "Puang") dan gelar akademik harus tampil persis seperti yang diisi, tanpa dinormalisasi, dipotong, atau diganti huruf besar-kecilnya. Jangan mengubah tanda apostrof (') pada ejaan.
9. **Hal yang sengaja tidak ditampilkan di undangan:** nominal atau rincian uang belanja dan mahar (misalnya uang panai), serta rincian ritual yang bersifat privat keluarga. Pembahasan keluarga tidak boleh menjadi konten publik.
10. **Satu sumber ejaan.** Semua istilah mengikuti ejaan di Kamus Konten Adat. Varian ejaan disimpan sebagai alias, tetapi tampilan memakai satu ejaan baku per entri.

### 1.2 Aturan Umum Tema

11. **Jangan menyalin aset referensi.** Seluruh ilustrasi dibuat sendiri sebagai SVG. Tema ini terinspirasi dari ide umum tema adat, bukan dari aset template mana pun.
12. **Setiap aset harus benar-benar dipakai** dan tercatat di `ASSET-USAGE.md`. Aset tak terpakai dianggap kegagalan.
13. **Animasi wajib ada dan terlihat** (bagian 9), dengan karakter **khidmat dan tenang**, bukan riuh.
14. **Font di-bundle lokal.** Dilarang memuat font dari CDN eksternal.
15. **Ikuti konvensi proyek.** Jangan mengubah tema atau fitur lain yang sudah berjalan.
16. **Mobile-first.** Rancang dari 360 px. Di desktop, kolom undangan di tengah (maks. sekitar 480 px).
17. **Tanpa emoji** di UI, kode, komentar, dan dokumen.
18. **Bahasa antarmuka: Bahasa Indonesia**, konsisten, lewat berkas bahasa. Istilah adat ditampilkan sesuai Kamus Konten Adat dengan penjelasan Indonesia.
19. **Data demo bersih** dan tidak bertentangan (nama tempat, alamat, tautan peta, jam acara).

---

## 2. Gambaran Tema

| Aspek | Keputusan |
|---|---|
| Nama tema | Bugis Heritage |
| Suasana | Hangat, klasik, khidmat, berwibawa, bernuansa kain sutra dan emas antik |
| Motif | Pola belah ketupat (walasuji/sulapa eppa), pola kotak sarung sutra (lipa' sabbe), siluet rumah panggung, daun pacar, bunga dan sulur halus |
| Bahasa visual | Geometris-tradisional dengan garis emas tipis, ruang kosong yang lega, tekstur kain halus |
| Format | Satu halaman bergulir, dibuka lewat cover |
| Gaya default | Klasik hangat. Preset "Krem Sutra" menyediakan versi lebih terang dan bersih |

---

## 3. Design Tokens

Simpan sebagai CSS custom properties pada scope tema (`.theme-bugis-heritage`). Semua warna wajib lewat variabel agar preset bisa diganti.

```css
.theme-bugis-heritage {
  /* Latar */
  --bh-bg:            #1f1a17;  /* cokelat kehitaman hangat */
  --bh-bg-elevated:   #2a2320;
  --bh-bg-deep:       #161210;
  --bh-bg-silk:       #f3e6cf;  /* krem sutra (section terang) */

  /* Teks */
  --bh-ink:           #f3e6cf;
  --bh-ink-soft:      #c9b99d;
  --bh-ink-faint:     #8a7d68;
  --bh-ink-on-silk:   #2a1f1a;

  /* Aksen (murni estetis, tanpa makna simbolik) */
  --bh-gold:          #c9a45c;  /* emas antik */
  --bh-gold-soft:     #e2c98f;
  --bh-maroon:        #6b2a2f;  /* marun */
  --bh-moss:          #3f5a3a;  /* hijau lumut */
  --bh-aubergine:     #5b3a54;  /* ungu terong */

  /* Garis & efek */
  --bh-line:          rgba(201, 164, 92, 0.35);
  --bh-glow:          rgba(201, 164, 92, 0.22);
  --bh-radius:        4px;      /* sudut tegas, bukan membulat */

  /* Tipografi */
  --bh-font-display:  "Cormorant Garamond", Georgia, serif;
  --bh-font-body:     "Lora", Georgia, serif;
  --bh-font-label:    "Jost", system-ui, sans-serif;
  --bh-font-arabic:   "Amiri", serif;
  --bh-font-lontara:  "Noto Sans Buginese", sans-serif;

  /* Gerak (tenang dan khidmat) */
  --bh-ease:          cubic-bezier(.25, .46, .45, .94);
  --bh-dur-fast:      260ms;
  --bh-dur-base:      800ms;
  --bh-dur-slow:      1600ms;
}
```

### Preset warna (pilihan pengguna)

| Preset | Latar | Aksen utama | Aksen sekunder |
|---|---|---|---|
| Marun & Emas (default) | `--bh-bg` | `--bh-gold` | `--bh-maroon` |
| Lumut & Emas | `--bh-bg` | `--bh-gold` | `--bh-moss` |
| Terong & Emas | `--bh-bg` | `--bh-gold` | `--bh-aubergine` |
| Krem Sutra (terang) | `--bh-bg-silk` | `--bh-maroon` | `--bh-gold` |

Preset hanya mengganti variabel warna. Tidak ada keterangan makna warna di UI.

### Tipografi
- Display (judul, nama mempelai): **Cormorant Garamond**.
- Body: **Lora** atau serif sejenis yang nyaman dibaca.
- Label kecil: **Jost**, huruf kapital berjarak.
- Teks Arab: **Amiri** (mendukung harakat).
- Lontara: **Noto Sans Buginese** (pakai berkas resmi dari Noto Project, periksa lisensinya dan tuliskan atribusi di modal Kredit).
- Semua font di-bundle lokal (`.woff2`), `font-display: swap`. Font Lontara hanya dimuat jika ada teks Lontara pada undangan.

---

## 4. Penanganan Aksara Lontara

- Standar teknis: aksara Lontara di Unicode dikenal sebagai blok **Buginese** (rentang U+1A00 sampai U+1A1F). Gunakan teks Unicode asli, bukan gambar, untuk teks yang dapat diedit.
- Beri atribut bahasa dan aksara yang benar pada elemen teksnya: `lang="bug-Bugi"`.
- Fitur penyusunan huruf (tanda vokal) bergantung pada font dan mesin render perangkat. Uji di Android dan iOS nyata. Jika sebuah perangkat tidak menampilkan dengan benar, sediakan **cadangan SVG** (teks yang sudah diverifikasi diubah menjadi path) atau tampilkan teks Latin saja.
- Teks Lontara dekoratif (judul, ornamen) harus berasal dari entri `diverifikasi`. Tidak boleh ada "huruf Lontara asal-asalan" sebagai hiasan.
- Setiap teks Lontara selalu punya padanan Latin dan `aria-label` berisi bacaan Latin.
- Tidak ada transliterasi otomatis (lihat aturan 5).

---

## 5. Aset SVG Orisinal

Simpan di `assets/themes/bugis-heritage/svg/`. Gunakan variabel CSS (`currentColor` atau `fill: var(--bh-...)`) agar warna mengikuti preset.

| File | Fungsi | Dipakai di |
|---|---|---|
| `walasuji-tile.svg` | Ubin pola belah ketupat bersusun yang dapat diulang | Latar section, pembatas |
| `walasuji-frame.svg` | Bingkai belah ketupat untuk foto | Foto mempelai |
| `lipa-check-tile.svg` | Ubin pola kotak seperti sarung sutra (abstrak, tanpa klaim motif tertentu) | Kartu acara, penutup |
| `border-gold.svg` | Garis tepi emas bergaya geometris | Bingkai kartu |
| `corner-ornament.svg` | Ornamen sudut geometris-floral (abstrak) | Cover, penutup |
| `rumah-panggung.svg` | Siluet rumah panggung sederhana | Cover, penutup |
| `pacci-leaf.svg` | Daun dan sulur halus (untuk acara mappacci, bila dipakai) | Kartu acara mappacci |
| `divider-diamond.svg` | Pemisah dengan deretan belah ketupat kecil | Antar section |
| `monogram-badge.svg` | Lencana inisial berbingkai belah ketupat | Cover, navigasi |
| `grain-silk.svg` (atau `.png` kecil) | Tekstur kain halus | Overlay halaman |
| `bg-pattern.svg` | Pola latar desktop | Sisi kiri-kanan desktop |
| `glossary-icon.svg` | Ikon buku/penjelasan istilah | Tombol glosarium |

Aturan aset:
- Tiap SVG di bawah 30 KB; optimalkan dengan SVGO.
- Ornamen dekoratif memakai `aria-hidden="true"`.
- Pecah elemen yang akan dianimasikan menjadi grup `<g id="...">`.
- **Tidak ada figur manusia berbusana adat** (aturan 7).
- Motif belah ketupat dipakai sebagai bahasa geometris. Tema tidak menjelaskan "makna" motif di UI kecuali entri glosarium terkait sudah `diverifikasi`.

---

## 6. Struktur Halaman dan Spesifikasi Section

Urutan scroll. Semua teks dari data undangan atau Kamus Konten Adat yang `diverifikasi`.

### 6.1 Cover
- "Undangan Pernikahan", nama panggilan mempelai, tanggal, dan "Kepada: [nama tamu]" dari `?to=KODE`. Jika kode tidak valid: "Tamu Undangan".
- Tombol **Buka Undangan**. Halaman terkunci sebelum dibuka. Saat diklik: musik mulai, animasi pembuka berjalan (bagian 9), lalu menggulir ke section pertama.
- Gaya cover: `default`, `top`, `space-between`, `frame`.
- Opsi: foto cover atau ilustrasi saja, warna dan opacity overlay, warna teks, tampilkan nama tamu.

### 6.2 Salam Pembuka dan Ayat
- Salam pembuka (misalnya "Assalamu'alaikum Warahmatullahi Wabarakatuh") dan kalimat pengantar, dapat diedit.
- Ayat opsional (teks Arab, terjemahan Indonesia, sumber). Default QS. Ar-Rum: 21 sebagai contoh, **wajib diverifikasi pemilik undangan**. Teks Arab memakai `lang="ar"`, `dir="rtl"`.
- Tidak ada ucapan atau pepatah Bugis di teks bawaan. Jika pemilik ingin memakainya, ia mengisinya sendiri dan menandainya `diverifikasi`.

### 6.3 Profil Mempelai
- Foto dalam bingkai belah ketupat (`walasuji-frame.svg`).
- Field: nama lengkap dengan gelar persis seperti diisi, "putri/putra ke-", nama orang tua (dengan gelar bila ada), asal/domisili, akun sosial opsional.
- **Monogram**: sediakan field `monogram_initial` yang dapat diisi manual, karena inisial dari nama berawalan gelar (misalnya "Andi") bisa bukan inisial yang diinginkan keluarga. Jika kosong, ambil dari nama panggilan.

### 6.4 Rangkaian Acara (Inti Tema Adat)
- Kartu acara bergaya kain (`lipa-check-tile.svg`) dengan bingkai emas, untuk **setiap acara yang diundang**: nama acara, hari-tanggal, jam, tempat, alamat, tombol peta dan kalender (data terstruktur dan konsisten seperti di panduan fitur tambahan).
- **Jenis acara dipilih dari katalog** (Lampiran A): Akad Nikah, Resepsi (Tudang Botting), serta pilihan seperti Mappacci, Mappanre Temme, Mappenre Botting, Mapparola, dan lainnya. Pemilik undangan **mencentang hanya yang berlaku** bagi keluarganya. Tidak ada urutan paksa.
- Nama acara tampil dengan ejaan baku katalog dan keterangan Indonesia (misalnya "Mappacci (malam pacar)"), dengan opsi mengganti label tampilan oleh pemilik.
- Tombol kecil **"Tentang acara ini"** membuka modal berisi penjelasan singkat **hanya jika entri glosariumnya `diverifikasi` dan dipublikasikan**.
- Catatan tampil/busana opsional (teks bebas oleh pemilik), misalnya "Mohon mengenakan busana adat". Tema tidak menyarankan atau menyebutkan busana tertentu sendiri.
- Acara yang bersifat keluarga saja bisa diberi label "Khusus keluarga" dan tidak tampil ke semua tamu (pengaturan visibilitas per grup tamu, jika model tamu sudah mendukung grup).

### 6.5 Hitung Mundur dan Simpan Tanggal
- Countdown ke acara pertama yang dipilih (default: akad). Zona waktu dari undangan. Jika sudah lewat: "Acara telah berlangsung".
- Link kalender dari data terstruktur (waktu selesai tidak boleh sebelum mulai), plus unduhan `.ics`.

### 6.6 Kisah Cinta (Opsional)
- Komponen bersama `LoveStory`, tampil sebagai timeline bergaya garis emas dengan penanda belah ketupat.

### 6.7 Galeri
- Judul dan kutipan dapat diedit. Grid 2 kolom bergaya bingkai tipis, maks. 12 foto, `loading="lazy"`, lightbox dengan swipe dan tombol tutup (Escape juga menutup).

### 6.8 Glosarium Adat (Opsional, Pembeda Tema)
- Tombol "Mengenal Istilah" membuka modal daftar istilah yang **diverifikasi dan dipublikasikan**: istilah, bacaan Latin, penjelasan singkat Indonesia.
- Berguna bagi tamu non-Bugis. Tampil hanya jika minimal satu entri tersedia.
- Setiap entri punya catatan kecil jika maknanya bervariasi antar daerah ("Dapat berbeda menurut daerah").

### 6.9 RSVP, Kado, dan Ucapan
- Komponen bersama dari panduan fitur tambahan: RSVP dengan jumlah orang dan pesan terima kasih berbeda, Kado Cashless (rekening dan QRIS), Daftar Kado, ucapan dan doa.
- Tidak ada teks tentang uang belanja atau mahar.

### 6.10 Informasi Tempat Duduk dan QR Tamu
- Komponen bersama `SeatingInfo` dan modal QR tamu sesuai aturan visibilitas di panduan fitur tambahan.

### 6.11 Penutup
- Kalimat terima kasih dan doa, nama mempelai, daftar "Turut Mengundang" (disembunyikan jika kosong), ornamen sudut dan siluet rumah panggung, dan modal **Kredit**.

### 6.12 Modal Kredit dan Atribusi
- Memuat kredit pembuat (A2 Dev) dengan tautan, lisensi musik, atribusi font (termasuk Noto Sans Buginese bila dipakai), dan catatan "Istilah adat dalam undangan ini diverifikasi oleh [nama pengecek, bila bersedia dicantumkan]". Pencantuman nama pengecek hanya dengan izin yang bersangkutan.

### 6.13 Navigasi Bawah (Sticky)
- Lima item: Mempelai, Acara, lencana monogram (kembali ke atas), Galeri/Kado, Ucapan. Ikon SVG garis buatan sendiri.
- Indikator aktif bergeser mulus (IntersectionObserver). Hormati safe area.
- Tombol musik putar/jeda terpisah dan selalu terlihat setelah undangan dibuka.

---

## 7. Opsi Kustomisasi Tema

Konfigurasi tema per undangan (JSON), diterjemahkan menjadi atribut `data-*` dan CSS variable.

```json
{
  "theme": "bugis-heritage",
  "preset": "marun-emas",
  "cover": {
    "style": "default",
    "usePhoto": false,
    "overlayColor": "#1f1a17",
    "overlayOpacity": 0.5,
    "textColor": "#f3e6cf",
    "showGuestName": true
  },
  "pattern": {
    "walasuji": "subtle"
  },
  "layout": {
    "mapsAndRsvpBelowEvents": true,
    "giftInline": true,
    "photoFrame": "diamond",
    "brideFirst": true
  },
  "adat": {
    "enabledEventTypes": ["akad", "resepsi"],
    "showGlossary": false,
    "showLontara": false
  },
  "motion": {
    "ambient": true
  },
  "sections": {
    "verse": true,
    "video": false,
    "loveStory": true,
    "gallery": true,
    "livestream": false,
    "gift": true,
    "wishes": true
  }
}
```

---

## 8. Model Data Tambahan

```
adat_event_type  (katalog, dikelola admin sistem)
  id, key, label_display, alt_spellings (json), description_id (nullable),
  regional_note (nullable), status (draf | diverifikasi | ditolak),
  verified_by (nullable), verified_at (nullable), is_published

adat_glossary_entry
  id, term, latin_reading, lontara_text (nullable), meaning_id,
  regional_note (nullable), source_note (nullable),
  status (draf | diverifikasi | ditolak), verified_by, verified_at, is_published

event  (kolom baru bila belum ada)
  adat_event_type_id (nullable)
  display_label_override (nullable)
  visibility (semua | grup tertentu)

invitation  (kolom baru)
  monogram_initial (nullable)
  dress_note (text, nullable)
  theme_config (json)

lontara_text
  id, invitation_id, latin_text, lontara_text, status, verified_by, verified_at
```

---

## 9. Spesifikasi Animasi

Karakter: **khidmat, tenang, berwibawa**. Gerak pelan, anggun, dan terkendali. Tidak ada konfeti, efek memantul, atau gerak ceria. Gunakan `transform` dan `opacity` saja.

### 9.1 Animasi Wajib

| No | Animasi | Detail |
|---|---|---|
| 1 | Cover membuka | Dua panel berpola belah ketupat bergeser pelan ke kiri-kanan (1,4-1,8 dtk) seperti gerbang terbuka, nama mempelai terungkap dengan fade lambat, lalu halaman bergulir otomatis |
| 2 | Pola tergambar | Garis emas pola walasuji "ditarik" satu per satu (stroke-dashoffset) di cover dan pemisah |
| 3 | Kilau emas | Garis dan ornamen emas memiliki kilau tipis yang lewat pelan sesekali (siklus 8-12 dtk) |
| 4 | Scroll reveal | Elemen masuk dengan fade dan naik 20-32 px, bertahap (stagger 100-140 ms) |
| 5 | Reveal teks | Judul section terungkap per baris dengan masking |
| 6 | Foto mempelai | Bingkai belah ketupat menggambar sisinya satu per satu, lalu foto muncul memudar dari dalam |
| 7 | Kartu acara | Pola kotak "ditenun" masuk dari tepi kartu (clip-path bertahap), lalu isi muncul |
| 8 | Timeline Kisah Cinta | Garis emas vertikal tergambar mengikuti scroll, penanda belah ketupat menyala saat dilewati |
| 9 | Countdown | Angka bergeser halus saat berganti |
| 10 | Galeri | Foto muncul bergantian dengan fade, lightbox membuka dengan skala lembut |
| 11 | Siluet rumah panggung | Muncul perlahan dari bawah di penutup dengan lampu hangat menyala bertahap (opsional, tema ambient) |
| 12 | Tombol dan navigasi | Hover/tap dengan cahaya emas tipis, indikator navigasi bergeser mulus |

---

## 10. Musik

- Berkas audio dari data undangan. Sediakan satu musik default instrumental berlisensi bebas.
- Jangan mengklaim suatu lagu atau instrumen sebagai "musik adat Bugis" kecuali pemilik proyek memastikannya.
- Mulai hanya setelah klik "Buka Undangan". Tombol putar/jeda selalu terlihat. `preload="none"` sebelum dibuka.

---

## 11. Performa

- Target (profil 4G lambat): LCP di bawah 2,5 dtk, CLS di bawah 0,1.
- Aset dekoratif berupa SVG; foto dalam WebP/AVIF.
- Font di-subset dan dipreload seperlunya; font Lontara hanya dimuat bila dibutuhkan.
- JavaScript tema di bawah 150 KB gzip.

---

## 12. Aksesibilitas

- Kontras teks minimal WCAG AA.
- Ornamen `aria-hidden="true"`; gambar bermakna punya `alt`.
- Teks Arab `lang="ar"` `dir="rtl"`; teks Lontara `lang="bug-Bugi"` dengan `aria-label` bacaan Latin.
- Fokus keyboard jelas, modal memerangkap fokus, ukuran sentuh minimal 44 px.

---

## 13. Keamanan

- Validasi kode tamu di server; sanitasi input; CSRF; rate limit.
- Konten adat yang belum `diverifikasi` tidak boleh bocor ke API publik.
- Nomor rekening dan QRIS dimuat setelah undangan dibuka. Tidak ada API key di klien.

---

## 14. Struktur Berkas yang Disarankan

```
public/themes/bugis-heritage/
  fonts/          (woff2 lokal)
  svg/            (aset bagian 5)
  audio/          (musik default berlisensi bebas)
  theme.css
  theme.js
  ASSET-USAGE.md
docs/
  KAMUS-KONTEN-ADAT.md    (salinan Lampiran A, sumber kebenaran konten)
  VERIFIKASI-BUDAYA.md    (log verifikasi, lihat Lampiran B)
resources/views/themes/bugis-heritage/
  layout, cover, verse, couple, events, countdown, love-story,
  gallery, glossary, rsvp, gift, wishes, closing, credits, bottom-nav
```

---

## 15. Rencana Kerja Bertahap

- **Fase 0 - Audit**: sistem tema, komponen bersama, skema data. Laporkan ringkasan dan rencana. Belum ada kode.
- **Fase 1 - Konten adat dan gerbang verifikasi (sebelum visual)**: Kamus Konten Adat, log verifikasi budaya, migrasi database dan model, gerbang verifikasi server, panel admin, seeder status draf.
- **Fase 2 - Fondasi dan aset**: Registrasi tema, CSS tokens, font lokal, SVG orisinal, ASSET-USAGE.md.
- **Fase 3 - Cover dan section dasar**: Cover, ayat, profil mempelai, countdown, galeri, penutup, navigasi, musik.
- **Fase 4 - Acara adat dan glosarium**: Katalog acara, pemilihan acara per undangan, modal glosarium, Lontara render/fallback.
- **Fase 5 - Komponen bersama dan kustomisasi**: RSVP, kado cashless & QRIS, ucapan, kisah cinta, tempat duduk, QR check-in, modal kredit, opsi tema.
- **Fase 6 - Animasi dan polesan**: 12 animasi khidmat, prefers-reduced-motion, optimasi performa dan aksesibilitas.
- **Fase 7 - QA teknis**: Pengujian fitur, keamanan, regresi, pint, phpunit.
- **Fase 8 - Verifikasi budaya dan rilis**: Review pengecek budaya, dokumentasi final.

---

## Lampiran A: Kamus Konten Adat (Draf Awal, Semua Berstatus `draf`)

### A.1 Katalog Jenis Acara

| Ejaan baku (usulan) | Varian ejaan | Penjelasan singkat (draf) | Catatan variasi |
|---|---|---|---|
| Mammanu'-manu' / Mappese'-pese' | mappesek-pesek | Tahap penjajakan sebelum lamaran | Tidak lazim tampil di undangan |
| Madduta | massuro | Peminangan/lamaran | Tidak lazim tampil di undangan |
| Mappettu Ada | mappettuada, mapettu ada | Pertemuan keluarga untuk menetapkan kesepakatan pernikahan | Rincian pembicaraannya privat; tidak ditampilkan |
| Mappasau Botting | - | Perawatan pengantin menjelang hari pernikahan | Kini jarang dilakukan menurut literatur |
| Mappanre Temme | mappanre temme' | Khatam Al-Qur'an; ada daerah yang menyertainya dengan pembacaan barzanji | Bervariasi antar daerah |
| Mappasili | cemme passili (belum pasti ejaannya) | Ritual pembersihan menjelang pernikahan (siraman) | Ejaan dan istilah perlu dikonfirmasi |
| Mappacci | mappaci, tudang penni (belum dikonfirmasi) | Malam penyucian dan pemberian doa kepada calon mempelai dengan daun pacar (pacci) | Pelaksanaan dan istilah bervariasi |
| Mappenre Botting | mappabotting, mappanre botting | Mengantar mempelai pria ke rumah mempelai wanita untuk akad nikah | Padanan Makassar berbeda; jangan dicampur |
| Madduppa Botting | - | Penyambutan kedatangan mempelai pria | Perlu dikonfirmasi urutannya |
| Akad Nikah | ijab kabul, ripakawin | Akad pernikahan | Istilah "ripakawin" perlu dikonfirmasi |
| Mappasikarawa | - | Prosesi setelah akad nikah, diikuti sungkem kepada orang tua dan keluarga yang dituakan | Detail prosesi perlu dikonfirmasi |
| Tudang Botting | - | Pesta/resepsi pernikahan | Pemakaian label ini bervariasi |
| Mapparola | marola | Kunjungan balasan mempelai wanita ke rumah mempelai pria; menurut sumber membawa sarung tenun sebagai hadiah | Bervariasi |
| Massita Baiseng | - | Muncul dalam satu sumber sebagai tahap setelah resepsi; arti belum jelas | Jangan dipakai sebelum dikonfirmasi |

### A.2 Glosarium Istilah, Motif, dan Busana (Draf)

| Istilah | Penjelasan singkat (draf) | Catatan |
|---|---|---|
| Sulapa Eppa | "Empat sisi"; bentuk belah ketupat yang dalam literatur dikaitkan dengan empat unsur (api, air, angin, tanah) dan ada yang menafsirkannya sebagai empat arah mata angin | Tafsir bervariasi antar sumber; jangan menyatakan satu tafsir sebagai satu-satunya |
| Walasuji | Pagar/pembatas anyaman bambu berbentuk belah ketupat yang dipakai dalam upacara atau pesta adat, dan dikaitkan dengan sulapa eppa | Penggunaan dan penafsiran bervariasi |
| Sulapa Eppa na Taue | Pandangan yang menyamakan empat sisi dengan empat sifat manusia: acca (cerdas), warani (berani), lempu (jujur), sugi (kaya) | Dari satu sumber; perlu konfirmasi |
| Lontara | Aksara tradisional Bugis dan Makassar; menurut literatur, bentuk dasarnya dikaitkan dengan filosofi sulapa eppa | Pernyataan asal-usul ditandai "menurut literatur" |
| Lipa' Sabbe | Sarung sutra khas Bugis; corak kotak sering dikaitkan dengan sulapa eppa | Corak dan makna beragam |
| Waju Tokko | Busana perempuan adat yang dikenal juga sebagai baju bodo; penamaan berbeda antar sumber dan daerah | Jangan mencampur sebutan tanpa konfirmasi |
| Jas Tutu' | Jas tertutup untuk pria, dipadukan dengan lipa' sabbe dan songkok | Kelengkapan bervariasi |
| Songkok Recca / Pa'biring | Penutup kepala dari serat pelepah lontar yang dapat dihiasi benang emas | Nama dan riwayat penggunaan perlu dikonfirmasi |
| Baju Bella Dada dan Passapu' | Busana pria tradisional dengan penutup kepala passapu' | Asal (Bugis atau Makassar) dan penggunaan berbeda antar sumber; jangan jadi default |
| Indo' Botting | Pendamping/inang pengantin | Perlu konfirmasi |
| Erang-erang | Seserahan yang dibawa dalam iring-iringan mempelai pria | Perlu konfirmasi |
| Pacci | Daun pacar (inai) yang dipakai dalam mappacci | Perlu konfirmasi |

---

## Lampiran B: Proses Verifikasi Budaya

Template tabel `VERIFIKASI-BUDAYA.md`:
```
| ID | Entri | Ejaan | Penjelasan tampil | Tampil di | Status | Pengecek 1 | Pengecek 2 | Catatan koreksi |
|----|-------|-------|-------------------|-----------|--------|------------|------------|-----------------|
```
