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