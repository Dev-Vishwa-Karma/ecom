<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>

<h2>Welcome {{ auth()->user()->name }}</h2>

<p>Email: {{ auth()->user()->email }}</p>
<p>Mobile: {{ auth()->user()->mobile }}</p>
<p>Address: {{ auth()->user()->address }}</p>

<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit">Logout</button>
</form>

</body>
</html>