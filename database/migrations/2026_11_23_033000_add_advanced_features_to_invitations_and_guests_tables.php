<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->text('wa_template')->nullable()->after('closing_text');
            $table->boolean('no_index')->default(false)->after('is_published');
            $table->string('preset_music', 120)->nullable()->after('music_file');
        });

        Schema::table('guests', function (Blueprint $table) {
            $table->timestamp('checked_in_at')->nullable()->after('opened_at');
            $table->json('allowed_event_ids')->nullable()->after('checked_in_at');
        });
    }

    public function down(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->dropColumn(['wa_template', 'no_index', 'preset_music']);
        });

        Schema::table('guests', function (Blueprint $table) {
            $table->dropColumn(['checked_in_at', 'allowed_event_ids']);
        });
    }
};
