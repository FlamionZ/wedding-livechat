<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard | {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#00c853] text-emerald-950">
    <div class="max-w-6xl mx-auto px-2 sm:px-6 py-4 sm:py-10 space-y-4 sm:space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.25em] text-emerald-900">Dashboard Admin</p>
                <h1 class="text-2xl sm:text-3xl font-bold">Moderasi Pesan Live Chat</h1>
                <p class="text-emerald-900/80 text-xs sm:text-base">Layar hijau (greenscreen) siap untuk kebutuhan multimedia/live streaming.</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="bg-white/80 px-4 py-2 rounded-xl shadow">
                    <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-emerald-900/70">Admin</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-900 text-white text-sm font-semibold shadow hover:bg-emerald-950">Logout</button>
                </form>
            </div>
        </div>

        @if (session('status'))
            <div class="bg-white/80 border border-emerald-200 rounded-xl px-4 py-3 text-emerald-900 shadow">{{ session('status') }}</div>
        @endif

        <div class="grid lg:grid-cols-3 gap-4 sm:gap-6">
            <!-- Greenscreen Area for Live Streaming & Feed -->
            <section class="col-span-1 lg:col-span-1 flex flex-col items-center justify-start rounded-2xl shadow-xl border border-green-700 bg-[#00FF00] min-h-[320px] sm:min-h-[400px] mb-4 lg:mb-0 p-0">
                <div class="w-full flex flex-col items-center justify-center p-2">
                    <span class="text-white text-lg sm:text-2xl font-bold drop-shadow-lg">Live Streaming Area</span>
                    <span class="text-white text-sm sm:text-base font-semibold drop-shadow-lg">(Greenscreen)</span>
                    <span class="text-white/80 text-xs mt-2 mb-2">Gunakan area ini untuk OBS/vMix, background bisa diganti saat live.</span>
                </div>
                <!-- Feed Pesan Live di dalam Greenscreen -->
                <div class="w-full flex-1 flex flex-col items-center justify-start">
                    <div class="w-full max-w-xs sm:max-w-sm md:max-w-md mx-auto bg-white/90 rounded-xl shadow border border-emerald-200 p-2 sm:p-4 mt-2" style="max-height:340px; min-height:120px; overflow-y:auto;">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs uppercase tracking-[0.2em] text-emerald-600">Live Feed</span>
                            <span class="px-2 py-0.5 text-xs rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">On Air</span>
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

            <section class="bg-white/95 rounded-2xl shadow-xl border border-emerald-100 p-3 sm:p-6 flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-emerald-600">Right Screen</p>
                        <h2 class="text-2xl font-semibold text-emerald-900">Antrian Pending</h2>
                    </div>
                    <span class="px-3 py-1 text-xs rounded-full bg-amber-50 text-amber-700 border border-amber-200">Review</span>
                </div>
                <div class="flex-1 overflow-y-auto space-y-3" data-pending-list>
                    @forelse ($pendingMessages as $message)
                        <article data-message-card="{{ $message->id }}" data-id="{{ $message->id }}" class="bg-white shadow-lg rounded-xl border border-emerald-100 p-2 sm:p-4 space-y-2 sm:space-y-3">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-xs uppercase tracking-wide text-emerald-700">{{ $message->created_at->format('H:i') }}</p>
                                    <p class="text-base sm:text-lg font-semibold text-emerald-900">{{ $message->username }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full bg-amber-100 text-amber-800">Pending</span>
                            </div>
                            <p class="text-xs sm:text-sm text-emerald-950 leading-relaxed">{{ $message->content }}</p>
                            @if ($message->image_path)
                                <div class="mt-2">
                                    <img src="{{ asset($message->image_path) }}" alt="Foto ucapan" class="max-h-60 rounded-lg border border-emerald-200 shadow-sm" loading="lazy">
                                </div>
                            @endif
                            <div class="flex gap-2">
                                <form data-action="approve" data-message="{{ $message->id }}" action="{{ route('admin.messages.approve', $message) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2 sm:px-3 py-1.5 sm:py-2 rounded-lg bg-emerald-600 text-white text-xs sm:text-sm font-semibold shadow hover:bg-emerald-700">Setujui</button>
                                </form>
                                <form data-action="reject" data-message="{{ $message->id }}" action="{{ route('admin.messages.reject', $message) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2 sm:px-3 py-1.5 sm:py-2 rounded-lg bg-red-50 text-red-700 border border-red-200 text-xs sm:text-sm font-semibold hover:bg-red-100">Tolak</button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <p class="text-sm text-emerald-800">Belum ada pesan baru.</p>
                    @endforelse
                    <p data-empty-pending class="text-sm text-emerald-800 {{ $pendingMessages->isEmpty() ? '' : 'hidden' }}">Belum ada pesan baru.</p>
                </div>
            </section>
        </div>
    </div>
</body>
</html>
