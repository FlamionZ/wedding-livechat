import './bootstrap';

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
const appTimeZone = document.querySelector('meta[name="app-timezone"]')?.getAttribute('content') || 'Asia/Jakarta';

const getDisplayTime = (message) => {
	if (message.display_time) return message.display_time;
	const iso = message.approved_at || message.created_at;
	if (!iso) return '';
	try {
		return new Intl.DateTimeFormat('id-ID', {
			hour: '2-digit',
			minute: '2-digit',
			hour12: false,
			timeZone: appTimeZone,
		}).format(new Date(iso));
	} catch (e) {
		const date = new Date(iso);
		return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false });
	}
};

const renderMessageCard = (message) => {
	const wrapper = document.createElement('li');
	wrapper.className = 'group bg-white rounded-2xl p-5 shadow-md hover:shadow-xl border border-gray-100 transition-all duration-300 animate-slideUp';

	const container = document.createElement('div');
	container.className = 'flex items-start space-x-4';

	// Avatar
	const avatar = document.createElement('div');
	avatar.className = 'flex-shrink-0 w-12 h-12 rounded-full bg-gradient-to-br from-rose-400 via-pink-400 to-purple-400 flex items-center justify-center text-white font-bold text-lg shadow-lg group-hover:scale-110 transition-transform';
	avatar.textContent = message.username.charAt(0).toUpperCase();

	// Content wrapper
	const contentWrapper = document.createElement('div');
	contentWrapper.className = 'flex-1 min-w-0';

	// Header with name and time
	const header = document.createElement('div');
	header.className = 'flex items-center justify-between mb-2';

	const name = document.createElement('span');
	name.className = 'text-base font-bold text-gray-900';
	name.textContent = message.username;

	const time = document.createElement('span');
	time.className = 'text-xs text-gray-500 font-medium';
	time.textContent = getDisplayTime(message);

	header.appendChild(name);
	header.appendChild(time);

	// Message body
	const body = document.createElement('p');
	body.className = 'text-sm text-gray-700 leading-relaxed';
	body.textContent = message.content;

	contentWrapper.appendChild(header);
	contentWrapper.appendChild(body);

	// Optional image
	if (message.image_path) {
		const imgWrap = document.createElement('div');
		imgWrap.className = 'mt-2';
		const img = document.createElement('img');
		img.src = message.image_path.startsWith('http') ? message.image_path : (window.location.origin + '/' + message.image_path.replace(/^\//, ''));
		img.alt = 'Foto ucapan';
		img.loading = 'lazy';
		img.className = 'max-h-60 rounded-lg border border-gray-200 shadow-sm';
		imgWrap.appendChild(img);
		contentWrapper.appendChild(imgWrap);
	}

	container.appendChild(avatar);
	container.appendChild(contentWrapper);
	wrapper.appendChild(container);

	return wrapper;
};

const initPublicChat = () => {
	const root = document.getElementById('chat-app');
	if (!root) return;

	const list = root.querySelector('[data-chat-list]');
	const chatScroll = root.querySelector('[data-chat-scroll]');
	const emptyState = root.querySelector('[data-empty-chat]');
	const existing = root.dataset.messages ? JSON.parse(root.dataset.messages) : [];

	// Fungsi untuk scroll ke bawah KERAS
	const forceScrollToBottom = () => {
		const container = document.getElementById('chatScrollContainer') || chatScroll;
		if (container) {
			container.scrollTop = container.scrollHeight + 9999;
			console.log('📍 Scroll to bottom:', container.scrollTop, '/', container.scrollHeight);
		}
	};

	const scrollToBottom = (behavior = 'smooth') => {
		if (chatScroll) {
			chatScroll.scrollTo({ top: chatScroll.scrollHeight, behavior });
		}
	};

	const appendMessage = (message) => {
		// Cek duplikasi: cari elemen dengan data-id pesan yang sama
		if (list?.querySelector(`[data-id="${message.id}"]`)) {
			return; // Sudah ada, jangan tambahkan lagi
		}
		emptyState?.classList.add('hidden');
		const card = renderMessageCard(message);
		card.setAttribute('data-id', message.id);
		list?.appendChild(card);
		scrollToBottom();
	};

	// JANGAN RENDER ULANG - pesan sudah ada di Blade
	// Langsung scroll ke bawah SEKARANG
	forceScrollToBottom();
	
	// Eksekusi scroll berkali-kali dengan berbagai timing
	requestAnimationFrame(forceScrollToBottom);
	setTimeout(forceScrollToBottom, 0);
	setTimeout(forceScrollToBottom, 10);
	setTimeout(forceScrollToBottom, 50);
	setTimeout(forceScrollToBottom, 100);
	setTimeout(forceScrollToBottom, 200);
	setTimeout(forceScrollToBottom, 500);
	setTimeout(forceScrollToBottom, 1000);
	
	// Event listener untuk gambar yang belum loaded
	const images = document.querySelectorAll('[data-chat-scroll] img');
	images.forEach(img => {
		if (!img.complete) {
			img.addEventListener('load', forceScrollToBottom);
			img.addEventListener('error', forceScrollToBottom);
		}
	});
	
	// Scroll saat window fully loaded
	window.addEventListener('load', forceScrollToBottom);

	// Render pesan dari existing data HANYA jika list kosong (untuk Echo realtime)
	const appendMessageForEcho = (message) => {
		// Cek duplikasi: cari elemen dengan data-id pesan yang sama
		if (list?.querySelector(`[data-id="${message.id}"]`)) {
			return; // Sudah ada, jangan tambahkan lagi
		}
		emptyState?.classList.add('hidden');
		const card = renderMessageCard(message);
		card.setAttribute('data-id', message.id);
		list?.appendChild(card);
		forceScrollToBottom();
	};

	if (window.Echo) {
		console.log('🔌 Subscribing to public.chat channel...');
		window.Echo.channel('public.chat')
			.listen('MessageApproved', (event) => {
				console.log('✅ Message approved received:', event);
				console.log('📦 Event data:', {
					id: event.id,
					username: event.username,
					content: event.content,
					approved_at: event.approved_at
				});
				appendMessageForEcho(event);
				console.log('✨ Message appended to chat list');
			});
	} else {
		console.warn('⚠️ Echo is not initialized');
	}
};

const initAdminDashboard = () => {
	const root = document.getElementById('admin-dashboard');
	if (!root) return;

	const pendingList = root.querySelector('[data-pending-list]');
	const feedList = document.querySelector('[data-feed-list]');
	const feedScroll = document.querySelector('[data-feed-scroll]');
	const emptyPending = root.querySelector('[data-empty-pending]');

	const wireActionForm = (form) => {
		if (!form) return;

		form.addEventListener('submit', async (event) => {
			event.preventDefault();
			const id = form.dataset.message;

			try {
				const response = await fetch(form.action, {
					method: 'POST',
					headers: {
						'X-CSRF-TOKEN': csrfToken,
						Accept: 'application/json',
					},
					credentials: 'same-origin',
				});

				const data = await response.json().catch(() => null);

				if (response.ok) {
					if (data?.data?.id) {
						removePendingCard(data.data.id);

						if (data.data.status === 'approved') {
							addFeedCard({
								...data.data,
								approved_at: data.data.approved_at,
							});
						}
					} else if (id) {
						removePendingCard(id);
					}
				}
			} catch (error) {
				console.error('Gagal memproses pesan', error);
			}
		});
	};

	const removePendingCard = (id) => {
		const card = root.querySelector(`[data-message-card="${id}"]`);
		card?.remove();
		if (!pendingList?.children.length) {
			emptyPending?.classList.remove('hidden');
		}
	};

	const addPendingCard = (message) => {
		emptyPending?.classList.add('hidden');
		// Cek duplikasi: cari elemen dengan data-id pesan yang sama
		if (pendingList?.querySelector(`[data-id="${message.id}"]`)) {
			return; // Sudah ada, jangan tambahkan lagi
		}
		const card = document.createElement('article');
		card.className = 'bg-white shadow-lg rounded-xl border border-emerald-100 p-4 space-y-3';
		card.setAttribute('data-message-card', message.id);
		card.setAttribute('data-id', message.id);

		const header = document.createElement('div');
		header.className = 'flex items-start justify-between gap-3';

		const headerText = document.createElement('div');
		const time = document.createElement('p');
		time.className = 'text-xs uppercase tracking-wide text-emerald-700';
		time.textContent = getDisplayTime(message);
		const username = document.createElement('p');
		username.className = 'text-lg font-semibold text-emerald-900';
		username.textContent = message.username;
		headerText.appendChild(time);
		headerText.appendChild(username);

		const badge = document.createElement('span');
		badge.className = 'px-2 py-1 text-xs rounded-full bg-amber-100 text-amber-800';
		badge.textContent = 'Pending';

		header.appendChild(headerText);
		header.appendChild(badge);

		const body = document.createElement('p');
		body.className = 'text-sm text-emerald-950 leading-relaxed';
		body.textContent = message.content;

		// Optional image for pending card
		let imgWrap = null;
		if (message.image_path) {
			imgWrap = document.createElement('div');
			imgWrap.className = 'mt-2';
			const img = document.createElement('img');
			img.src = message.image_path.startsWith('http') ? message.image_path : (window.location.origin + '/' + message.image_path.replace(/^\//, ''));
			img.alt = 'Foto ucapan';
			img.loading = 'lazy';
			img.className = 'max-h-60 rounded-lg border border-emerald-200 shadow-sm';
			imgWrap.appendChild(img);
		}

		const actions = document.createElement('div');
		actions.className = 'flex gap-2';

		const approveForm = document.createElement('form');
		approveForm.dataset.action = 'approve';
		approveForm.dataset.message = message.id;
		approveForm.action = root.dataset.approveUrl?.replace('__id__', message.id);
		approveForm.method = 'POST';
		approveForm.className = 'inline';
		const approveButton = document.createElement('button');
		approveButton.type = 'submit';
		approveButton.className = 'px-3 py-2 rounded-lg bg-emerald-600 text-white text-sm font-semibold shadow hover:bg-emerald-700';
		approveButton.textContent = 'Setujui';
		approveForm.appendChild(approveButton);

		const rejectForm = document.createElement('form');
		rejectForm.dataset.action = 'reject';
		rejectForm.dataset.message = message.id;
		rejectForm.action = root.dataset.rejectUrl?.replace('__id__', message.id);
		rejectForm.method = 'POST';
		rejectForm.className = 'inline';
		const rejectButton = document.createElement('button');
		rejectButton.type = 'submit';
		rejectButton.className = 'px-3 py-2 rounded-lg bg-red-50 text-red-700 border border-red-200 text-sm font-semibold hover:bg-red-100';
		rejectButton.textContent = 'Tolak';
		rejectForm.appendChild(rejectButton);

		actions.appendChild(approveForm);
		actions.appendChild(rejectForm);

		card.appendChild(header);
		card.appendChild(body);
		if (imgWrap) card.appendChild(imgWrap);
		card.appendChild(actions);

		pendingList?.prepend(card);

		wireActionForm(approveForm);
		wireActionForm(rejectForm);
	};

	const scrollFeedBottom = (behavior = 'smooth') => {
		if (feedScroll) {
			feedScroll.scrollTo({ top: feedScroll.scrollHeight, behavior });
		}
	};

	const addFeedCard = (message) => {
		// Cek duplikasi: cari elemen dengan data-id pesan yang sama
		if (feedList?.querySelector(`[data-id="${message.id}"]`)) {
			return; // Sudah ada, jangan tambahkan lagi
		}
		
		const card = document.createElement('li');
		card.className = 'list-none bg-white border border-emerald-100 rounded-xl px-2 sm:px-4 py-2 sm:py-3 shadow-sm';
		card.setAttribute('data-id', message.id);

		const header = document.createElement('div');
		header.className = 'flex items-center justify-between mb-1';

		const username = document.createElement('p');
		username.className = 'text-sm font-semibold text-emerald-900';
		username.textContent = message.username;

		const time = document.createElement('span');
		time.className = 'text-xs text-emerald-600';
		time.textContent = getDisplayTime(message);

		header.appendChild(username);
		header.appendChild(time);

		const body = document.createElement('p');
		body.className = 'text-xs sm:text-sm text-emerald-950 leading-relaxed';
		body.textContent = message.content;

		card.appendChild(header);
		card.appendChild(body);

		// Add image if exists
		if (message.image_path) {
			const imgWrap = document.createElement('div');
			imgWrap.className = 'mt-2';
			const img = document.createElement('img');
			img.src = message.image_path.startsWith('http') ? message.image_path : (window.location.origin + '/' + message.image_path.replace(/^\//, ''));
			img.alt = 'Foto ucapan';
			img.loading = 'lazy';
			img.className = 'max-h-60 rounded-lg border border-emerald-200 shadow-sm';
			imgWrap.appendChild(img);
			card.appendChild(imgWrap);
		}

		feedList?.appendChild(card);
		scrollFeedBottom();
	};

	root.querySelectorAll('form[data-action]').forEach(wireActionForm);

	// Pastikan feed awal berada di bawah (pesan terbaru di bawah)
	setTimeout(() => {
		const lastFeed = feedList?.lastElementChild;
		if (lastFeed) {
			feedScroll?.scrollTo({ top: feedScroll.scrollHeight, behavior: 'auto' });
		}
	}, 50);

	if (window.Echo) {
		console.log('🔌 Admin: Subscribing to channels...');
		
		window.Echo.private('admin.messages')
			.listen('MessageSubmitted', (event) => {
				console.log('📨 New message submitted:', event);
				console.log('📦 Submitted message data:', {
					id: event.id,
					username: event.username,
					content: event.content
				});
				addPendingCard(event);
				console.log('✨ Pending card added to admin dashboard');
			});

		window.Echo.channel('public.chat')
			.listen('MessageApproved', (event) => {
				console.log('✅ Message approved in admin:', event);
				console.log('🗑️ Removing pending card:', event.id);
				removePendingCard(event.id);
				addFeedCard(event);
				console.log('✨ Feed card added to admin dashboard');
			});
	} else {
		console.warn('⚠️ Echo is not initialized for admin');
	}
};

initPublicChat();
initAdminDashboard();

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
