# 🚀 Setup Sistem Real-time Wedding Chat

## ✅ Perbaikan yang Sudah Dilakukan

1. **Fix Event Listeners** - Memperbaiki struktur event listener untuk menangkap data dengan benar
2. **Console Logging** - Menambahkan console.log untuk debugging WebSocket connection
3. **Echo Configuration** - Memperbaiki konfigurasi Laravel Echo
4. **Assets Rebuilt** - Frontend assets sudah di-rebuild

## 🔥 Cara Menjalankan Sistem Real-time

### 1️⃣ Jalankan Laravel Reverb Server

Buka terminal baru dan jalankan:

```bash
php artisan reverb:start
```

atau dengan debug mode:

```bash
php artisan reverb:start --debug
```

**Output yang benar:**
```
INFO  Reverb server started on http://0.0.0.0:8080
```

### 2️⃣ Jalankan Laravel Server

Di terminal lain:

```bash
php artisan serve
```

### 3️⃣ Jalankan Queue Worker (untuk broadcasting)

Di terminal ketiga:

```bash
php artisan queue:work
```

> **Penting:** Queue worker diperlukan karena broadcasting events di-queue

### 4️⃣ (Optional) Watch Mode untuk Development

Jika ingin auto-rebuild saat edit JS/CSS:

```bash
npm run dev
```

---

## 📡 Testing Real-time

### Test 1: Public Chat
1. Buka browser: `http://localhost:8000`
2. Buka Console (F12)
3. Kirim pesan sebagai tamu
4. **Expected Console Output:**
   ```
   🔧 Echo Configuration: {...}
   ✅ Laravel Echo initialized successfully
   🔌 Subscribing to public.chat channel...
   ```

### Test 2: Admin Dashboard
1. Login sebagai admin
2. Buka: `http://localhost:8000/admin/dashboard`
3. Buka Console (F12)
4. **Expected Console Output:**
   ```
   🔌 Admin: Subscribing to channels...
   ```
5. Ketika ada pesan baru masuk:
   ```
   📨 New message submitted: {message data}
   ```
6. Ketika approve pesan:
   ```
   ✅ Message approved in admin: {message data}
   ```

### Test 3: Simulasi Real-time Flow

**Window 1:** Public Chat Page
- Buka `http://localhost:8000`
- Biarkan terbuka

**Window 2:** Admin Dashboard
- Login dan buka dashboard
- Biarkan terbuka

**Window 3:** Public Chat (untuk submit)
- Buka `http://localhost:8000` lagi
- Submit pesan baru

**Expected Behavior:**
1. ✅ Window 2 (Admin) langsung menerima notif pesan baru **tanpa reload**
2. ✅ Admin approve pesan
3. ✅ Window 1 & 3 langsung menampilkan pesan yang di-approve **tanpa reload**

---

## 🐛 Troubleshooting

### Problem 1: Echo tidak connect
**Gejala:** Console tidak menampilkan log Echo
**Solusi:**
```bash
# Pastikan Reverb running
php artisan reverb:start --debug

# Rebuild assets
npm run build

# Clear cache browser (Ctrl+Shift+R)
```

### Problem 2: Pesan tidak muncul real-time
**Gejala:** Harus reload manual untuk lihat pesan baru
**Solusi:**
```bash
# Pastikan queue worker running
php artisan queue:work

# Check .env
BROADCAST_CONNECTION=reverb
QUEUE_CONNECTION=database
```

### Problem 3: Admin tidak bisa subscribe private channel
**Gejala:** Admin tidak terima notif pesan baru
**Solusi:**
- Pastikan user sudah login
- Check `is_admin` = true di database
- Check console untuk error

### Problem 4: Port 8080 sudah digunakan
**Gejala:** Reverb gagal start
**Solusi:**
```bash
# Ubah port di .env
REVERB_PORT=8081
VITE_REVERB_PORT=8081

# Rebuild
npm run build

# Restart Reverb
php artisan reverb:start
```

---

## 📊 Architecture Overview

```
User Submit Message
       ↓
ChatController::store()
       ↓
event(MessageSubmitted) → Queue
       ↓
Broadcast to: admin.messages (Private Channel)
       ↓
Admin Dashboard receives → Display in Pending
       ↓
Admin Click "Approve"
       ↓
MessageModerationController::approve()
       ↓
event(MessageApproved) → Queue
       ↓
Broadcast to: public.chat (Public Channel)
       ↓
All Public Chat Pages receive → Display Message (Auto-scroll)
```

---

## ✨ Features yang Sudah Bekerja Real-time

✅ **Public Chat:**
- Auto-append pesan yang di-approve
- Auto-scroll ke pesan terbaru
- Tidak perlu reload

✅ **Admin Dashboard:**
- Notifikasi real-time pesan baru masuk
- Auto-remove dari pending setelah approve/reject
- Auto-add ke feed setelah approve
- AJAX submit tanpa reload page

✅ **Console Debugging:**
- Echo connection status
- Event received logs
- Error tracking

---

## 🎯 Next Steps

Untuk production:
1. Ubah `APP_DEBUG=false` di `.env`
2. Gunakan `npm run build` (bukan dev)
3. Setup supervisor untuk queue worker
4. Setup websocket proxy (nginx/caddy)
5. Remove console.log dari production build

