<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitations', function (Blueprint $table): void {
            $table->json('feature_flags')->nullable()->after('theme_config');
            $table->string('seat_info_visibility', 20)->default('after_checkin')->after('feature_flags');
            $table->string('rsvp_mode', 20)->default('per_undangan')->after('seat_info_visibility');
            $table->text('rsvp_thanks_attending')->nullable()->after('rsvp_mode');
            $table->text('rsvp_thanks_declined')->nullable()->after('rsvp_thanks_attending');
            $table->text('gift_note')->nullable()->after('thank_you_gift_note');
            $table->string('gift_whatsapp_number', 30)->nullable()->after('gift_note');
            $table->text('gift_whatsapp_template')->nullable()->after('gift_whatsapp_number');
            $table->string('love_story_title', 120)->nullable()->after('gift_whatsapp_template');
            $table->text('love_story_intro')->nullable()->after('love_story_title');
            $table->text('registry_intro')->nullable()->after('love_story_intro');
            $table->string('seating_unassigned_note', 255)->nullable()->after('registry_intro');
        });

        Schema::create('love_story_chapters', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->string('title', 80);
            $table->text('body');
            $table->string('photo_path')->nullable();
            $table->string('time_label', 60)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        });

        Schema::create('seating_tables', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->string('name', 80);
            $table->unsignedInteger('capacity')->default(10);
            $table->text('notes')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('allow_over_capacity')->default(false);
            $table->timestamps();

            $table->unique(['invitation_id', 'name']);
        });

        Schema::table('guests', function (Blueprint $table): void {
            $table->foreignId('seating_table_id')->nullable()->after('group')->constrained('seating_tables')->nullOnDelete();
            $table->string('seat_label', 40)->nullable()->after('seating_table_id');
            $table->unsignedInteger('max_adults')->default(1)->after('max_pax');
            $table->unsignedInteger('max_children')->default(0)->after('max_adults');
        });

        DB::table('guests')->update([
            'max_adults' => DB::raw('CASE WHEN max_pax > 1 THEN max_pax ELSE 1 END'),
        ]);

        Schema::create('qris_accounts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->string('label', 120)->nullable();
            $table->string('image_path');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('gift_registry_links', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->string('label', 120);
            $table->string('url', 500);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('events', function (Blueprint $table): void {
            $table->string('maps_url', 500)->nullable()->after('maps_query');
        });

        Schema::create('rsvp_event', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('rsvp_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->boolean('attending')->default(true);
            $table->unsignedInteger('adults')->default(1);
            $table->unsignedInteger('children')->default(0);
            $table->timestamps();

            $table->unique(['rsvp_id', 'event_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rsvp_event');

        Schema::table('events', function (Blueprint $table): void {
            $table->dropColumn('maps_url');
        });

        Schema::dropIfExists('gift_registry_links');
        Schema::dropIfExists('qris_accounts');

        Schema::table('guests', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('seating_table_id');
            $table->dropColumn(['seat_label', 'max_adults', 'max_children']);
        });

        Schema::dropIfExists('seating_tables');
        Schema::dropIfExists('love_story_chapters');

        Schema::table('invitations', function (Blueprint $table): void {
            $table->dropColumn([
                'feature_flags',
                'seat_info_visibility',
                'rsvp_mode',
                'rsvp_thanks_attending',
                'rsvp_thanks_declined',
                'gift_note',
                'gift_whatsapp_number',
                'gift_whatsapp_template',
                'love_story_title',
                'love_story_intro',
                'registry_intro',
                'seating_unassigned_note',
            ]);
        });
    }
};
