@extends('layouts.app')
@section('title', 'Country')
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
                                   @if((isset($country) && isset($country->id)))
                                   <h3 class="card-title">Edit Area</h3>
                                   @else
                                   <h3 class="card-title">Add Area</h3>
                                   @endif
                                   </div>
                                   <div class="card-body">
                                        <form id="country_form" class="form" action="javascript:void(0);" method="POST">
                                             <div class="row">
                                                  @if((isset($country) && isset($country->id)))
                                                  <input type="hidden" name="country_id" value="{{ $country->id }}">
                                                  @endif
                                                  <div class="col-md-12 col-12 mb-1 custom-input-group">
                                                       <label class="form-label" for="country_name">Area Name<span class="text-danger">*</span></label>
                                                       <input type="text" class="form-control" name="country_name" id="country_name" placeholder="Name" value="{{ ((isset($country) && isset($country->country_name)) ? $country->country_name : old('country_name'))  }}">
                                                       <span class="invalid-feedback d-block" id="error_country_name" role="alert"></span>
                                                  </div>
                                                  <div class="col-md-12 col-12 custom-input-group">
                                                       <button type="submit" class="btn btn-primary float-right save-country">Submit</button>
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
          $('.save-country').on('click', function() {
               var formData = new FormData($("#country_form")[0]);
               if ($("#country_name").val() != "") {
                    $.ajax({
                         type: "POST",
                         url: "{{route('country.store')}}",
                         data: formData,
                         dataType: 'json',
                         cache: false,
                         contentType: false,
                         processData: false,
                         beforeSend: function() {
                              $("#error_country_name").html(' ');
                              $(".save-country").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Wait');
                              $(".save-country").attr('disabled', true);
                         },
                         success: function(response) {
                              $(".save-country").html('Submit');
                              $(".save-country").attr('disabled', false);
                              if (response.status == false) {
                                   $.each(response.errors, function(key, value) {
                                        $('#error_' + key).html('<p class="text-danger mb-0">' + value + '</p>');
                                   });
                                   toastr.warning('Please Input Propper Data.', 'Warning');
                              } else if (response.server_error && response.status == true) {
                                   toastr.error(response.server_error, 'Error');
                              } else {
                                   $('#country_form')[0].reset();
                                   toastr.success(response.message, 'Success');
                                   setTimeout(function() {
                                        location.href = response.data;
                                   }, 2000);
                              }
                         }
                    });
               } else {
                    $("#country_form").validate({
                         rules: {
                              country_name: {
                                   required: true,
                              },
                         },
                         messages: {
                              country_name: {
                                   required: "Enter Country"
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