<!DOCTYPE html>
<html>
<head>
    <title>@yield('title')</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: #121212;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            width: 400px;
            background: #1e1e1e;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(255, 140, 0, 0.3);
        }

        h2 {
            text-align: center;
            color: #ff8c00;
            margin-bottom: 20px;
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border-radius: 5px;
            border: 1px solid #333;
            background: #2a2a2a;
            color: white;
        }

        input:focus {
            outline: none;
            border-color: #ff8c00;
            box-shadow: 0 0 5px #ff8c00;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #ff8c00;
            border: none;
            border-radius: 5px;
            color: black;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
            transition: 0.3s;
        }

        button:hover {
            background: #ffa733;
        }

        a {
            color: #ff8c00;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .error {
            color: #ff4d4d;
            margin-bottom: 10px;
        }

        .success {
            color: #ff8c00;
            margin-bottom: 10px;
        }

        p {
            text-align: center;
            color: #ccc;
        }
    </style>
</head>
<body>

<div class="container">
    @yield('content')
</div>
<script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    // console.log(" DOM Loaded - Starting Firebase setup");

    const firebaseConfig = {
        apiKey: "{{ env('FIREBASE_API_KEY') }}",
        authDomain: "{{ env('FIREBASE_AUTH_DOMAIN') }}",
        projectId: "{{ env('FIREBASE_PROJECT_ID') }}",
        storageBucket: "{{ env('FIREBASE_STORAGE_BUCKET') }}",
        messagingSenderId: "{{ env('FIREBASE_MESSAGING_SENDER_ID') }}",
        appId: "{{ env('FIREBASE_APP_ID') }}"
    };

    firebase.initializeApp(firebaseConfig);
    // console.log("Firebase initialized", firebaseConfig);

    const messaging = firebase.messaging();

    // Register Service Worker
    navigator.serviceWorker.register('/firebase-messaging-sw.js')
        .then((registration) => {
            // console.log("Service Worker registered:", registration);

            Notification.requestPermission()
                .then((permission) => {
                    // console.log("Notification permission:", permission);

                    if (permission === "granted") {

                        messaging.getToken({
                            vapidKey: "{{env('FIREBASE_VAPID_KEY')}}",
                            serviceWorkerRegistration: registration
                        }).then((token) => {
                            // console.log("FCM Token received:", token);

                            if (token) {
                                localStorage.setItem('fcm_token', token);

                                let input = document.getElementById('fcm_token');
                                if (input) input.value = token;                                
                            }

                        }).catch((err) => {
                            // console.error(" Error getting FCM token:", err);
                        });

                    } else {
                        // console.warn(" Notification permission denied by user");
                    }

                }).catch(err => console.error(" error:", err));

        }).catch(err => console.error(" Service Worker registration error:", err));

    // Listen for foreground messages
    messaging.onMessage((payload) => {
        console.log(" Foreground message received:", payload);
        alert("Notification: " + payload.notification.title + "\n" + payload.notification.body);
    });
});
</script>
</body>

</html>