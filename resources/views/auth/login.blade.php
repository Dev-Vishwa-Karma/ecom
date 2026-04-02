<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

@extends('layouts.guest')

@section('title', 'Login')

@section('content')

<h2>Login</h2>

@if(session('success'))
    <p class="success">{{ session('success') }}</p>
@endif

@if ($errors->any())
    <div class="error">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

<form method="POST" action="{{ route('login') }}">
    @csrf

    <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>

    <input type="password" name="password" placeholder="Password" required>
    <input type="hidden" name="fcm_token" id="fcm_token">
<input type="hidden" name="device_type" id="device_type">

    <button type="submit">Login</button>
</form>

<p>Don't have an account? 
    <a href="{{ route('register') }}">Register</a>
</p>
<div> 
    <div>   
    <p style="display:block; text-align:center; margin:5px">OR Continue with</p>
</div> 
   <div class="d-flex justify-content-center gap-3">
     <a id="google-login" href="{{ url('/auth/google') }}" class="d-flex flex-column align-items-center" ><img src="Google.png" style="padding:4px; width: 40px; height: 40px;" alt="Google Login">Google</a>
    <a id="google-login" href="{{ url('/auth/google') }}" class="d-flex flex-column align-items-center"><img src="facebook.png" style="width: 40px; height: 40px;" alt="Facebook Login">Facebook</a>
   </div>
</div>


@endsection
<script>
// document.addEventListener("DOMContentLoaded", function () {

//     const form = document.querySelector("form");

//     form.addEventListener("submit", function (e) {

//         let token = localStorage.getItem('fcm_token');

//         // console.log("Before Submit Token:", token);

//         if (!token) {
//             e.preventDefault();

//             alert("Waiting for FCM token...");

//             // wait until token comes
//             let interval = setInterval(() => {
//                 let newToken = localStorage.getItem('fcm_token');

//                 // console.log("Checking Token:", newToken);

//                 if (newToken) {
//                     clearInterval(interval);

//                     document.getElementById('fcm_token').value = newToken;

//                     // console.log("Token Set:", newToken);

//                     form.submit(); 
//                 }

//             }, 1000);

//         } else {
//             document.getElementById('fcm_token').value = token;
//         }

//     });

// });
// document.addEventListener("DOMContentLoaded", function () {

//     let deviceInput = document.getElementById('device_type');

//     let userAgent = navigator.userAgent.toLowerCase();

//     if (/android|iphone|ipad|ipod/.test(userAgent)) {
//         deviceInput.value = "mobile";
//     } else {
//         deviceInput.value = "web";
//     }

//     // console.log("Device Type:", deviceInput.value);

// });
// document.getElementById('google-login').addEventListener('click', function(e) {
//     e.preventDefault();

//     let fcmToken = localStorage.getItem('fcm_token') || '';
//     let deviceType = /android|iphone|ipad|ipod/i.test(navigator.userAgent) ? 'mobile' : 'web';

//     // Redirect to Google login with query params
//     let url = '/auth/google?fcm_token=' + encodeURIComponent(fcmToken) + '&device_type=' + deviceType;
//     window.location.href = url;
// });

</script>
</body>

</html>
