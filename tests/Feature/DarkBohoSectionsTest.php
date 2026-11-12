<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\GalleryPhoto;
use App\Models\Invitation;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DarkBohoSectionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_countdown_renders_target_iso_save_date_links_and_elapsed_state(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->get('/raka-nadia');
        $response->assertOk();
        $response->assertSee('id="db-countdown"', false);
        $response->assertSee('data-target="', false);
        $response->assertSee('https://calendar.google.com/calendar/render', false);
        $response->assertSee('ctz=Asia%2FJakarta', false);

        $invitation = Invitation::where('slug', 'raka-nadia')->firstOrFail();
        $firstEvent = $invitation->events()->orderBy('sort_order')->firstOrFail();
        $firstEvent->update([
            'starts_at' => now('Asia/Jakarta')->subDays(3),
            'ends_at' => now('Asia/Jakarta')->subDays(3)->addHours(2),
        ]);

        $elapsedResponse = $this->get('/raka-nadia');
        $elapsedResponse->assertOk();
        $elapsedResponse->assertSee('Acara telah berlangsung');
        $elapsedResponse->assertDontSee('id="db-countdown-grid"', false);
    }

    public function test_verse_and_couple_order_respect_bride_first_config(): void
    {
        $this->seed(DatabaseSeeder::class);

        $defaultResponse = $this->get('/raka-nadia');
        $defaultResponse->assertOk();
        $defaultResponse->assertSee('lang="ar" dir="rtl"', false);

        $defaultHtml = $defaultResponse->getContent();
        $this->assertLessThan(
            strpos($defaultHtml, 'data-role="groom"'),
            strpos($defaultHtml, 'data-role="bride"'),
        );

        $invitation = Invitation::where('slug', 'raka-nadia')->firstOrFail();
        $config = $invitation->resolvedThemeConfig();
        $config['layout']['brideFirst'] = false;
        $invitation->update(['theme_config' => $config]);

        $groomFirstHtml = $this->get('/raka-nadia')->getContent();
        $this->assertLessThan(
            strpos($groomFirstHtml, 'data-role="bride"'),
            strpos($groomFirstHtml, 'data-role="groom"'),
        );
    }

    public function test_video_section_renders_youtube_facade_without_eager_iframe(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::where('slug', 'raka-nadia')->firstOrFail();
        $config = $invitation->resolvedThemeConfig();
        $config['sections']['video'] = true;
        $invitation->update([
            'video_url' => 'https://youtu.be/dQw4w9WgXcQ',
            'theme_config' => $config,
        ]);

        $response = $this->get('/raka-nadia');
        $response->assertOk();
        $response->assertSee('id="section-video"', false);
        $response->assertSee('data-youtube-id="dQw4w9WgXcQ"', false);
        $response->assertSee('https://i.ytimg.com/vi/dQw4w9WgXcQ/hqdefault.jpg', false);
        $response->assertDontSee('youtube-nocookie.com/embed/dQw4w9WgXcQ', false);
    }

    public function test_gallery_caps_at_twelve_lazy_images_and_renders_accessible_lightbox(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::where('slug', 'raka-nadia')->firstOrFail();
        GalleryPhoto::where('invitation_id', $invitation->id)->delete();

        for ($i = 1; $i <= 15; $i++) {
            GalleryPhoto::create([
                'invitation_id' => $invitation->id,
                'path' => 'demo-gallery/momen-'.(($i - 1) % 4 + 1).'.webp',
                'caption' => 'Foto '.$i,
                'width' => 720,
                'height' => 960,
                'sort_order' => $i,
            ]);
        }

        $html = $this->get('/raka-nadia')->assertOk()->getContent();
        $this->assertSame(12, substr_count($html, 'class="db-gallery-item"'));
        $this->assertStringContainsString('id="db-lightbox"', $html);
        $this->assertStringContainsString('role="dialog"', $html);
        $this->assertStringContainsString('aria-modal="true"', $html);
        $this->assertStringContainsString('loading="lazy"', $html);
    }

    public function test_events_maps_single_source_and_ics_download_work(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::where('slug', 'raka-nadia')->firstOrFail();
        $event = $invitation->events()->orderBy('sort_order')->firstOrFail();

        $response = $this->get('/raka-nadia');
        $response->assertOk();
        $response->assertSee('output=embed', false);
        $response->assertSee('WIB', false);
        $response->assertSee('id="db-bottom-nav"', false);
        $response->assertSee('Mempelai');
        $response->assertSee('Galeri');
        $response->assertSee('Lokasi');
        $response->assertSee('Ucapan');

        $icsResponse = $this->get(route('invitation.event.ics', [
            'slug' => $invitation->slug,
            'event' => $event->id,
        ]));

        $icsResponse->assertOk();
        $icsResponse->assertHeader('Content-Type', 'text/calendar; charset=utf-8');
        $icsContent = $icsResponse->getContent();
        $this->assertStringContainsString('BEGIN:VCALENDAR', $icsContent);
        $this->assertStringContainsString('DTSTART;TZID=Asia/Jakarta:', $icsContent);
        $this->assertStringContainsString('DTEND;TZID=Asia/Jakarta:', $icsContent);

        $otherInvitation = Invitation::create([
            'user_id' => $invitation->user_id,
            'slug' => 'tamu-lain',
            'theme' => 'dark-boho',
            'title' => 'Undangan Lain',
            'bride_name' => 'Ayu',
            'bride_nickname' => 'Ayu',
            'groom_name' => 'Bima',
            'groom_nickname' => 'Bima',
            'timezone' => 'Asia/Jakarta',
            'is_published' => true,
        ]);
        $otherEvent = Event::create([
            'invitation_id' => $otherInvitation->id,
            'title' => 'Akad',
            'starts_at' => now()->addMonth(),
            'venue_name' => 'Gedung',
            'address' => 'Jakarta',
        ]);

        $this->get(route('invitation.event.ics', [
            'slug' => $invitation->slug,
            'event' => $otherEvent->id,
        ]))->assertNotFound();
    }

    public function test_closing_hides_co_inviters_when_empty(): void
    {
        $this->seed(DatabaseSeeder::class);

        $withCoInviters = $this->get('/raka-nadia');
        $withCoInviters->assertOk();
        $withCoInviters->assertSee('Turut Mengundang');

        $invitation = Invitation::where('slug', 'raka-nadia')->firstOrFail();
        $invitation->coInviters()->delete();

        $withoutCoInviters = $this->get('/raka-nadia');
        $withoutCoInviters->assertOk();
        $withoutCoInviters->assertDontSee('Turut Mengundang');
    }
}
