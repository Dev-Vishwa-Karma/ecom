<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Dashboard')</title>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css"/>
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>

<style>

body{
margin:0;
font-family:Arial,sans-serif;
background:#121212;
color:white;
}

.sidebar{
width:220px;
height:100vh;
background:#1e1e1e;
padding-top:20px;
position:fixed;
display:flex;
flex-direction:column;
transition:transform .3s ease;
z-index:1000;
}

.sidebar.hide{
transform:translateX(-100%);
}

.sidebar h3{
text-align:center;
color:#ff8c00;
}

.sidebar a{
display:block;
padding:12px 20px;
color:#ccc;
text-decoration:none;
}

.sidebar a:hover{
background:#ff8c00;
color:black;
border-radius:10px;
}

.sidebar a.active-link{
background:#ff8c00;
color:black!important;
font-weight:bold;
border-radius:10px;
}

.content{
margin-left:220px;
padding:30px;
width:100%;
transition:margin-left .3s ease;
}

.content.full{
margin-left:0;
}

.top-bar{
display:flex;
align-items:center;
margin-bottom:15px;
justify-content: space-between;
}

.hamburger{
background:none;
border:none;
color:white;
font-size:26px;
cursor:pointer;
margin-right:10px;
}

button{
padding:8px 12px;
background:#ff8c00;
border:none;
border-radius:5px;
font-weight:bold;
cursor:pointer;
}

table{
width:100%;
margin-top:20px;
border-collapse:collapse;
}

th,td{
padding:10px;
border-bottom:1px solid #333;
}

th{
background:#1e1e1e;
color:#ff8c00;
}

.pagination{
margin-top:20px;
}

.menu-items{
flex:1;
}

.logout-form{
margin-top:auto;
}

.logout-btn{
width:100%;
}

/* MOBILE */

@media (max-width:768px){

.sidebar{
transform:translateX(-100%);
}

.sidebar.show{
transform:translateX(0);
}

.content{
margin-left:0;
}

}
.close-sidebar{
background:none;
border:none;
color:white;
font-size:20px;
display:none;
}

/* Mobile */

@media (max-width:768px){

.close-sidebar{
display:block;
}

}

</style>
</head>
<body>

<div class="d-flex">

<!-- Sidebar -->

<div class="sidebar" id="sidebar">

<div class="sidebar-header" style="gap:10px;border-bottom:1px solid #333;display:flex;justify-content:space-between;align-items:center;padding:0 10px;">
<div style="display:flex;gap:10px;align-items:center;">

<img 
src="{{ (optional(auth()->user()?->images)->image ?? 'avatar.jpeg') }}"

alt="Profile Image"
width="50"
height="50"
style="border-radius:50%;"
>

<h4 style="margin-top:10px;">{{ auth()->user()->name }}</h4>


</div>

<button class="close-sidebar" onclick="toggleSidebar()">
<i class="bi bi-x-lg"></i>
</button>

</div>

<div class="mt-3">
            
            @if(auth()->user()->role === 'super_admin')
            <a href="{{ route('super.dashboard') }}" class="nav-link {{ request()->routeIs('super.dashboard') ? 'active-link' : '' }}">Dashboard</a>
            <a href="{{ route('super.profile') }}" class="nav-link {{ request()->routeIs('super.profile') ? 'active-link' : '' }}">Profile</a>
            <a href="{{ route('super.admin.list') }}" class="nav-link {{ request()->routeIs('super.admin.list') ? 'active-link' : '' }}">Admin List</a>
            <a href="{{ route('customer.list') }}" class="nav-link {{ request()->routeIs('customer.list') ? 'active-link' : '' }}">Customers</a>
            <a href="{{ route('all-products') }}" class="nav-link {{ request()->routeIs('all-products') ? 'active-link' : '' }}">Products</a>
            <a href="{{ route('my-wishlist') }}" class="nav-link {{ request()->routeIs('my-wishlist') ? 'active-link' : '' }}">Wishlist</a>
            <a href="{{ route('orders') }}" class="nav-link {{ request()->routeIs('orders') ? 'active-link' : '' }}">My Orders</a>



            @elseif(auth()->user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active-link' : '' }}">Dashboard</a>
            <a href="{{ route('admin.profile') }}" class="nav-link {{ request()->routeIs('admin.profile') ? 'active-link' : '' }}">Profile</a>
            <a href="{{ route('admin.orders') }}" class="nav-link {{ request()->routeIs('admin.orders') ? 'active-link' : '' }}">Order List</a>
            <a href="{{ route('my-wishlist') }}" class="nav-link {{ request()->routeIs('my-wishlist') ? 'active-link' : '' }}">Wishlist</a>
            <a href="{{ route('admin.my-products') }}" class="nav-link {{ request()->routeIs('admin.my-products') ? 'active-link' : '' }}">My Products</a>
            <a href="{{ route('admin.all-products') }}" class="nav-link {{ request()->routeIs('admin.all-products') ? 'active-link' : '' }}">All Products</a>
            <a href="{{ route('orders') }}" class="nav-link {{ request()->routeIs('orders') ? 'active-link' : '' }}">My Orders</a>

            
            
            @elseif(auth()->user()->role === 'customer')
            <a href="{{ route('customer.dashboard') }}" class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active-link' : '' }}">Dashboard</a>
            <a href="{{ route('profile') }}" class="nav-link {{ request()->routeIs('profile') ? 'active-link' : '' }}">Profile</a>
            <a href="{{ route('orders') }}" class="nav-link {{ request()->routeIs('orders') ? 'active-link' : '' }}">My Orders</a>
            <a href="{{ route('my-wishlist') }}" class="nav-link {{ request()->routeIs('my-wishlist') ? 'active-link' : '' }}">Wishlist</a>
            <a href="#" class="nav-link">Support</a>
            @endif
        </div>

<form method="POST" action="{{ route('logout') }}" class="logout-form">
@csrf
    <input type="hidden" name="fcm_token" id="logout_fcm_token">

<button type="submit" class="logout-btn">
Logout
</button>
</form>

</div>

<!-- Content -->

<div class="content" id="content">

<div class="top-bar">
<button class="hamburger" onclick="toggleSidebar()">
<i class="bi bi-list"></i>
</button>

</div>

@yield('content')

</div>

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
    // console.log(" Firebase initialized", firebaseConfig);

    const messaging = firebase.messaging();

    // Register Service Worker
    navigator.serviceWorker.register('/firebase-messaging-sw.js')
        .then((registration) => {
            // console.log(" Service Worker registered:", registration);

            Notification.requestPermission()
                .then((permission) => {
                    // console.log(" Notification permission:", permission);

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
                        // console.warn("Notification permission denied by user");
                    }

                }).catch(err => console.error(" Notification permission request error:", err));

        }).catch(err => console.error(" Service Worker registration error:", err));

    // Listen for foreground messages
    messaging.onMessage((payload) => {
        console.log(" Foreground message received:", payload);
        alert("Notification : " + payload.notification.title + "\n" + payload.notification.body )
    });
});
    
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById('logout_fcm_token').value = localStorage.getItem('fcm_token') ;
});

document.addEventListener("DOMContentLoaded", function () {

    let token = localStorage.getItem('fcm_token');

    if (token) {
        fetch('/save-fcm', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                fcm_token: token,
                device_type: /android|iphone|ipad|ipod/i.test(navigator.userAgent) ? 'mobile' : 'web'
            })
        });
    }

});




function toggleSidebar(){

let sidebar = document.getElementById("sidebar");
let content = document.getElementById("content");

if(window.innerWidth <= 768){

sidebar.classList.toggle("show");

}else{

sidebar.classList.toggle("hide");
content.classList.toggle("full");

}

}



</script>


</body>
</html>



