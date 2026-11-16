<?php

namespace Database\Seeders;

use App\Models\Invitation;
use App\Models\User;
use App\Support\ThemeRegistry;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SupardinEkaSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->firstOrCreate(
            ['email' => 'asgar@undangan.test'],
            [
                'name' => 'Admin Undangan',
                'password' => bcrypt('password'),
            ]
        );

        $invitation = Invitation::query()->updateOrCreate(
            ['slug' => 'supardin-eka'],
            [
                'user_id' => $user->id,
                'theme' => 'sunny-meadow',
                'theme_config' => ThemeRegistry::defaultConfig('sunny-meadow'),
                'timezone' => 'Asia/Makassar',
                'title' => 'Pernikahan Supardin & Eka',
                'bride_name' => 'Eka Rahmawati',
                'bride_nickname' => 'Eka',
                'bride_father' => 'Bapak Junaedi',
                'bride_mother' => 'Ibu Atikah',
                'bride_order_label' => 'Putri pertama',
                'bride_origin' => 'Konawe Selatan',
                'bride_instagram' => '_r4hma.14',
                'groom_name' => 'Supardin Kusmar, S.Kom.',
                'groom_nickname' => 'Supardin',
                'groom_father' => 'Bapak Kusnadi, S.Pd.',
                'groom_mother' => 'Ibu Sumarni',
                'groom_order_label' => 'Putra pertama',
                'groom_origin' => 'Desa Wadonggo, Kec. Tinanggea, Kab. Konawe Selatan',
                'groom_instagram' => 'isupar',
                'opening_text' => 'Dengan memohon rahmat dan ridho Allah SWT, kami bermaksud mengundang Bapak/Ibu/Saudara/i untuk menghadiri dan memberikan doa restu pada acara pernikahan kami.',
                'verse_arabic' => 'وَمِنْ آيَاتِهِ أَنْ خَلَقَ لَكُم مِّنْ أَنفُسِكُمْ أَزْوَاجًا لِّتَسْكُنُوا إِلَيْهَا وَجَعَلَ بَيْنَكُم مَّوَدَّةً وَرَحْمَةً ۚ إِنَّ فِي ذَٰلِكَ لَآيَاتٍ لِّقَوْمٍ يَتَفَكَّرُونَ',
                'verse_translation' => 'Dan di antara tanda-tanda (kebesaran)-Nya ialah Dia menciptakan pasangan-pasangan untukmu dari jenismu sendiri, agar kamu cenderung dan merasa tenteram kepadanya, dan Dia menjadikan di antaramu rasa kasih dan sayang. Sungguh, pada yang demikian itu benar-benar terdapat tanda-tanda (kebesaran Allah) bagi kaum yang berpikir.',
                'verse_source' => 'QS. Ar-Rum: 21',
                'closing_text' => 'Merupakan sebuah kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir dan memberikan doa restu. Atas kehadiran dan doa restunya, kami ucapkan terima kasih.',
                'thank_you_gift_note' => 'Doa restu Bapak/Ibu/Saudara/i merupakan hadiah terindah bagi kami. Namun apabila berkenan menyampaikan tanda kasih, dapat disalurkan melalui rekening berikut.',
                'is_published' => true,
            ]
        );

        // Acara Mempelai Pria: Minggu, 29 November 2026 jam 18:00 WITA - selesai
        $eventDate = Carbon::create(2026, 11, 29, 18, 0, 0, 'Asia/Makassar')->setTimezone('UTC');

        $invitation->events()->delete();
        $invitation->events()->create([
            'title' => 'Acara Pernikahan (Mempelai Pria)',
            'starts_at' => $eventDate,
            'ends_at' => null,
            'venue_name' => 'Kediaman Mempelai Pria',
            'address' => 'Desa Wadonggo, Kec. Tinanggea, Kab. Konawe Selatan, Sulawesi Tenggara',
            'maps_query' => 'Desa Wadonggo, Tinanggea, Konawe Selatan, Sulawesi Tenggara',
            'sort_order' => 1,
        ]);

        // Rekening Bank BRI
        $invitation->giftAccounts()->delete();
        $invitation->giftAccounts()->create([
            'bank_name' => 'Bank BRI',
            'account_number' => '492901030159500',
            'account_name' => 'Supardin Kusmar',
            'sort_order' => 1,
        ]);

        // Tamu awal untuk memudahkan pengujian link personal undangan
        $guests = [
            ['name' => 'Tamu Undangan', 'code' => 'SE-TAMU01', 'group' => 'Umum', 'max_pax' => 2],
            ['name' => 'Keluarga Besar', 'code' => 'SE-KEL01', 'group' => 'Keluarga', 'max_pax' => 4],
            ['name' => 'Sahabat & Rekan', 'code' => 'SE-SHB01', 'group' => 'Sahabat', 'max_pax' => 2],
        ];

        foreach ($guests as $guestData) {
            $invitation->guests()->updateOrCreate(
                ['code' => $guestData['code']],
                $guestData
            );
        }
    }
}
