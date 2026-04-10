<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta property="og:title" content="Check out my review!" />
    <meta property="og:description" content="I just rated this product." />
    <meta property="og:image" content="{{ asset('storage/reviews/' . $filename) }}" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />
    <meta property="og:type" content="website" />
    <title>Share Review</title>
</head>
<body>
<img src="{{ asset('storage/reviews/' . $filename) }}" style=" width:100%; max-width:1200px; height:auto;" alt="Review">
</body>
</html>