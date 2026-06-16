<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans">

    <div class="min-h-screen flex flex-col">

        <header class="bg-blue-600 text-white py-4 shadow-md">
            <div class="container mx-auto flex justify-between items-center px-4">
                <h1 class="text-2xl font-bold">LDAP Admin Dashboard</h1>

                <div class="flex items-center gap-4">
                    <span class="font-semibold">
                        👋 {{ auth()->user()->username }}
                    </span>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded transition">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <main class="flex-1 container mx-auto px-4 py-8">

            @if(session('success'))
                <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-6 text-center border border-green-200">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-lg rounded-xl p-8 text-center max-w-3xl mx-auto">

                <h2 class="text-3xl font-semibold mb-4 text-gray-800">
                    Welcome, {{ auth()->user()->username }}!
                </h2>

                <p class="text-gray-600 mb-8">
                    You are successfully logged in to the LDAP Authentication System.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <a href="{{ route('users.index') }}"
                        class="flex items-center justify-center gap-2 bg-purple-500 hover:bg-purple-600 text-white px-6 py-3 rounded-lg transition">
                        👥 Manage Users
                    </a>

                    <a href="{{ url('audit-logs') }}" 
                        class="flex items-center justify-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-3 rounded-lg transition">
                        📜 View Audit Logs
                    </a>

                    <form action="{{ route('users.sync') }}" method="POST" class="col-span-1 md:col-span-2">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition">
                            🔄 Sync LDAP Users to Database
                        </button>
                    </form>

                </div>
            </div>

        </main>

        <footer class="bg-gray-200 text-gray-700 py-4 mt-auto">
            <div class="container mx-auto text-center">
                &copy; {{ date('Y') }} LDAP Laravel App. All rights reserved.
            </div>
        </footer>

    </div>

</body>
</html>