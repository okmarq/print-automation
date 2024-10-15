<?php

use App\Http\Controllers\AdminSettingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PrintJobController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('dashboard', function () {
    return (new PrintJobController)->index();
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('admin')->group(function () {
        Route::get('/', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::get('settings', [AdminSettingController::class, 'create'])->name('settings.create');
        Route::post('settings', [AdminSettingController::class, 'store'])->name('settings.store');
        Route::patch('settings/{adminSetting}', [AdminSettingController::class, 'store'])->name('settings.update');
    });

    Route::prefix('print')->group(function () {
        Route::get('/', [PrintJobController::class, 'index'])->name('print.upload');
        Route::get('upload', [PrintJobController::class, 'create'])->name('print.create')->middleware('settings');;
        Route::post('upload', [PrintJobController::class, 'store'])->name('print.upload.store');
        Route::post('submit', [PrintJobController::class, 'submit']);
    });

    Route::get('pay/{printJob}', [PaymentController::class, 'create'])->name('payment.create');
    Route::post('pay/{printJob}', [PaymentController::class, 'store'])->name('payment.store');
});

require __DIR__ . '/auth.php';
