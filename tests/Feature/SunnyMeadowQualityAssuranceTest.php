<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Guest;
use App\Models\Invitation;
use App\Support\ThemeRegistry;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SunnyMeadowQualityAssuranceTest extends TestCase
{
    use RefreshDatabase;

    public function test_audit_section_fifteen_acceptance_criteria(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::first();
        $invitation->update([
            'theme' => 'sunny-meadow',
            'theme_config' => ThemeRegistry::defaultConfig('sunny-meadow'),
        ]);

        $guest = Guest::where('invitation_id', $invitation->id)->first();

        // 1. Valid guest code personalization
        $validResponse = $this->get('/'.$invitation->slug.'?to='.$guest->code);
        $validResponse->assertOk();
        $validResponse->assertSee($guest->name);
        $validResponse->assertSee('theme-sunny-meadow', false);
        $validResponse->assertSee('themes/sunny-meadow/fonts/fonts.css', false);
        $validResponse->assertDontSee('fonts.googleapis.com', false);
        $validResponse->assertDontSee('fonts.gstatic.com', false);

        // 2. Invalid guest code fallback
        $invalidResponse = $this->get('/'.$invitation->slug.'?to=KODETIDAKVALID');
        $invalidResponse->assertOk();
        $invalidResponse->assertSee('Tamu Undangan');

        // 3. Calendar .ics download
        $event = Event::where('invitation_id', $invitation->id)->first();
        $icsResponse = $this->get(route('invitation.event.ics', ['slug' => $invitation->slug, 'event' => $event->id]));
        $icsResponse->assertOk();
        $this->assertStringContainsString('text/calendar', (string) $icsResponse->headers->get('content-type'));

        // 4. Verify no emoji in theme blade files
        $themeFiles = glob(resource_path('views/themes/sunny-meadow/**/*.blade.php'));
        $themeFiles = array_merge($themeFiles, glob(resource_path('views/themes/sunny-meadow/*.blade.php')));

        $emojiRegex = '/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{1F1E0}-\x{1F1FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u';

        foreach ($themeFiles as $file) {
            $content = file_get_contents($file);
            $this->assertSame(0, preg_match($emojiRegex, $content), "Found emoji in {$file}");
        }

        // 5. Verify dark-boho remains intact and functional without regressions
        $invitation->update([
            'theme' => 'dark-boho',
            'theme_config' => ThemeRegistry::defaultConfig('dark-boho'),
        ]);

        $darkResponse = $this->get('/'.$invitation->slug);
        $darkResponse->assertOk();
        $darkResponse->assertSee('theme-dark-boho', false);
        $darkResponse->assertSee('themes/dark-boho/theme.css', false);
    }
}
