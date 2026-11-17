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
                'theme' => 'dark-boho',
                'theme_config' => ThemeRegistry::defaultConfig('dark-boho'),
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
            'maps_query' => 'https://www.google.com/maps?q=-4.4725533,122.2729199&z=17&hl=id',
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

        // Daftar tamu undangan Supardin & Eka
        $guests = [
            ['name' => 'Tamu Undangan', 'code' => 'SE-TAMU01', 'group' => 'Umum', 'max_pax' => 2],
            ['name' => 'Jetriawan & Partner', 'code' => 'SE-JET01', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
            ['name' => 'Andi & Partner', 'code' => 'SE-AND01', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
            ['name' => 'Bima Sakti & Partner', 'code' => 'SE-BIM01', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
            ['name' => 'Endriyano & Partner', 'code' => 'SE-END01', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
            ['name' => 'Amar & Partner', 'code' => 'SE-AMR01', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
            ['name' => 'Amir & Partner', 'code' => 'SE-AMI01', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
            ['name' => 'Muh. Syahrul & Partner', 'code' => 'SE-SYA01', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
            ['name' => 'Rifki & Partner', 'code' => 'SE-RIF01', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
            ['name' => 'Eka & Partner', 'code' => 'SE-EKA01', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
            ['name' => 'Eko & Partner', 'code' => 'SE-EKO01', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
            ['name' => 'Arul & Partner', 'code' => 'SE-ARU01', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
            ['name' => 'Hasrul & Partner', 'code' => 'SE-HAS01', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
            ['name' => 'Aris Amril & Partner', 'code' => 'SE-ARI01', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
            ['name' => 'Muh. Aksan, S.Pi. dan Ibu', 'code' => 'SE-AKS01', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
            ['name' => 'Haikal & Partner', 'code' => 'SE-HAI01', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
            ['name' => 'Rian & Partner', 'code' => 'SE-RIA01', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
            ['name' => 'Ahmad Nuriangsyah & Partner', 'code' => 'SE-NUR01', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
            ['name' => 'Rendi dan Ibu', 'code' => 'SE-REN01', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
            ['name' => 'Filla Aferial & Partner', 'code' => 'SE-FIL01', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
            ['name' => 'Novaldiansyah, S.T. & Partner', 'code' => 'SE-NOV01', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
            ['name' => 'Asnur & Partner', 'code' => 'SE-ASN01', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
            ['name' => 'Aksan dan Ibu', 'code' => 'SE-AKS02', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
            ['name' => 'Darmawan & Partner', 'code' => 'SE-DAR01', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
            ['name' => 'Fitrianingsih & Partner', 'code' => 'SE-FIT01', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
            ['name' => 'Sapriawan & Partner', 'code' => 'SE-SAP01', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
            ['name' => 'Ariani, S.Pi. & Partner', 'code' => 'SE-ARN01', 'group' => 'Rekan & Kolega', 'max_pax' => 2],
            ['name' => 'Risdayanti, S.AP. & Partner', 'code' => 'SE-RIS01', 'group' => 'Rekan & Kolega', 'max_pax' => 2],
            ['name' => 'Deni Sulistian, S.AP. & Partner', 'code' => 'SE-DEN01', 'group' => 'Rekan & Kolega', 'max_pax' => 2],
            ['name' => 'Serly, S.H. & Partner', 'code' => 'SE-SER01', 'group' => 'Rekan & Kolega', 'max_pax' => 2],
            ['name' => 'Astrid Mustavani & Partner', 'code' => 'SE-AST01', 'group' => 'Rekan & Kolega', 'max_pax' => 2],
            ['name' => 'Suci Handayani Farma & Partner', 'code' => 'SE-SUC01', 'group' => 'Rekan & Kolega', 'max_pax' => 2],
            ['name' => 'Septiana & Partner', 'code' => 'SE-SEP01', 'group' => 'Rekan & Kolega', 'max_pax' => 2],
            ['name' => 'Elsa Asrilia, S.E. & Partner', 'code' => 'SE-ELS01', 'group' => 'Rekan & Kolega', 'max_pax' => 2],
            ['name' => 'Noviana, S.E. & Partner', 'code' => 'SE-NOV02', 'group' => 'Rekan & Kolega', 'max_pax' => 2],
            ['name' => 'Zamaeni & Partner', 'code' => 'SE-ZAM01', 'group' => 'Rekan & Kolega', 'max_pax' => 2],
            ['name' => 'Adam & Partner', 'code' => 'SE-ADM01', 'group' => 'Rekan & Kolega', 'max_pax' => 2],
            ['name' => 'Patra Jumadil & Partner', 'code' => 'SE-PAT01', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
            ['name' => 'Ebing Sastrawan & Partner', 'code' => 'SE-EBI01', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
            ['name' => 'Ilham Agus Prasetyo & Partner', 'code' => 'SE-ILH01', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
            ['name' => 'Tito Desprianto & Partner', 'code' => 'SE-TIT01', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
            ['name' => 'Fitria Nurandini, S.Kom. & Partner', 'code' => 'SE-FIT02', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
            ['name' => 'Alifah Rudi Syakur, S.Kom. & Partner', 'code' => 'SE-ALI01', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
            ['name' => 'Danang Sutowijoyo, S.H. & Partner', 'code' => 'SE-DAN01', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
            ['name' => 'Ayu Mugirahayu & Partner', 'code' => 'SE-AYU01', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
            ['name' => 'Silvia Nurfaidzah dan Suami', 'code' => 'SE-SIL01', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
            ['name' => 'Fifi Anjasari & Partner', 'code' => 'SE-FIF01', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
            ['name' => 'Dwi Setia Ningsih & Partner', 'code' => 'SE-DWI01', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
            ['name' => 'Andi Amul Wahyu & Partner', 'code' => 'SE-AND02', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
            ['name' => 'Nurfadilah, S.Pd. & Partner', 'code' => 'SE-NUR02', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
            ['name' => 'Andi Andrea Destriana & Partner', 'code' => 'SE-AND03', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
            ['name' => 'Ekmun & Partner', 'code' => 'SE-EKM01', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
            ['name' => 'Andi Sastra & Partner', 'code' => 'SE-AND04', 'group' => 'Sahabat & Teman', 'max_pax' => 2],
        ];

        foreach ($guests as $guestData) {
            $invitation->guests()->updateOrCreate(
                ['code' => $guestData['code']],
                $guestData
            );
        }
    }
}
