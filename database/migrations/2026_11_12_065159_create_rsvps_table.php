<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rsvps', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guest_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->boolean('attending')->default(true);
            $table->unsignedInteger('pax')->default(1);
            $table->text('message')->nullable();
            $table->timestamps();

            $table->unique(['invitation_id', 'guest_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rsvps');
    }
};
