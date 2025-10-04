<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\tourController;

Route::get('tour/{tour}', [tourController::class, 'show'])->name('tour.show');
Route::middleware(['auth', 'can:employer'])->group(function () {

    Route::get('dashboard/tour/create', [tourController::class, 'create'])->name('tour.create');
    Route::post('dashboard/tour', [tourController::class, 'store'])->name('tour.store');

    Route::get('dashboard/mytour/{tour}/edit', [tourController::class, 'edit'])->name('mytour.edit');
    Route::patch('dashboard/mytour/{tour}', [tourController::class, 'update'])->name('mytour.update');
    Route::delete('dashboard/mytour/{tour}', [tourController::class, 'destroy'])->name('mytour.destroy');
    Route::get('dashboard/myTours', [tourController::class, 'myTours'])->name('myTours');

});

Route::fallback(function() {
    abort(404);
});