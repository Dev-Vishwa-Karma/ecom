<!DOCTYPE html>
<html>
<head>
    <title>Back in Stock</title>
</head>
<body>
    <h2>Hello</h2>

    <p>The product you requested is now back in stock.</p>

    <h3>Product Details:</h3>
    <ul>
        <li>Color: {{ $variant->color }}</li>
        <li>Size: {{ $variant->size }}</li>
        <li>Price: {{ $variant->price }}</li>
    </ul>

    <p>Visit our website and grab it before it runs out again 🚀</p>
    <a href="{{ url('http://127.0.0.1:8000/') }}" target="_blank">Go to Website</a>
</body>
</html>