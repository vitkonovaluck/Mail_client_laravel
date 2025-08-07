<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'features' => [
            'Безпечне зберігання листів',
            '20GB сховища для пошти',
            'Захист від спаму та вірусів',
            'Можливість створення аліасів',
            'Доступ з будь-якого пристрою'
        ],
        'pricing' => [
            'basic' => [
                'price' => '0',
                'features' => ['5GB сховища', '1 домен', '10 аліасів']
            ],
            'pro' => [
                'price' => '99',
                'features' => ['20GB сховища', '3 домени', 'необмежені аліаси']
            ]
        ],
        'auth' => [
            'user' => auth()->user()
        ]
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class,'index'])->middleware(['auth', 'verified'])->name('dashboard');
    Route::get('/emails/{email}', [DashboardController::class, 'show'])->name('emails.show');
    Route::get('/emails/create', [DashboardController::class, 'create'])->name('emails.create');
    Route::post('/emails/send', [DashboardController::class, 'store'])->name('emails.send');
    Route::post('/emails', [DashboardController::class, 'store'])->name('emails.store');
    Route::get('/emails/{email}/forward', [DashboardController::class, 'forward'])->name('emails.forward');
    Route::post('/emails/{email}/mark-unread', [DashboardController::class, 'markUnread'])->name('emails.mark-unread');
    Route::delete('/emails/{email}', [DashboardControllerr::class, 'destroy'])->name('emails.destroy');
    Route::post('/emails/{email}/move', [DashboardController::class, 'move'])->name('emails.move');
});
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
