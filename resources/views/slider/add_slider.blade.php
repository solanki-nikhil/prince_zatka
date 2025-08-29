@extends('layouts.app')
@section('title', 'Slider')
@section('content')
<div class="app-content content ">
     <div class="content-wrapper container-xxl p-0">
          <div class="content-body">
               <section id="multiple-column-form">
                    <div class="row">
                         <div class="col-12">
                              <div class="card">
                                   <div class="card-header">
                                        @if((isset($slider) && isset($slider->id)))
                                        <h3 class="card-title">Edit <small>Slider</small></h3>
                                        @else
                                        <h3 class="card-title">Add <small>Slider</small></h3>
                                        @endif
                                   </div>
                                   <div class="card-body">
                                        <form id="slider_form" class="form" action="javascript:void(0);" method="POST" enctype="multipart/form-data">
                                             <div class="row">
                                                  @if((isset($slider) && isset($slider->id)))
                                                  <input type="hidden" name="slider_id" id="slider_id" value="{{ $slider->id }}">
                                                  @endif
                                                  <div class="col-12 col-md-6 col-lg-6 mb-1 custom-input-group">
                                                       <label class="form-label" for="title">Title<span class="text-danger">*</span></label>
                                                       <input type="text" class="form-control" name="title" id="title" placeholder="Title" value="{{ ((isset($slider) && isset($slider->title)) ? $slider->title : '')  }}">
                                                  </div>
                                                  <div class="col-12 col-md-6 col-lg-6 mb-1 mb-sm-0 custom-input-group">
                                                       <label for=" image" class="form-label"> Image @if((!isset($slider) && !isset($slider->id)))<span class="text-danger">*</span>@endif</label>
                                                       <input class="form-control" type="file" id="image" name="image" accept="image/*">
                                                       @if((isset($slider) && isset($slider->id)))
                                                       <img class="mt-1 rounded" src="{{asset('upload/slider/'.$slider->image)}}" height="100" width="100">
                                                       @endif
                                                  </div>
                                                  <div class="col-12 mt-2">
                                                       <button type="submit" class="btn btn-primary float-end save-slider">Submit</button>
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
          $('.save-slider').on('click', function() {
               var formData = new FormData($("#slider_form")[0]);
               if ($("#slider_id").val() != "" && $("#slider_id").val() != undefined) {
                    var temp = $("#title").val() != "";
                    var img = false;
               } else {
                    var temp = $("#title").val() != "" && $("#image").val() != "";
                    var img = true;
               }
               if (temp) {
                    $.ajax({
                         type: "POST",
                         url: "{{route('slider.store')}}",
                         data: formData,
                         dataType: 'json',
                         cache: false,
                         contentType: false,
                         processData: false,
                         beforeSend: function() {
                              $(".save-slider").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Wait');
                              $(".save-slider").attr('disabled', true);
                         },
                         success: function(response) {
                              $(".save-slider").html('Submit');
                              $(".save-slider").attr('disabled', false);
                              if (response.status == false) {
                                   $.each(response.errors, function(key, value) {
                                        $('#error_' + key).html('<p class="text-danger mb-0">' + value + '</p>');
                                   });
                                   toastr.warning('Please Input Propper Data.', 'Warning');
                              } else if (response.server_error && response.status == true) {
                                   toastr.error(response.server_error, 'Error');
                              } else {
                                   $('#slider_form')[0].reset();
                                   toastr.success(response.message, 'Success');
                                   setTimeout(function() {
                                        location.href = response.data;
                                   }, 2000);
                              }
                         }
                    });
               } else {
                    $("#slider_form").validate({
                         rules: {
                              title: {
                                   required: true,
                              },
                              image: {
                                   required: img,
                              }
                         },
                         messages: {
                              title: {
                                   required: "Enter Title"
                              },
                              image: {
                                   required: "Upload Image"
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