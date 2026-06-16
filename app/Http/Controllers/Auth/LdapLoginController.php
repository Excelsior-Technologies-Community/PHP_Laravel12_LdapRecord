<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use LdapRecord\Models\OpenLDAP\User as LdapUser;
use LdapRecord\Auth\BindException;
use App\Models\LocalLdapUser;
use App\Models\LdapAuditLog;
use LdapRecord\Container;
use Exception;

class LdapLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'domain' => 'required|string',
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = $request->input('username');
        $password = $request->input('password');
        $domain = $request->input('domain');

        try {
            Container::setDefaultConnection($domain);
            $connection = Container::getConnection($domain);
            $connection->connect();

            $ldapUser = LdapUser::where('uid', $username)->first();

            if (!$ldapUser) {
                $this->logAttempt($username, $request->ip(), 'failed');
                return back()->withErrors(['username' => 'User not found in LDAP.']);
            }

            $ldapUser->getConnection()->auth()->attempt(
                $ldapUser->getDn(),
                $password
            );

            $localUser = LocalLdapUser::updateOrCreate(
                ['guid' => $ldapUser->getFirstAttribute('entryuuid')],
                [
                    'username' => $ldapUser->getFirstAttribute('uid'),
                    'email' => $ldapUser->getFirstAttribute('mail') ?? null,
                ]
            );

            Auth::login($localUser);
            
            $this->logAttempt($username, $request->ip(), 'success');

            return redirect()->intended('dashboard');

        } catch (BindException $e) {
            $this->logAttempt($username, $request->ip(), 'failed');
            return back()->withErrors(['username' => 'Invalid credentials.']);
        } catch (Exception $e) {
            return back()->withErrors(['username' => 'LDAP Server Error: ' . $e->getMessage()]);
        }
    }

    private function logAttempt($username, $ip, $status)
    {
        LdapAuditLog::create([
            'username' => $username,
            'ip_address' => $ip,
            'status' => $status,
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}