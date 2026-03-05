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