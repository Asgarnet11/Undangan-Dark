<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('theme')->default('dark-boho');
            $table->json('theme_config')->nullable();
            $table->string('timezone')->default('Asia/Jakarta');
            $table->string('title');

            $table->string('bride_name');
            $table->string('bride_nickname');
            $table->string('bride_father')->nullable();
            $table->string('bride_mother')->nullable();
            $table->string('bride_order_label')->nullable();
            $table->string('bride_origin')->nullable();
            $table->string('bride_instagram')->nullable();
            $table->string('bride_photo')->nullable();

            $table->string('groom_name');
            $table->string('groom_nickname');
            $table->string('groom_father')->nullable();
            $table->string('groom_mother')->nullable();
            $table->string('groom_order_label')->nullable();
            $table->string('groom_origin')->nullable();
            $table->string('groom_instagram')->nullable();
            $table->string('groom_photo')->nullable();

            $table->text('opening_text')->nullable();
            $table->text('verse_arabic')->nullable();
            $table->text('verse_translation')->nullable();
            $table->string('verse_source')->nullable();

            $table->string('cover_photo')->nullable();
            $table->string('music_file')->nullable();
            $table->string('music_title')->nullable();

            $table->string('video_url')->nullable();
            $table->string('gallery_title')->nullable();
            $table->text('gallery_quote')->nullable();

            $table->string('livestream_url')->nullable();
            $table->string('livestream_title')->nullable();
            $table->text('livestream_note')->nullable();

            $table->text('closing_text')->nullable();
            $table->text('thank_you_gift_note')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
