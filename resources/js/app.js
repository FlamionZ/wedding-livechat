import './bootstrap';

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

const formatTime = (iso) => {
	if (!iso) return '';
	const date = new Date(iso);
	return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
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
	time.textContent = formatTime(message.approved_at || message.created_at);

	header.appendChild(name);
	header.appendChild(time);

	// Message body
	const body = document.createElement('p');
	body.className = 'text-sm text-gray-700 leading-relaxed';
	body.textContent = message.content;

	contentWrapper.appendChild(header);
	contentWrapper.appendChild(body);

	container.appendChild(avatar);
	container.appendChild(contentWrapper);
	wrapper.appendChild(container);

	return wrapper;
};

const initPublicChat = () => {
	const root = document.getElementById('chat-app');
	if (!root) return;

	const list = root.querySelector('[data-chat-list]');
	const emptyState = root.querySelector('[data-empty-chat]');
	const existing = root.dataset.messages ? JSON.parse(root.dataset.messages) : [];

	const appendMessage = (message) => {
		emptyState?.classList.add('hidden');
		list?.appendChild(renderMessageCard(message));
		list?.lastElementChild?.scrollIntoView({ behavior: 'smooth', block: 'end' });
	};

	if (list && list.children.length === 0) {
		existing.forEach((message) => appendMessage(message));
	}

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
				appendMessage(event);
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
	const feedList = root.querySelector('[data-feed-list]');
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

		const card = document.createElement('article');
		card.dataset.messageCard = message.id;
		card.className = 'bg-white shadow-lg rounded-xl border border-emerald-100 p-4 space-y-3';

		const header = document.createElement('div');
		header.className = 'flex items-start justify-between gap-3';

		const headerText = document.createElement('div');
		const time = document.createElement('p');
		time.className = 'text-xs uppercase tracking-wide text-emerald-700';
		time.textContent = formatTime(message.created_at);
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
		card.appendChild(actions);

		pendingList?.prepend(card);

		wireActionForm(approveForm);
		wireActionForm(rejectForm);
	};

	const addFeedCard = (message) => {
		const card = renderMessageCard(message);
		card.classList.add('bg-white/95', 'border', 'border-emerald-100');
		feedList?.appendChild(card);
		feedList?.lastElementChild?.scrollIntoView({ behavior: 'smooth', block: 'end' });
	};

	root.querySelectorAll('form[data-action]').forEach(wireActionForm);

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
