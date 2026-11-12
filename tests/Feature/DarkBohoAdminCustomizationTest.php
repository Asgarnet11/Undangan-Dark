<?php

namespace Tests\Feature;

use App\Filament\Resources\Invitations\Pages\EditInvitation;
use App\Filament\Resources\Invitations\Pages\ListInvitations;
use App\Filament\Resources\Rsvps\Pages\ListRsvps;
use App\Filament\Resources\Wishes\Pages\ListWishes;
use App\Models\Invitation;
use App\Models\User;
use App\Models\Wish;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DarkBohoAdminCustomizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_pages_and_live_preview_require_auth_and_render_when_signed_in(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::where('slug', 'raka-nadia')->firstOrFail();
        $admin = User::where('email', 'admin@undangan.test')->firstOrFail();

        $this->get('/admin/invitations')->assertRedirect();
        $this->get(route('invitation.preview', ['invitation' => $invitation]))->assertRedirect();

        $this->actingAs($admin);

        Livewire::test(ListInvitations::class)->assertOk();
        Livewire::test(ListWishes::class)->assertOk();
        Livewire::test(ListRsvps::class)->assertOk();

        $editResponse = $this->get("/admin/invitations/{$invitation->id}/edit");
        $editResponse->assertOk();
        $editResponse->assertSee('Pratinjau Langsung');
        $editResponse->assertSee('id="db-admin-preview-frame"', false);

        $previewResponse = $this->get(route('invitation.preview', ['invitation' => $invitation]));
        $previewResponse->assertOk();
        $previewResponse->assertSee('data-preview-mode="true"', false);
        $previewResponse->assertSee('data-preview-section="verse"', false);
    }

    public function test_preview_forbids_unrelated_user(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::where('slug', 'raka-nadia')->firstOrFail();
        $otherUser = User::factory()->create(['email' => '']);

        $this->actingAs($otherUser)
            ->get(route('invitation.preview', ['invitation' => $invitation]))
            ->assertForbidden();
    }

    public function test_editing_theme_options_updates_config_and_changes_rendered_presets(): void
    {
        $this->seed(DatabaseSeeder::class);

        $invitation = Invitation::where('slug', 'raka-nadia')->firstOrFail();
        $admin = User::where('email', 'admin@undangan.test')->firstOrFail();

        $this->actingAs($admin);

        Livewire::test(EditInvitation::class, ['record' => $invitation->getRouteKey()])
            ->fillForm([
                'theme_config.preset' => 'sage',
                'theme_config.cover.style' => 'new',
                'theme_config.cover.usephoto' => false,
                'theme_config.cover.overlayColor' => '#2b241d',
                'theme_config.cover.overlayOpacity' => 0.72,
                'theme_config.cover.textColor' => '#f5eee0',
                'theme_config.cover.showGuestName' => false,
                'theme_config.layout.illustrationMode' => 'minimal',
                'theme_config.layout.brideFirst' => false,
                'theme_config.layout.giftInline' => false,
                'theme_config.sections.verse' => false,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $invitation->refresh();
        $config = $invitation->resolvedThemeConfig();

        $this->assertSame('sage', $config['preset']);
        $this->assertSame('new', $config['cover']['style']);
        $this->assertFalse($config['cover']['usephoto']);
        $this->assertSame('#2b241d', $config['cover']['overlayColor']);
        $this->assertEquals(0.72, $config['cover']['overlayOpacity']);
        $this->assertFalse($config['cover']['showGuestName']);
        $this->assertSame('minimal', $config['layout']['illustrationMode']);
        $this->assertFalse($config['layout']['brideFirst']);
        $this->assertFalse($config['layout']['giftInline']);
        $this->assertFalse($config['sections']['verse']);

        $publicHtml = $this->get('/raka-nadia')->assertOk()->getContent();
        $this->assertStringContainsString('data-preset="sage"', $publicHtml);
        $this->assertStringContainsString('data-cover-style="new"', $publicHtml);
        $this->assertStringContainsString('data-cover-use-photo="false"', $publicHtml);
        $this->assertStringContainsString('data-illustration-mode="minimal"', $publicHtml);
        $this->assertStringContainsString('data-gift-inline="false"', $publicHtml);
        $this->assertStringContainsString('data-bride-first="false"', $publicHtml);
        $this->assertStringContainsString('--db-cover-overlay-color: #2b241d', $publicHtml);
        $this->assertStringNotContainsString('id="section-verse"', $publicHtml);
        $this->assertStringNotContainsString('class="db-cover-guest"', $publicHtml);
        $this->assertStringContainsString('id="db-gift-fab"', $publicHtml);
    }

    public function test_wish_moderation_toggle_and_bulk_actions_hide_and_restore_wishes(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::where('email', 'admin@undangan.test')->firstOrFail();
        $wish = Wish::firstOrFail();

        $this->actingAs($admin);

        Livewire::test(ListWishes::class)
            ->callTableBulkAction('hide_selected', [$wish]);

        $this->assertFalse($wish->fresh()->is_visible);
        $this->get('/raka-nadia')->assertOk()->assertDontSee($wish->message);

        Livewire::test(ListWishes::class)
            ->callTableBulkAction('show_selected', [$wish]);

        $this->assertTrue($wish->fresh()->is_visible);
        $this->get('/raka-nadia')->assertOk()->assertSee($wish->message);
    }
}
