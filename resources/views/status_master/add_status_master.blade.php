@extends('layouts.app')
@section('title', 'Status')
@section('content')
<div class="app-content content ">
     <div class="content-overlay"></div>
     <div class="header-navbar-shadow"></div>
     <div class="content-wrapper container-xxl p-0">
          <div class="content-body">
               <section id="multiple-column-form">
                    <div class="row">
                         <div class="col-12">
                              <div class="card">
                                   <div class="card-header">
                                        @if((isset($statusMaster) && isset($statusMaster->id)))
                                        <h3 class="card-title">Edit <small>Status</small></h3>
                                        @else
                                        <h3 class="card-title">Add <small>Status</small></h3>
                                        @endif
                                   </div>
                                   <div class="card-body">
                                        <form id="status_master_form" class="form" action="javascript:void(0);" method="POST">
                                             <div class="row">
                                                  @if((isset($statusMaster) && isset($statusMaster->id)))
                                                  <input type="hidden" name="status_id" value="{{ $statusMaster->id }}">
                                                  @endif
                                                  <div class="col-md-12 col-12 mb-1 custom-input-group">
                                                       <label class="form-label" for="status_name">Status Name<span class="text-danger">*</span></label>
                                                       <input type="text" class="form-control" name="status_name" id="status_name" placeholder="Status Name" value="{{ ((isset($statusMaster) && isset($statusMaster->status_name)) ? $statusMaster->status_name : old('status_name'))  }}">
                                                       <span class="invalid-feedback d-block" id="error_status_name" role="alert"></span>
                                                  </div>
                                                  <div class="col-md-12 col-12 custom-input-group">
                                                       <button type="submit" class="btn btn-primary float-right save-status-master">Submit</button>
                                                  </div>
                                             </div>
                                        </form>
                                   </div>
                              </div>
                         </div>
                    </div>
               </section>
          </div>
     </div>
</div>
@endsection

@section('pagescript')
<script type="text/javascript">
     $.ajaxSetup({
          headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
     });

     $(document).ready(function() {
          $('.save-status-master').on('click', function() {
               var formData = new FormData($("#status_master_form")[0]);
               if ($("#status_name").val() != "") {
                    $.ajax({
                         type: "POST",
                         url: "{{route('status-master.store')}}",
                         data: formData,
                         dataType: 'json',
                         cache: false,
                         contentType: false,
                         processData: false,
                         beforeSend: function() {
                              $("#error_status_name").html(' ');
                              $(".save-status-master").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Wait');
                              $(".save-status-master").attr('disabled', true);
                         },
                         success: function(response) {
                              $(".save-status-master").html('Submit');
                              $(".save-status-master").attr('disabled', false);
                              if (response.status == false) {
                                   $.each(response.errors, function(key, value) {
                                        $('#error_' + key).html('<p class="text-danger mb-0">' + value + '</p>');
                                   });
                                   toastr.warning('Please Input Propper Data.', 'Warning');
                              } else if (response.server_error && response.status == true) {
                                   toastr.error(response.server_error, 'Error');
                              } else {
                                   $('#status_master_form')[0].reset();
                                   toastr.success(response.message, 'Success');
                                   setTimeout(function() {
                                        location.href = response.data;
                                   }, 2000);
                              }
                         }
                    });
               } else {
                    $("#status_master_form").validate({
                         rules: {
                              status_name: {
                                   required: true,
                              },
                         },
                         messages: {
                              status_name: {
                                   required: "Enter Status"
                              },
                         },
                         errorElement: "p",
                         errorClass: "text-danger mb-0",

                         highlight: function(element) {
                              $(element).addClass('has-error');
                         },
                         unhighlight: function(element) {
                              $(element).removeClass('has-error');
                         },
                         errorPlacement: function(error, element) {
                              $(element).closest('.custom-input-group').append(error);
                         }
                    });
               }
          });
     });
</script>
@endsection