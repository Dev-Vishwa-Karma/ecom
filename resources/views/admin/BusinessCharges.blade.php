@extends('layouts.app')

@section('title','Monthly Charges')
<style>
    .table-box {
        padding: 20px;
        background: #1e1e1e;
        border-radius: 10px;
        color: #fff;
    }

    .status-badge {
        padding: 4px 10px;
        border-radius: 5px;
        font-size: 12px;
    }

    .pending {
        background: orange;
        color: black;
    }

    .approved {
        background: green;
    }

    .rejected {
        background: red;
    }



    .view-btn {
        background: #ff8c00;
        color: black;
    }
</style>

@section('content')

<div class="table-box">

    <h2 style="text-align:center; margin-bottom:20px;">Monthly Charges</h2>

    <table>
        <thead>
            <tr>
                <th>Month/year</th>
                <th>Sales</th>
                <th>Charges</th>
                <th>Status</th>
                <th>Confirm date</th>
            </tr>
        </thead>

        <tbody>
            @foreach($data as $data)
            <tr>

                <td>{{ $data->month }}/{{ $data->year }}</td>
                <td>{{ $data->total_amount }}</td>
                <td>{{ $data->commission }}</td>
                <td>
                    <span class="status-badge {{ $data->status }}">
                        {{ ucfirst($data->status) }}
                    </span>
                </td>
                <td>
                    <span>
{{ ucfirst($data->paid_at) }}
                     </span>
            </td>

               

            </tr>
            @endforeach
        </tbody>
    </table>

</div>


   
@endsection