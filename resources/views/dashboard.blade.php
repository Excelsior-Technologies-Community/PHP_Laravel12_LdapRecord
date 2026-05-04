<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans">

    <div class="min-h-screen flex flex-col">

        <!-- HEADER -->
        <header class="bg-blue-600 text-white py-4 shadow-md">
            <div class="container mx-auto flex justify-between items-center px-4">
                <h1 class="text-2xl font-bold">Dashboard</h1>

                <div class="flex items-center gap-4">
                    <span class="font-semibold">
                        👋 {{ auth()->user()->username }}
                    </span>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- MAIN -->
        <main class="flex-1 container mx-auto px-4 py-8">

            <!-- SUCCESS ALERT -->
            @if(session('success'))
                <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4 text-center">
                    {{ session('success') }}
                </div>
            @endif

            <!-- DASHBOARD CARD -->
            <div class="bg-white shadow-lg rounded-lg p-8 text-center">

                <h2 class="text-3xl font-semibold mb-4">
                    Welcome, {{ auth()->user()->username }}!
                </h2>

                <p class="text-gray-600 mb-6">
                    You are successfully logged in to LDAP Authentication System.
                </p>

                <!-- ACTION BUTTONS -->
                <div class="flex justify-center gap-4 flex-wrap">

                    <a href="#" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded">
                        👤 View Profile
                    </a>

                    <a href="#" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded">
                        ⚙ Settings
                    </a>

                    <!-- NEW: USERS MANAGEMENT -->
                    <a href="{{ route('users.index') }}"
                        class="bg-purple-500 hover:bg-purple-600 text-white px-6 py-2 rounded">
                        👥 Manage Users
                    </a>

                </div>
            </div>

        </main>

        <!-- FOOTER -->
        <footer class="bg-gray-200 text-gray-700 py-4 mt-auto">
            <div class="container mx-auto text-center">
                &copy; {{ date('Y') }} LDAP Laravel App. All rights reserved.
            </div>
        </footer>

    </div>

</body>

</html>