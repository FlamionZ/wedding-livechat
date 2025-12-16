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
    <div class="max-w-6xl mx-auto px-6 py-10 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.25em] text-emerald-900">Dashboard Admin</p>
                <h1 class="text-3xl font-bold">Moderasi Pesan Live Chat</h1>
                <p class="text-emerald-900/80">Layar hijau (greenscreen) siap untuk kebutuhan multimedia.</p>
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

        <div id="admin-dashboard" data-approve-url="{{ route('admin.messages.approve', '__id__') }}" data-reject-url="{{ route('admin.messages.reject', '__id__') }}" class="grid lg:grid-cols-2 gap-6">
            <section class="bg-white/90 rounded-2xl shadow-xl border border-emerald-100 p-6 flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-emerald-600">Left Screen</p>
                        <h2 class="text-2xl font-semibold text-emerald-900">Pesan Ditayangkan</h2>
                    </div>
                    <span class="px-3 py-1 text-xs rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Live feed</span>
                </div>
                <div class="flex-1 overflow-y-auto space-y-3 bg-emerald-50/60 border border-emerald-100 rounded-xl p-4" data-feed-list>
                    @forelse ($approvedMessages as $message)
                        <li class="list-none bg-white border border-emerald-100 rounded-xl px-4 py-3 shadow-sm">
                            <div class="flex items-center justify-between mb-1">
                                <p class="text-sm font-semibold text-emerald-900">{{ $message->username }}</p>
                                <span class="text-xs text-emerald-600">{{ $message->approved_at?->format('H:i') ?? $message->created_at->format('H:i') }}</span>
                            </div>
                            <p class="text-sm text-emerald-950 leading-relaxed">{{ $message->content }}</p>
                        </li>
                    @empty
                        <p class="text-sm text-emerald-800">Belum ada pesan disetujui.</p>
                    @endforelse
                </div>
            </section>

            <section class="bg-white/95 rounded-2xl shadow-xl border border-emerald-100 p-6 flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-emerald-600">Right Screen</p>
                        <h2 class="text-2xl font-semibold text-emerald-900">Antrian Pending</h2>
                    </div>
                    <span class="px-3 py-1 text-xs rounded-full bg-amber-50 text-amber-700 border border-amber-200">Review</span>
                </div>
                <div class="flex-1 overflow-y-auto space-y-3" data-pending-list>
                    @forelse ($pendingMessages as $message)
                        <article data-message-card="{{ $message->id }}" data-id="{{ $message->id }}" class="bg-white shadow-lg rounded-xl border border-emerald-100 p-4 space-y-3">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-xs uppercase tracking-wide text-emerald-700">{{ $message->created_at->format('H:i') }}</p>
                                    <p class="text-lg font-semibold text-emerald-900">{{ $message->username }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full bg-amber-100 text-amber-800">Pending</span>
                            </div>
                            <p class="text-sm text-emerald-950 leading-relaxed">{{ $message->content }}</p>
                            <div class="flex gap-2">
                                <form data-action="approve" data-message="{{ $message->id }}" action="{{ route('admin.messages.approve', $message) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-2 rounded-lg bg-emerald-600 text-white text-sm font-semibold shadow hover:bg-emerald-700">Setujui</button>
                                </form>
                                <form data-action="reject" data-message="{{ $message->id }}" action="{{ route('admin.messages.reject', $message) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-2 rounded-lg bg-red-50 text-red-700 border border-red-200 text-sm font-semibold hover:bg-red-100">Tolak</button>
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
