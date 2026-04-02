

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    @extends('layouts.app')
@section('title','customer List')

@section('content')

<div class="top-bar">
    <h2>customer List</h2>
    
</div>

<table>
    <tr>
             <th class="text-justify">Name</th>
                <th>Email</th>
                <th>Address</th>
                <th>Mobile</th>
    </tr>

    @foreach($customers as $customer)
    <tr>
        <td>{{ $customer->name }}</td>
                <td>{{ $customer->email }}</td>
                <td>{{ $customer->address }}</td>
                <td>{{ $customer->mobile }}</td>
    </tr>
    @endforeach
</table>

<div class="pagination">
    {{ $customers->links() }}
</div>

@endsection
</body>
</html>
</body>
</html>