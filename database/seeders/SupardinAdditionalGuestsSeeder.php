<?php

namespace Database\Seeders;

use App\Models\Invitation;
use Illuminate\Database\Seeder;

class SupardinAdditionalGuestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $invitation = Invitation::where('slug', 'supardin-eka')->first();

        if (! $invitation) {
            $this->command?->warn('Undangan dengan slug "supardin-eka" tidak ditemukan.');

            return;
        }

        $newGuests = [
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

        foreach ($newGuests as $guestData) {
            $invitation->guests()->updateOrCreate(
                ['code' => $guestData['code']],
                $guestData
            );
        }

        $this->command?->info('Berhasil menambahkan 16 tamu baru untuk undangan Supardin & Eka.');
    }
}
