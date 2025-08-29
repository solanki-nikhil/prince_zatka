@extends('layouts.guest')
@section('title', 'Register')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
    </div>
    <div class="content-body">
        <div class="auth-wrapper auth-basic px-2">
            <div class="auth-inner my-2">
                <!-- Login basic -->
                <div class="card mb-0">
                    <div class="card-body">
                        <a class="navbar-brand" href="{{route('home')}}">
                            <span class="brand-logos">
                                <img class="top-logos" src="{{asset('image/logo-one.png')}}">
                            </span>
                        </a>

                        <p class="card-text mb-2">Create Your Account to Manage Prince Energyer</p>

                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                        <form class="form form-horizontal" action="{{route('register')}}" method="post">
                            @csrf
                            <div class="row">
                                
                        
                                                  
                                <div class="col-12">
                                    <div class="mb-1 row">
                                        <div class="col-sm-12">
                                        <label class="form-label" for="name">Name<span class="text-danger">*</span></label>
                                            <div class="input-group input-group-merge">
                                                <span class="input-group-text">
                                                    <span class="fa fa-user"></span>
                                                </span>
                                            
                                                <input type="text" class="form-control" name="name" id="name" placeholder="Enter Name" >
                                                @error('name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-1 row">
                                        <div class="col-sm-12">
                                        <label class="form-label" for="name">Mobile No<span class="text-danger"></span></label>
                                            <div class="input-group input-group-merge">
                                                <span class="input-group-text">
                                                    <span class="fa fa-phone"></span>
                                                </span>
                                                <input type="mobile" class="form-control" name="mobile" id="mobile" placeholder="Enter Mobile No." >
                                                @error('mobile')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                
                                <div class="col-12">
                                    <div class="mb-1 row">
                                        <div class="col-sm-12">
                                        <label class="form-label" for="name">Passward<span class="text-danger">*</span></label>
                                            <div class="input-group input-group-merge">
                                                <span class="input-group-text">
                                                    <span class="fa fa-lock"></span>
                                                </span>
                                                <input type="password" class="form-control @error('password') is-invalid @enderror" name="password"  placeholder="Password" />
                                                @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                   
                                <div class="col-12">
                                    <div class="mb-1 row">
                                        <div class="col-sm-12">
                                        <label class="form-label" for="name">Company Name<span class="text-danger">*</span></label>
                                            <div class="input-group input-group-merge">
                                                <span class="input-group-text">
                                                    <span class="fa fa-building"></span>
                                                </span>
                                            
                                                <input type="text" class="form-control" name="company_name" id="company_name" placeholder="Enter Company Name" >
                                                @error('company')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-1 row">
                                        <div class="col-sm-12">
                                        <label class="form-label" for="name">GST Number<span class="text-danger"></span></label>
                                            <div class="input-group input-group-merge">
                                                <span class="input-group-text">
                                                    <span class="fa fa-bars"></span>
                                                </span>
                                            
                                                <input type="text" class="form-control" name="gst" id="gst" placeholder="Enter GST" >
                                                @error('gst')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-1 row">
                                        <div class="col-sm-12">
                                        <label class="form-label" for="name">Select Your Area<span class="text-danger">*</span></label>
                                            <div class="input-group input-group-merge">
                                                <span class="input-group-text">
                                                    <span class="fa fa-area-chart"></span>
                                                </span>
                                            
                                                <select class="form-select select2" name="country_id" id="country_id">
                                                            <option value="" selected disabled>Select Area</option>
                                                            @foreach ($country as $item)
                                                            <option value="{{ $item->id }}" >{{ $item->country_name}}</option>
                                                            @endforeach
                                                       </select>
                                                @error('area')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                
                                <div class="col-12">
                                    <div class="mb-1 row">
                                        <div class="col-sm-12">
                                        <label class="form-label" for="name">Address<span class="text-danger"></span></label>
                                            <div class="input-group input-group-merge">
                                                <span class="input-group-text">
                                                    <span class="fa fa-location"></span>
                                                </span>
                                            
                                                <input type="text" class="form-control" name="address" id="address" placeholder="Enter Address" >
                                                @error('address')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                               
                            
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary w-100" tabindex="4">Register</button>
                                </div>
                            </div>
                        </form>
                        <br/>
                        <p class="card-text mb-2">
                            Back to? <a href="{{ route('login') }}" class="text-primary">Login</a>
                        </p>

                    </div>
                </div>
                <!-- /Login basic -->
            </div>
        </div>
    </div>
</div>
@endsection

