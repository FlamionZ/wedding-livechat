<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Wedding Live Chat') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-emerald-900 via-emerald-800 to-emerald-700 text-emerald-50">
    <div class="max-w-6xl mx-auto px-4 py-12 space-y-10">
        <header class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="space-y-2">
                <p class="text-emerald-200 uppercase tracking-[0.2em] text-xs">Wedding Live Chat</p>
                <h1 class="text-3xl lg:text-4xl font-bold leading-tight">Temui tamu secara langsung, biarkan pesan mereka tampil setelah disetujui.</h1>
                <p class="text-emerald-100/80 max-w-2xl">Tamu tidak perlu login. Cukup tulis nama dan pesan, lalu admin akan menyaring sebelum ditampilkan ke layar live.</p>
                <div class="flex gap-2 text-sm text-emerald-100">
                    <span class="px-3 py-1 rounded-full bg-emerald-700/50 border border-emerald-400/40">Realtime by Laravel Reverb</span>
                    <span class="px-3 py-1 rounded-full bg-white/10 border border-white/20">Approval-first workflow</span>
                </div>
            </div>
            <div class="bg-white/10 border border-white/15 backdrop-blur rounded-2xl px-6 py-4 text-right shadow-lg">
                <p class="text-sm text-emerald-50">Admin panel:</p>
                <p class="text-lg font-semibold">{{ route('admin.dashboard') }}</p>
                <p class="text-xs text-emerald-100/70">Login diperlukan untuk moderasi.</p>
            </div>
        </header>

        <div id="chat-app" data-messages='@json($messages)' class="grid gap-6 lg:grid-cols-3">
            <section class="lg:col-span-2 bg-white/10 border border-white/15 backdrop-blur rounded-2xl p-6 shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-emerald-100">Live chat</p>
                        <h2 class="text-2xl font-semibold">Pesan yang disetujui</h2>
                    </div>
                    <span class="text-xs px-3 py-1 rounded-full bg-white/20 border border-white/30">Auto-update realtime</span>
                </div>
                <div class="bg-white/10 border border-white/15 rounded-xl p-4 h-[520px] overflow-y-auto space-y-3" data-chat-scroll>
                    <p data-empty-chat class="text-sm text-emerald-100/80 {{ $messages->isNotEmpty() ? 'hidden' : '' }}">Belum ada pesan tampil. Kirim pesan pertamamu!</p>
                    <ul data-chat-list class="space-y-3">
                        @foreach ($messages as $message)
                            <li class="bg-white/90 backdrop-blur shadow-md rounded-xl px-4 py-3 border border-emerald-50">
                                <div class="flex items-center justify-between mb-1">
                                    <div class="text-sm font-semibold text-emerald-900">{{ $message->username }}</div>
                                    <div class="text-xs text-emerald-600">{{ $message->approved_at?->format('H:i') ?? $message->created_at->format('H:i') }}</div>
                                </div>
                                <p class="text-sm text-emerald-950 leading-relaxed">{{ $message->content }}</p>
                                @if ($message->image_path)
                                    <div class="mt-2">
                                        <img src="{{ asset($message->image_path) }}" alt="Foto ucapan" class="max-h-60 rounded-lg border border-emerald-200 shadow-sm" loading="lazy">
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </section>

            <section class="bg-white text-emerald-950 rounded-2xl shadow-xl border border-emerald-100 p-6 space-y-4">
                @if (session('status'))
                    <div class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">
                        {{ session('status') }}
                    </div>
                @endif
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-emerald-500">Kirim ucapan</p>
                    <h3 class="text-xl font-semibold">Tulis pesan untuk pengantin</h3>
                    <p class="text-sm text-emerald-700">Pesan akan ditinjau admin sebelum tayang.</p>
                </div>
                <form action="{{ route('messages.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-emerald-800" for="username">Nama atau panggilan</label>
                        <input id="username" name="username" type="text" required maxlength="80" value="{{ old('username') }}" class="w-full rounded-lg border border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500 text-emerald-900" placeholder="Contoh: Sahabat SMP">
                        @error('username')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-emerald-800" for="content">Pesan</label>
                        <textarea id="content" name="content" rows="4" maxlength="500" required class="w-full rounded-lg border border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500 text-emerald-900" placeholder="Tulis doa, ucapan, atau kesan">{{ old('content') }}</textarea>
                        @error('content')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-700 text-white font-semibold py-3 shadow-lg shadow-emerald-800/30 hover:bg-emerald-800 transition">
                        Kirim pesan
                    </button>
                    <p class="text-xs text-emerald-600">Keamanan: pesan Anda tidak langsung tampil, admin akan menyetujui terlebih dahulu.</p>
                </form>
            </section>
        </div>
    </div>
</body>
</html>
