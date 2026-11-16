<?php

namespace Database\Seeders;

use App\Models\Invitation;
use App\Models\User;
use App\Support\ThemeRegistry;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $user = User::query()->firstOrCreate(
            ['email' => 'asgar@undangan.test'],
            [
                'name' => 'Admin Undangan',
                'password' => bcrypt('password'),
            ]
        );

        User::query()->firstOrCreate(
            ['email' => 'admin@undangan.test'],
            [
                'name' => 'Admin Undangan',
                'password' => bcrypt('password'),
            ]
        );

        $invitation = Invitation::query()->updateOrCreate(
            ['slug' => 'raka-nadia'],
            [
                'user_id' => $user->id,
                'theme' => 'dark-boho',
                'theme_config' => ThemeRegistry::defaultConfig('dark-boho'),
                'timezone' => 'Asia/Jakarta',
                'title' => 'Pernikahan Nadia & Raka',
                'bride_name' => 'Nadia Putri Pramesti, S.Ars.',
                'bride_nickname' => 'Nadia',
                'bride_father' => 'Bapak Ir. Hendra Kusuma',
                'bride_mother' => 'Ibu Ratna Sari Dewi',
                'bride_order_label' => 'Putri pertama',
                'bride_origin' => 'Sleman, Yogyakarta',
                'bride_instagram' => 'nadiapramesti',
                'groom_name' => 'Raka Aditya Pratama, S.T.',
                'groom_nickname' => 'Raka',
                'groom_father' => 'Bapak Drs. Bambang Wijaya',
                'groom_mother' => 'Ibu Endang Lestari',
                'groom_order_label' => 'Putra kedua',
                'groom_origin' => 'Bantul, Yogyakarta',
                'groom_instagram' => 'rakaaditya',
                'opening_text' => 'Dengan memohon rahmat dan ridho Allah SWT, kami mengundang Bapak/Ibu/Saudara/i untuk menghadiri dan memberikan doa restu pada hari bahagia pernikahan putra-putri kami.',
                'verse_arabic' => 'وَمِنْ آيَاتِهِ أَنْ خَلَقَ لَكُم مِّنْ أَنفُسِكُمْ أَزْوَاجًا لِّتَسْكُنُوا إِلَيْهَا وَجَعَلَ بَيْنَكُم مَّوَدَّةً وَرَحْمَةً ۚ إِنَّ فِي ذَٰلِكَ لَآيَاتٍ لِّقَوْمٍ يَتَفَكَّرُونَ',
                'verse_translation' => 'Dan di antara tanda-tanda (kebesaran)-Nya ialah Dia menciptakan pasangan-pasangan untukmu dari jenismu sendiri, agar kamu cenderung dan merasa tenteram kepadanya, dan Dia menjadikan di antaramu rasa kasih dan sayang. Sungguh, pada yang demikian itu benar-benar terdapat tanda-tanda (kebesaran Allah) bagi kaum yang berpikir.',
                'verse_source' => 'QS. Ar-Rum: 21',
                'music_title' => 'Melodi Senja Akustik',
                'gallery_title' => 'Galeri Momen',
                'gallery_quote' => 'Langkah kecil yang berjalan searah hingga berujung pada satu janji sehidup semati.',
                'livestream_title' => 'Siaran Langsung Akad Nikah',
                'livestream_note' => 'Bagi tamu yang berhalangan hadir secara langsung, prosesi akad nikah dapat disaksikan melalui siaran daring.',
                'closing_text' => 'Merupakan sebuah kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir dan memberikan doa restu. Atas perhatian dan kehadirannya, kami ucapkan terima kasih.',
                'thank_you_gift_note' => 'Doa restu Bapak/Ibu/Saudara/i merupakan hadiah terindah bagi kami. Namun apabila berkenan menyampaikan tanda kasih, dapat disalurkan melalui rekening maupun alamat berikut.',
                'is_published' => true,
            ]
        );

        $invitation->events()->delete();
        $invitation->events()->createMany([
            [
                'title' => 'Akad Nikah',
                'starts_at' => now()->addDays(42)->setTime(8, 30),
                'ends_at' => now()->addDays(42)->setTime(10, 0),
                'venue_name' => 'Pendopo Taman Sari Heritage',
                'address' => 'Jl. Palagan Tentara Pelajar Km. 8, Sleman, Daerah Istimewa Yogyakarta 55581',
                'maps_query' => 'Pendopo Taman Sari Heritage, Jl. Palagan Tentara Pelajar Km. 8, Sleman, Yogyakarta',
                'sort_order' => 1,
            ],
            [
                'title' => 'Resepsi Pernikahan',
                'starts_at' => now()->addDays(42)->setTime(11, 30),
                'ends_at' => now()->addDays(42)->setTime(14, 30),
                'venue_name' => 'Pendopo Taman Sari Heritage',
                'address' => 'Jl. Palagan Tentara Pelajar Km. 8, Sleman, Daerah Istimewa Yogyakarta 55581',
                'maps_query' => 'Pendopo Taman Sari Heritage, Jl. Palagan Tentara Pelajar Km. 8, Sleman, Yogyakarta',
                'sort_order' => 2,
            ],
        ]);

        $invitation->giftAccounts()->delete();
        $invitation->giftAccounts()->createMany([
            [
                'bank_name' => 'Bank BCA',
                'account_number' => '8465021940',
                'account_name' => 'Nadia Putri Pramesti',
                'sort_order' => 1,
            ],
            [
                'bank_name' => 'Bank Mandiri',
                'account_number' => '1370019284750',
                'account_name' => 'Raka Aditya Pratama',
                'sort_order' => 2,
            ],
        ]);

        $invitation->giftAddresses()->delete();
        $invitation->giftAddresses()->create([
            'label' => 'Kediaman Mempelai Wanita',
            'address' => 'Perumahan Griya Asri Palagan Blok B-12, Sleman, Daerah Istimewa Yogyakarta 55581',
            'sort_order' => 1,
        ]);

        $invitation->coInviters()->delete();
        $invitation->coInviters()->createMany([
            ['name' => 'Keluarga Besar Bapak H. Sutrisno (Yogyakarta)', 'sort_order' => 1],
            ['name' => 'Keluarga Besar Ibu Hj. Kartini (Surakarta)', 'sort_order' => 2],
        ]);

        $invitation->galleryPhotos()->delete();
        $invitation->galleryPhotos()->createMany([
            ['path' => 'demo-gallery/momen-1.webp', 'caption' => 'Pertunangan di Yogyakarta', 'width' => 720, 'height' => 960, 'sort_order' => 1],
            ['path' => 'demo-gallery/momen-2.webp', 'caption' => 'Senja di Taman Sari', 'width' => 720, 'height' => 960, 'sort_order' => 2],
            ['path' => 'demo-gallery/momen-3.webp', 'caption' => 'Potret Pra-Nikah Pertama', 'width' => 720, 'height' => 960, 'sort_order' => 3],
            ['path' => 'demo-gallery/momen-4.webp', 'caption' => 'Menuju Hari Bahagia', 'width' => 720, 'height' => 960, 'sort_order' => 4],
        ]);

        $guests = [
            ['name' => 'Bapak Agus Salim & Keluarga', 'code' => 'KEL01', 'group' => 'Keluarga', 'max_pax' => 4],
            ['name' => 'Ibu Kartika Sari', 'code' => 'SHB01', 'group' => 'Sahabat', 'max_pax' => 2],
            ['name' => 'Dimas Pratama', 'code' => 'KRJ01', 'group' => 'Rekan Kerja', 'max_pax' => 2],
        ];
        foreach ($guests as $guestData) {
            $invitation->guests()->updateOrCreate(
                ['code' => $guestData['code']],
                $guestData
            );
        }

        if ($invitation->wishes()->count() === 0) {
            $invitation->wishes()->createMany([
                [
                    'name' => 'Kartika Sari',
                    'origin' => 'Yogyakarta',
                    'message' => 'Selamat menempuh hidup baru untuk Nadia dan Raka. Semoga senantiasa dilimpahi ketenteraman, kasih sayang, dan keberkahan.',
                    'is_visible' => true,
                ],
                [
                    'name' => 'Dimas Pratama',
                    'origin' => 'Bandung',
                    'message' => 'Turut berbahagia atas pernikahan kalian berdua. Semoga menjadi keluarga yang harmonis dan langgeng hingga menua bersama.',
                    'is_visible' => true,
                ],
            ]);
        }

        $this->call(SupardinEkaSeeder::class);
    }
}
