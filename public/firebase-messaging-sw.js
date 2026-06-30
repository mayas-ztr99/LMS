importScripts(
    "https://www.gstatic.com/firebasejs/10.7.0/firebase-app-compat.js",
);
importScripts(
    "https://www.gstatic.com/firebasejs/10.7.0/firebase-messaging-compat.js",
);

firebase.initializeApp({
    apiKey: "AIzaSyAlGxEKcNss_Fv5XvQU63IaHdLZafKsMz0",
    authDomain: "lms-99.firebaseapp.com",
    projectId: "lms-99",
    storageBucket: "lms-99.firebasestorage.app",
    messagingSenderId: "326718606678",
    appId: "1:326718606678:web:d9773896e87b706b0c9af7",
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
    self.registration.showNotification(payload.notification.title, {
        body: payload.notification.body,
        icon: "/logo.png",
    });
});
