<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\Login;
use App\Http\Controllers\Auth\Logout;
use App\Http\Controllers\Auth\Register;
use App\Http\Controllers\PropertyController;


Route::get('/', [PropertyController::class, 'index'])->name('home');
Route::post('/properties', [PropertyController::class, 'store']);



Route::view('/login', 'auth.login')->middleware('guest')->name('login');
Route::post('/login', Login::class)->middleware('guest');

Route::post('/logout', Logout::class)->middleware('auth')->name('logout');

Route::view('/register', 'auth.register')->middleware('guest')->name('register');
Route::post('/register', Register::class)->middleware('guest');


Route::get('/list_property', function (){
    return view('propertypage');
});
