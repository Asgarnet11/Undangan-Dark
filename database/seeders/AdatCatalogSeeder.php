<?php

namespace Database\Seeders;

use App\Models\AdatEventType;
use App\Models\AdatGlossaryEntry;
use Illuminate\Database\Seeder;

class AdatCatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seluruh entri awal berstatus draf sesuai Lampiran A BUGIS-HERITAGE-THEME-GUIDE.md
     */
    public function run(): void
    {
        $eventTypes = [
            [
                'key' => 'mammanu_manu',
                'label_display' => "Mammanu'-manu' / Mappese'-pese'",
                'alt_spellings' => ['mappesek-pesek'],
                'description_id' => 'Tahap penjajakan sebelum lamaran',
                'regional_note' => 'Tidak lazim tampil di undangan publik',
                'status' => 'draf',
                'is_published' => false,
                'sort_order' => 1,
            ],
            [
                'key' => 'madduta',
                'label_display' => 'Madduta',
                'alt_spellings' => ['massuro'],
                'description_id' => 'Peminangan atau lamaran resmi',
                'regional_note' => 'Tidak lazim tampil di undangan publik',
                'status' => 'draf',
                'is_published' => false,
                'sort_order' => 2,
            ],
            [
                'key' => 'mappettu_ada',
                'label_display' => 'Mappettu Ada',
                'alt_spellings' => ['mappettuada', 'mapettu ada'],
                'description_id' => 'Pertemuan keluarga untuk menetapkan kesepakatan pernikahan',
                'regional_note' => 'Rincian pembicaraan privat; tidak ditampilkan',
                'status' => 'draf',
                'is_published' => false,
                'sort_order' => 3,
            ],
            [
                'key' => 'mappasau_botting',
                'label_display' => 'Mappasau Botting',
                'alt_spellings' => [],
                'description_id' => 'Perawatan pengantin menjelang hari pernikahan',
                'regional_note' => 'Kini jarang dilakukan menurut literatur',
                'status' => 'draf',
                'is_published' => false,
                'sort_order' => 4,
            ],
            [
                'key' => 'mappanre_temme',
                'label_display' => 'Mappanre Temme',
                'alt_spellings' => ["mappanre temme'"],
                'description_id' => 'Khatam Al-Qur\'an; ada daerah yang menyertainya dengan pembacaan barzanji',
                'regional_note' => 'Bervariasi antar daerah',
                'status' => 'draf',
                'is_published' => false,
                'sort_order' => 5,
            ],
            [
                'key' => 'mappasili',
                'label_display' => 'Mappasili',
                'alt_spellings' => ['cemme passili'],
                'description_id' => 'Ritual pembersihan menjelang pernikahan (siraman)',
                'regional_note' => 'Ejaan dan istilah perlu dikonfirmasi',
                'status' => 'draf',
                'is_published' => false,
                'sort_order' => 6,
            ],
            [
                'key' => 'mappacci',
                'label_display' => 'Mappacci',
                'alt_spellings' => ['mappaci', 'tudang penni'],
                'description_id' => 'Malam penyucian dan pemberian doa kepada calon mempelai dengan daun pacar (pacci)',
                'regional_note' => 'Pelaksanaan dan istilah bervariasi',
                'status' => 'draf',
                'is_published' => false,
                'sort_order' => 7,
            ],
            [
                'key' => 'mappenre_botting',
                'label_display' => 'Mappenre Botting',
                'alt_spellings' => ['mappabotting', 'mappanre botting'],
                'description_id' => 'Mengantar mempelai pria ke rumah mempelai wanita untuk akad nikah',
                'regional_note' => 'Padanan Makassar berbeda; jangan dicampur',
                'status' => 'draf',
                'is_published' => false,
                'sort_order' => 8,
            ],
            [
                'key' => 'madduppa_botting',
                'label_display' => 'Madduppa Botting',
                'alt_spellings' => [],
                'description_id' => 'Penyambutan kedatangan mempelai pria',
                'regional_note' => 'Perlu dikonfirmasi urutannya',
                'status' => 'draf',
                'is_published' => false,
                'sort_order' => 9,
            ],
            [
                'key' => 'akad_nikah',
                'label_display' => 'Akad Nikah',
                'alt_spellings' => ['ijab kabul', 'ripakawin'],
                'description_id' => 'Akad pernikahan secara syariat',
                'regional_note' => 'Istilah ripakawin perlu dikonfirmasi',
                'status' => 'draf',
                'is_published' => false,
                'sort_order' => 10,
            ],
            [
                'key' => 'mappasikarawa',
                'label_display' => 'Mappasikarawa',
                'alt_spellings' => [],
                'description_id' => 'Prosesi sentuhan pertama setelah akad nikah, diikuti sungkem kepada orang tua dan keluarga yang dituakan',
                'regional_note' => 'Detail prosesi perlu dikonfirmasi',
                'status' => 'draf',
                'is_published' => false,
                'sort_order' => 11,
            ],
            [
                'key' => 'tudang_botting',
                'label_display' => 'Tudang Botting',
                'alt_spellings' => [],
                'description_id' => 'Pesta atau resepsi pernikahan adat',
                'regional_note' => 'Pemakaian label ini bervariasi antar daerah',
                'status' => 'draf',
                'is_published' => false,
                'sort_order' => 12,
            ],
            [
                'key' => 'mapparola',
                'label_display' => 'Mapparola',
                'alt_spellings' => ['marola'],
                'description_id' => 'Kunjungan balasan mempelai wanita ke rumah mempelai pria; menurut sumber membawa sarung tenun sebagai hadiah',
                'regional_note' => 'Bervariasi antar daerah',
                'status' => 'draf',
                'is_published' => false,
                'sort_order' => 13,
            ],
            [
                'key' => 'massita_baiseng',
                'label_display' => 'Massita Baiseng',
                'alt_spellings' => [],
                'description_id' => 'Tahap pertemuan pasca resepsi',
                'regional_note' => 'Muncul dalam satu sumber; jangan dipakai sebelum dikonfirmasi',
                'status' => 'draf',
                'is_published' => false,
                'sort_order' => 14,
            ],
        ];

        foreach ($eventTypes as $item) {
            AdatEventType::updateOrCreate(
                ['key' => $item['key']],
                $item
            );
        }

        $glossaryEntries = [
            [
                'term' => 'Sulapa Eppa',
                'latin_reading' => 'Sulapa Eppa',
                'lontara_text' => 'ᨔᨘᨒᨄ ᨕᨙᨄ',
                'meaning_id' => '"Empat sisi"; bentuk belah ketupat yang dalam literatur dikaitkan dengan empat unsur (api, air, angin, tanah) dan ada yang menafsirkannya sebagai empat arah mata angin.',
                'regional_note' => 'Tafsir bervariasi antar sumber; jangan menyatakan satu tafsir sebagai satu-satunya.',
                'source_note' => 'Jurnal antropologi dan kajian budaya Sulawesi Selatan.',
                'status' => 'draf',
                'is_published' => false,
                'sort_order' => 1,
            ],
            [
                'term' => 'Walasuji',
                'latin_reading' => 'Walasuji',
                'lontara_text' => 'ᨓᨒᨔᨘᨍᨗ',
                'meaning_id' => 'Pagar atau pembatas anyaman bambu berbentuk belah ketupat yang dipakai dalam upacara atau pesta adat, dan dikaitkan dengan sulapa eppa.',
                'regional_note' => 'Penggunaan dan penafsiran bervariasi antar daerah.',
                'source_note' => 'Kajian sulapa eppa walasuji ISI Surakarta.',
                'status' => 'draf',
                'is_published' => false,
                'sort_order' => 2,
            ],
            [
                'term' => 'Sulapa Eppa na Taue',
                'latin_reading' => 'Sulapa Eppa na Taue',
                'lontara_text' => 'ᨔᨘᨒᨄ ᨕᨙᨄ ᨊ ᨈᨕᨘᨕᨙ',
                'meaning_id' => 'Pandangan yang menyamakan empat sisi dengan empat sifat manusia: acca (cerdas), warani (berani), lempu (jujur), sugi (kaya).',
                'regional_note' => 'Perlu konfirmasi dengan budayawan setempat.',
                'source_note' => 'Kajian nilai kearifan lokal Bugis.',
                'status' => 'draf',
                'is_published' => false,
                'sort_order' => 3,
            ],
            [
                'term' => 'Lontara',
                'latin_reading' => 'Lontara',
                'lontara_text' => 'ᨒᨚᨈᨑ',
                'meaning_id' => 'Aksara tradisional Bugis dan Makassar; menurut literatur, bentuk dasarnya dikaitkan dengan filosofi sulapa eppa.',
                'regional_note' => 'Pernyataan asal-usul ditandai menurut literatur.',
                'source_note' => 'Dokumentasi aksara Unicode Buginese block.',
                'status' => 'draf',
                'is_published' => false,
                'sort_order' => 4,
            ],
            [
                'term' => "Lipa' Sabbe",
                'latin_reading' => "Lipa' Sabbe",
                'lontara_text' => 'ᨒᨗᨄ ᨔᨅᨙ',
                'meaning_id' => 'Sarung sutra khas Bugis; corak kotak sering dikaitkan dengan sulapa eppa.',
                'regional_note' => 'Corak dan ragam tenun bervariasi di Sengkang/Wajo dan Soppeng.',
                'source_note' => 'Dokumentasi tenun sutra Bugis.',
                'status' => 'draf',
                'is_published' => false,
                'sort_order' => 5,
            ],
            [
                'term' => 'Waju Tokko',
                'latin_reading' => 'Waju Tokko',
                'lontara_text' => 'ᨓᨍᨘ ᨈᨚᨀᨚ',
                'meaning_id' => 'Busana perempuan adat yang dikenal juga sebagai baju bodo; penamaan berbeda antar sumber dan daerah.',
                'regional_note' => 'Jangan mencampur sebutan tanpa konfirmasi.',
                'source_note' => 'Kajian busana adat UNM.',
                'status' => 'draf',
                'is_published' => false,
                'sort_order' => 6,
            ],
            [
                'term' => "Jas Tutu'",
                'latin_reading' => "Jas Tutu'",
                'lontara_text' => 'ᨍ ᨈᨘᨈᨘ',
                'meaning_id' => 'Jas tertutup untuk pria, dipadukan dengan lipa\' sabbe dan songkok recca.',
                'regional_note' => 'Kelengkapan dan ornamen kancing bervariasi.',
                'source_note' => 'Dokumentasi busana adat pria Bugis.',
                'status' => 'draf',
                'is_published' => false,
                'sort_order' => 7,
            ],
            [
                'term' => "Songkok Recca / Pa'biring",
                'latin_reading' => "Songkok Recca / Pa'biring",
                'lontara_text' => 'ᨔᨚᨀᨚ ᨑᨙᨌ',
                'meaning_id' => 'Penutup kepala dari serat pelepah lontar yang dapat dihiasi benang emas.',
                'regional_note' => 'Berasal dari tradisi Bone; nama dan riwayat perlu dikonfirmasi.',
                'source_note' => 'Kajian warisan budaya takbenda Bone.',
                'status' => 'draf',
                'is_published' => false,
                'sort_order' => 8,
            ],
            [
                'term' => "Baju Bella Dada dan Passapu'",
                'latin_reading' => "Baju Bella Dada dan Passapu'",
                'lontara_text' => 'ᨅᨍᨘ ᨅᨙᨒ ᨉᨉ',
                'meaning_id' => 'Busana pria tradisional dengan penutup kepala passapu\'.',
                'regional_note' => 'Asal (Bugis atau Makassar) berbeda antar sumber; tidak dijadikan default.',
                'source_note' => 'Kajian literatur busana adat Sulsel.',
                'status' => 'draf',
                'is_published' => false,
                'sort_order' => 9,
            ],
            [
                'term' => "Indo' Botting",
                'latin_reading' => "Indo' Botting",
                'lontara_text' => 'ᨕᨗᨉᨚ ᨅᨚᨈᨗ',
                'meaning_id' => 'Pendamping atau inang pengantin yang membimbing jalannya tata rias dan prosesi adat.',
                'regional_note' => 'Peran dan tugas bervariasi.',
                'source_note' => 'Literatur pernikahan adat Bugis.',
                'status' => 'draf',
                'is_published' => false,
                'sort_order' => 10,
            ],
            [
                'term' => 'Erang-erang',
                'latin_reading' => 'Erang-erang',
                'lontara_text' => 'ᨕᨙᨑ-ᨕᨙᨑ',
                'meaning_id' => 'Seserahan atau bawaan yang dibawa dalam iring-iringan mempelai pria.',
                'regional_note' => 'Jumlah dan isi disepakati dalam mappettu ada.',
                'source_note' => 'Literatur pernikahan adat Bugis.',
                'status' => 'draf',
                'is_published' => false,
                'sort_order' => 11,
            ],
            [
                'term' => 'Pacci',
                'latin_reading' => 'Pacci',
                'lontara_text' => 'ᨄᨌᨗ',
                'meaning_id' => 'Daun pacar (inai) yang dipakai dalam prosesi mappacci sebagai simbol penyucian diri dan doa restu.',
                'regional_note' => 'Kerap dikaitkan dengan makna kesucian (paccing).',
                'source_note' => 'Kajian ritual mappacci.',
                'status' => 'draf',
                'is_published' => false,
                'sort_order' => 12,
            ],
        ];

        foreach ($glossaryEntries as $entry) {
            AdatGlossaryEntry::updateOrCreate(
                ['term' => $entry['term']],
                $entry
            );
        }
    }
}
