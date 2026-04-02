<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    @extends('layouts.guest')
@section('title', 'Register')
@section('content')

    <h2>Create Admin</h2>

@if(session('success'))
    <p style="color:green">{{ session('success') }}</p>
@endif

<form method="POST" action="{{ route('super.admin.store') }}">
    @csrf

    <input type="text" name="name" placeholder="Name" required>
    <br><br>

    <input type="email" name="email" placeholder="Email" required>
    <br><br>

    <input type="text" name="address" placeholder="Address" required>
    <br><br>

    <input type="text" name="mobile" placeholder="Mobile" required>
    <br><br>

    <input type="password" name="password" placeholder="Password" required>
    <br><br>

    <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
    <br><br>

   <a >
    <button onclick="window.history.back()">Back</button>
</a>
 <button type="submit">Create Admin</button>
</form>
@endsection

</body>
</html>