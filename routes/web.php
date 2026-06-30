<?php

use App\Http\Controllers\CityController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\ContributorController;
use App\Http\Controllers\DiscoverController;
use App\Http\Controllers\Restaurant\RestaurantController;
use App\Http\Controllers\Restaurant\VoteController;
use App\Models\Contributor;
use App\Notifications\User\Contributor\ContributorRequestApprovedNotification;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

require __DIR__ . '/settings.php';

Route::inertia('/welcome', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::get('/notification', function () {
    $invoice = Contributor::first();
    return (new ContributorRequestApprovedNotification($invoice))->toMail($invoice->user);
});

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Restaurant Claims
    Route::controller(RestaurantController::class)
        ->prefix('restaurants/{restaurant:slug}')
        ->as('restaurants.')
        ->middleware('can:create_restaurant_claims')
        ->group(function () {
            Route::get('/claim', 'createClaim')->name('claim.create');
            Route::post('/claim', 'storeClaim')->name('claim.store');
        });
    
    // Community
    Route::prefix('community')->as('community.')->group(function () {
        
        Route::controller(ContributorController::class)
            ->middleware('can:create_contributors')
            ->group(function () {
                Route::get('/join', 'create')->name('join.create');
                Route::post('/join', 'store')->name('join.store');
            });
        
        Route::delete('/{contributor}', [ContributorController::class, 'destroy'])
            ->middleware('can:delete_contributors')
            ->name('leave');
    });
});

Route::get('/', [DiscoverController::class, 'index'])->name('discover.index');
Route::get('/home', [DiscoverController::class, 'home'])->name('discover.home');
Route::get('/how-it-works', [DiscoverController::class, 'howItWorks'])->name('discover.how-it-works');
Route::get('/about', [DiscoverController::class, 'about'])->name('discover.about');
Route::get('/how-claim-restaurant-works', [DiscoverController::class, 'howClaimRestaurantWorks'])->name('discover.how-claim-restaurant-works');
Route::get('/privacy-policy', [DiscoverController::class, 'privacyPolicy'])->name('discover.privacy-policy');
Route::get('/terms-of-service', [DiscoverController::class, 'termsOfService'])->name('discover.terms-of-service');
Route::get('/contact-us', [DiscoverController::class, 'contactUs'])->name('discover.contact-us');
Route::get('/community', [DiscoverController::class, 'community'])->name('discover.community');
Route::get('explore', [DiscoverController::class, 'explore'])->name('discover.explore');

Route::get('/cities', [CityController::class, 'index'])->name('city.index');
Route::get('/coming-soon-cities', [CityController::class, 'comingSoonCities'])->name('city.coming-soon-cities');
Route::post('/{city:slug}/wishlist', [CityController::class, 'storeWishlist'])->name('city.wishlist.store');


Route::get('/{city:slug}/restaurants', [RestaurantController::class, 'index'])->name('restaurants.index');
Route::get('/{city:slug}/{restaurant:slug}', [RestaurantController::class, 'show'])->name('restaurants.show');
Route::get('/{city:slug}/{restaurant:slug}/photos', [RestaurantController::class, 'photos'])->name('restaurants.photos');

// Allow 1 request per 24 hours per IP address
Route::post('/{city:slug}/{restaurant:slug}/vote', [VoteController::class, 'store'])->name('restaurants.vote')->middleware('throttle:1,1,200');

Route::get('/qr/{qrCode}', [RestaurantController::class, 'showWithQr'])->name('restaurants.qr.show');
Route::get('/restaurant/{restaurant}/qr/download', [RestaurantController::class, 'download'])->name('restaurants.qr.download');
