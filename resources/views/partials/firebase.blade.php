<script type="module">
    const apiToken = localStorage.getItem('api_token');

    import {
        initializeApp
    } from "https://www.gstatic.com/firebasejs/10.7.0/firebase-app.js";
    import {
        getMessaging,
        getToken,
        onMessage
    } from "https://www.gstatic.com/firebasejs/10.7.0/firebase-messaging.js";

    const firebaseConfig = {
        apiKey: "AIzaSyAlGxEKcNss_Fv5XvQU63IaHdLZafKsMz0",
        authDomain: "lms-99.firebaseapp.com",
        projectId: "lms-99",
        storageBucket: "lms-99.firebasestorage.app",
        messagingSenderId: "326718606678",
        appId: "1:326718606678:web:d9773896e87b706b0c9af7"
    };

    const app = initializeApp(firebaseConfig);
    const messaging = getMessaging(app);

    Notification.requestPermission().then(permission => {
        if (permission === 'granted') {
            console.log('indise')
            getToken(messaging, {
                vapidKey: "BEdgyhDcZIFXj3iXhjRDJ3YMLNTtuqPoNCvd2miSeINAFrHnWYm7pLXqhHVJMwoB9js6knjwgpA-4y3BCnnok5o"
            }).then(token => {
                console.log('FCM Token:', token);
                fetch('http://127.0.0.1:8000/api/fcm-token', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json', // 🔥 IMPORTANT
                        'Authorization': `Bearer ${apiToken}`,
                        // 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                         //   .getAttribute('content')
                    },
                    body: JSON.stringify({
                        fcm_token: token
                    })
                })
                    .then(async res => {
                        const text = await res.text(); // see raw response
                        console.log('RAW RESPONSE:', text);
                        return JSON.parse(text);
                    })
                    .then(data => console.log('OK:', data))
                    .catch(err => console.error('ERROR:', err));

            });
        } else {
            console.log('Permission not granted for notifications');
        }
    });

    // Foreground handler
    onMessage(messaging, payload => {
        console.log('FCM Foreground:', payload);

        // ✅ عرض notification في foreground
        if (Notification.permission === 'granted') {
            new Notification(payload.notification.title, {
                body: payload.notification.body,
                icon: '/logo.png'
            });
        }

        // (اختياري) event
        window.dispatchEvent(new CustomEvent('fcm-message', {
            detail: payload
        }));
    });

    navigator.serviceWorker.register('/firebase-messaging-sw.js')
        .then(registration => {
            console.log('SW registered');
        })
        .catch(err => console.error('SW failed', err));
</script>
