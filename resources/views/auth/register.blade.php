<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>

@extends('layouts.guest')

@section('title', 'Register')

@section('content')

<h2>Register</h2>

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

<form method="POST" action="{{ route('register') }}">
    @csrf

    <input type="text" name="name" placeholder="Name" value="{{ old('name') }}" required>

    <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>

    <input type="text" name="address" placeholder="Address" value="{{ old('address') }}" required>

    <input type="text" name="mobile" placeholder="Mobile" value="{{ old('mobile') }}" required>

    <input type="password" name="password" placeholder="Password" required>

    <input type="password" name="password_confirmation" placeholder="Confirm Password" required>

    <button type="submit">Register</button>
</form>

<p>Already have an account? 
    <a href="{{ route('login') }}">Login</a>
</p>

@endsection

</body>
</html>