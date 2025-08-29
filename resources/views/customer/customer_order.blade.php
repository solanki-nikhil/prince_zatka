@extends('layouts.app')
@section('title', 'Customer Order')
@section('content')
@php
    $title = request()->has('outofstock') && request()->outofstock === 'true' ? 'Replaced Order' : 'View Order';
@endphp
<div class="app-content content ">
     <div class="content-overlay"></div>
     <div class="header-navbar-shadow"></div>
     <div class="content-wrapper container-xxl p-0">
          <div class="content-header row">
               <div class="content-header-left col-md-12 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                         <div class="col-12">
                              <h2 class="content-header-title float-start mb-0">{{ $title }}</h2>

                              <a role="button" class="btn btn-primary float-right filter mx-2">Filter</a>
                         </div>
                    </div>
               </div>
               
          </div>
          <div class="content-body">
               <section id="multiple-column-form">
                    <div class="row">
                         <div class="col-12">
                              <div class="card">
                                   <div class="card-header">
                                        <h3 class="card-title">Filter <small>Order</small></h3>
                                   </div>
                                   <div class="card-body">
                                        <form id="order_report" class="form" action="javascript:void(0)" method="POST">
                                             @csrf
                                             <div class="row">
                                                  <!--Start Status-->
                                                  <div class="col-md-6 col-12 mb-1 custom-input-group">
                                                       <label class="form-label" for="status">Status <span class="text-danger">*</span></label>
                                                       <select id="status" name="status" class="form-select">
                                                            <option selected>All</option>
                                                            <option value="1">Pending</option>
                                                            <option value="2">Active</option>
                                                            <option value="3">Reject</option>
                                                            <option value="4">Dispatch</option>
                                                            <!-- <option value="5">Deliver</option> -->
                                                       </select>
                                                  </div>
                                                  <!--End Status-->

                                                  <!--Start Product Model Name -->
                                                  <div class="col-md-6 col-12 mb-1 custom-input-group">
                                                       <label class="form-label" for="name">Name<span class="text-danger">*</span></label>
                                                       <input type="text" class="form-control" name="name" id="name" autocomplete="off" placeholder="Enter Name">
                                                  </div>
                                                  <!--End Product Model Name -->
                                                  <!--Start start date-->
                                                  <div class="col-md-6 col-12 mb-1 custom-input-group">
                                                       <label class="form-label" for="s_date">Start Date</label>
                                                       <input type="text" class="form-control date" name="s_date" id="s_date" autocomplete="off" placeholder="Start Date">
                                                  </div>
                                                  <!--End start date-->

                                                  <!--Start end date-->
                                                  <div class="col-md-6 col-12 mb-1 custom-input-group">
                                                       <label class="form-label" for="e_date">End Date</label>
                                                       <input type="text" class="form-control date" name="e_date" id="e_date" autocomplete="off" placeholder="End Date">
                                                  </div>
                                                  <!--End end date-->

                                                  <div class="col-md-12 col-12 custom-input-group text-end">
                                                       <button type="button" class="btn btn-primary order-report" id="submit">Submit</button>
                                                       <button type="submit" class="btn btn-primary order-report-export">Download</button>
                                                  </div>
                                             </div>
                                        </form>
                                   </div>
                              </div>
                         </div>
                    </div>
               </section>
               <section id="basic-datatable">
                    <div class="row">
                         <div class="col-12">
                              <div class="card p-2">
                                   <table id="order" class="datatables-basic table">
                                        <thead>
                                             <tr>
                                                  <th>Sr.No</th>
                                                  <th>Action</th>
                                                  <th>Status</th>
                                                  <th>Order By</th>
                                                  <th>Order ID</th>
                                                  <th>Date</th>
                                                  <th>Total Qty</th>
                                                  <th>Total Amount</th>
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

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered modal-xl">
          <div class="modal-content">
               <div class="modal-header bg-transparent border-bottom">
                    <h1 class="text-center mb-1" id="exampleModalTitle">Products</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <div class="modal-body p-0">

               </div>
          </div>
     </div>
</div>

<div class="modal fade" id="exampleModalBill" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered modal-md">
          <div class="modal-content">
               <div class="modal-header bg-transparent border-bottom">
                    <h1 class="text-center mb-1" id="exampleModalTitleBill">Bill No.</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <div class="modals-body p-1">
                    <form id="bill_form" class="form" action="javscript:void(0);" method="post">
                         <div class="row">
                              <div class="col-md-12 col-12 mb-1 custom-input-group">
                                   <label class="form-label" for="bill_number">Bill Number<span class="text-danger">*</span></label>
                                   <input type="text" class="form-control" name="bill_number" id="bill_number" placeholder="Enter Bill Number" value="{{ ((isset($stockMaster) && isset($stockMaster->quantity)) ? $stockMaster->quantity : '')  }}">
                                   <input type="hidden" name="status" id="status_set">
                                   <input type="hidden" name="id" id="id_set">
                              </div>
                              <div class="col-md-12 col-12 custom-input-group">
                                   <button type="button" class="btn btn-primary float-right save-bill">Submit</button>
                              </div>
                         </div>
                    </form>
               </div>
          </div>
     </div>
</div>

<div class="modal fade" id="exampleConfirm" tabindex="-1" aria-labelledby="exampleConfirmLabel" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered modal-md">
          <div class="modal-content">
               <div class="modal-header bg-transparent border-bottom">
                    <h1 class="text-center mb-1" id="exampleModalTitleBill">Verify Password</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <div class="modals-body p-1">
                    <form id="password_form" class="form" action="javscript:void(0);" method="post">
                         <div class="row">
                              <div class="col-md-12 col-12 mb-1 custom-input-group">
                                   <label class="form-label" for="password">Password<span class="text-danger">*</span></label>
                                   <input type="password" class="form-control" name="password" id="password" placeholder="Enter Password" value="">
                                   <input type="hidden" name="bill_number" id="bill_number_set" value="">
                                   <input type="hidden" name="status" id="status_set" value="">
                                   <input type="hidden" name="id" id="id_set" value="">
                              </div>
                              <div class="col-md-12 col-12 custom-input-group">
                                   <button type="button" class="btn btn-primary float-right verify-password">Submit</button>
                              </div>
                         </div>
                    </form>
               </div>
          </div>
     </div>
</div>

<!-- display LR Details -->
<div class="modal fade" id="lrModal" tabindex="-1" aria-labelledby="lrModalLabel" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered modal-md">
          <div class="modal-content">
               <div class="modal-header bg-transparent border-bottom">
                    <h1 class="text-center mb-1" id="lrModalTitle">LR Details</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <div class="modal-body p-0">

               </div>
          </div>
     </div>
</div>
<!-- Modal for Reject Reason -->
<div class="modal fade" id="rejectReasonModal" tabindex="-1" aria-labelledby="rejectReasonModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content">
      <div class="modal-header bg-transparent border-bottom">
        <h1 class="text-center mb-1" id="rejectReasonModalLabel">Reject Reason</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modals-body p-1">
        <form id="reject_reason_form" class="form" action="javascript:void(0);" method="post">
          <div class="row">
            <div class="col-md-12 col-12 mb-1 custom-input-group">
              <label class="form-label" for="reject_remarks">Reason<span class="text-danger">*</span></label>
              <textarea class="form-control" name="reject_remarks" id="reject_remarks" placeholder="Enter Reject Reason"></textarea>
              <input type="hidden" name="reject_id" id="reject_id">
            </div>
            <div class="col-md-12 col-12 custom-input-group">
              <button type="button" class="btn btn-primary float-right save-reject-reason">Submit</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('pagescript')
<script type="application/javascript">
     'use strict';
     const URL = "{{route('customerorder')}}";

     // var url = new URL(window.location.href);

     var urlParams = new URLSearchParams(window.location.search);
     var isOutStockFromURL = urlParams.get('outofstock') === 'true' ? 'true' : 'false'; // Ensure it's a string

     //order listing using ajax server side datatable
     var table = '';
     $(function() {
          table = $('#order').DataTable({
               processing: true,
               serverSide: true,
               fixedHeader: true,
               rowId: 'id',
               scrollX: true,
               ajax: {
                    url: URL,
                    data: function(d) {
                         d.name = $('#name').val();
                         d.status = $('#status').val();
                         d.s_date = $('#s_date').val();
                         d.e_date = $('#e_date').val();
                         d.is_outstock = isOutStockFromURL;
                    }
               },
               columns: [{
                         data: 'id',
                         render: function(data, type, row, meta) {
                              return meta.row + meta.settings._iDisplayStart + 1;
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
                         data: 'order_status',
                         name: 'order_status'
                    },
                    {
                         data: 'order_by',
                         name: 'order_by'
                    },
                    {
                         data: 'order_id',
                         name: 'order_id',
                         orderable: false,
                         sortable: false
                    },
                    {
                         data: 'created_at',
                         name: 'created_at'
                    },
                    {
                         data: 'quantity',
                         name: 'quantity',
                         orderable: false,
                         sortable: false
                    },
                    {
                         data: 'total_amount',
                         name: 'total_amount',
                         orderable: false,
                         sortable: false
                    },
                    {
                         data: 'remarks',
                         name: 'remarks',
                         defaultContent: '',
                         orderable: false,
                         sortable: false
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

     $(".date").datepicker({
          dateFormat: 'dd-mm-yy',
     });

     // reject or cancel order
     $(document).on('click', '.reject', function() {
          var btn = $(this);
          var id = btn.data('id');
          $('#reject_id').val(id);
          $('#reject_remarks').val('');
          $('#rejectReasonModal').modal('show');
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

     //delete single product of a order
     $(document).on('click', '.delete-single-order', function() {
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
                         $.ajax({
                              url: "{{route('admin-single-order-delete')}}",
                              type: "POST",
                              dataType: 'JSON',
                              data: {
                                   "_token": "{{ csrf_token() }}",
                                   "id": id,
                              },
                              success: function(response) {
                                   if (response.status == true) {
                                        Swal.fire({
                                             icon: 'success',
                                             title: 'Deleted!',
                                             text: 'Your item has been deleted.',
                                             customClass: {
                                                  confirmButton: 'btn btn-success'
                                             }
                                        });
                                        btn.parent().parent().remove();
                                   } else {
                                        toastr.error(response.message, 'Opps!');
                                   }
                              }
                         })
                    } else {
                         Swal.fire({
                              text: 'Your Order is safe!'
                         });
                    }
               });
     });

     $(document).on('click', '.view_product', function(e) {
          e.preventDefault();
          var id = $(this).data('id');
          var url = "{{route('order.show','id')}}".replace('id', id);
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

     $(document).on('click', '.change-status', function() {
          let status = $(this).data('value');
          let id = $(this).data('id');
          let no = $(this).data('bill');
          var user_id = "{{ Auth::user()->id }}";
          $("#password_form").trigger("reset");
          $('#bill_number_set').val(no);
          $("#status_set").val(status);
          $("#id_set").val(id);
          $('#exampleConfirm').modal('show');

     });

     $(".save-bill").on('click', function() {
          var no = $('#bill_number').val();
          var status = $("#status_set").val();
          var id = $("#id_set").val();
          if (no != '') {
               updateStatus(status, id, no);
          } else {
               toastr.warning('Enter Bill Number', 'Opps!');
          }
     });

     $(".verify-password").on("click", function() {
          var user_id = "{{ Auth::user()->id }}";
          var password = $("#password").val();
          var status = $("#status_set").val();
          var id = $("#id_set").val();
          var no = $('#bill_number_set').val();
          if (password != '') {
               $.ajax({
                    type: "post",
                    url: "{{ route('verify-password') }}",
                    dataType: 'json',
                    data: {
                         "_token": "{{ csrf_token() }}",
                         "user_id": user_id,
                         "password": password,
                         "status": status,
                         "id": id,
                         "no": no,
                    },
                    beforeSend: function() {},
                    success: function(data) {
                         $('#exampleConfirm').modal('hide');
                         if (data.status == true) {
                              if ((status == 4) || (status == 5)) {
                                   $('#exampleModalBill').modal('show');
                                   $("#status_set").val(status);
                                   $("#id_set").val(id);
                                   if (no != '' && no != 0) {
                                        $("#bill_number").val(no);
                                   }
                              } else {
                                   no = '0';
                                   updateStatus(status, id, no);
                              }
                         } else {
                              toastr.error('Verification is Incorrect', 'Opps!');
                         }
                    },
                    error: function(error) {
                         toastr.error('Something went wrong', 'Opps!');
                         $(document.body).css('pointer-events', '');
                    }
               });
          } else {
               toastr.warning('Enter Password', 'Opps!');
          }

     });

     $(document).on('click', '.save-reject-reason', function() {
          var id = $('#reject_id').val();
          var remarks = $('#reject_remarks').val();
          if (remarks.trim() === '') {
              toastr.warning('Please enter a reject reason.', 'Opps!');
              return;
          }
          // 3 is for reject
          updateOrderStatus(3, id, null, remarks);
          $('#rejectReasonModal').modal('hide');
     });

     function updateOrderStatus(status, id, btn, remarks = '') {
          var url = "{{route('customer.order.update','id')}}".replace('id', id);
          $.ajax({
               type: "PUT",
               url: url,
               dataType: 'json',
               data: {
                    "_token": "{{ csrf_token() }}",
                    "status": status,
                    "remarks": remarks // <-- send remarks
               },
               beforeSend: function() {},
               success: function(data) {
                    if (data.status == true) {

                         toastr.success(data.message, 'Success');
                         var updatedOrder = data.data

                         table.row(`#${id}`).data(updatedOrder).draw(false);
                    } else {
                         toastr.error(data.msg_content, 'Opps!');
                    }
               },
               error: function(error) {
                    toastr.error('Something went wrong', 'Opps!');
                    $(document.body).css('pointer-events', '');
               }
          });
     }

     function updateStatus(status, id, no) {
          var url = "{{route('order.update','id')}}".replace('id', id);
          $.ajax({
               type: "PUT",
               url: url,
               dataType: 'json',
               data: {
                    "_token": "{{ csrf_token() }}",
                    "status": status,
                    "no": no,
               },
               beforeSend: function() {},
               success: function(data) {
                    if (data.status == true) {
                         $("#bill_form").trigger("reset");
                         $('#exampleModalBill').modal('hide');
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
     }

     $(document).ready(function() {
          $('#exampleModalBill').on('hidden.bs.modal', function() {
               $(this).find('form').trigger('reset');
          });

          $("#multiple-column-form").hide();
          $(document).on('click', '.filter', function() {

               if ($(this).text() == "Filter") {
                    $(this).text("Close");
               } else {
                    $(this).text("Filter");
               };
               $("#multiple-column-form").toggle();
          });
     });

     $(document).on('click', '.order-report', function() {
          table.draw();
     });

     // START :: For Excel Export 
     $(document).on('click', '.order-report-export', function() {
          $('#order_report').attr('action', "{{route('report-export')}}");
          $('form#order_report').submit();
     });
     // END :: For Excel Export 

     $(document).on('click', '.dispatch', function(e) {
          e.preventDefault();
          // Get data from the clicked button
          let orderId = $(this).data('order-id');
          let lrImage = $(this).data('lr-photo');
          let lrNumber = $(this).data('lr-number');
          let lrDate = $(this).data('lr-date');
          let cases = $(this).data('cases');

          // Generate the HTML dynamically
          let htmlContent = `<table class="table table-bordered">
                                   <tr>
                                        <th>Order number</th>
                                        <td>${orderId}</td>
                                   </tr>
                                   <tr>
                                        <th>LR Photo</th>
                                        <td>
                                             <img src="${lrImage}" alt="LR Photo" class="img-fluid" style="max-height: 150px;">
                                        </td>
                                   </tr>
                                   <tr>
                                        <th>LR Number</th>
                                        <td>${lrNumber}</td>
                                   </tr>
                                   <tr>
                                        <th>LR Date</th>
                                        <td>${lrDate}</td>
                                   </tr>
                                   <tr>
                                        <th>Cases</th>
                                        <td>${cases}</td>
                                   </tr>
                              </table>
                              `;

          $("#lrModal .modal-body").html(htmlContent);
          $("#lrModal").modal("show");
     });

</script>
@endsection