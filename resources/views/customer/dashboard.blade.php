<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    
</head>
<body>
    @extends('layouts.app')
    @include('all-products')

@section('title', 'customer Dashboard')

@section('content')
    <div class="container-fluid">
        <h2 class="mb-4">Welcome, customer!</h2>
    </div>
    <div id="carouselExampleSlidesOnly" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="Promo-Ecommerce-Website-Banner-Design-1180x664.jpg " class="d-block w-100" alt="Slide 1 Image">
    </div>
    <div class="carousel-item">
      <img src="..." class="d-block w-100" alt="Slide 2 Image">
    </div>
    <div class="carousel-item">
      <img src="..." class="d-block w-100" alt="Slide 3 Image">
    </div>
  </div>
</div>
<x-all-product :product="$product"/>
@endsection


</body>
</html>