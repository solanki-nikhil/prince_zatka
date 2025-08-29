@extends('layouts.app')
@section('title', 'Users')
@section('content')

<div class="app-content content ">
     <div class="content-overlay"></div>
     <div class="header-navbar-shadow"></div>
     <div class="content-wrapper container-xxl p-0">
          <div class="content-header row">
               <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                         <div class="col-12">
                              <h2 class="content-header-title float-start mb-0">View User</h2>
                              <a role="button" class="btn btn-primary btn-create waves-effect waves-float waves-light" href="{{route('user.create')}}">Add User</a>
                         </div>
                    </div>
               </div>
          </div>
          <div class="content-body">
               <section id="basic-datatable">
                    <div class="row">
                         <div class="col-12">
                              <div class="card p-2">
                                   <table id="user" class="datatables-basic table">
                                        <thead>
                                             <tr>
                                                  <th>Sr.No</th>
                                                  <th>Action</th>
                                                  <th>Status</th>
                                                  <!-- <th>Role</th> -->
                                                  <th>Name</th>
                                                  <th>Mobile</th>
                                                  <!-- <th>Email</th> -->
                                                  <th>Area</th>
                                                  <th>Company/GST</th>
                                                  <!-- <th>Address</th> -->
                                                  <th>Date</th>
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
     const URL = "{{route('user.index')}}";

     //user listing using ajax server side datatable
     var table = '';
     $(function() {
          table = $('#user').DataTable({
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
                         data: 'status',
                         name: 'status'
                    },
                    {
                         data: 'user.name',
                         name: 'user.name'
                    },
                    {
                         data: 'user.mobile',
                         name: 'user.mobile'
                    },
                    {
                         data: 'country.country_name',
                         name: 'country.country_name'
                    },
                    {
                         data: 'company',
                         name: 'company'
                    },
                    {
                         data: 'date',
                         name: 'date'
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
                                   if (response.data.status == true) {
                                        Swal.fire({
                                             icon: 'success',
                                             title: 'Deleted!',
                                             text: 'Your record has been deleted.',
                                             customClass: {
                                                  confirmButton: 'btn btn-success'
                                             }
                                        });
                                        btn.parent().parent().parent().remove();
                                   } else if (response.data.status == false) {
                                        toastr.warning(response.data.message, 'Opps!');
                                   } else {
                                        toastr.error(response.data.message, 'Opps!');
                                   }
                              })
                              .catch(function() {
                                   toastr.error('Someting went wrong. Please try again.', 'Opps!');
                              });
                    } else {
                         Swal.fire({
                              text: 'Your Order is safe!'
                         });
                    }
               });
     });

     // function changeRole(role, id) {
     $(document).on('click', '.role-change', function() {
          let role = $(this).data('value');
          let id = $(this).data('id');
          var url = "{{route('user.update','id')}}".replace('id', id);
          $.ajax({
               type: "PUT",
               url: url,
               dataType: 'json',
               data: {
                    "_token": "{{ csrf_token() }}",
                    "role": role,
               },
               beforeSend: function() {},
               success: function(data) {
                    if (data.status == true) {
                         table.ajax.reload(null, false);
                         toastr.success(data.message, 'Success');
                    } else {
                         toastr.error(data.msg_content, 'Opps!');
                    }
               },
               error: function(error) {
                    toastr.error('Something went wrong', 'Opps!');
                    $(document.body).css('pointer-events', '');
               }
          });
     });

     $(document).on('click', '.status-change', function() {
          let status = $(this).data('value');
          let id = $(this).data('id');
          var url = "{{route('user.show','id')}}".replace('id', id);
          $.ajax({
               type: "GET",
               url: url,
               dataType: 'json',
               data: {
                    "_token": "{{ csrf_token() }}",
                    "status": status,
               },
               beforeSend: function() {},
               success: function(data) {
                    if (data.status == true) {
                         table.ajax.reload(null, false);
                         toastr.success(data.message, 'Success');
                    } else {
                         toastr.error(data.msg_content, 'Opps!');
                    }
               },
               error: function(error) {
                    toastr.error('Something went wrong', 'Opps!');
                    $(document.body).css('pointer-events', '');
               }
          });

     });
</script>
@endsection