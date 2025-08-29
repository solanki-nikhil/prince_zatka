@extends('layouts.app')
@section('title', 'Redeem Points')
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
                                        <h3 class="card-title">Redeem <small>Points</small></h3>
                                   </div>
                                   <div class="card-body">
                                        <form id="redeem_form" class="form" action="javascript:void(0);" method="POST">
                                             <div class="row">
                                                  <div class="col-md-4 col-12 mb-1 custom-input-group">
                                                       <label class="form-label" for="user_profile_id">User<span class="text-danger">*</span></label>
                                                       <select class="form-select" name="user_profile_id" id="user_profile_id">
                                                            <option value="" selected disabled>Select User</option>
                                                            @foreach ($userProfile as $item)
                                                            <option value="{{ $item->id }}">{{ $item->user->name}}</option>
                                                            @endforeach
                                                       </select>
                                                  </div>
                                                  <div class="col-md-4 col-12 mb-1 custom-input-group d-none" id="label_point">
                                                       <label class="form-label" for="point">Receivable Point<span class="text-danger">*</span></label>
                                                       <input type="number" min="1" class="form-control" name="point" id="point" placeholder="Enter Point" value="" disabled>
                                                  </div>
                                                  <div class="col-md-4 col-12 mb-1 custom-input-group">
                                                       <label class="form-label" for="redeem_point">Withdrawal Point<span class="text-danger">*</span></label>
                                                       <input type="number" min="1" class="form-control" name="redeem_point" id="redeem_point" placeholder="Enter Redeem Point" value="">
                                                  </div>
                                                  <div class="col-md-12 col-12 custom-input-group">
                                                       <button type="submit" class="btn btn-primary float-right redeem-now">Redeem Now</button>
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
          $('.redeem-now').on('click', function() {
               var formData = new FormData($("#redeem_form")[0]);
               if ($("#user_profile_id").val() != "" && $("#redeem_point").val() > 0) {
                    $.ajax({
                         type: "POST",
                         url: "{{route('redeem-point.store')}}",
                         data: formData,
                         dataType: 'json',
                         cache: false,
                         contentType: false,
                         processData: false,
                         beforeSend: function() {
                              $(".redeem-now").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Wait');
                              $(".redeem-now").attr('disabled', true);
                         },
                         success: function(response) {
                              $(".redeem-now").html('Submit');
                              $(".redeem-now").attr('disabled', false);
                              if (response.status == false) {
                                   toastr.warning(response.message, 'Warning');
                              } else if (response.server_error && response.status == true) {
                                   toastr.error(response.server_error, 'Error');
                              } else {
                                   $('#redeem_form')[0].reset();
                                   toastr.success(response.message, 'Success');
                                   setTimeout(function() {
                                        location.href = response.data;
                                   }, 2000);
                              }
                         }
                    });
               } else {
                    $("#redeem_form").validate({
                         rules: {
                              user_profile_id: {
                                   required: true,
                              },
                              redeem_point: {
                                   required: true,
                                   minlength: 1
                              },
                         },
                         messages: {
                              user_profile_id: {
                                   required: "Select User",
                              },
                              redeem_point: {
                                   required: "Enter Redeem Point",
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

          $(document).on('change', '#user_profile_id', function() {
               var id = $("#user_profile_id").val();
               var route = "{{route('redeem-point.update','id')}}".replace('id', id);
               $.ajax({
                    type: 'PUT',
                    url: route,
                    datatype: 'json',
                    data: {
                         "id": id,
                    },
                    success: function(response) {
                         $("#label_point").removeClass('d-none');
                         $("#point").val(response.redeem);
                    }
               });
          });
     });
</script>
@endsection