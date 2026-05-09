@extends('layouts.app')

@section('title', 'Admin List')

@section('content')

<style>
.top-bar {
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.filter-box {
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    margin-bottom:20px;
}

.filter-box input,
.filter-box select {
    padding:8px;
    border-radius:6px;
    border:1px solid #ccc;
}

.filter-box button {
    padding:8px 15px;
    background:#ff8c00;
    border:none;
    border-radius:6px;
}

table {
    width:100%;
    border-collapse:collapse;
    background:#1e1e1e;
    color:#fff;
}

th, td {
    padding:12px;
    border-bottom:1px solid #333;
    text-align:left;
}

th {
    background:#2a2a2a;
}

.status-active {
    color:#28a745;
    font-weight:bold;
}

.status-deactive {
    color:#dc3545;
    font-weight:bold;
}

.action-btn {
    padding:5px 10px;
    border:none;
    border-radius:5px;
    cursor:pointer;
}

.view-btn {  }
.delete-btn {  }

.pagination {
    margin-top:20px;
}
</style>

<div class="top-bar">
    <h2>Admin List</h2>

    <a href="{{ route('super.admin.create') }}">
        <button class="action-btn view-btn">+ Create Admin</button>
    </a>
</div>

<!--  FILTER -->
<div class="filter-box">

<form method="GET" style="display:flex; gap:10px; flex-wrap:wrap;">

    <input type="text" 
           name="search" 
           value="{{ request('search') }}"
           placeholder="Search name, email, mobile">

    <select name="status">
        <option value="">All Status</option>
        <option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option>
        <option value="deactivated" {{ request('status')=='deactivated'?'selected':'' }}>Deactivated</option>
    </select>

    <button type="submit">Filter</button>

    <a href="{{ route('super.admin.list') }}">
        <button type="button">Reset</button>
    </a>

</form>

</div>

<!-- TABLE -->
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Mobile</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        @forelse($admins as $admin)

        <tr>
            <td>{{ $admin->name }}</td>
            <td>{{ $admin->email }}</td>
            <td>{{ $admin->mobile }}</td>

            <td>
                @if($admin->status == 'active')
                    <span class="status-active">Active</span>
                @else
                    <span class="status-deactive">Deactivated</span>
                @endif
            </td>

            <td>
                <button class="action-btn view-btn"
                        data-bs-toggle="modal"
                        data-bs-target="#editAdminModal{{ $admin->id }}">
                    View/Edit
                </button>

                <button class="action-btn delete-btn"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteModal{{ $admin->id }}">
                    Delete
                </button>
            </td>
        </tr>

        <!--  EDIT MODAL -->
        <div class="modal fade" id="editAdminModal{{ $admin->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">

                    <form method="POST" action="{{ route('super.admin.update', $admin->id) }}">
                        @csrf

                        <div class="modal-header">
                            <h5>Edit Admin</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">

                            <input type="text" name="name" class="form-control mb-2"
                                   value="{{ $admin->name }}">

                            <input type="email" name="email" class="form-control mb-2"
                                   value="{{ $admin->email }}">

                            <input type="text" name="mobile" class="form-control mb-2"
                                   value="{{ $admin->mobile }}">

                            <textarea name="address" class="form-control mb-2">{{ $admin->address }}</textarea>

                            <select name="status" class="form-control">
                                <option value="active" {{ $admin->status=='active'?'selected':'' }}>Active</option>
                                <option value="deactivated" {{ $admin->status=='deactivated'?'selected':'' }}>Deactivated</option>
                            </select>

                        </div>

                        <div class="modal-footer">
                            <button type="button" data-bs-dismiss="modal">Close</button>
                            <button type="submit">Save</button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

        <!--  DELETE MODAL -->
        <div class="modal fade" id="deleteModal{{ $admin->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">

                    <form method="POST" action="{{ route('super.admin.delete', $admin->id) }}">
                        @csrf

                        <div class="modal-header">
                            <h5>Confirm Delete</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            Delete <strong>{{ $admin->name }}</strong> ?
                        </div>

                        <div class="modal-footer">
                            <button type="button" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit">Delete</button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

        @empty
        <tr>
            <td colspan="5" style="text-align:center; color:#aaa;">
                No Admins Found
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

<!-- PAGINATION -->
<div class="pagination">
    {{ $admins->appends(request()->query())->links() }}
</div>

@endsection