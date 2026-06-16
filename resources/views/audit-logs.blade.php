<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Logs</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen p-6">

    <div class="max-w-5xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-3xl font-bold text-gray-800">Audit Logs</h2>
                <p class="text-gray-500">Track all login attempts and system activity.</p>
            </div>
            <a href="{{ url('dashboard') }}" class="bg-gray-800 hover:bg-black text-white px-5 py-2 rounded-lg transition">
                ← Back to Dashboard
            </a>
        </div>

        <div class="bg-white shadow-md rounded-2xl overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 uppercase font-semibold text-xs">
                        <tr>
                            <th class="px-6 py-4">User</th>
                            <th class="px-6 py-4">IP Address</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4">Date & Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($logs as $log)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $log->username }}</td>
                            <td class="px-6 py-4 text-gray-500 font-mono">{{ $log->ip_address }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider 
                                    {{ $log->status == 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $log->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-500">{{ $log->created_at->format('d M Y, H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-400">No logs found yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-100">
                {{ $logs->links() }}
            </div>
        </div>
    </div>

</body>
</html>