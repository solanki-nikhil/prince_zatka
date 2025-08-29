@extends('layouts.app')
@section('title', 'SerialNo')
@section('content')

<div class="app-content content ">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row">
            <div class="content-header-left col-md-12 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">{{$title ?? 'View Serial No'}} </h2>
                        <a role="button" class="btn btn-primary float-right" data-bs-toggle="modal" data-bs-target="#serialNoModal">Add SerialNo</a>
                        <a role="button" class="btn btn-success float-right mx-2" href="{{ route('warranty.add') }}">Add/Check Warranty</a>

                    </div>

                </div>
            </div>
        </div>


        <div class="content-body">
            <section id="basic-datatable">
                <div class="row">
                    <div class="col-12">
                        <div class="card p-2">
                            <table id="serialno" class="datatables-basic table">
                                <thead>
                                    <tr>
                                        <th>Sr.No</th>
                                        <th>Action</th>
                                        <th>Status</th>
                                        <th>S/N</th>
                                        <!-- <th>Category</th> -->
                                        <th>Product</th>
                                        <th>Code</th>
                                        <th>Dealer</th>
                                        <th>Customer Details</th>
                                        <!-- <th>Cus.Name</th> -->
                                        <!-- <th>Cus.Village</th> -->
                                        <!-- <th>Cus.Mobile</th> -->
                                        <th>StartDate</th>
                                        <th>ExpireDate</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

</div>
<!-- Add SerialNo Modal -->
<!-- Serial Number Modal (Add & Edit) -->
<div class="modal fade" id="serialNoModal" tabindex="-1" aria-labelledby="serialNoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="serialNoModalLabel">Add Serial Number</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="serialNoForm">
                    @csrf
                    <input type="hidden" id="serial_no_id" name="serial_no_id">
                    <div class="form-group">
                        <label for="serial_no">Serial Number</label>
                        <input type="text" class="form-control" id="serial_no" name="serial_no" required>
                    </div>
                    <div class="d-flex  mt-2">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <div class="mx-auto"></div>
                        <button class="btn" id="btnUploadExcel" data-bs-toggle="modal" data-bs-target="#uploadModal">
                            Upload Excel
                            <i class="fa fa-table" aria-hidden="true"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- upload csv to load serial numbers -->

<!-- Bootstrap Modal for Upload -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Upload CSV File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="csvUploadForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="file" name="csv_file" id="csvFileInput" class="form-control" accept=".csv" required>
                </div>
                <div class="modal-footer d-flex justify-content-between w-100">
                    <a href="{{ asset('sample_serial_no_upload.csv') }}" download>Download Sample File</a>
                    <div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>



<!-- END: Content-->
@endsection

@section('pagescript')
<script type="application/javascript">
    'use strict';
    var apiURL = "{{route('serialno.index')}}";
    let path = window.location.pathname; // Get the current URL path

    if (path.includes('/serialno/replaced')) {
        apiURL = "{{ route('serialno.replaced') }}";
    } else if (path.includes('/serialno/rejected')) {
        apiURL = "{{ route('serialno.rejected') }}";
    } else {
        apiURL = "{{ route('serialno.index') }}"; // Default (All)
    }

    //product listing using ajax server side datatable
    var table = '';
    $(function() {
        table = $('#serialno').DataTable({
            // ajax: URL,
            processing: true,
            serverSide: true,
            fixedHeader: true,
            scrollX: true,
            ajax: {
                url: apiURL,
                type: "GET"
            },
            columns: [{
                    data: 'id',
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    },
                    orderable: false,
                    sortable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    sortable: false
                },
                {
                    data: null,
                    name: 'order_status',
                    render: function(data, type, row, meta) {
                        let is_reject = row.is_reject || false;
                        let is_replace = row.is_replace || false;
                        let status = row.status;
                        if (is_reject) {
                            if (status) {
                                return "Reject" + " <br>" + row.status;
                            }
                            return "Reject";
                        }
                        if (is_replace) {
                            if (status) {
                                return "Replace" + " <br>" + row.status;
                            }
                            return "Replace";
                        }
                        return "";
                    },
                },
                {
                    data: 'sn',
                    name: 'sn'
                },
                {
                    data: null,
                    name: 'product_info',
                    render: function(data, type, row) {
                        let category = row.product?.category?.category_name || '-';
                        let productName = row.product?.product_name || '-';
                        return category !== '-' && productName !== '-' ? `${category} / ${productName}` : category !== '-' ? category : productName;
                    }
                },

                // {
                //     data: 'product.category.category_name',
                //     name: 'product.category.category_name',
                //     defaultContent: '-'
                // },
                // {
                //     data: 'product.product_name',
                //     name: 'product.product_name'
                // },
                {
                    data: 'product.product_code',
                    name: 'product.product_code',
                    defaultContent: '-'
                },
                {
                    data: 'user.name',
                    name: 'user.name',
                },
                // {
                //     data: 'cus_name',
                //     name: 'cus_name'
                // },
                // {
                //     data: 'cus_village',
                //     name: 'cus_village'
                // },
                // {
                //     data: 'cus_mobile',
                //     name: 'cus_mobile'
                // },
                {
                    data: null,
                    name: 'customer_info',
                    render: function(data, type, row) {
                        let name = row.cus_name || '-';
                        let village = row.cus_village || '-';
                        let mobile = row.cus_mobile || '-';

                        let details = [];

                        if (name !== '-') details.push(name);
                        if (village !== '-') details.push(village);
                        if (mobile !== '-') details.push(mobile);

                        return details.length > 0 ? details.join(' | ') : '-';
                    }
                },
                {
                    data: 'valid_from',
                    name: 'valid_from'
                },
                {
                    data: 'valid_to',
                    name: 'valid_to'
                },
            ],
            initComplete: function(settings, json) {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                })
            },

        });
    });


    $(document).on('click', '.delete', function() {
        var btn = $(this);
        var id = btn.data('id');
        Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-danger ms-1'
                },
                buttonsStyling: false
            })
            .then(function(result) {
                if (result.value) {
                    axios.delete(apiURL + '/' + id)
                        .then(function(response) {
                            if (response.data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Your record has been deleted.',
                                    customClass: {
                                        confirmButton: 'btn btn-success'
                                    }
                                });
                                btn.parent().parent().parent().remove();
                            } else if (response.data.warning) {
                                toastr.warning(response.data.errorMessage, 'Opps!');
                            } else {
                                toastr.error(response.data.errorMessage, 'Opps!');
                            }
                        })
                        .catch(function() {
                            toastr.error('Someting went wrong. Please try again.', 'Opps!');
                        });
                } else {
                    Swal.fire({
                        text: 'Your data is safe!'
                    });
                }
            });
    });

    $(document).ready(function() {

        $("#serialNoForm").on("submit", function(e) {
            e.preventDefault();

            var id = $("#serial_no_id").val();
            var formData = $(this).serialize();

            var url = id ? "{{ route('serialno.update') }}" : "{{ route('serialno.store') }}"; // Use different routes
            var method = id ? "PUT" : "POST";

            $.ajax({
                url: url,
                type: method,
                data: formData,
                success: function(response) {
                    $("#serialNoModal").modal("hide");
                    table.ajax.reload(); // Refresh DataTable
                    toastr.success(response.message);
                },
                error: function(xhr) {
                    toastr.error("Something went wrong!");
                }
            });
        });


    });

    // START :: For Excel Export 
    $(document).on('click', '.product-report-export', function() {
        $('#product_report').attr('action', "{{route('product-report-export')}}");
        $('form#product_report').submit();
    });

    $(document).on("click", ".editSerial", function() {
        var id = $(this).data("id");
        var serial_no = $(this).data("serial");

        // Set values in the modal
        $("#serialNoModalLabel").text("Edit Serial Number");
        $("#serial_no").val(serial_no);
        $("#serial_no_id").val(id);

        // Change button text
        $("#serialNoForm button[type=submit]").text("Update");

        // Show modal
        $("#serialNoModal").modal("show");
    });

    // Reset modal for Add
    $(document).on("click", "#addSerialBtn", function() {
        $("#serialNoModalLabel").text("Add Serial Number");
        $("#serial_no").val("");
        $("#serial_no_id").val("");
        $("#serialNoForm button[type=submit]").text("Save");
        $("#serialNoModal").modal("show");
    });
    // END :: For Excel Export 

    $(document).on("click", "#btnUploadExcel", function() {
        $("#serialNoModal").modal("hide");

    });

    $(document).on('submit', '#csvUploadForm', function(e) {
        e.preventDefault();

        var formData = new FormData(this);

        $.ajax({
            url: "{{ route('serialno.uploadcsvserialno') }}",
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function() {
                Swal.fire({
                    title: 'Uploading...',
                    text: 'Please wait while we process your CSV file.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                // Disable the submit button to prevent multiple submissions
                $('#csvUploadForm button[type="submit"]').prop('disabled', true);
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message || 'CSV uploaded successfully.',
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                let errorMessage = 'Failed to upload CSV. Please try again.';

                if (xhr.responseJSON) {
                    if (xhr.responseJSON.errors) {
                        // Show multiple validation errors
                        errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    } else if (xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage,
                });
            },
            complete: function() {
                // Re-enable the submit button
                $('#csvUploadForm button[type="submit"]').prop('disabled', false);
            }
        });
    });
</script>
@endsection