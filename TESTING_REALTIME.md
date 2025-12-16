# Testing Real-Time Chat Functionality

## Prerequisites
✅ Reverb server running on port 8080
✅ Queue worker processing broadcast events
✅ Assets rebuilt with latest JavaScript changes

## Test Steps

### 1. Open Chat Page (User Mode)
1. Open browser and navigate to: `http://localhost:8000`
2. Enter nickname (e.g., "Test User")
3. Click "Masuk ke Wedding Chat" button
4. **Open Browser Console** (F12) - you should see:
   ```
   🔧 Echo Configuration: {...}
   ✅ Laravel Echo initialized successfully
   🔌 Subscribing to public.chat channel...
   ```

### 2. Open Admin Dashboard (Another Tab/Window)
1. Open new tab: `http://localhost:8000/admin/dashboard`
2. Login as admin if needed
3. **Open Browser Console** (F12) - you should see:
   ```
   🔌 Admin: Subscribing to channels...
   ```

### 3. Test User → Admin Real-Time (Message Submission)
1. Go back to **User Chat Tab**
2. Type a message: "Test pesan real-time 1"
3. Click "Kirim Pesan" button
4. **Immediately switch to Admin Tab** (DON'T reload!)
5. **Expected Result**: New message appears in "Pesan Menunggu" section WITHOUT reload
6. **Check Admin Console** - you should see:
   ```
   📨 New message submitted: {...}
   📦 Submitted message data: {id: X, username: "Test User", content: "Test pesan real-time 1"}
   ✨ Pending card added to admin dashboard
   ```

### 4. Test Admin → User Real-Time (Message Approval)
1. In **Admin Tab**, click "Setujui" button on the pending message
2. **Immediately switch to User Chat Tab** (DON'T reload!)
3. **Expected Result**: Approved message appears in chat list WITHOUT reload
4. **Check User Chat Console** - you should see:
   ```
   ✅ Message approved received: {...}
   📦 Event data: {id: X, username: "Test User", content: "Test pesan real-time 1", approved_at: "..."}
   ✨ Message appended to chat list
   ```

### 5. Test Multiple Messages
1. Send 3 more messages from user chat:
   - "Test pesan real-time 2"
   - "Test pesan real-time 3"
   - "Test pesan real-time 4"
2. Each message should appear in admin dashboard INSTANTLY
3. Approve all messages one by one
4. Each approved message should appear in user chat INSTANTLY

## Troubleshooting

### If real-time NOT working:

#### Check Console Logs
- **User Console**: Look for "🔌 Subscribing to public.chat channel..."
- **Admin Console**: Look for "🔌 Admin: Subscribing to channels..."
- If these logs are missing, Echo is not initialized

#### Check Network Tab
1. Open Network tab in Developer Tools
2. Filter by "WS" (WebSocket)
3. You should see connection to `ws://localhost:8080/app/nyjrnpagg5onrnf5dg4y`
4. Status should be "101 Switching Protocols" (green)

#### Check Reverb Server
```powershell
# Check if Reverb is running
Get-Process | Where-Object { $_.ProcessName -eq 'php' }

# If not running, start it:
php artisan reverb:start
```

#### Check Queue Worker
```powershell
# Check if queue worker is running
# Should see multiple PHP processes

# If not running, start it:
php artisan queue:work
```

#### Check Browser Console Errors
- Look for errors related to:
  - `Echo is not defined`
  - WebSocket connection failed
  - CORS errors
  - 404 errors for assets

#### Common Issues

**Issue**: Console shows "⚠️ Echo is not initialized"
**Solution**: 
- Clear browser cache
- Rebuild assets: `npm run build`
- Hard refresh: Ctrl+Shift+R

**Issue**: WebSocket connection failed
**Solution**:
- Restart Reverb: Stop all PHP processes and run `php artisan reverb:start`
- Check .env: `REVERB_HOST=localhost` and `REVERB_PORT=8080`

**Issue**: Events not received
**Solution**:
- Check queue worker is processing: `php artisan queue:work`
- Clear queue cache: `php artisan queue:restart`
- Check Laravel logs: `storage/logs/laravel.log`

**Issue**: Old JavaScript loaded
**Solution**:
- Rebuild assets: `npm run build`
- Clear browser cache
- Hard refresh (Ctrl+Shift+R)

## Expected Console Output Examples

### User Chat Page
```
🔧 Echo Configuration: {
  broadcaster: "reverb",
  key: "nyjrnpagg5onrnf5dg4y",
  wsHost: "localhost",
  wsPort: 8080,
  scheme: "http"
}
✅ Laravel Echo initialized successfully
🔌 Subscribing to public.chat channel...
✅ Message approved received: {id: 1, username: "Test User", content: "Hello", ...}
📦 Event data: {id: 1, username: "Test User", content: "Hello", approved_at: "2025-12-16..."}
✨ Message appended to chat list
```

### Admin Dashboard
```
🔧 Echo Configuration: {...}
✅ Laravel Echo initialized successfully
🔌 Admin: Subscribing to channels...
📨 New message submitted: {id: 2, username: "Test User", content: "Test", ...}
📦 Submitted message data: {id: 2, username: "Test User", content: "Test"}
✨ Pending card added to admin dashboard
```

## Success Criteria
✅ User can send messages without page reload
✅ Admin sees new messages instantly in pending section
✅ Admin can approve/reject messages without reload
✅ User sees approved messages instantly in chat
✅ No console errors
✅ WebSocket connection established (green in Network tab)
✅ All console logs appear as expected
