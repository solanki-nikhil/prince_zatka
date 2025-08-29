@extends('layouts.app')
@section('title', 'QRCode')
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
                                        @if((isset($qRCode) && isset($qRCode->id)))
                                        <h3 class="card-title">Edit <small>QRCode</small></h3>
                                        @else
                                        <h3 class="card-title">Add <small>QRCode</small></h3>
                                        @endif
                                   </div>
                                   <div class="card-body">
                                        <form id="qrcode_form" class="form" action="javascript:void(0);" method="POST">
                                             <div class="row">
                                                  @if((isset($qRCode) && isset($qRCode->id)))
                                                  <input type="hidden" name="qr_code_id" id="qr_code_id" value="{{ $qRCode->id }}">
                                                  @endif
                                                  @if((!isset($qRCode) && !isset($qRCode->id)))
                                                  <div class="col-md-4 col-12 mb-1 custom-input-group">
                                                       <label class="form-label" for="number">Number of QRCode<span class="text-danger">*</span></label>
                                                       <input type="number" min="1" class="form-control" name="number" id="number" placeholder="Enter Number of QRCode" value="">
                                                       <span class="invalid-feedback d-block" id="error_number" role="alert"></span>
                                                  </div>
                                                  @endif
                                                  <div class="col-md-4 col-12 mb-1 custom-input-group">
                                                       <label class="form-label" for="amount">Point<span class="text-danger">*</span></label>
                                                       <input type="number" min="1" class="form-control" name="amount" id="amount" placeholder="Enter Point" value="{{ ((isset($qRCode) && isset($qRCode->amount)) ? $qRCode->amount : old('amount'))  }}">
                                                       <span class="invalid-feedback d-block" id="error_amount" role="alert"></span>
                                                  </div>
                                                  <div class="col-md-12 col-12 custom-input-group">
                                                       <button type="submit" class="btn btn-primary float-right save-qrcode">Submit</button>
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
          $('.save-qrcode').on('click', function() {
               var formData = new FormData($("#qrcode_form")[0]);
               // var id = $("#qr_code_id").val();
               if ($("#number").val() != "" && $("#amount").val() != "") {
                    $.ajax({
                         type: "POST",
                         url: "{{route('qr-code.store')}}",
                         data: formData,
                         dataType: 'json',
                         cache: false,
                         contentType: false,
                         processData: false,
                         beforeSend: function() {
                              $(".save-qrcode").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Wait');
                              $(".save-product").attr('disabled', true);
                         },
                         success: function(response) {
                              $(".save-qrcode").html('Submit');
                              $(".save-qrcode").attr('disabled', false);
                              if (response.status == false) {
                                   $.each(response.errors, function(key, value) {
                                        $('#error_' + key).html('<p class="text-danger mb-0">' + value + '</p>');
                                   });
                                   toastr.warning('Please Input Propper Data.', 'Warning');
                              } else if (response.server_error && response.status == true) {
                                   toastr.error(response.server_error, 'Error');
                              } else {
                                   $('#qrcode_form')[0].reset();
                                   toastr.success(response.message, 'Success');
                                   setTimeout(function() {
                                        location.href = response.data;
                                   }, 2000);
                              }
                         }
                    });
               } else {
                    $("#qrcode_form").validate({
                         rules: {
                              number: {
                                   required: true,
                                   minlength: 1
                              },
                              amount: {
                                   required: true,
                                   minlength: 1
                              },
                         },
                         messages: {
                              number: {
                                   required: "Enter Number of QRCode",
                                   minlength: "Please Input 1 or greater Number",
                              },
                              amount: {
                                   required: "Enter Point",
                                   minlength: "Please Input 1 or greater Number",
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