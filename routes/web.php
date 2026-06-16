<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LdapLoginController;
use App\Http\Controllers\UserController;

Route::get('/users', [UserController::class, 'index'])->middleware('auth')->name('users.index');
Route::post('/users/sync', [UserController::class, 'syncUsers'])->middleware('auth')->name('users.sync');
Route::delete('/users/{id}', [UserController::class, 'destroy'])->middleware('auth')->name('users.delete');

Route::get('login', [LdapLoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LdapLoginController::class, 'login'])->name('ldap.login');
Route::post('logout', [LdapLoginController::class, 'logout'])->name('logout');

Route::get('dashboard', function () {
    return view('dashboard');
})->middleware('auth');

Route::get('audit-logs', function () {
    $logs = \App\Models\LdapAuditLog::latest()->paginate(20);
    return view('audit-logs', compact('logs'));
})->middleware('auth');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/check-ldap', function () {
    return [
        'function_exists' => function_exists('ldap_escape'),
        'extension_loaded' => extension_loaded('ldap'),
        'php_ini' => php_ini_loaded_file(),
    ];
});