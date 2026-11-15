<?php

use App\Http\Controllers\InvitationController;
use App\Models\Invitation;
use Illuminate\Support\Facades\Route;

$slugPattern = '^(?!admin|livewire|up|storage|themes|preview)[a-z0-9-]+$';

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
    ->middleware('auth')
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
