# PHP_Laravel12_LdapRecord

## Introduction

The PHP_Laravel12_LdapRecord project demonstrates how to integrate LDAP authentication into a Laravel 12 application using LdapRecord-Laravel v3. LDAP (Lightweight Directory Access Protocol) is widely used in enterprise environments for managing and authenticating users across organizational directories, such as Active Directory or OpenLDAP.

This project provides a complete workflow for:

- Authenticating users against an LDAP server.

- Optionally caching LDAP users in a local MySQL database.

- Maintaining a Laravel session-based login system.

- Displaying a modern, styled dashboard using Tailwind CSS.

- It is ideal for developers or organizations that need secure, enterprise-grade authentication while leveraging Laravel’s built-in authentication features.

---

## Project Overview

The PHP_Laravel12_LdapRecord project is a Laravel 12 application that demonstrates enterprise-ready LDAP authentication using LdapRecord-Laravel v3. It integrates LDAP login functionality while optionally syncing user data into a local MySQL database for session-based authentication.

Key features of this project include:

- LDAP Authentication: Users can log in using credentials stored in an LDAP directory (OpenLDAP, Active Directory, or other LDAP servers).

- Local User Sync: LDAP users are optionally cached in a local database to leverage Laravel’s built-in authentication and session management.

- Secure Session Management: After successful login, Laravel handles user sessions securely, including logout functionality.

- Modern Dashboard: A responsive, Tailwind CSS-styled dashboard displays a welcome message and provides placeholders for additional features like profile and settings.

- Enterprise-Ready Workflow: Ideal for organizations or developers looking to integrate LDAP-based authentication while maintaining Laravel’s familiar authentication structure.

- Extensible Architecture: The project uses separate models for LDAP (LdapUser) and local Eloquent authentication (LocalLdapUser), making it easy to extend or modify LDAP-related logic without affecting the local user system.

---

## Prerequisites

- PHP >= 8.1

- XAMPP (Apache + MySQL)

- Composer

- LDAP server (Active Directory, OpenLDAP, etc.)

---

## Step 1: Create Laravel 12 Project

```bash
composer create-project laravel/laravel PHP_Laravel12_LdapRecord "12.*"
cd PHP_Laravel12_LdapRecord
```

---

## Step 2: Configure Environment

Update .env:

```.env
APP_NAME=PHP_Laravel12_LdapRecord
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ldaprecord_db
DB_USERNAME=root
DB_PASSWORD=
```

Create the database ldaprecord_db in phpMyAdmin.

Or

run migration command:

```bash
php artisan migrate
```
---

## Step 3: Install LdapRecord-Laravel

```bash
composer require directorytree/ldaprecord-laravel
```
Note: Ensure the PHP LDAP extension is enabled in php.ini (extension=ldap) and Apache restarted.

---

## Step 4: Create Config Files Manually

Since vendor:publish no longer works in v3:

### config/ldap.php

```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | LDAP Connections
    |--------------------------------------------------------------------------
    */
'connections' => [
    'default' => [
        'hosts' => ['ldap.forumsys.com'],           // LDAP server
        'username' => 'cn=read-only-admin,dc=example,dc=com',  // Bind DN
        'password' => 'password',                   // Bind password
        'port' => 389,
        'base_dn' => 'dc=example,dc=com',          // Base DN for searching users
        'timeout' => 5,
        'use_ssl' => false,
        'use_tls' => false,
    ],
],

];
```

### config/ldap_auth.php

```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | LDAP Authentication
    |--------------------------------------------------------------------------
    */

    'username_attribute' => 'uid',
    'login_fallback' => false,
    'bind_user_to_model' => true,

];
```

---

## Step 5: Create LDAP Users Migration

```bash
php artisan make:migration create_ldap_users_table
```

Edit the migration:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('ldap_users', function (Blueprint $table) {
            $table->id();
            $table->string('guid')->unique();
            $table->string('username');
            $table->string('email')->nullable();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('ldap_users');
    }
};
```

Run migration:

```bash
php artisan migrate
```
---

## Step 6: Create Model

### LdapUser Model

```bash
php artisan make:model LdapUser
```

In app/Models/LdapUser.php:

```php
<?php

namespace App\Models;

use LdapRecord\Models\OpenLDAP\User as LdapUserModel;

class LdapUser extends LdapUserModel
{
    protected ?string $connection = 'default';
}
```

### LocalLdapUser Model

```bash
php artisan make:model LocalLdapUser
```

In app/Models/LocalLdapUser.php:

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class LocalLdapUser extends Authenticatable
{
    protected $table = 'ldap_users';

    protected $fillable = [
        'guid',
        'username',
        'email',
    ];

    // Optional: If you don’t have passwords in local DB
    protected $hidden = [
        // 'password',
    ];

    public $timestamps = true;
}
```

---

## Step 7: Update Authentication Guard

In config/auth.php:

```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'ldap_users',
    ],
],

'providers' => [
        'ldap_users' => [
            'driver' => 'eloquent',
            'model' => App\Models\LocalLdapUser::class,
      ],
],
```

---

## Step 8: Create Login Controller

```bash
php artisan make:controller Auth/LdapLoginController
```
app/Http/Controllers/Auth/LdapLoginController.php:

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use LdapRecord\Models\OpenLDAP\User as LdapUser; // LDAP model
use LdapRecord\Auth\BindException;
use App\Models\LocalLdapUser; // Local MySQL model

class LdapLoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        // Validate input
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = $request->input('username');
        $password = $request->input('password');

        // Step 1: Find user in LDAP
        $ldapUser = LdapUser::where('uid', $username)->first();

        if (!$ldapUser) {
            return back()->withErrors([
                'username' => 'User not found in LDAP.'
            ]);
        }

        // Step 2: Attempt LDAP bind (check password)
        try {
            $ldapUser->getConnection()->auth()->attempt(
                $ldapUser->getDn(),
                $password
            );

            // Step 3: Sync user to local DB (Eloquent)
            $localUser = LocalLdapUser::updateOrCreate(
                ['guid' => $ldapUser->getFirstAttribute('entryuuid')], // Unique ID from LDAP
                [
                    'username' => $ldapUser->getFirstAttribute('uid'),
                    'email' => $ldapUser->getFirstAttribute('mail') ?? null,
                ]
            );

            // Step 4: Log in via Laravel session
            Auth::login($localUser);

            return redirect()->intended('dashboard');

        } catch (BindException $e) {
            return back()->withErrors([
                'username' => 'Invalid credentials.'
            ]);
        }
    }

    /**
     * Logout user
     */
    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
```

---

## Step 9: Create Blade files

### login.blade.php

resources/views/auth/login.blade.php:

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LDAP Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="bg-white shadow-lg rounded-xl w-full max-w-md p-8">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">LDAP Login</h2>

        @if($errors->any())
            <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4 text-center">
                {{ $errors->first() }}
            </div>
        @endif

        @if(session('success'))
            <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4 text-center">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('ldap.login') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-gray-700 mb-1" for="username">Username</label>
                <input type="text" name="username" id="username" placeholder="Enter your username"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required>
            </div>

            <div>
                <label class="block text-gray-700 mb-1" for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Enter your password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" required>
            </div>

            <button type="submit" 
                class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 rounded-lg transition duration-200">
                Login
            </button>
        </form>

        <p class="mt-6 text-center text-gray-500 text-sm">
            &copy; {{ date('Y') }} LDAP Authentication System
        </p>
    </div>

</body>
</html>
```

### dashboard.blade.php

resources/views/dashboard.blade.php:

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-blue-600 text-white py-4 shadow-md">
            <div class="container mx-auto flex justify-between items-center px-4">
                <h1 class="text-2xl font-bold">Dashboard</h1>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                        Logout
                    </button>
                </form>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 container mx-auto px-4 py-8">
            <div class="bg-white shadow-lg rounded-lg p-8 text-center">
                <h2 class="text-3xl font-semibold mb-4">
                    Welcome, {{ auth()->user()->username }}!
                </h2>
                <p class="text-gray-600 mb-6">
                    You are successfully logged in. Here is your dashboard.
                </p>
                <div class="flex justify-center gap-4">
                    <a href="#" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded">
                        View Profile
                    </a>
                    <a href="#" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded">
                        Settings
                    </a>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-gray-200 text-gray-700 py-4 mt-auto">
            <div class="container mx-auto text-center">
                &copy; {{ date('Y') }} My Laravel App. All rights reserved.
            </div>
        </footer>
    </div>
</body>
</html>
```

---

## Step 10: Define Routes

routes/web.php:

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LdapLoginController;


Route::get('login', [LdapLoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LdapLoginController::class, 'login'])->name('ldap.login');
Route::post('logout', [LdapLoginController::class, 'logout'])->name('logout');

Route::get('dashboard', function () {
    return view('dashboard');
})->middleware('auth');
```

---

## Step 11: Test Application

Run the server:

```bash
php artisan serve
```
Visit http://localhost:8000/login

enter your LDAP username/password, and you should see the dashboard.

use below link to know username and password

```
https://www.forumsys.com/2022/05/10/online-ldap-test-server/
```
Use the LDAP test credentials

Since you’re using ldap.forumsys.com, the LDAP test users are predefined. For example:

| Username (`uid`) | Password |
| ---------------- | -------- |
| einstein         | password |
| galileo          | password |
| tesla            | password |


These usernames come from the Forumsys public LDAP server.

---

## Output

### LDAP Login

<img width="1918" height="1026" alt="Screenshot 2026-03-05 100421" src="https://github.com/user-attachments/assets/08843fbe-294c-4561-a0aa-bcd2f328b346" />

### Dashboard

<img width="1919" height="1029" alt="Screenshot 2026-03-05 100453" src="https://github.com/user-attachments/assets/cfb4e00c-58df-41bd-a8b9-a86e88a5d0c2" />

---

## Project Structure

```
PHP_Laravel12_LdapRecord/
│
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── Auth/
│   │           └── LdapLoginController.php
│   │
│   └── Models/
│       ├── LdapUser.php                 <-- LDAP model
│       └── LocalLdapUser.php            <-- Local Eloquent model for Laravel auth
│
├── config/
│   ├── auth.php                          <-- Laravel authentication config
│   ├── ldap.php                           <-- LDAP connection config
│   └── ldap_auth.php                      <-- LDAP auth rules
│
├── database/
│   └── migrations/
│       └── create_ldap_users_table.php  <-- Migration for local LDAP users
│
├── resources/
│   └── views/
│       ├── auth/
│       │   └── login.blade.php           <-- Login form
│       └── dashboard.blade.php           <-- Dashboard page
│
├── routes/
│   └── web.php                            <-- Routes file
│
├── .env                                   <-- Environment variables (DB, LDAP)
├── composer.json                          <-- Composer packages
└── README.md                               <-- Project README
```

---

Your PHP_Laravel12_LdapRecord Project is now ready!
