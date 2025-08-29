@extends('layouts.app')
@section('title', 'Redeem Point')
@section('content')
<div class="app-content content ">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row">
            <div class="content-header-left col-md-12 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">Redeem User</h2>
                        <a role="button" class="btn btn-primary btn-create" href="{{route('redeem-point.create')}}">Redeem Point</a></li>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <section id="basic-datatable">
                <div class="row">
                    <div class="col-12">
                        <div class="card p-2">
                            <table id="redeem" class="datatables-basic table">
                                <thead>
                                    <tr>
                                        <th>Sr.No</th>
                                        <th>Action</th>
                                        <th>Name</th>
                                        <th>Point</th>
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
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header bg-transparent border-bottom"> 
                <h1 class="text-center mb-1" id="exampleModalTitle">Transection History</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" id="recive-body">

            </div>
        </div>
    </div>
</div>
@endsection

@section('pagescript')
<script type="application/javascript">
    'use strict';
    const URL = "{{route('redeem-point.index')}}";

    //status_master listing using ajax server side datatable
    var table = '';
    $(function() {
        table = $('#redeem').DataTable({
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
                    data: 'name',
                    name: 'user.name'
                },
                {
                    data: 'redeem',
                    name: 'redeem'
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

    $(document).on('click', '.recive', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var title = $(this).data('name');
        var url = "{{route('redeem-point.show','id')}}".replace('id', id);
        $("#exampleModal").modal("show");
        $.ajax({
            url: url,
            type: 'get',
            datatype: 'json',
            data: {
                "_token": "{{ csrf_token() }}",
            },
            success: function(response) {
                $("#recive-body").html(response.html);
            }
        });
    });

</script>
@endsection