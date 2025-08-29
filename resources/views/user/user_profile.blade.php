@extends('layouts.app')
@section('title', 'Profile')
@section('content')
<div class="app-content content ">
     <div class="content-overlay"></div>
     <div class="header-navbar-shadow"></div>
     <div class="content-wrapper container-xxl p-0">
          <div class="content-body">
               <section id="profile-edit-form">
                    <div class="row">
                         <div class="col-12">
                              <div class="card">
                                   <div class="card-header">
                                        <h3 class="card-title">Edit <small>Profile</small></h3>
                                   </div>
                                   <div class="card-body">
                                        <form id="profile_form" class="form" action="{{ route('profile.update') }}" method="POST">
                                             @csrf
                                             @method('PUT')
                                             <div class="row">
                                                  <div class="col-12 col-md-4 mb-1 custom-input-group">
                                                       <label class="form-label" for="name">Name<span class="text-danger">*</span></label>
                                                       <input type="text" class="form-control" name="name" id="name" placeholder="Enter Name" value="{{ old('name', auth()->user()->name) }}">
                                                       <span class="invalid-feedback d-block" id="error_name" role="alert"></span>
                                                  </div>
                                                  <div class="col-12 col-md-4 mb-1 custom-input-group">
                                                       <label class="form-label" for="email">Email</label>
                                                       <input type="email" class="form-control" name="email" id="email" value="{{ auth()->user()->email }}" readonly>
                                                  </div>
                                                  <div class="col-12 col-md-4 mb-1 custom-input-group">
                                                       <label class="form-label" for="mobile">Mobile No.<span class="text-danger">*</span></label>
                                                       <input type="text" class="form-control" name="mobile" id="mobile" placeholder="Enter Mobile No." value="{{ old('mobile', auth()->user()->mobile) }}" readonly>
                                                       <span class="invalid-feedback d-block" id="error_mobile" role="alert"></span>
                                                  </div>
                                                  <div class="col-12 col-md-4 mb-1 custom-input-group">
                                                       <label class="form-label" for="password">New Password</label>
                                                       <input type="password" class="form-control" name="password" id="password" placeholder="Enter New Password">
                                                       @error('password')
                                                       <span class="text-danger">{{ $message }}</span>
                                                       @enderror
                                                  </div>

                                                  <div class="col-12 col-md-4 mb-1 custom-input-group">
                                                       <label class="form-label" for="password_confirmation">Confirm Password</label>
                                                       <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="Confirm New Password">
                                                  </div>

                                                  <div class="col-md-12 col-12 custom-input-group">
                                                       <button type="submit" class="btn btn-primary float-right update-profile">Update Profile</button>
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
@if(session('success'))
    <script>
        toastr.success("{{ session('success') }}");
    </script>
@endif
@endsection