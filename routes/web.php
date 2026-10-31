<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehicleSubmissionController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\OwnershipGuideController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OwnershipGuideController as AdminOwnershipGuideController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\InquiryFollowUpController;
use App\Http\Controllers\Admin\VehicleController as AdminVehicleController;
use App\Http\Controllers\Admin\VehicleImageController;
use App\Http\Controllers\Admin\VehicleSubmissionController as AdminVehicleSubmissionController;
use App\Http\Controllers\Admin\SellerBrokerageTermsController;
use App\Http\Controllers\Admin\VehiclePromotionController;
use App\Http\Controllers\Admin\MarketingDashboardController;
use App\Http\Controllers\Admin\HomeHeroSlideController;

Route::get('/', [VehicleController::class, 'home'])
    ->name('home');

Route::get('/vehicles', [VehicleController::class, 'index'])
    ->name('vehicles.index');

Route::get('/vehicles/{vehicle:slug}', [VehicleController::class, 'show'])
    ->name('vehicles.show');

Route::get('/how-to-own-a-car', [OwnershipGuideController::class, 'show'])
    ->name('ownership-guide.show');

Route::post('/inquiries', [InquiryController::class, 'store'])
    ->name('inquiries.store');

Route::get('/sell-your-car', [VehicleSubmissionController::class, 'create'])
    ->name('sell.create');

Route::post('/sell-your-car', [VehicleSubmissionController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('sell.store');

Route::get('/sell-your-car/track/{token}', [VehicleSubmissionController::class, 'track'])
    ->where('token', '[a-f0-9]{64}')
    ->name('sell.track');

Route::post('/sell-your-car/track/{token}/information', [VehicleSubmissionController::class, 'provideInformation'])
    ->where('token', '[a-f0-9]{64}')
    ->middleware('throttle:5,1')
    ->name('sell.information');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])
            ->name('login');

        Route::post('/login', [AuthController::class, 'login'])
            ->name('login.submit');
    });

    Route::middleware(['auth', 'active.staff'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/marketing', [MarketingDashboardController::class, 'index'])
            ->name('marketing.index');

        Route::get('/home-slides', [HomeHeroSlideController::class, 'index'])
            ->name('home-slides.index');
        Route::post('/home-slides', [HomeHeroSlideController::class, 'store'])
            ->name('home-slides.store');
        Route::put('/home-slides/{slide}', [HomeHeroSlideController::class, 'update'])
            ->name('home-slides.update');
        Route::delete('/home-slides/{slide}', [HomeHeroSlideController::class, 'destroy'])
            ->name('home-slides.destroy');

        Route::patch('/inquiries/{inquiry}/status', [
            DashboardController::class,
            'updateInquiryStatus',
        ])->name('inquiries.status');
        
Route::patch('/inquiries/{inquiry}/assign', [
    DashboardController::class,
    'assignInquiry',
])->name('inquiries.assign');


        Route::post('/logout', [AuthController::class, 'logout'])
            ->name('logout');

        Route::get('/seller-terms', [SellerBrokerageTermsController::class, 'edit'])
            ->name('seller-terms.edit');

        Route::put('/seller-terms', [SellerBrokerageTermsController::class, 'update'])
            ->name('seller-terms.update');

        Route::get('/staff', [StaffController::class, 'index'])
    ->name('staff.index');

Route::get('/staff/create', [StaffController::class, 'create'])
    ->name('staff.create');

Route::post('/staff', [StaffController::class, 'store'])
    ->name('staff.store');

Route::patch('/staff/{staff}/suspend', [StaffController::class, 'suspend'])
    ->name('staff.suspend');

Route::patch('/staff/{staff}/reactivate', [StaffController::class, 'reactivate'])
    ->name('staff.reactivate');

Route::delete('/staff/{staff}', [StaffController::class, 'remove'])
    ->name('staff.remove');

Route::delete('/staff/{staff}/delete', [StaffController::class, 'destroy'])
    ->name('staff.destroy');
    
Route::post(
    '/inquiries/{inquiry}/follow-ups',
    [InquiryFollowUpController::class, 'store']
)->name('inquiries.follow-ups.store');


Route::get(
    '/inquiries/{inquiry}/follow-ups',
    [InquiryFollowUpController::class, 'index']
)->name('inquiries.follow-ups.index');

Route::get('/vehicles', [AdminVehicleController::class, 'index'])
    ->name('vehicles.index');

Route::get('/vehicles/{vehicle}/promote', [VehiclePromotionController::class, 'show'])
    ->name('vehicles.promote');

Route::post('/vehicles/{vehicle}/promotions', [VehiclePromotionController::class, 'store'])
    ->name('vehicles.promotions.store');

Route::get('/ownership-guide', [AdminOwnershipGuideController::class, 'edit'])
    ->name('ownership-guide.edit');

Route::put('/ownership-guide', [AdminOwnershipGuideController::class, 'update'])
    ->name('ownership-guide.update');

Route::get('/ownership-guide/preview', [AdminOwnershipGuideController::class, 'preview'])
    ->name('ownership-guide.preview');

Route::post('/ownership-guide/steps', [AdminOwnershipGuideController::class, 'storeStep'])
    ->name('ownership-guide.steps.store');

Route::put('/ownership-guide/steps/{step}', [AdminOwnershipGuideController::class, 'updateStep'])
    ->name('ownership-guide.steps.update');

Route::delete('/ownership-guide/steps/{step}', [AdminOwnershipGuideController::class, 'destroyStep'])
    ->name('ownership-guide.steps.destroy');

Route::get('/vehicle-submissions', [AdminVehicleSubmissionController::class, 'index'])
    ->name('submissions.index');

Route::get('/vehicle-submissions/{submission}', [AdminVehicleSubmissionController::class, 'show'])
    ->name('submissions.show');

Route::get('/vehicle-submissions/{submission}/photos/{image}', [AdminVehicleSubmissionController::class, 'photo'])
    ->name('submissions.photos.show');

Route::post('/vehicle-submissions/{submission}/approve', [AdminVehicleSubmissionController::class, 'approve'])
    ->name('submissions.approve');

Route::post('/vehicle-submissions/{submission}/reject', [AdminVehicleSubmissionController::class, 'reject'])
    ->name('submissions.reject');

Route::post('/vehicle-submissions/{submission}/request-information', [AdminVehicleSubmissionController::class, 'requestInformation'])
    ->name('submissions.request-information');
    
Route::get('/vehicles/create', [AdminVehicleController::class, 'create'])
    ->name('vehicles.create');

Route::post('/vehicles', [AdminVehicleController::class, 'store'])
    ->name('vehicles.store');

// Display the Edit Vehicle form
Route::get('/vehicles/{vehicle}/edit', [AdminVehicleController::class, 'edit'])
    ->name('vehicles.edit');

// Save updated vehicle details
Route::put('/vehicles/{vehicle}', [AdminVehicleController::class, 'update'])
    ->name('vehicles.update');


Route::patch(
    '/vehicles/{vehicle}/publication',
    [AdminVehicleController::class, 'updatePublication']
)->name('vehicles.publication');

Route::get(
    '/vehicles/{vehicle}/images',
    [VehicleImageController::class, 'index']
)->name('vehicles.images.index');

Route::post(
    '/vehicles/{vehicle}/images',
    [VehicleImageController::class, 'store']
)->name('vehicles.images.store');


Route::patch(
    '/vehicles/{vehicle}/images/{image}/primary',
    [VehicleImageController::class, 'setPrimary']
)->name('vehicles.images.primary');

Route::post(
    '/vehicles/{vehicle}/images/{image}/replace',
    [VehicleImageController::class, 'replace']
)->name('vehicles.images.replace');


Route::delete(
    '/vehicles/{vehicle}/images/{image}',
    [VehicleImageController::class, 'destroy']
)->name('vehicles.images.destroy');

    });
});
