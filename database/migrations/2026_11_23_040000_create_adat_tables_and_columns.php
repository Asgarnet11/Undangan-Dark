<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('adat_event_types', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('label_display');
            $table->json('alt_spellings')->nullable();
            $table->text('description_id')->nullable();
            $table->text('regional_note')->nullable();
            $table->string('status')->default('draf'); // draf, diverifikasi, ditolak
            $table->string('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->boolean('is_published')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('adat_glossary_entries', function (Blueprint $table) {
            $table->id();
            $table->string('term');
            $table->string('latin_reading');
            $table->string('lontara_text')->nullable();
            $table->text('meaning_id');
            $table->text('regional_note')->nullable();
            $table->text('source_note')->nullable();
            $table->string('status')->default('draf'); // draf, diverifikasi, ditolak
            $table->string('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->boolean('is_published')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('lontara_texts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->string('latin_text');
            $table->string('lontara_text');
            $table->string('status')->default('draf'); // draf, diverifikasi, ditolak
            $table->string('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('adat_event_type_id')->nullable()->after('invitation_id')->constrained('adat_event_types')->nullOnDelete();
            $table->string('display_label_override')->nullable()->after('title');
        });

        Schema::table('invitations', function (Blueprint $table) {
            $table->string('monogram_initial', 10)->nullable()->after('title');
            $table->text('dress_note')->nullable()->after('closing_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->dropColumn(['monogram_initial', 'dress_note']);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropConstrainedForeignId('adat_event_type_id');
            $table->dropColumn(['display_label_override']);
        });

        Schema::dropIfExists('lontara_texts');
        Schema::dropIfExists('adat_glossary_entries');
        Schema::dropIfExists('adat_event_types');
    }
};
