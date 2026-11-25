<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\Login;
use App\Http\Controllers\Auth\Logout;
use App\Http\Controllers\Auth\Register;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\PropertyImageController; 
use App\Http\Controllers\AdminController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\Property;

Route::get('/', [PropertyController::class, 'index'])->name('home');

Route::get('/properties/{property}', [PropertyController::class, 'show'])
    ->name('properties.show');

Route::middleware(['auth', 'can:level-1'])->group(function () {
    Route::get('/list_property', fn () => view('propertypage'))->name('properties.create');
    Route::post('/properties', [PropertyController::class, 'store']);

    Route::delete('properties{property}', [PropertyController::class, 'destroy'])->name('properties.destroy');

    Route::put('/properties/{property}', [PropertyController::class, 'update'])
        ->name('properties.update');

    Route::delete('/properties/{property}/images/{image}', [PropertyImageController::class, 'destroy'])
        ->name('properties.images.destroy');
});

Route::get('/adminindex', [AdminController::class, 'index'])->name('adminindex');
Route::get('/employeeindex', [AdminController::class, 'employee_index'])->name('emp_index');


Route::post('/properties/{property}/inquiries', [InquiryController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('properties.inquiries.store');

#login routes
Route::view('/login', 'auth.login')->middleware('guest')->name('login');
Route::post('/login', Login::class)->middleware('guest');
Route::post('/logout', Logout::class)->middleware('auth')->name('logout');


#register routes
Route::view('/register', 'auth.register')->middleware(['auth', 'can:level-2'])->name('register');
Route::post('/register', Register::class)->middleware(['auth', 'can:level-2']);
