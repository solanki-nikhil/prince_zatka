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
                        <h2 class="content-header-title float-start mb-0">{{$title ?? 'View Replaced Items'}} </h2>
                        <!-- <a role="button" class="btn btn-primary float-right" data-bs-toggle="modal" data-bs-target="#serialNoModal">Add SerialNo</a>
                        <a role="button" class="btn btn-success float-right mx-2" href="{{ route('warranty.add') }}">Add/Check Warranty</a> -->

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
                                        <th>Location</th>
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
                                        <th>Remarks</th>
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
                    data: 'location_status',
                    name: 'location_status',
                    orderable: false,
                    sortable: false
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
                //     name: 'cus_name',
                //     orderable: false,
                //     sortable: false
                // },
                // {
                //     data: 'cus_village',
                //     name: 'cus_village'
                // },
                // {
                //     data: 'cus_mobile',
                //     name: 'cus_mobile',
                //     orderable: false,
                //     sortable: false
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
                {
                    data: 'remarks',
                    name: 'remarks',
                    defaultContent: '-',
                    orderable: false,
                    sortable: false

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

    $(document).on("show.bs.dropdown", ".table .dropdown", function() {
        let $menu = $(this).find(".dropdown-menu");
        $("body").append($menu.detach()); // Move dropdown to body
        let offset = $(this).offset();
        let width = $(this).outerWidth();
        $menu.css({
            "position": "absolute",
            "top": offset.top + $(this).outerHeight(),
            "left": offset.left,
            "width": width,
            "z-index": "1050"
        });
    });

    $(document).on("hide.bs.dropdown", ".table .dropdown", function() {
        let $menu = $(this).find(".dropdown-menu");
        $(this).append($menu.detach()); // Move dropdown back to original position
    });

    $(document).on('click', '.change-status', function() {
        let status = $(this).data('value');
        let id = $(this).data('id');
        updateStatus(status, id);
    });

    function updateStatus(status, id) {
        var url = "{{route('serialno.update')}}";
        $.ajax({
            type: "PUT",
            url: url,
            dataType: 'json',
            data: {
                "_token": "{{ csrf_token() }}",
                "status": status,
                "id": id,
                "serial_no_id": id,
            },
            beforeSend: function() {},
            success: function(data) {
                console.log(data);
                table.ajax.reload(null, false);
                toastr.success(data.message, 'Success');
            },
            error: function(error) {
                toastr.error('Something went wrong', 'Opps!');
                $(document.body).css('pointer-events', '');
            }
        });
    }
</script>

<style>
    .dropdown-menu {
        position: absolute !important;
        will-change: transform;
    }
</style>
@endsection