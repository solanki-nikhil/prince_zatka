@extends('layouts.app')
@section('title', 'Status')
@section('content')

<div class="app-content content ">
     <div class="content-overlay"></div>
     <div class="header-navbar-shadow"></div>
     <div class="content-wrapper container-xxl p-0">
          <div class="content-header row">
               <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                         <div class="col-12">
                              <h2 class="content-header-title float-start mb-0">View Status</h2>
                              <!-- <a role="button" class="btn btn-primary btn-create" href="{{route('status-master.create')}}">Add Status</a></li> -->
                         </div>
                    </div>
               </div>
          </div>
          <div class="content-body">
               <section id="basic-datatable">
                    <div class="row">
                         <div class="col-12">
                              <div class="card p-2">
                                   <table id="status_master" class="datatables-basic table">
                                        <thead>
                                             <tr>
                                                  <th>Sr.No</th>
                                                  <th>Action</th>
                                                  <th>Status</th>
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
     const URL = "{{route('status-master.index')}}";

     //status_master listing using ajax server side datatable
     var table = '';
     $(function() {
          table = $('#status_master').DataTable({
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
                         data: 'status_name',
                         name: 'status_name'
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
                                   if (response.data.success) {
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
</script>
@endsection