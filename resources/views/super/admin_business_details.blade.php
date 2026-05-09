@extends('layouts.app')

@section('title','Business Approval')
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

    <h2 style="text-align:center; margin-bottom:20px;">Business Approval</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Business</th>
                <th>Email</th>
                <th>Status</th>
                <th>Updated</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            @foreach($list as $item)
            <tr>

                <td>{{ $item->id }}</td>
                <td>{{ $item->business_name }}</td>
                <td>{{ $item->email }}</td>

                <td>
                    <span class="status-badge {{ $item->status }}">
                        {{ ucfirst($item->status) }}
                    </span>
                </td>

                <td>{{ $item->updated_at?->format('d M Y') }}</td>

                <td>
                    <button class=" view-btn"
                        data-id="{{ $item->id }}"
                        data-name="{{ $item->business_name }}"
                        data-email="{{ $item->email }}"
                        data-phone="{{ $item->business_phone }}"
                        data-address="{{ $item->business_address }}"
                        data-account="{{ $item->account_number }}"
                        data-ifsc="{{ $item->ifsc_code }}"
                        data-account_holder="{{ $item->account_holder_name }}"
                        data-pan="{{ $item->pan_card }}"
                        data-gst="{{ $item->gst_certificate }}"
                        data-status="{{ $item->status }}">
                        View
                    </button>
                </td>

            </tr>
            @endforeach
        </tbody>
    </table>

</div>
<!-- Modal -->
<div class="modal fade" id="detailModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header d-flex justify-content-between">
                <h5>Business Details</h5>
                <span class="close" style="cursor: pointer;" onclick="closeModal()">×</span>

            </div>

            <div class="modal-body">

                <p><b>Name:</b> <span id="mName"></span></p>
                <p><b>Email:</b> <span id="mEmail"></span></p>
                <p><b>Phone:</b> <span id="mPhone"></span></p>
                <p><b>Address:</b> <span id="mAddress"></span></p>
                <p><b>Account Number:</b> <span id="mAccount"></span></p>
                <p><b>IFSC Code:</b> <span id="mIfsc"></span></p>
                <p><b>Account Holder Name:</b> <span id="mAccountHolder"></span></p>

                <button onclick="togglePreview('panImg')">View PAN</button>
                <img id="panImg" style="display:none;max-width:100%;margin-top:10px;">

                <button onclick="togglePreview('gstImg')">View GST</button>
                <img id="gstImg" style="display:none;max-width:100%;margin-top:10px;">

            </div>

            <div class="modal-footer">

                <!-- <input type="text" id="remark" placeholder="Reject reason"> -->

                <button id="approveBtn" style="background:green;color:#fff;">Approve</button>
                <button id="rejectBtn" style="background:red;color:#fff;">Reject</button>

            </div>

        </div>
    </div>
</div>

<script>

    let currentId = null;
    function closeModal() {
    const modalEl = document.getElementById('detailModal');
    const modalInstance = bootstrap.Modal.getInstance(modalEl);
    if (modalInstance) {
        modalInstance.hide();
    }
}



    document.querySelectorAll('.view-btn').forEach(btn => {

        btn.addEventListener('click', function() {

            currentId = this.dataset.id;

            document.getElementById('mName').innerText = this.dataset.name;
            document.getElementById('mEmail').innerText = this.dataset.email;
            document.getElementById('mPhone').innerText = this.dataset.phone;
            document.getElementById('mAddress').innerText = this.dataset.address;
            document.getElementById('mAccount').innerText = this.dataset.account;
            document.getElementById('mIfsc').innerText = this.dataset.ifsc;
            document.getElementById('mAccountHolder').innerText = this.dataset.account_holder;

            document.getElementById('panImg').src = this.dataset.pan;
            document.getElementById('gstImg').src = this.dataset.gst;

            // hide images initially
            document.getElementById('panImg').style.display = 'none';
            document.getElementById('gstImg').style.display = 'none';

            // disable buttons if already processed
            let status = this.dataset.status;

            document.getElementById('approveBtn').style.display =
                status === 'pending' ? 'inline-block' : 'none';

            document.getElementById('rejectBtn').style.display =
                status === 'pending' ? 'inline-block' : 'none';

            new bootstrap.Modal(document.getElementById('detailModal')).show();
        });
    });

    // APPROVE
    document.getElementById('approveBtn').addEventListener('click', function() {

        fetch(`/super/admin/business/${currentId}/approve`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                location.reload();
            });
    });

    // REJECT
    document.getElementById('rejectBtn').addEventListener('click', function() {

        let remark = document.getElementById('remark').value;

        fetch(`/super/admin/business/${currentId}/reject`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    remark
                })
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                location.reload();
            });
    });

    // preview toggle
    function togglePreview(id) {
        let img = document.getElementById(id);
        img.style.display = img.style.display === 'none' ? 'block' : 'none';
    }
</script>
@endsection