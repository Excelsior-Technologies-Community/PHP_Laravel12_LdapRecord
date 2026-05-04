<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LocalLdapUser;

class UserController extends Controller
{
    // Show users with search + pagination
    public function index(Request $request)
    {
        $search = $request->search;

        $users = LocalLdapUser::when($search, function ($query) use ($search) {
            $query->where('username', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%");
        })
            ->orderBy('id', 'asc') //  ASC ORDER
            ->paginate(5);

        return view('users.index', compact('users', 'search'));
    }
    // Delete user
    public function destroy($id)
    {
        $user = LocalLdapUser::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', 'User deleted successfully!');
    }
}