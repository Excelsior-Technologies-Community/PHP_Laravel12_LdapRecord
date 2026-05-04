<!DOCTYPE html>
<html>

<head>
    <title>Users Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 min-h-screen p-6">

    <div class="max-w-6xl mx-auto">

        <!-- HEADER CARD -->
        <div class="bg-white shadow-md rounded-xl p-6 mb-6 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">👥 Users Management</h2>
                <p class="text-sm text-gray-500">Manage LDAP & system users easily</p>
            </div>
        </div>

        <!-- SUCCESS ALERT -->
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- SEARCH CARD -->
        <div class="bg-white shadow-sm rounded-xl p-4 mb-5">
            <form method="GET" action="{{ route('users.index') }}" class="flex gap-3">

                <input type="text" name="search" value="{{ $search }}" placeholder="🔍 Search by username or email..."
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">

                <button class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg transition">
                    Search
                </button>

            </form>
        </div>

        <!-- TABLE CARD -->
        <div class="bg-white shadow-md rounded-xl overflow-hidden">

            <table class="w-full text-sm text-left">

                <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                    <tr>
                        <th class="p-4">ID</th>
                        <th class="p-4">Username</th>
                        <th class="p-4">Email</th>
                        <th class="p-4 text-center">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y">

                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 transition">

                            <td class="p-4 font-medium text-gray-600">
                                #{{ $user->id }}
                            </td>

                            <td class="p-4">
                                <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-semibold">
                                    {{ $user->username }}
                                </span>
                            </td>

                            <td class="p-4 text-gray-600">
                                {{ $user->email }}
                            </td>

                            <td class="p-4 text-center">

                                <form method="POST" action="{{ route('users.delete', $user->id) }}">
                                    @csrf
                                    @method('DELETE')

                                    <button onclick="return confirm('Are you sure you want to delete this user?')"
                                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-1 rounded-lg text-sm transition">
                                        Delete
                                    </button>

                                </form>

                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-6 text-center text-gray-500">
                                😕 No users found
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>

        </div>

        <!-- PAGINATION -->
        <div class="mt-6">
            <div class="bg-white p-4 rounded-xl shadow-sm">
                {{ $users->links() }}
            </div>
        </div>

    </div>

</body>

</html>