<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LdapLoginController;


Route::get('login', [LdapLoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LdapLoginController::class, 'login'])->name('ldap.login');
Route::post('logout', [LdapLoginController::class, 'logout'])->name('logout');

Route::get('dashboard', function () {
    return view('dashboard');
})->middleware('auth');

Route::get('/', function () {
    return view('welcome');
});
