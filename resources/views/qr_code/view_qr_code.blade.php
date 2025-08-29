@extends('layouts.app')
@section('title', 'QRCode')
@section('content')

<div class="app-content content ">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row">
            <div class="content-header-left col-md-12 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">View QRCode</h2>
                        <a role="button" class="btn btn-primary float-right" href="{{route('qr-code.create')}}">Add QRCode</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <section id="basic-datatable">
                <div class="row">
                    <div class="col-12">
                        <div class="card p-2">
                            <table id="qrcode" class="datatables-basic table">
                                <thead>
                                    <tr>
                                        <th>Sr.No</th>
                                        <th>Action</th>
                                        <th>Date</th>
                                        <th>Total QR</th>
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

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-transparent border-bottom">
                <h1 class="text-center mb-1" id="exampleModalTitle">QRCode</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">

            </div>
        </div>
    </div>
</div>
@endsection

@section('pagescript')
<script type="application/javascript">
    'use strict';
    const URL = "{{route('qr-code.index')}}";

    //status_master listing using ajax server side datatable
    var table = '';
    $(function() {
        table = $('#qrcode').DataTable({
            ajax: URL,
            processing: true,
            serverSide: true,
            fixedHeader: true,
            scrollX: true,
            columns: [{
                    data: 'id',
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    sortable: false
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'total',
                    name: 'total'
                },
            ],
            initComplete: function(settings, json) {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                })
            }
        });
    });

    $(document).on('click', '.view_qrcode', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var url = "{{route('qr-code.show','id')}}".replace('id', id);
        $("#exampleModal").modal("show");
        $.ajax({
            url: url,
            type: 'get',
            datatype: 'json',
            data: {
                "_token": "{{ csrf_token() }}",
            },
            success: function(response) {
                $(".modal-body").html(response.html);
            }
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
                    axios.delete(URL + '/' + id)
                        .then(function(response) {
                            if (response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Your record has been deleted.',
                                    customClass: {
                                        confirmButton: 'btn btn-success'
                                    }
                                });
                                btn.parent().parent().parent().remove();
                            } else {
                                toastr.error("Someting went wrong. Please try again.", 'Opps!');
                            }
                        })
                        .catch(function() {
                            toastr.error('Someting went wrong. Please try again.', 'Opps!');
                        });
                } else {
                    Swal.fire({
                        text: 'Your QRCode is safe!'
                    });
                }
            });
    });

    $(document).on('click', '.delete-qrcode', function() {
        var btn = $(this);
        var id = btn.data('id');
        var url = "{{route('qr-code.update','id')}}".replace('id', id);
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
            // .then(function(result) {
            //     if (result.value) {
            //         axios.delete(url)
            //             .then(function(response) {
            //                 if (response) {
            //                     Swal.fire({
            //                         icon: 'success',
            //                         title: 'Deleted!',
            //                         text: 'Your record has been deleted.',
            //                         customClass: {
            //                             confirmButton: 'btn btn-success'
            //                         }
            //                     });
            //                     btn.parent().parent().remove();
            //                 } else {
            //                     toastr.error("Someting went wrong. Please try again.", 'Opps!');
            //                 }
            //             })
            //             .catch(function() {
            //                 toastr.error('Someting went wrong. Please try again.', 'Opps!');
            //             });
            //     } else {
            //         Swal.fire({
            //             text: 'Your QRCode is safe!'
            //         });
            //     }
            // });
            .then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        type: "PUT",
                        url: url,
                        dataType: 'json',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "id": id,
                        },
                        success: function(response) {
                            if (response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Your record has been deleted.',
                                    customClass: {
                                        confirmButton: 'btn btn-success'
                                    }
                                });
                                btn.parent().parent().remove();
                            } else {
                                toastr.error("Someting went wrong. Please try again.", 'Opps!');
                            }
                        }
                    });
                } else {
                    swal("Your QRCode is safe!");
                }
            });
    });
</script>
@endsection