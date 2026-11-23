<?php

use App\Http\Controllers\InvitationController;
use App\Models\Invitation;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

$slugPattern = '^(?!admin|livewire|up|storage|themes|preview)[a-z0-9-]+$';

Route::get('/storage/{path}', function (string $path) {
    if (str_contains($path, '..')) {
        abort(404);
    }

    $disk = Storage::disk('public');
    if (! $disk->exists($path)) {
        abort(404);
    }

    return response()->file($disk->path($path), [
        'Cache-Control' => 'public, max-age=86400, stale-while-revalidate=604800',
    ]);
})->where('path', '.*')->name('storage.fallback');

Route::get('/', function () {
    $first = Invitation::query()->where('is_published', true)->orderBy('id')->first();

    if ($first !== null) {
        return redirect()->route('invitation.show', ['slug' => $first->slug]);
    }

    return redirect('/admin');
})->name('home');

Route::get('/supardin', function () {
    $target = Invitation::query()->where('slug', 'alfian-yuli')->first()
        ?? Invitation::query()->where('is_published', true)->first();

    return $target !== null
        ? redirect()->route('invitation.show', ['slug' => $target->slug])
        : redirect('/');
});

Route::get('/preview/invitations/{invitation}', [InvitationController::class, 'preview'])
    ->name('invitation.preview');

Route::get('/{slug}', [InvitationController::class, 'show'])
    ->where('slug', $slugPattern)
    ->name('invitation.show');

Route::get('/{slug}/events/{event}/calendar.ics', [InvitationController::class, 'calendarIcs'])
    ->where('slug', $slugPattern)
    ->name('invitation.event.ics');

Route::post('/{slug}/rsvp', [InvitationController::class, 'storeRsvp'])
    ->middleware('throttle:invitation-rsvp')
    ->where('slug', $slugPattern)
    ->name('invitation.rsvp.store');

Route::get('/{slug}/wishes', [InvitationController::class, 'wishes'])
    ->where('slug', $slugPattern)
    ->name('invitation.wishes.index');

Route::post('/{slug}/wishes', [InvitationController::class, 'storeWish'])
    ->middleware('throttle:invitation-wish')
    ->where('slug', $slugPattern)
    ->name('invitation.wishes.store');

Route::get('/{slug}/gifts', [InvitationController::class, 'gifts'])
    ->middleware('throttle:60,1')
    ->where('slug', $slugPattern)
    ->name('invitation.gifts.index');

Route::post('/{slug}/gifts/confirm', [InvitationController::class, 'storeGiftConfirmation'])
    ->middleware('throttle:30,1')
    ->where('slug', $slugPattern)
    ->name('invitation.gifts.confirm');

Route::get('/{slug}/check-in', [InvitationController::class, 'checkInPage'])
    ->where('slug', $slugPattern)
    ->name('invitation.check-in');

Route::post('/{slug}/check-in', [InvitationController::class, 'processCheckIn'])
    ->middleware('throttle:120,1')
    ->where('slug', $slugPattern)
    ->name('invitation.check-in.process');

Route::post('/{slug}/check-in/{guest}/undo', [InvitationController::class, 'undoCheckIn'])
    ->middleware('throttle:60,1')
    ->where('slug', $slugPattern)
    ->name('invitation.check-in.undo');
