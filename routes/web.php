<?php

use App\Http\Controllers\ProfileController;
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

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
