<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Wedding Live Chat') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-rose-100 via-pink-50 to-purple-100">
    <!-- Header with Nickname -->
    <header class="bg-white/80 backdrop-blur-lg border-b border-rose-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('chat.index') }}" class="text-gray-600 hover:text-gray-900 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Nickname</p>
                    <p class="text-lg font-bold text-transparent bg-clip-text bg-gradient-to-r from-rose-600 to-purple-600">
                        {{ session('nickname', 'Guest') }}
                    </p>
                </div>
            </div>
            
            <button onclick="window.location.reload()" class="flex items-center space-x-2 px-4 py-2 bg-gradient-to-r from-rose-500 to-pink-500 hover:from-rose-600 hover:to-pink-600 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span>Refresh</span>
            </button>
        </div>
    </header>

    <!-- Disclaimer Banner -->
    <div class="bg-gradient-to-r from-rose-500 via-pink-500 to-purple-500 text-white py-3 px-4 text-center text-sm shadow-lg">
        <div class="max-w-6xl mx-auto flex items-center justify-center space-x-2">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <p>Saya bertanggung jawab atas setiap teks dan gambar yang saya kirimkan melalui platform ini.</p>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-6xl mx-auto px-4 py-8">
        <!-- Info Banner -->
        @if (session('pending_messages_count') && session('pending_messages_count') > 0)
        <div class="mb-6 bg-amber-50 border border-amber-200 rounded-2xl p-4 flex items-start space-x-3">
            <svg class="w-5 h-5 text-amber-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <div class="text-sm text-amber-800">
                <p class="font-semibold">Anda memiliki {{ session('pending_messages_count') }} pesan menunggu review</p>
                <p class="text-amber-700">(Pesan akan tampil setelah disetujui admin)</p>
            </div>
        </div>
        @endif

        <!-- Success Message -->
        @if (session('status'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-2xl p-4 flex items-center space-x-3">
            <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <p class="text-sm text-green-800 font-medium">{{ session('status') }}</p>
        </div>
        @endif

        <div id="chat-app" data-messages='@json($messages)' class="grid lg:grid-cols-3 gap-6">
            <!-- Chat Messages Section -->
            <section class="lg:col-span-2 bg-white/80 backdrop-blur-lg rounded-3xl shadow-xl border border-rose-200 overflow-hidden">
                <div class="bg-gradient-to-r from-rose-500 via-pink-500 to-purple-500 px-6 py-4">
                    <div class="flex items-center justify-between text-white">
                        <div>
                            <p class="text-xs uppercase tracking-wider opacity-90">Live Chat</p>
                            <h2 class="text-xl font-bold">Pesan yang Disetujui</h2>
                        </div>
                        <span class="px-3 py-1 bg-white/20 backdrop-blur rounded-full text-xs font-semibold">Auto-update</span>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="bg-gradient-to-br from-rose-50 to-pink-50 rounded-2xl p-4 h-[520px] overflow-y-auto space-y-3 border border-rose-100" data-chat-scroll>
                        <p data-empty-chat class="text-sm text-gray-500 text-center py-8 {{ $messages->isNotEmpty() ? 'hidden' : '' }}">
                            💌 Belum ada pesan tampil. Kirim pesan pertama Anda!
                        </p>
                        <ul data-chat-list class="space-y-3">
                            @foreach ($messages as $message)
                                <li class="bg-white rounded-2xl px-5 py-4 shadow-md border border-rose-100 hover:shadow-lg transition-shadow">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-rose-400 to-pink-400 flex items-center justify-center text-white font-bold text-sm">
                                                {{ strtoupper(substr($message->username, 0, 1)) }}
                                            </div>
                                            <span class="text-sm font-bold text-gray-900">{{ $message->username }}</span>
                                        </div>
                                        <div class="text-xs text-gray-500">{{ $message->approved_at?->format('H:i') ?? $message->created_at->format('H:i') }}</div>
                                    </div>
                                    <p class="text-sm text-gray-700 leading-relaxed pl-10">{{ $message->content }}</p>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Send Message Form Section -->
            <section class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-xl border border-rose-200 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-500 via-pink-500 to-rose-500 px-6 py-4">
                    <p class="text-xs uppercase tracking-wider text-white/90">Kirim Ucapan</p>
                    <h3 class="text-xl font-bold text-white">Tulis Pesan</h3>
                </div>
                
                <form action="{{ route('messages.store') }}" method="POST" class="p-6 space-y-5" id="messageForm">
                    @csrf
                    <input type="hidden" name="username" value="{{ session('nickname', 'Guest') }}">
                    
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-800" for="content">
                            Pesan Anda
                        </label>
                        <textarea 
                            id="content" 
                            name="content" 
                            rows="6" 
                            maxlength="500" 
                            required
                            placeholder="Tulis doa, ucapan, atau kesan Anda untuk pengantin..."
                            class="w-full px-4 py-3 bg-white border-2 border-rose-200 rounded-2xl focus:border-rose-500 focus:ring-4 focus:ring-rose-100 transition-all outline-none text-gray-800 placeholder-gray-400 resize-none"
                        >{{ old('content') }}</textarea>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-gray-500">
                                @error('content')
                                    <span class="text-red-600">{{ $message }}</span>
                                @else
                                    Max 500 karakter
                                @enderror
                            </span>
                            <span class="text-gray-400" id="charCount">0/500</span>
                        </div>
                    </div>

                    <!-- Disclaimer Modal Trigger (before submit) -->
                    <div class="bg-rose-50 border border-rose-200 rounded-2xl p-4">
                        <div class="flex items-start space-x-2 text-xs text-gray-600">
                            <svg class="w-4 h-4 mt-0.5 text-rose-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <p class="leading-relaxed">
                                <strong>Catatan:</strong> Pesan Anda akan ditinjau admin terlebih dahulu sebelum tampil di layar live chat.
                            </p>
                        </div>
                    </div>

                    <button 
                        type="submit"
                        class="w-full bg-gradient-to-r from-rose-500 via-pink-500 to-purple-500 hover:from-rose-600 hover:via-pink-600 hover:to-purple-600 text-white font-bold py-4 rounded-2xl shadow-lg hover:shadow-xl transform hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center space-x-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        <span>Kirim Pesan</span>
                    </button>
                </form>
            </section>
        </div>
    </main>

    <!-- Success Modal (will be shown by JavaScript) -->
    <div id="successModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-3xl p-8 max-w-md mx-4 shadow-2xl transform transition-all">
            <div class="text-center space-y-4">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900">Berhasil!</h3>
                <p class="text-gray-600">Pesan Anda telah ditambahkan ke antrian review dan akan segera ditinjau oleh admin.</p>
                <button onclick="closeSuccessModal()" class="w-full mt-4 px-6 py-3 bg-gradient-to-r from-rose-500 to-pink-500 hover:from-rose-600 hover:to-pink-600 text-white font-semibold rounded-xl shadow-md transition-all">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        // Character counter
        const textarea = document.getElementById('content');
        const charCount = document.getElementById('charCount');
        
        textarea.addEventListener('input', function() {
            const length = this.value.length;
            charCount.textContent = `${length}/500`;
            if (length > 450) {
                charCount.classList.add('text-rose-600', 'font-semibold');
            } else {
                charCount.classList.remove('text-rose-600', 'font-semibold');
            }
        });

        // Success modal functions
        function showSuccessModal() {
            document.getElementById('successModal').classList.remove('hidden');
            // Clear textarea
            document.getElementById('content').value = '';
            charCount.textContent = '0/500';
        }

        function closeSuccessModal() {
            document.getElementById('successModal').classList.add('hidden');
        }

        // Show modal if there's a status session
        @if (session('status'))
            setTimeout(() => showSuccessModal(), 300);
        @endif

        // Close modal when clicking outside
        document.getElementById('successModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeSuccessModal();
            }
        });
    </script>
</body>
</html>
