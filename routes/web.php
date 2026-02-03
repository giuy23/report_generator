<?php

use App\Http\Controllers\Report\CreditReportController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('MainApp');
})->name(name: 'home');

// Route::get('dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::controller(CreditReportController::class)->group(function () {
    Route::post('/generate-report', action: 'generate_report')->name('report.generate');
    Route::post('/verify-report', action: 'verify_document')->name('report.verify');
    Route::get('/download-report', action: 'download')->name('report.download');
});

require __DIR__.'/settings.php';
