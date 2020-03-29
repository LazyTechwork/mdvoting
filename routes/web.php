<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/login', 'UserController@loginPage')->name('login');
Route::post('/login', 'UserController@login')->name('login');
Route::get('/register', 'UserController@registerPage')->name('register');
Route::post('/register', 'UserController@register')->name('register');
Route::get('/logout', 'UserController@logout')->name('logout');
