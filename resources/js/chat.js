import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

document.addEventListener('DOMContentLoaded', () => {
    // تهيئة Echo
    const echo = new Echo({
        broadcaster: 'pusher',
        key: import.meta.env.VITE_PUSHER_APP_KEY,
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
        forceTLS: true,
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
        },
    });

    window.Echo = echo;

    const conversationIdInput = document.getElementById('conversationId');
    let currentConversationId = conversationIdInput ? conversationIdInput.value : null;

    loadConversations();

    if (currentConversationId) {
        loadConversation(currentConversationId);
    }

    function listenToConversation(conversationId) {
        if (window._echoChannel) {
            window._echoChannel.stopListening('.message.sent');
            window.Echo.leaveChannel('conversation.' + conversationId);
        }

        window._echoChannel = window.Echo.private('conversation.' + conversationId)
            .listen('.message.sent', (data) => {
                appendMessage(data);
            });
    }

    async function loadConversations() {
        try {
            const response = await fetch('/api/conversations', {
                headers: {
                    'Authorization': 'Bearer ' + getToken(),
                    'Accept': 'application/json',
                },
            });
            const result = await response.json();
            const conversations = result.data || [];
            const list = document.getElementById('conversations');
            list.innerHTML = '';
            conversations.forEach(conv => {
                const li = document.createElement('li');
                const partner = conv.student_id === getUserId() ? conv.instructor.name : conv.student.name;
                li.textContent = partner;
                li.dataset.conversationId = conv.id;
                li.style.cursor = 'pointer';
                li.addEventListener('click', () => {
                    loadConversation(conv.id);
                });
                list.appendChild(li);
            });
        } catch (error) {
            console.error('Error loading conversations', error);
        }
    }

    async function loadConversation(conversationId) {
        currentConversationId = conversationId;
        document.getElementById('conversationId').value = conversationId;

        try {
            const response = await fetch(`/api/conversations/${conversationId}`, {
                headers: {
                    'Authorization': 'Bearer ' + getToken(),
                    'Accept': 'application/json',
                },
            });
            const result = await response.json();
            const data = result.data || {};
            const messagesList = document.getElementById('messages');
            messagesList.innerHTML = '';
            if (data.messages && data.messages.length) {
                data.messages.forEach(msg => appendMessage(msg));
            }
            const conversation = data.conversation || {};
            const partner = conversation.student_id === getUserId() ? conversation.instructor?.name : conversation.student?.name;
            document.getElementById('chatPartner').textContent = partner || 'محادثة';

            listenToConversation(conversationId);
        } catch (error) {
            console.error('Error loading conversation', error);
        }
    }

    function appendMessage(message) {
        const ul = document.getElementById('messages');
        if (!ul) return;
        const li = document.createElement('li');
        li.textContent = `${message.user_name}: ${message.message}`;
        ul.appendChild(li);
        ul.scrollTop = ul.scrollHeight;
    }

    document.getElementById('messageForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const input = document.getElementById('messageInput');
        const message = input.value.trim();
        if (!message || !currentConversationId) return;

        try {
            const response = await fetch('/api/messages', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + getToken(),
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({
                    conversation_id: currentConversationId,
                    message: message,
                }),
            });
            if (response.ok) {
                input.value = '';
            } else {
                console.error('Failed to send message');
            }
        } catch (error) {
            console.error('Error sending message', error);
        }
    });

    function getToken() {
        return localStorage.getItem('api_token') || '';
    }

    function getUserId() {
        return parseInt(localStorage.getItem('user_id')) || 0;
    }
});
