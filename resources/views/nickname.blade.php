<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Wedding Chat') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
        .animate-float { animation: float 6s ease-in-out infinite; }
        .animate-fadeInUp { animation: fadeInUp 0.6s ease-out forwards; }
        .animate-scaleIn { animation: scaleIn 0.5s ease-out forwards; }
        .font-display { font-family: 'Playfair Display', serif; }
        .font-sans { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="antialiased font-sans" style="min-height: 100vh; background: linear-gradient(135deg, #ffe4e6 0%, #fce7f3 50%, #f3e8ff 100%);">
    <div class="min-h-screen flex items-center justify-center px-4 py-12 relative overflow-hidden">
        <!-- Enhanced decorative elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-20 left-10 rounded-full animate-float" style="width: 256px; height: 256px; background: linear-gradient(135deg, #fecdd3 0%, #fbcfe8 100%); filter: blur(60px); opacity: 0.5;"></div>
            <div class="absolute top-1/4 right-16 rounded-full animate-float" style="width: 288px; height: 288px; background: linear-gradient(135deg, #fbcfe8 0%, #e9d5ff 100%); filter: blur(60px); opacity: 0.4; animation-delay: 1s;"></div>
            <div class="absolute bottom-32 left-1/4 rounded-full animate-float" style="width: 224px; height: 224px; background: linear-gradient(135deg, #e9d5ff 0%, #fecdd3 100%); filter: blur(60px); opacity: 0.45; animation-delay: 2s;"></div>
            <div class="absolute bottom-20 right-1/3 rounded-full animate-float" style="width: 320px; height: 320px; background: linear-gradient(135deg, #fecdd3 0%, #fbcfe8 50%, #e9d5ff 100%); filter: blur(60px); opacity: 0.35; animation-delay: 0.5s;"></div>
        </div>

        <div class="max-w-lg w-full relative z-10">
            <!-- Logo/Header with animation -->
            <div class="text-center mb-12 space-y-4 animate-fadeInUp">
                <div class="inline-flex items-center justify-center mb-4">
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-br from-rose-400 to-pink-400 rounded-full blur-xl opacity-50 animate-pulse"></div>
                        <svg class="w-16 h-16 sm:w-24 sm:h-24 text-rose-500 relative drop-shadow-lg" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                        </svg>
                    </div>
                </div>
                <h1 class="text-5xl md:text-6xl font-display font-black text-transparent bg-clip-text bg-gradient-to-r from-rose-700 via-pink-700 to-purple-700 tracking-tight leading-tight">
                    Wedding Live Chat
                </h1>
                <p class="text-base sm:text-lg text-gray-800 max-w-md mx-auto font-semibold">Kirim ucapan & doa terbaik untuk pengantin</p>
            </div>

            <!-- Main Card with enhanced design -->
            <div class="bg-white/90 backdrop-blur-2xl rounded-2xl sm:rounded-3xl shadow-2xl border border-white/20 overflow-hidden animate-scaleIn" style="animation-delay: 0.2s;">
                @if (session('error'))
                    <div class="m-8 mb-0 p-4 bg-red-50 border-l-4 border-red-500 text-red-800 rounded-xl text-sm font-medium">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                <form id="nicknameForm" action="{{ route('chat.enter') }}" method="POST" class="p-4 sm:p-8 space-y-5 sm:space-y-8">
                    @csrf
                    
                    <!-- Nickname Input with icon -->
                    <div class="space-y-3">
                        <label for="nickname" class="block text-sm sm:text-base font-black text-gray-900 uppercase tracking-wide">
                            Nama Anda
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400 group-focus-within:text-rose-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                id="nickname" 
                                name="nickname" 
                                required 
                                maxlength="50"
                                value="{{ old('nickname') }}"
                                placeholder="Masukkan nama atau panggilan..."
                                class="w-full pl-10 pr-3 py-3 sm:pl-12 sm:pr-4 sm:py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:border-rose-500 focus:ring-4 focus:ring-rose-100 focus:bg-white transition-all outline-none text-gray-900 placeholder-gray-400 font-medium text-base sm:text-lg"
                            />
                        </div>
                        @error('nickname')
                            <p class="text-sm text-red-600 flex items-center space-x-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <!-- Disclaimer Checkbox with better visual -->
                    <div class="space-y-2 sm:space-y-3 bg-gradient-to-br from-rose-50 to-pink-50 border-2 border-rose-200/60 rounded-xl sm:rounded-2xl p-3 sm:p-5">
                        <label class="flex items-start space-x-4 cursor-pointer group">
                            <input 
                                type="checkbox" 
                                id="disclaimer" 
                                name="disclaimer" 
                                required
                                class="mt-1 w-6 h-6 text-rose-600 border-2 border-rose-300 rounded-lg focus:ring-rose-500 focus:ring-4 cursor-pointer transition-all"
                            />
                            <span class="text-sm text-gray-800 leading-relaxed flex-1">
                                <strong class="text-gray-900 font-bold block mb-1">📋 Pernyataan</strong>
                                <span class="text-gray-900 font-medium">Saya bertanggung jawab atas setiap teks dan gambar yang saya kirimkan melalui platform ini.</span>
                            </span>
                        </label>
                    </div>

                    <!-- Submit Button with enhanced design -->
                    <button 
                        type="submit"
                        class="group relative w-full text-white font-bold py-3 sm:py-5 rounded-xl sm:rounded-2xl shadow-xl hover:shadow-2xl transform hover:scale-[1.02] hover:-translate-y-1 active:scale-[0.98] active:translate-y-0 transition-all duration-200 overflow-hidden"
                        style="background: linear-gradient(90deg, #f43f5e 0%, #ec4899 50%, #a855f7 100%);"
                        onmouseover="this.style.background='linear-gradient(90deg, #e11d48 0%, #db2777 50%, #9333ea 100%)'"
                        onmouseout="this.style.background='linear-gradient(90deg, #f43f5e 0%, #ec4899 50%, #a855f7 100%)'"
                    >
                        <div class="absolute inset-0 bg-white/20 transform -skew-x-12 translate-x-full group-hover:translate-x-[-200%] transition-transform duration-700"></div>
                        <div class="relative flex items-center justify-center space-x-3">
                            <span class="text-base sm:text-lg">Masuk ke Live Chat</span>
                            <svg class="w-6 h-6 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </div>
                    </button>
                </form>

                <!-- Info Box dengan icon dan better spacing -->
                <div class="px-8 pb-8">
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border-2 border-blue-200/60 rounded-xl sm:rounded-2xl p-3 sm:p-5">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-500 rounded-lg sm:rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-900 mb-1">💡 Informasi</h4>
                                <p class="text-sm text-gray-900 leading-relaxed font-medium">
                                    Pesan Anda akan ditinjau oleh admin terlebih dahulu sebelum ditampilkan di layar live chat.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer with better design -->
            <div class="text-center mt-10 space-y-3 animate-fadeInUp" style="animation-delay: 0.4s;">
                <div class="inline-flex items-center space-x-2 px-4 py-2 bg-white/80 backdrop-blur-lg rounded-full shadow-lg border border-gray-200">
                    <span class="text-red-500 text-base sm:text-lg animate-pulse">❤️</span>
                    <p class="text-gray-900 text-sm font-bold">
                        Astheron Technologies
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add smooth form validation
        document.getElementById('nicknameForm').addEventListener('submit', function(e) {
            const nickname = document.getElementById('nickname').value.trim();
            const disclaimer = document.getElementById('disclaimer').checked;
            
            if (!nickname) {
                e.preventDefault();
                alert('Mohon masukkan nama Anda');
                return false;
            }
            
            if (!disclaimer) {
                e.preventDefault();
                alert('Mohon setujui pernyataan terlebih dahulu');
                return false;
            }
        });
    </script>
</body>
</html>
