@extends('layouts.app')
@section('title', 'State')
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
                                        @if((isset($state) && isset($state->id)))
                                        <h3 class="card-title">Edit <small>State</small></h3>
                                        @else
                                        <h3 class="card-title">Add <small>State</small></h3>
                                        @endif
                                   </div>
                                   <div class="card-body">
                                        <form id="state_form" class="form" action="javascript:void(0);" method="POST">
                                             <div class="row">
                                                  @if((isset($state) && isset($state->id)))
                                                  <input type="hidden" name="state_id" value="{{ $state->id }}">
                                                  @endif
                                                  <div class="col-md-6 col-12 mb-1 custom-input-group">
                                                       <label class="form-label" for="country_id">Country<span class="text-danger">*</span></label>
                                                       <select class="form-select" name="country_id" required id="country_id">
                                                            <option value="" selected disabled>Select Country</option>
                                                            @foreach ($country as $item)
                                                            <option value="{{ $item->id }}" {{ (isset($state) && $state->country_id == $item->id ) ? 'selected' : '' }}>{{ $item->country_name}}</option>
                                                            @endforeach
                                                       </select>
                                                       <span class="invalid-feedback d-block" id="error_country_id" role="alert"></span>
                                                  </div>
                                                  <div class="col-md-6 col-12 mb-1 custom-input-group">
                                                       <label class="form-label" for="state_name">State<span class="text-danger">*</span></label>
                                                       <input type="text" class="form-control" name="state_name" id="state_name" placeholder="State Name" value="{{ ((isset($state) && isset($state->state_name)) ? $state->state_name : old('state_name'))  }}">
                                                       <span class="invalid-feedback d-block" id="error_state_name" role="alert"></span>
                                                  </div>
                                                  <div class="col-md-12 col-12 custom-input-group">
                                                       <button type="submit" class="btn btn-primary float-right save-state">Submit</button>
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
          $('.save-state').on('click', function() {
               var formData = new FormData($("#state_form")[0]);
               if ($("#country_id").val() != "" && $("#state_name").val() != "") {
                    $.ajax({
                         type: "POST",
                         url: "{{route('state.store')}}",
                         data: formData,
                         dataType: 'json',
                         cache: false,
                         contentType: false,
                         processData: false,
                         beforeSend: function() {
                              $("#error_state_name").html(' ');
                              $("#error_country_id").html(' ');                              
                              $(".save-state").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Wait');
                              $(".save-state").attr('disabled', true);
                         },
                         success: function(response) {
                              $(".save-state").html('Submit');
                              $(".save-state").attr('disabled', false);
                              if (response.status == false) {
                                   $.each(response.errors, function(key, value) {
                                        $('#error_' + key).html('<p class="text-danger mb-0">' + value + '</p>');
                                   });
                                   toastr.warning('Please Input Propper Data.', 'Warning');
                              } else if (response.server_error && response.status == true) {
                                   toastr.error(response.server_error, 'Error');
                              } else {
                                   $('#state_form')[0].reset();
                                   toastr.success(response.message, 'Success');
                                   setTimeout(function() {
                                        location.href = response.data;
                                   }, 2000);
                              }
                         }
                    });
               } else {
                    $("#state_form").validate({
                         rules: {
                              country_id: {
                                   required: true,
                              },
                              state_name: {
                                   required: true,
                              },
                         },
                         messages: {
                              country_id: {
                                   required: "Select Country"
                              },
                              state_name: {
                                   required: "Enter State"
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