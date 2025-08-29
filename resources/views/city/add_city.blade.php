@extends('layouts.app')
@section('title', 'District')
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
                                        @if((isset($city) && isset($city->id)))
                                        <h3 class="card-title">Edit <small>District</small></h3>
                                        @else
                                        <h3 class="card-title">Add <small>District</small></h3>
                                        @endif
                                   </div>
                                   <div class="card-body">
                                        <form id="city_form" class="form" action="javascript:void(0);" method="POST">
                                             <div class="row">
                                                  @if((isset($city) && isset($city->id)))
                                                  <input type="hidden" name="city_id" value="{{ $city->id }}">
                                                  @endif
                                                  <div class="col-md-4 col-12 mb-1 custom-input-group">
                                                       <label class="form-label" for="country_id">Country<span class="text-danger">*</span></label>
                                                       <select class="form-select" name="country_id" id="country_id">
                                                            <option value="" selected disabled>Select Country</option>
                                                            @foreach ($country as $item)
                                                            <option value="{{ $item->id }}" {{ (isset($city) && $city->country_id == $item->id ) ? 'selected' : '' }}>{{ $item->country_name}}</option>
                                                            @endforeach
                                                       </select>
                                                       <span class="invalid-feedback d-block" id="error_country_id" role="alert"></span>
                                                  </div>
                                                  <div class="col-md-4 col-12 mb-1 custom-input-group">
                                                       <label class="form-label" for="state_id">State<span class="text-danger">*</span></label>
                                                       <select class="form-select" name="state_id" id="state_id">
                                                            <option value="" selected disabled>Select State</option>
                                                            @if((isset($city) && isset($city->id)))
                                                                 @foreach ($state as $item)
                                                                 <option value="{{ $item->id }}" {{ (isset($city) && $city->state_id == $item->id ) ? 'selected' : '' }}>{{ $item->state_name}}</option>
                                                                 @endforeach
                                                            @endif
                                                       </select>
                                                       <span class="invalid-feedback d-block" id="error_state_id" role="alert"></span>
                                                  </div>
                                                  <div class="col-md-4 col-12 mb-1 custom-input-group">
                                                       <label class="form-label" for="city_name">District<span class="text-danger">*</span></label>
                                                       <input type="text" class="form-control" name="city_name" id="city_name" placeholder="District Name" value="{{ ((isset($city) && isset($city->city_name)) ? $city->city_name : old('city_name') )  }}">
                                                       <span class="invalid-feedback d-block" id="error_city_name" role="alert"></span>
                                                  </div>
                                                  <div class="col-md-12 col-12 custom-input-group">
                                                       <button type="submit" class="btn btn-primary float-right save-city">Submit</button>
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
          $('.save-city').on('click', function() {
               var formData = new FormData($("#city_form")[0]);
               if ($("#country_id").val() != "" && $("#city_name").val() != "") {
                    $.ajax({
                         type: "POST",
                         url: "{{route('city.store')}}",
                         data: formData,
                         dataType: 'json',
                         cache: false,
                         contentType: false,
                         processData: false,
                         beforeSend: function() {
                              $("#error_country_id").html(' ');
                              $("#error_state_id").html(' ');
                              $("#error_city_name").html(' ');
                              $(".save-city").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Wait');
                              $(".save-city").attr('disabled', true);
                         },
                         success: function(response) {
                              $(".save-city").html('Submit');
                              $(".save-city").attr('disabled', false);
                              if (response.status == false) {
                                   $.each(response.errors, function(key, value) {
                                        $('#error_' + key).html('<p class="text-danger mb-0">' + value + '</p>');
                                   });
                                   toastr.warning('Please Input Propper Data.', 'Warning');
                              } else if (response.server_error && response.status == true) {
                                   toastr.error(response.server_error, 'Error');
                              } else {
                                   $('#city_form')[0].reset();
                                   toastr.success(response.message, 'Success');
                                   setTimeout(function() {
                                        location.href = response.data;
                                   }, 2000);
                              }
                         }
                    });
               } else {
                    $("#city_form").validate({
                         rules: {
                              country_id: {
                                   required: true,
                              },
                              state_id: {
                                   required: true,
                              },
                              city_name: {
                                   required: true,
                              },
                         },
                         messages: {
                              country_id: {
                                   required: "Select Country"
                              },
                              state_id: {
                                   required: "Select State"
                              },
                              city_name: {
                                   required: "Enter City"
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


     $(document).ready(function() {
          var city_id = $("#city_id").val();
          if (city_id != "") {
               changeCity();
          }
          $("#country_id").on('change', function() {
               changeCity();
          });
     });

     function changeCity() {
          var id = $("#country_id").val();
          var city_id = $("#city_id").val();
          var route = "{{route('city.show','id')}}".replace('id', id);
          $.ajax({
               type: 'get',
               url: route,
               datatype: 'json',
               data: {
                    "id": id,
                    "city_id":city_id,
               },
               success: function(response) {
                    $("#state_id").empty('');
                    $.each(response, function(i, value) {
                         $("#state_id").append('<option value="' + value.id + '">' + value.state_name + '</option>');
                    });
               }
          });
     }
</script>
@endsection