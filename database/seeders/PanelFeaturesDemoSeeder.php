<?php

namespace Database\Seeders;

use App\Models\GiftRegistryLink;
use App\Models\Invitation;
use App\Models\LoveStoryChapter;
use App\Models\SeatingTable;
use Illuminate\Database\Seeder;

class PanelFeaturesDemoSeeder extends Seeder
{
    public function run(): void
    {
        $invitation = Invitation::query()->first();

        if ($invitation === null) {
            return;
        }

        $invitation->update([
            'feature_flags' => [
                'love_story' => true,
                'seating' => true,
                'qris' => true,
                'registry' => true,
            ],
            'seat_info_visibility' => 'after_checkin',
            'rsvp_mode' => 'per_undangan',
            'love_story_title' => 'Kisah Cinta',
            'love_story_intro' => 'Perjalanan singkat yang mengantarkan kami hingga hari bahagia ini.',
            'registry_intro' => 'Kami telah menyiapkan daftar hadiah sebagai referensi bagi yang ingin berbagi kebahagiaan.',
        ]);

        if ($invitation->loveStoryChapters()->count() === 0) {
            LoveStoryChapter::create([
                'invitation_id' => $invitation->id,
                'title' => 'Pertemuan Pertama',
                'time_label' => '2022',
                'body' => 'Pertama kali dipertemukan dalam sebuah kegiatan kampus yang sederhana, lalu perlahan tumbuh menjadi percakapan yang hangat.',
                'sort_order' => 1,
                'is_visible' => true,
            ]);

            LoveStoryChapter::create([
                'invitation_id' => $invitation->id,
                'title' => 'Komitmen Bersama',
                'time_label' => '2024',
                'body' => 'Setelah saling mengenal keluarga masing-masing, kami sepakat untuk melangkah menuju ikatan yang lebih serius.',
                'sort_order' => 2,
                'is_visible' => true,
            ]);

            LoveStoryChapter::create([
                'invitation_id' => $invitation->id,
                'title' => 'Menuju Pelaminan',
                'time_label' => '2026',
                'body' => 'Dengan memohon restu kedua orang tua dan keluarga besar, kami siap mengikat janji suci pernikahan.',
                'sort_order' => 3,
                'is_visible' => true,
            ]);
        }

        if ($invitation->seatingTables()->count() === 0) {
            $vipTable = SeatingTable::create([
                'invitation_id' => $invitation->id,
                'name' => 'Meja VIP 1',
                'capacity' => 8,
                'notes' => 'Keluarga Inti & Tetua',
                'sort_order' => 1,
            ]);

            $friendTable = SeatingTable::create([
                'invitation_id' => $invitation->id,
                'name' => 'Meja Sahabat 1',
                'capacity' => 10,
                'notes' => 'Rekan Kerja & Sahabat',
                'sort_order' => 2,
            ]);

            $guests = $invitation->guests()->orderBy('id')->take(2)->get();
            if (isset($guests[0])) {
                $guests[0]->update([
                    'seating_table_id' => $vipTable->id,
                    'seat_label' => '1-2',
                ]);
            }
            if (isset($guests[1])) {
                $guests[1]->update([
                    'seating_table_id' => $friendTable->id,
                    'seat_label' => '3',
                ]);
            }
        }

        if ($invitation->giftRegistryLinks()->count() === 0) {
            GiftRegistryLink::create([
                'invitation_id' => $invitation->id,
                'label' => 'Daftar Kado Rumah Tangga',
                'url' => 'https://example.com/registry/keluarga-baru',
                'sort_order' => 1,
                'is_active' => true,
            ]);
        }
    }
}
