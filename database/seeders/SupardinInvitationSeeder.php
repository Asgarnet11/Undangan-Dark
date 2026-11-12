<?php

namespace Database\Seeders;

use App\Models\Invitation;
use App\Models\User;
use App\Support\ThemeRegistry;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupardinInvitationSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $user = User::query()->firstOrCreate(
            ['email' => 'admin@undangan.test'],
            [
                'name' => 'Admin Undangan',
                'password' => bcrypt('password'),
            ]
        );

        $existing = Invitation::query()->where('slug', 'supardin')->first();

        $invitation = Invitation::query()->updateOrCreate(
            ['slug' => 'supardin'],
            [
                'user_id' => $existing?->user_id ?? $user->id,
                'theme' => $existing?->theme ?? 'dark-boho',
                'theme_config' => $existing?->theme_config ?? ThemeRegistry::defaultConfig('dark-boho'),
                'timezone' => 'Asia/Makassar',
                'title' => 'Pernikahan Aulia & Supardin',
                'bride_name' => 'Aulia Rahmawati, S.Pd.',
                'bride_nickname' => 'Aulia',
                'bride_father' => 'Bapak H. Muhammad Yusuf, S.Pd.',
                'bride_mother' => 'Ibu Hj. Hasnah, S.Ag.',
                'bride_order_label' => 'Putri kedua',
                'bride_origin' => 'Andoolo, Konawe Selatan',
                'bride_instagram' => 'auliarahma_',
                'groom_name' => 'Supardin Kusmar, S.Kom.',
                'groom_nickname' => 'Supardin',
                'groom_father' => 'Bapak Kusnadi, S.Sos.',
                'groom_mother' => 'Ibu Endang Lestari',
                'groom_order_label' => 'Putra pertama',
                'groom_origin' => 'Wadonggo, Konawe Selatan',
                'groom_instagram' => 'Supardin_',
                'opening_text' => 'Dengan memohon rahmat dan ridho Allah SWT, kami bermaksud mengundang Bapak/Ibu/Saudara/i untuk menghadiri serta memberikan doa restu pada acara pernikahan putra-putri kami.',
                'verse_arabic' => 'وَمِنْ آيَاتِهِ أَنْ خَلَقَ لَكُم مِّنْ أَنفُسِكُمْ أَزْوَاجًا لِّتَسْكُنُوا إِلَيْهَا وَجَعَلَ بَيْنَكُم مَّوَدَّةً وَرَحْمَةً ۚ إِنَّ فِي ذَٰلِكَ لَآيَاتٍ لِّقَوْمٍ يَتَفَكَّرُونَ',
                'verse_translation' => 'Dan di antara tanda-tanda (kebesaran)-Nya ialah Dia menciptakan pasangan-pasangan untukmu dari jenismu sendiri, agar kamu cenderung dan merasa tenteram kepadanya, dan Dia menjadikan di antaramu rasa kasih dan sayang. Sungguh, pada yang demikian itu benar-benar terdapat tanda-tanda (kebesaran Allah) bagi kaum yang berpikir.',
                'verse_source' => 'QS. Ar-Rum: 21',
                'music_title' => 'Melodi Kasih - Musik Pilihan',
                'gallery_title' => 'Galeri Momen',
                'gallery_quote' => 'Dua langkah yang dipertemukan oleh doa hingga bermuara pada satu ikatan suci.',
                'livestream_title' => 'Siaran Langsung Akad Nikah',
                'livestream_note' => 'Bagi keluarga dan sahabat yang berhalangan hadir langsung di Konawe Selatan, prosesi akad nikah dapat disaksikan secara daring.',
                'closing_text' => 'Merupakan sebuah kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir dan memberikan doa restu. Atas perhatian dan kehadirannya, kami ucapkan terima kasih.',
                'thank_you_gift_note' => 'Doa restu Bapak/Ibu/Saudara/i merupakan karunia terindah bagi kami. Namun apabila berkenan menyampaikan tanda kasih, dapat disalurkan melalui rekening maupun alamat berikut.',
                'is_published' => true,
            ]
        );

        $eventDate = now('Asia/Makassar')->addDays(35);

        $invitation->events()->delete();
        $invitation->events()->createMany([
            [
                'title' => 'Akad Nikah',
                'starts_at' => $eventDate->copy()->setTime(9, 0)->utc(),
                'ends_at' => $eventDate->copy()->setTime(10, 30)->utc(),
                'venue_name' => 'Masjid Agung Andoolo',
                'address' => 'Jl. Poros Andoolo, Kel. Potoro, Kec. Andoolo, Kab. Konawe Selatan, Sulawesi Tenggara 93819',
                'maps_query' => 'Masjid Agung Andoolo, Konawe Selatan, Sulawesi Tenggara',
                'sort_order' => 1,
            ],
            [
                'title' => 'Resepsi Pernikahan',
                'starts_at' => $eventDate->copy()->setTime(11, 30)->utc(),
                'ends_at' => $eventDate->copy()->setTime(15, 0)->utc(),
                'venue_name' => 'Gedung Islamic Center Andoolo',
                'address' => 'Jl. Poros Andoolo, Kec. Andoolo, Kab. Konawe Selatan, Sulawesi Tenggara 93819',
                'maps_query' => 'Andoolo, Konawe Selatan, Sulawesi Tenggara',
                'sort_order' => 2,
            ],
        ]);

        $invitation->giftAccounts()->delete();
        $invitation->giftAccounts()->createMany([
            [
                'bank_name' => 'Bank BRI',
                'account_number' => '492001028374531',
                'account_name' => 'Aulia Rahmawati',
                'sort_order' => 1,
            ],
            [
                'bank_name' => 'Bank Mandiri',
                'account_number' => '1620004829173',
                'account_name' => 'Supardin Kusmar',
                'sort_order' => 2,
            ],
        ]);

        $invitation->giftAddresses()->delete();
        $invitation->giftAddresses()->create([
            'label' => 'Kediaman Mempelai Pria',
            'address' => 'Desa Wadonggo, Kec. Tinanggea, Kab. Konawe Selatan, Sulawesi Tenggara 93884',
            'sort_order' => 1,
        ]);

        $invitation->coInviters()->delete();
        $invitation->coInviters()->createMany([
            ['name' => 'Keluarga Besar Bapak Kusnadi, S.Sos. (Wadonggo, Konawe Selatan)', 'sort_order' => 1],
            ['name' => 'Keluarga Besar Bapak H. Muhammad Yusuf, S.Pd. (Andoolo, Konawe Selatan)', 'sort_order' => 2],
        ]);

        $invitation->galleryPhotos()->delete();
        $invitation->galleryPhotos()->createMany([
            ['path' => 'demo-gallery/momen-1.webp', 'caption' => 'Momen Lamaran di Konawe Selatan', 'width' => 720, 'height' => 960, 'sort_order' => 1],
            ['path' => 'demo-gallery/momen-2.webp', 'caption' => 'Senja di Pesisir Kendari', 'width' => 720, 'height' => 960, 'sort_order' => 2],
            ['path' => 'demo-gallery/momen-3.webp', 'caption' => 'Potret Pra-Nikah Pertama', 'width' => 720, 'height' => 960, 'sort_order' => 3],
            ['path' => 'demo-gallery/momen-4.webp', 'caption' => 'Menuju Hari Bahagia', 'width' => 720, 'height' => 960, 'sort_order' => 4],
        ]);

        $guests = [
            ['name' => 'Bapak Hendra Wijaya & Keluarga', 'code' => 'KEL01', 'group' => 'Keluarga', 'max_pax' => 4],
            ['name' => 'Rizky Pratama, S.Kom.', 'code' => 'SHB01', 'group' => 'Sahabat', 'max_pax' => 2],
            ['name' => 'Siti Nurhaliza', 'code' => 'KRJ01', 'group' => 'Rekan Kerja', 'max_pax' => 2],
        ];
        foreach ($guests as $guestData) {
            $invitation->guests()->updateOrCreate(
                ['code' => $guestData['code']],
                $guestData
            );
        }

        $invitation->wishes()->delete();
        $invitation->wishes()->createMany([
            [
                'name' => 'Rizky Pratama',
                'origin' => 'Kendari',
                'message' => 'Selamat berbahagia untuk Supardin dan Aulia. Semoga menjadi keluarga sakinah, mawaddah, warahmah.',
                'is_visible' => true,
            ],
            [
                'name' => 'Siti Nurhaliza',
                'origin' => 'Konawe Selatan',
                'message' => 'Barakallahu lakuma wa baraka alaikuma. Lancar sampai hari akad dan resepsi.',
                'is_visible' => true,
            ],
        ]);
    }
}
