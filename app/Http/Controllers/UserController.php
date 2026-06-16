<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LocalLdapUser;
use LdapRecord\Models\OpenLDAP\User as LdapUser;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $users = LocalLdapUser::when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%");
        })
            ->orderBy('id', 'asc')
            ->paginate(5);

        return view('users.index', compact('users', 'search'));
    }

    public function syncUsers()
    {
        try {
            $ldapUsers = LdapUser::get();

            foreach ($ldapUsers as $ldapUser) {
                LocalLdapUser::updateOrCreate(
                    ['guid' => $ldapUser->getFirstAttribute('entryuuid')],
                    [
                        'username' => $ldapUser->getFirstAttribute('uid'),
                        'name' => $ldapUser->getFirstAttribute('cn'),
                        'email' => $ldapUser->getFirstAttribute('mail')
                    ]
                );
            }

            return redirect()->back()->with('success', 'Users synced successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Sync failed: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $user = LocalLdapUser::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', 'User deleted successfully!');
    }
}