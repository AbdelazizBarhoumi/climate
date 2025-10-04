<?php
use App\Http\Controllers\tourController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\EmployerController;
use Illuminate\Support\Facades\Auth;

// Public routes
Route::get('/', [tourController::class, 'index'])->name('home');
Route::get('/search', SearchController::class);
Route::get('/tags/{tagName}', TagController::class)->name('tags.show');
// Authentication required routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
});

// Weather API routes (secure backend endpoint)
Route::get('/api/weather/config', [\App\Http\Controllers\WeatherController::class, 'getConfig'])->name('weather.config');
Route::get('/api/weather/{city}', [\App\Http\Controllers\WeatherController::class, 'getWeather'])->name('weather.api');

// Include other route files
require __DIR__ . '/application.php';
require __DIR__ . '/profile.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/tour.php';

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // User management
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('admin.users.show');
    Route::post('/users/{user}/promote', [AdminController::class, 'promote'])->name('admin.promote');
    Route::post('/users/{user}/demote', [AdminController::class, 'demote'])->name('admin.demote');
    Route::post('/users/{user}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('admin.users.toggle-status');

    // Tour management
    Route::get('/tours', [AdminController::class, 'tours'])->name('admin.tours');
    Route::get('/tours/{tour}', [AdminController::class, 'showtour'])->name('admin.tours.show');
   Route::delete('/tours/{tour}', [AdminController::class, 'deletetour'])->name('admin.tours.delete');

    // Application management
    Route::get('/applications', [AdminController::class, 'applications'])->name('admin.applications');
    Route::get('/applications/{application}', [AdminController::class, 'showApplication'])->name('admin.applications.show');
    Route::post('/applications/{application}/note', [AdminController::class, 'UpdateNotes'])->name('admin.applications.update-notes');
    Route::delete('/applications/{application}/destroy', [AdminController::class, 'deleteApplication'])->name('admin.applications.destroy');
    
    // Temporarily commented out suspension route
    // Route::get('/account/suspended', [App\Http\Controllers\AccountController::class, 'suspended'])
    //     ->name('account.suspended');

    // Optional contact support route
    Route::post('/account/contact-support', [App\Http\Controllers\AccountController::class, 'contactSupport'])
        ->name('account.contact-support');

    // Temporarily commented out duplicate suspension route
    // Route::get('/account/suspended', [App\Http\Controllers\AccountController::class, 'suspended'])
    //     ->name('account.suspended');

Route::post('/account/appeal', [App\Http\Controllers\AccountController::class, 'submitAppeal'])->name('account.appeal');
});

Route::middleware(['auth', 'can:employer'])->group(function () {
    Route::get('/employer/dashboard', [EmployerController::class, 'dashboard'])->name('employer.dashboard');
    Route::post('/tours/{tour}/toggle-status', [EmployerController::class, 'toggletourStatus'])->name('mytour.active');
});

// Admin toggle tour status route
Route::post('/admin/tours/{tour}/toggle-status', [AdminController::class, 'toggletourStatus'])->name('admin.tours.toggle-status');



Route::fallback(function() {
    abort(404);
});


//test route

Route::get('/test', function () {
    return view('test');
});