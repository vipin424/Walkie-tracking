<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    DashboardController,
    ClientController,
    DispatchController,
    ReturnController,
    PaymentController
};

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('clients', ClientController::class);
    Route::resource('dispatches', DispatchController::class)->only(['index','create','store','show','destroy']);
    Route::post('returns', [ReturnController::class, 'store'])->name('returns.store');

    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('payments/{dispatch}', [PaymentController::class, 'storeOrUpdate'])->name('payments.store');
});
