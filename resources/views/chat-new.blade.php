<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Wedding Live Chat') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes shimmer {
            0% { background-position: -1000px 0; }
            100% { background-position: 1000px 0; }
        }
        .animate-slideDown { animation: slideDown 0.5s ease-out; }
        .animate-slideUp { animation: slideUp 0.5s ease-out; }
        .font-display { font-family: 'Playfair Display', serif; }
        .font-sans { font-family: 'Inter', sans-serif; }
        .shimmer {
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            background-size: 200% 100%;
            animation: shimmer 2s infinite;
        }
    </style>
</head>
<body class="font-sans" style="min-height: 100vh; background: linear-gradient(135deg, #ffe4e6 0%, #fce7f3 50%, #f3e8ff 100%);">
    <!-- Premium Header -->
    <header class="bg-white/95 backdrop-blur-xl border-b border-gray-200/50 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4">
            <div class="flex items-center justify-between">
                <!-- Left: Back + Nickname -->
                <div class="flex items-center space-x-4">
                    <a href="{{ route('chat.logout') }}" class="group flex items-center justify-center w-10 h-10 rounded-xl bg-gray-100 hover:bg-red-100 hover:border-red-300 border-2 border-transparent transition-all" title="Kembali & Ganti Nickname">
                        <svg class="w-5 h-5 text-gray-600 group-hover:text-red-600 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-rose-400 to-pink-500 flex items-center justify-center text-white font-bold shadow-lg">
                            {{ strtoupper(substr(session('nickname', 'G'), 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Your Nickname</p>
                            <p class="text-lg font-bold text-gray-900">
                                {{ session('nickname', 'Guest') }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Right: Refresh Button -->
                <button onclick="window.location.reload()" class="group flex items-center space-x-2 px-5 py-2.5 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all" style="background: linear-gradient(90deg, #f43f5e 0%, #ec4899 50%, #a855f7 100%);" onmouseover="this.style.background='linear-gradient(90deg, #e11d48 0%, #db2777 50%, #9333ea 100%)'" onmouseout="this.style.background='linear-gradient(90deg, #f43f5e 0%, #ec4899 50%, #a855f7 100%)'">
                    <svg class="w-5 h-5 group-hover:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span class="hidden sm:inline">Refresh</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Disclaimer Mini Banner -->
    <div class="shadow-lg" style="background: linear-gradient(90deg, #f43f5e 0%, #ec4899 50%, #a855f7 100%);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3">
            <div class="flex items-center justify-center space-x-3 text-white">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm font-medium">Saya bertanggung jawab atas setiap konten yang saya kirimkan</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-8 space-y-6">
        <!-- Alert Messages -->
        @if (session('pending_messages_count') && session('pending_messages_count') > 0)
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 border-l-4 border-amber-500 rounded-2xl p-5 shadow-sm animate-slideDown">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <h4 class="font-bold text-amber-900 text-lg mb-1">📝 {{ session('pending_messages_count') }} Pesan Menunggu Review</h4>
                    <p class="text-sm text-amber-700">Pesan Anda akan tampil setelah disetujui oleh admin.</p>
                </div>
            </div>
        </div>
        @endif

        @if (session('status'))
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 rounded-2xl p-5 shadow-sm animate-slideDown">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-green-500 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <p class="text-green-900 font-bold">{{ session('status') }}</p>
            </div>
        </div>
        @endif

        <!-- Main Grid -->
        <div id="chat-app" data-messages='@json($messages)' class="grid lg:grid-cols-3 gap-6">
            <!-- Chat Messages (Left 2/3) -->
            <section class="lg:col-span-2 space-y-4">
                <!-- Section Header -->
                <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200/50 p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-rose-500 to-pink-500 rounded-2xl flex items-center justify-center shadow-lg">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Live Chat Messages</h2>
                                <p class="text-sm text-gray-500">Pesan yang telah disetujui</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-3 py-1.5 bg-green-100 text-green-800 text-xs font-bold rounded-full">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                            LIVE
                        </span>
                    </div>
                </div>

                <!-- Messages Container -->
                <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200/50 overflow-hidden">
                    <div class="p-6">
                        <div class="bg-gradient-to-br from-rose-50 via-pink-50 to-purple-50 rounded-2xl p-4 h-[580px] overflow-y-auto space-y-4 custom-scrollbar" data-chat-scroll>
                            <p data-empty-chat class="text-center py-16 {{ $messages->isNotEmpty() ? 'hidden' : '' }}">
                                <span class="inline-block text-6xl mb-4">💌</span>
                                <span class="block text-lg text-gray-500 font-medium">Belum ada pesan</span>
                                <span class="block text-sm text-gray-400">Jadilah yang pertama mengirim ucapan!</span>
                            </p>
                            <ul data-chat-list class="space-y-4">
                                @foreach ($messages as $message)
                                    <li class="group bg-white rounded-2xl p-3 sm:p-5 shadow-md hover:shadow-xl border border-gray-100 transition-all duration-300 animate-slideUp">
                                        <div class="flex items-start space-x-4">
                                            <div class="flex-shrink-0">
                                                <div class="w-9 h-9 sm:w-12 sm:h-12 rounded-full bg-gradient-to-br from-rose-400 via-pink-400 to-purple-400 flex items-center justify-center text-white font-bold text-base sm:text-lg shadow-lg group-hover:scale-110 transition-transform">
                                                    {{ strtoupper(substr($message->username, 0, 1)) }}
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between mb-2">
                                                    <span class="text-sm sm:text-base font-bold text-gray-900">{{ $message->username }}</span>
                                                    <span class="text-xs text-gray-500 font-medium">{{ $message->approved_at?->format('H:i') ?? $message->created_at->format('H:i') }}</span>
                                                </div>
                                                <p class="text-xs sm:text-sm text-gray-700 leading-relaxed">{{ $message->content }}</p>
                                                @if ($message->image_path)
                                                    <div class="mt-2">
                                                        <img src="{{ asset($message->image_path) }}" alt="Foto ucapan" class="max-h-60 rounded-lg border border-gray-200 shadow-sm" loading="lazy">
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Send Message Form (Right 1/3) -->
            <section class="space-y-4">
                <!-- Section Header -->
                <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200/50 p-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center shadow-lg">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Kirim Pesan</h3>
                            <p class="text-sm text-gray-500">Tulis ucapan Anda</p>
                        </div>
                    </div>
                </div>

                <!-- Form Card -->
                <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200/50 overflow-hidden">
                    <form action="{{ route('messages.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6" id="messageForm">
                        @csrf
                        <input type="hidden" name="username" value="{{ session('nickname', 'Guest') }}">
                        
                        <div class="space-y-3">
                                                    <!-- Input Gambar -->
                                                    <div class="space-y-1">
                                                        <label for="image" class="block text-base font-black text-gray-900 uppercase tracking-wide">Foto (opsional)</label>
                                                        <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/webp" class="block w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100" />
                                                        <p class="text-xs text-gray-500">Format: JPG, PNG, atau WEBP. Maksimal 1 gambar per pesan.</p>
                                                        @error('image')
                                                            <p class="text-sm text-red-600 flex items-center space-x-1">
                                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                                </svg>
                                                                <span>{{ $message }}</span>
                                                            </p>
                                                        @enderror
                                                    </div>
                            <label class="block text-base font-black text-gray-900 uppercase tracking-wide" for="content">
                                Pesan Anda
                            </label>
                            <div class="relative">
                                <textarea 
                                    id="content" 
                                    name="content" 
                                    rows="7" 
                                    maxlength="500" 
                                    required
                                    placeholder="Tulis doa, ucapan selamat, atau kesan Anda untuk pengantin..."
                                    class="w-full px-4 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:border-rose-500 focus:ring-4 focus:ring-rose-100 focus:bg-white transition-all outline-none text-gray-900 placeholder-gray-400 resize-none text-sm leading-relaxed"
                                >{{ old('content') }}</textarea>
                                <div class="absolute bottom-3 right-3 text-xs font-bold text-gray-400 transition-all" id="charCount">0/500</div>
                            </div>
                            @error('content')
                                <p class="text-sm text-red-600 flex items-center space-x-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        <!-- Info Box -->
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border-2 border-blue-200/60 rounded-2xl p-4">
                            <div class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-sm text-blue-900 leading-relaxed font-bold">
                                    Pesan akan ditinjau admin sebelum ditampilkan di live chat
                                </p>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button 
                            type="submit"
                            class="group relative w-full text-white font-bold py-4 rounded-2xl shadow-xl hover:shadow-2xl transform hover:scale-105 hover:-translate-y-1 active:scale-100 active:translate-y-0 transition-all duration-200 overflow-hidden"
                            style="background: linear-gradient(90deg, #f43f5e 0%, #ec4899 50%, #a855f7 100%);"
                            onmouseover="this.style.background='linear-gradient(90deg, #e11d48 0%, #db2777 50%, #9333ea 100%)'"
                            onmouseout="this.style.background='linear-gradient(90deg, #f43f5e 0%, #ec4899 50%, #a855f7 100%)'"
                        >
                            <div class="absolute inset-0 shimmer"></div>
                            <div class="relative flex items-center justify-center space-x-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                <span class="text-lg">Kirim Pesan</span>
                            </div>
                        </button>
                    </form>
                </div>
            </section>
        </div>
    </main>

    <!-- Premium Success Modal -->
    <div id="successModal" class="fixed inset-0 bg-black/60 backdrop-blur-md flex items-center justify-center z-50 hidden p-4">
        <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl transform transition-all animate-scaleIn overflow-hidden">
            <div class="p-8 text-center" style="background: linear-gradient(90deg, #10b981 0%, #059669 100%);">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-full mb-4 shadow-lg">
                    <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h3 class="text-3xl font-bold text-white mb-2">Berhasil!</h3>
                <p class="text-green-50">Pesan terkirim</p>
            </div>
            <div class="p-8 text-center space-y-6">
                <p class="text-gray-700 leading-relaxed">
                    Pesan Anda telah ditambahkan ke antrian review dan akan segera ditinjau oleh admin.
                </p>
                <button onclick="closeSuccessModal()" class="w-full px-6 py-4 text-white font-bold rounded-2xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all" style="background: linear-gradient(90deg, #f43f5e 0%, #ec4899 50%, #a855f7 100%);" onmouseover="this.style.background='linear-gradient(90deg, #e11d48 0%, #db2777 50%, #9333ea 100%)'" onmouseout="this.style.background='linear-gradient(90deg, #f43f5e 0%, #ec4899 50%, #a855f7 100%)'">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Custom Scrollbar Styles -->
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom, #f43f5e, #ec4899);
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(to bottom, #e11d48, #db2777);
        }
        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
        .animate-scaleIn { animation: scaleIn 0.3s ease-out forwards; }
    </style>

    <script>
        // Character counter dengan visual feedback
        const textarea = document.getElementById('content');
        const charCount = document.getElementById('charCount');
        
        textarea.addEventListener('input', function() {
            const length = this.value.length;
            charCount.textContent = `${length}/500`;
            
            if (length > 450) {
                charCount.classList.add('text-rose-600', 'font-black', 'scale-110');
                charCount.classList.remove('text-gray-400');
            } else if (length > 400) {
                charCount.classList.add('text-amber-600', 'font-bold');
                charCount.classList.remove('text-gray-400', 'text-rose-600', 'scale-110');
            } else {
                charCount.classList.remove('text-rose-600', 'text-amber-600', 'font-black', 'font-bold', 'scale-110');
                charCount.classList.add('text-gray-400');
            }
        });

        // Success modal functions
        function showSuccessModal() {
            document.getElementById('successModal').classList.remove('hidden');
            document.getElementById('content').value = '';
            charCount.textContent = '0/500';
            charCount.classList.remove('text-rose-600', 'text-amber-600', 'font-black', 'font-bold', 'scale-110');
            charCount.classList.add('text-gray-400');
        }

        function closeSuccessModal() {
            document.getElementById('successModal').classList.add('hidden');
        }

        // Show modal if status session exists
        @if (session('status'))
            setTimeout(() => showSuccessModal(), 300);
        @endif

        // Close on outside click
        document.getElementById('successModal').addEventListener('click', function(e) {
            if (e.target === this) closeSuccessModal();
        });

        // Escape key to close
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeSuccessModal();
        });
    </script>
</body>
</html>
