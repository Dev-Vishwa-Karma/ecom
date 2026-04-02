<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    @extends('layouts.app')
@section('title', 'Admin List')

@section('content')

<div class="top-bar">
    <h2>Admin List</h2>
    <a href="{{ route('super.admin.create') }}">
        <button>Create Admin</button>
    </a>
</div>

<table >
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
        @foreach($admins as $admin)
        <tr>
            <td>{{ $admin->name }}</td>
            <td>{{ $admin->email }}</td>
            <td>{{ $admin->mobile }}</td>
            <td>{{ ucfirst($admin->status) }}</td>

            <td>
                <!-- View/Edit Button - opens the edit modal -->
                <button  data-bs-toggle="modal" data-bs-target="#editAdminModal{{ $admin->id }}">View/Edit</button>

                <!-- Delete Button - opens the delete confirmation modal -->
                <button  data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal{{ $admin->id }}">Delete</button>
            </td>
        </tr>

        <!-- Modal for Editing/View Admin -->
        <div class="modal fade" id="editAdminModal{{ $admin->id }}" tabindex="-1" aria-labelledby="editAdminModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ route('super.admin.update', $admin->id) }}">
                        @csrf
                        @method('POST')
                        <div class="modal-header">
                            <h5 class="modal-title" id="editAdminModalLabel">Edit Admin</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ $admin->name }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ $admin->email }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="mobile" class="form-label">Mobile</label>
                                <input type="text" class="form-control" id="mobile" name="mobile" value="{{ $admin->mobile }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="address" required>{{ $admin->address }}</textarea>
                            </div>

                            <!-- New Status Dropdown -->
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="active" {{ $admin->status === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="deactivated" {{ $admin->status === 'deactivated' ? 'selected' : '' }}>Deactivated</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button"  data-bs-dismiss="modal">Close</button>
                            <button type="submit" >Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal for Delete Confirmation -->
        <div class="modal fade" id="deleteConfirmationModal{{ $admin->id }}" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ route('super.admin.delete', $admin->id) }}">
                        @csrf
                        @method('POST')  <!-- DELETE method for actual delete action -->
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirm Deletion</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to delete the admin <strong>{{ $admin->name }}</strong>? </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button"  data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" >Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @endforeach
    </tbody>
</table>

<div class="pagination">
    {{ $admins->links() }}
</div>

@endsection

<!-- Bootstrap JS and Popper for modals -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>

</body>
</html>