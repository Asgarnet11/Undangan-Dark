<?php

namespace Database\Seeders;

use App\Models\Invitation;
use App\Models\User;
use App\Support\ThemeRegistry;
use Carbon\Carbon;
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

        $existing = Invitation::query()
            ->where('slug', 'alfian-yuli')
            ->orWhere('slug', 'supardin')
            ->first();

        $invitation = $existing ?? new Invitation;
        $invitation->fill([
            'user_id' => $existing?->user_id ?? $user->id,
            'slug' => 'alfian-yuli',
            'theme' => $existing?->theme ?? 'dark-boho',
            'theme_config' => $existing?->theme_config ?? ThemeRegistry::defaultConfig('dark-boho'),
            'timezone' => 'Asia/Makassar',
            'title' => 'Pernikahan Yuli & Alfian',
            'bride_name' => 'Yuli Witianingsih',
            'bride_nickname' => 'Yuli',
            'bride_father' => 'Bapak Sulanto',
            'bride_mother' => 'Ibu Sumiati Ningsih',
            'bride_order_label' => 'Putri pertama',
            'bride_origin' => 'Desa Pa\'awaru, Morowali Utara',
            'bride_instagram' => 'Ylii_wty',
            'groom_name' => 'Alfian Rezky Saputra',
            'groom_nickname' => 'Alfian',
            'groom_father' => 'Bapak Sudirman Arsyad',
            'groom_mother' => 'Ibu Sukmawati',
            'groom_order_label' => 'Putra pertama',
            'groom_origin' => 'Morowali Utara, Sulawesi Tengah',
            'groom_instagram' => 'alipinrzky_',
            'opening_text' => 'Dengan memohon rahmat dan ridho Allah SWT, kami bermaksud mengundang Bapak/Ibu/Saudara/i untuk menghadiri serta memberikan doa restu pada acara pernikahan putra-putri kami.',
            'verse_arabic' => 'وَمِنْ آيَاتِهِ أَنْ خَلَقَ لَكُم مِّنْ أَنفُسِكُمْ أَزْوَاجًا لِّتَسْكُنُوا إِلَيْهَا وَجَعَلَ بَيْنَكُم مَّوَدَّةً وَرَحْمَةً ۚ إِنَّ فِي ذَٰلِكَ لَآيَاتٍ لِّقَوْمٍ يَتَفَكَّرُونَ',
            'verse_translation' => 'Dan di antara tanda-tanda (kebesaran)-Nya ialah Dia menciptakan pasangan-pasangan untukmu dari jenismu sendiri, agar kamu cenderung dan merasa tenteram kepadanya, dan Dia menjadikan di antaramu rasa kasih dan sayang. Sungguh, pada yang demikian itu benar-benar terdapat tanda-tanda (kebesaran Allah) bagi kaum yang berpikir.',
            'verse_source' => 'QS. Ar-Rum: 21',
            'music_title' => 'Melodi Kasih - Musik Pilihan',
            'gallery_title' => 'Galeri Momen',
            'gallery_quote' => 'Dua insan yang dipersatukan dalam ikatan suci pernikahan menuju ridho Allah SWT.',
            'livestream_title' => 'Siaran Langsung Akad Nikah',
            'livestream_note' => 'Bagi keluarga dan kerabat yang berhalangan hadir langsung di Morowali Utara, prosesi akad nikah dapat disaksikan secara daring.',
            'closing_text' => 'Merupakan sebuah kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir dan memberikan doa restu. Atas perhatian dan kehadirannya, kami ucapkan terima kasih.',
            'thank_you_gift_note' => 'Doa restu Bapak/Ibu/Saudara/i merupakan karunia terindah bagi kami. Namun apabila berkenan menyampaikan tanda kasih, dapat disalurkan melalui rekening berikut.',
            'is_published' => true,
        ]);
        $invitation->save();

        $akadStart = Carbon::create(2026, 12, 16, 10, 0, 0, 'Asia/Makassar')->utc();
        $resepsiStart = Carbon::create(2026, 12, 16, 11, 30, 0, 'Asia/Makassar')->utc();

        $invitation->events()->delete();
        $invitation->events()->createMany([
            [
                'title' => 'Akad Nikah',
                'starts_at' => $akadStart,
                'ends_at' => null,
                'venue_name' => 'Kediaman Mempelai Wanita (Desa Pa\'awaru)',
                'address' => 'Desa Pa\'awaru, Kec. Lembo Raya, Kab. Morowali Utara, Sulawesi Tengah',
                'maps_query' => 'Desa Pa\'awaru, Lembo Raya, Morowali Utara, Sulawesi Tengah',
                'sort_order' => 1,
            ],
            [
                'title' => 'Resepsi Pernikahan',
                'starts_at' => $resepsiStart,
                'ends_at' => null,
                'venue_name' => 'Kediaman Mempelai Wanita (Desa Pa\'awaru)',
                'address' => 'Desa Pa\'awaru, Kec. Lembo Raya, Kab. Morowali Utara, Sulawesi Tengah',
                'maps_query' => 'Desa Pa\'awaru, Lembo Raya, Morowali Utara, Sulawesi Tengah',
                'sort_order' => 2,
            ],
        ]);

        $invitation->giftAccounts()->delete();
        $invitation->giftAccounts()->create([
            'bank_name' => 'Bank BSI',
            'account_number' => '7341307788',
            'account_name' => 'Yuli Witianingsih',
            'sort_order' => 1,
        ]);

        $invitation->giftAddresses()->delete();
        $invitation->giftAddresses()->create([
            'label' => 'Kediaman Mempelai Wanita',
            'address' => 'Desa Pa\'awaru, Kec. Lembo Raya, Kab. Morowali Utara, Sulawesi Tengah',
            'sort_order' => 1,
        ]);

        $invitation->coInviters()->delete();
        $invitation->coInviters()->createMany([
            ['name' => 'Keluarga Besar Bapak Sulanto & Ibu Sumiati Ningsih (Morowali Utara)', 'sort_order' => 1],
            ['name' => 'Keluarga Besar Bapak Sudirman Arsyad & Ibu Sukmawati', 'sort_order' => 2],
        ]);

        $invitation->galleryPhotos()->delete();
        $invitation->galleryPhotos()->createMany([
            ['path' => 'demo-gallery/momen-1.webp', 'caption' => 'Momen Lamaran di Morowali Utara', 'width' => 720, 'height' => 960, 'sort_order' => 1],
            ['path' => 'demo-gallery/momen-2.webp', 'caption' => 'Senja Bahagia di Pa\'awaru', 'width' => 720, 'height' => 960, 'sort_order' => 2],
            ['path' => 'demo-gallery/momen-3.webp', 'caption' => 'Potret Pra-Nikah Pertama', 'width' => 720, 'height' => 960, 'sort_order' => 3],
            ['path' => 'demo-gallery/momen-4.webp', 'caption' => 'Menuju Hari Bahagia', 'width' => 720, 'height' => 960, 'sort_order' => 4],
        ]);

        $guests = [
            ['name' => 'Bapak Kepala Desa Pa\'awaru & Keluarga', 'code' => 'KEL01', 'group' => 'Keluarga', 'max_pax' => 4],
            ['name' => 'Sahabat & Rekan', 'code' => 'SHB01', 'group' => 'Sahabat', 'max_pax' => 2],
            ['name' => 'Rekan Kerja Morowali Utara', 'code' => 'KRJ01', 'group' => 'Rekan Kerja', 'max_pax' => 2],
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
                'name' => 'Wahyu Saputra',
                'origin' => 'Morowali Utara',
                'message' => 'Selamat berbahagia untuk Yuli dan Alfian. Semoga menjadi keluarga sakinah, mawaddah, warahmah.',
                'is_visible' => true,
            ],
            [
                'name' => 'Nurul Aisyah',
                'origin' => 'Sulawesi Tengah',
                'message' => 'Barakallahu lakuma wa baraka alaikuma wa jama\'a bainakuma fii khair. Lancar sampai hari H.',
                'is_visible' => true,
            ],
        ]);
    }
}
