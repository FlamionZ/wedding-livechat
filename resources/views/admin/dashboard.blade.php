<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-timezone" content="{{ config('app.timezone') }}">
    <title>Admin Dashboard | {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }
        .animate-slideIn { animation: slideIn 0.4s ease-out; }
        .animate-pulse-slow { animation: pulse 3s ease-in-out infinite; }
    </style>
</head>
<body class="min-h-screen text-gray-900" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #4facfe 75%, #00f2fe 100%);">
    <div class="max-w-7xl mx-auto px-2 sm:px-6 py-4 sm:py-10 space-y-4 sm:space-y-6">
        <div class="bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-4 sm:p-6 animate-slideIn">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center shadow-lg">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-[0.25em] font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">Dashboard Admin</p>
                            <h1 class="text-2xl sm:text-3xl font-black bg-gradient-to-r from-purple-600 via-pink-600 to-blue-600 bg-clip-text text-transparent">Moderasi Pesan Live Chat</h1>
                        </div>
                    </div>
                    <p class="text-gray-600 text-xs sm:text-sm font-medium ml-15">Layar hijau (greenscreen) siap untuk kebutuhan multimedia/live streaming.</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 px-4 py-3 rounded-2xl shadow-lg border-2 border-purple-200">
                        <p class="text-sm font-bold text-purple-900">{{ auth()->user()->name }}</p>
                        <p class="text-xs font-semibold text-purple-600">👑 Admin</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="px-5 py-3 rounded-2xl bg-gradient-to-r from-red-500 to-pink-500 text-white text-sm font-bold shadow-xl hover:shadow-2xl hover:scale-105 transform transition-all duration-200">Logout</button>
                    </form>
                </div>
            </div>
        </div>

        @if (session('status'))
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-300 rounded-2xl px-5 py-4 shadow-xl animate-slideIn">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-green-500 flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <p class="text-green-900 font-bold text-sm">{{ session('status') }}</p>
                </div>
            </div>
        @endif

        <div class="bg-white/95 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-4 sm:p-5 animate-slideIn">
            <div class="flex flex-wrap gap-3 items-center justify-between">
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.messages.download.chats') }}" class="group px-5 py-3 rounded-2xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm font-bold shadow-lg hover:shadow-2xl hover:scale-105 transform transition-all duration-200 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                        </svg>
                        <span>Download Chat</span>
                    </a>
                    <a href="{{ route('admin.messages.download.photos') }}" class="group px-5 py-3 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white text-sm font-bold shadow-lg hover:shadow-2xl hover:scale-105 transform transition-all duration-200 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>Download Foto</span>
                    </a>
                </div>
                <div class="flex gap-2">
                    <button id="btnHorizontal" onclick="setLayoutHorizontal()" class="px-4 py-3 rounded-2xl bg-gradient-to-r from-purple-500 to-pink-500 text-white text-sm font-bold shadow-lg hover:shadow-xl hover:scale-105 transform transition-all duration-200 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="5" rx="1" stroke-width="2"/>
                            <rect x="3" y="15" width="18" height="5" rx="1" stroke-width="2"/>
                        </svg>
                        <span>Horizontal</span>
                    </button>
                    <button id="btnVertical" onclick="setLayoutVertical()" class="px-4 py-3 rounded-2xl bg-white text-gray-700 text-sm font-bold shadow-lg hover:shadow-xl hover:scale-105 transform transition-all duration-200 flex items-center gap-2 border-2 border-gray-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect x="4" y="3" width="5" height="18" rx="1" stroke-width="2"/>
                            <rect x="15" y="3" width="5" height="18" rx="1" stroke-width="2"/>
                        </svg>
                        <span>Vertikal</span>
                    </button>
                </div>
            </div>
        </div>

        <div id="dashboardGrid" class="grid lg:grid-cols-3 gap-4 sm:gap-6 transition-all duration-300">
            <!-- Greenscreen Area for Live Streaming & Feed -->
            <section class="col-span-1 lg:col-span-1 flex flex-col items-center justify-start rounded-2xl shadow-xl border border-green-700 bg-[#00FF00] min-h-[320px] sm:min-h-[400px] mb-4 lg:mb-0 p-0">
                <div class="w-full flex flex-col items-center justify-center p-2">
                    <span class="text-white text-lg sm:text-2xl font-bold drop-shadow-lg">Live Streaming Area</span>
                    <span class="text-white text-sm sm:text-base font-semibold drop-shadow-lg">(Greenscreen)</span>
                    <span class="text-white/80 text-xs mt-2 mb-2">Gunakan area ini untuk OBS/vMix, background bisa diganti saat live.</span>
                </div>
                <!-- Feed Pesan Live di dalam Greenscreen -->
                <div class="w-full flex-1 flex flex-col items-center justify-start">
                    <div class="w-full max-w-xs sm:max-w-sm md:max-w-md mx-auto bg-[#00FF00] rounded-xl shadow border border-emerald-200 p-2 sm:p-4 mt-2" style="max-height:340px; min-height:120px; overflow-y:auto;" data-feed-scroll>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs uppercase tracking-[0.2em] text-emerald-600">Live Feed</span>
                            <span class="px-2 py-0.5 text-xs rounded-full bg-yellow-200 text-yellow-900 border border-yellow-300">On Air</span>
                        </div>
                        <ul class="space-y-2" data-feed-list>
                            @forelse ($approvedMessages as $message)
                                <li class="list-none bg-white border border-emerald-100 rounded-xl px-2 sm:px-4 py-2 sm:py-3 shadow-sm">
                                    <div class="flex items-center justify-between mb-1">
                                        <p class="text-sm font-semibold text-emerald-900">{{ $message->username }}</p>
                                        <span class="text-xs text-emerald-600">{{ $message->approved_at?->format('H:i') ?? $message->created_at->format('H:i') }}</span>
                                    </div>
                                    <p class="text-xs sm:text-sm text-emerald-950 leading-relaxed">{{ $message->content }}</p>
                                    @if ($message->image_path)
                                        <div class="mt-2">
                                            <img src="{{ asset($message->image_path) }}" alt="Foto ucapan" class="max-h-60 rounded-lg border border-emerald-200 shadow-sm" loading="lazy">
                                        </div>
                                    @endif
                                </li>
                            @empty
                                <p class="text-sm text-emerald-800">Belum ada pesan disetujui.</p>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </section>
            <div id="admin-dashboard" data-approve-url="{{ route('admin.messages.approve', '__id__') }}" data-reject-url="{{ route('admin.messages.reject', '__id__') }}" class="col-span-2 grid sm:grid-cols-1 gap-4 sm:gap-6">

            <section class="bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-4 sm:p-6 flex flex-col animate-slideIn">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center shadow-lg animate-pulse-slow">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] font-bold text-orange-600">Review Queue</p>
                            <h2 class="text-2xl font-black bg-gradient-to-r from-amber-600 to-orange-600 bg-clip-text text-transparent">Antrian Pending</h2>
                        </div>
                    </div>
                    <span class="px-4 py-2 text-sm font-bold rounded-2xl bg-gradient-to-r from-yellow-200 to-amber-200 text-amber-900 border-2 border-amber-300 shadow-lg">⏰ Review</span>
                </div>
                <div class="overflow-y-auto space-y-3" style="max-height: 600px;" data-pending-list>
                    @forelse ($pendingMessages as $message)
                        <article data-message-card="{{ $message->id }}" data-id="{{ $message->id }}" class="bg-white shadow-md rounded-xl border border-gray-200 p-4 space-y-3">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-xs text-gray-500">{{ $message->created_at->format('H:i') }}</p>
                                    <p class="text-base font-bold text-gray-900">{{ $message->username }}</p>
                                </div>
                                <span class="px-3 py-1 text-xs font-semibold rounded-lg bg-amber-100 text-amber-800">Pending</span>
                            </div>
                            <p class="text-sm text-gray-700 leading-relaxed">{{ $message->content }}</p>
                            @if ($message->image_path)
                                <div class="mt-2">
                                    <img src="{{ asset($message->image_path) }}" alt="Foto ucapan" class="max-h-60 rounded-lg border border-gray-200 shadow-sm" loading="lazy">
                                </div>
                            @endif
                            <div class="flex gap-2">
                                <form data-action="approve" data-message="{{ $message->id }}" action="{{ route('admin.messages.approve', $message) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-semibold shadow hover:bg-emerald-700">Setujui</button>
                                </form>
                                <form data-action="reject" data-message="{{ $message->id }}" action="{{ route('admin.messages.reject', $message) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 rounded-lg bg-white text-red-600 border border-red-300 text-sm font-semibold hover:bg-red-50">Tolak</button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <div class="text-center py-12">
                            <div class="w-20 h-20 mx-auto mb-4 rounded-3xl bg-gradient-to-br from-purple-100 to-pink-100 flex items-center justify-center">
                                <svg class="w-10 h-10 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                            </div>
                            <p class="text-lg font-bold text-gray-600">Belum ada pesan baru</p>
                            <p class="text-sm text-gray-500 mt-1">Semua pesan sudah ditinjau 🎉</p>
                        </div>
                    @endforelse
                    <p data-empty-pending class="text-center py-12 {{ $pendingMessages->isEmpty() ? '' : 'hidden' }}">
                        <span class="text-lg font-bold text-gray-600">Belum ada pesan baru</span><br>
                        <span class="text-sm text-gray-500">Semua pesan sudah ditinjau 🎉</span>
                    </p>
                </div>
            </section>
        </div>
    </div>

    <script>
        // Load saved layout preference
        const savedLayout = localStorage.getItem('adminLayoutMode') || 'horizontal';
        if (savedLayout === 'vertical') {
            setLayoutVertical(true);
        }

        function setLayoutHorizontal(skipSave = false) {
            const grid = document.getElementById('dashboardGrid');
            const btnH = document.getElementById('btnHorizontal');
            const btnV = document.getElementById('btnVertical');
            
            // Horizontal: 1 column (stacked)
            grid.classList.remove('lg:grid-cols-3');
            grid.classList.add('grid-cols-1');
            
            // Update button styles
            btnH.classList.remove('bg-white', 'text-gray-700', 'border-2', 'border-gray-200');
            btnH.classList.add('bg-gradient-to-r', 'from-purple-500', 'to-pink-500', 'text-white');
            btnV.classList.remove('bg-gradient-to-r', 'from-purple-500', 'to-pink-500', 'text-white');
            btnV.classList.add('bg-white', 'text-gray-700', 'border-2', 'border-gray-200');
            
            if (!skipSave) {
                localStorage.setItem('adminLayoutMode', 'horizontal');
            }
        }

        function setLayoutVertical(skipSave = false) {
            const grid = document.getElementById('dashboardGrid');
            const btnH = document.getElementById('btnHorizontal');
            const btnV = document.getElementById('btnVertical');
            
            // Vertical: 3 columns (side by side)
            grid.classList.remove('grid-cols-1');
            grid.classList.add('lg:grid-cols-3');
            
            // Update button styles
            btnV.classList.remove('bg-white', 'text-gray-700', 'border-2', 'border-gray-200');
            btnV.classList.add('bg-gradient-to-r', 'from-purple-500', 'to-pink-500', 'text-white');
            btnH.classList.remove('bg-gradient-to-r', 'from-purple-500', 'to-pink-500', 'text-white');
            btnH.classList.add('bg-white', 'text-gray-700', 'border-2', 'border-gray-200');
            
            if (!skipSave) {
                localStorage.setItem('adminLayoutMode', 'vertical');
            }
        }
    </script>
</body>
</html>
