<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\LoveStoryChapter;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PanelFeaturesFase5Test extends TestCase
{
    use RefreshDatabase;

    public function test_love_story_enforces_eight_chapter_limit_and_sanitizes_text(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::firstOrFail();

        for ($i = 1; $i <= 8; $i++) {
            LoveStoryChapter::create([
                'invitation_id' => $invitation->id,
                'title' => "<b>Bab {$i}</b>",
                'time_label' => '<i>202'.$i.'</i>',
                'body' => '<script>alert(1)</script>Cerita bab '.$i,
                'sort_order' => $i,
                'is_visible' => $i !== 8,
            ]);
        }

        $first = $invitation->loveStoryChapters()->firstOrFail();
        $this->assertSame('Bab 1', $first->title);
        $this->assertSame('2021', $first->time_label);
        $this->assertSame('alert(1)Cerita bab 1', $first->body);

        $this->assertCount(7, $invitation->visibleLoveStoryChapters()->get());

        // 9th chapter must be rejected by server
        $this->expectException(ValidationException::class);
        LoveStoryChapter::create([
            'invitation_id' => $invitation->id,
            'title' => 'Bab 9',
            'body' => 'Tidak boleh tersimpan',
            'sort_order' => 9,
            'is_visible' => true,
        ]);
    }
}
