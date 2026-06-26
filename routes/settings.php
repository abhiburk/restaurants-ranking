<?php

use App\Http\Controllers\CommunityController;
use App\Http\Controllers\ContributorController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\SecurityController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::redirect('account', '/account/profile');

    Route::get('account/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('account/profile', [ProfileController::class, 'update'])->name('profile.update');
});

Route::middleware(['auth', 'verified'])->prefix('account')->group(function () {
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/security', [SecurityController::class, 'edit'])->name('security.edit');

    Route::put('/password', [SecurityController::class, 'update'])->middleware('throttle:6,1')->name('user-password.update');

    Route::inertia('/appearance', 'settings/Appearance')->name('appearance.edit');

    Route::get('/contributors', [ContributorController::class, 'index'])->middleware(['can:create_contributors'])->name('settings.contributors.index');

    Route::get('/contributors/{contributor}', [ContributorController::class, 'show'])->middleware(['can:view_contributors'])->name('settings.contributors.show');
});
