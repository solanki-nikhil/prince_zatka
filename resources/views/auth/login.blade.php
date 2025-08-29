@extends('layouts.guest')
@section('title', 'Welcome')
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

                        <h4 class="card-title mb-1">Welcome to Prince Energyer! 👋</h4>
                        <p class="card-text mb-2">Please sign-in to your account and start the adventure</p>
                        <form class="form form-horizontal" action="{{route('login')}}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-1 row">
                                        <div class="col-sm-12">
                                            <div class="input-group input-group-merge">
                                                <span class="input-group-text">
                                                    <span class="fa fa-phone"></span>
                                                </span>
                                                <input type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Mobile Number" />
                                                @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ session('custom_message') ?: $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="mb-1 row">
                                        <div class="col-sm-12">
                                            <div class="input-group input-group-merge">
                                                <span class="input-group-text">
                                                    <span class="fa fa-lock"></span>
                                                </span>
                                                <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Password" />
                                                @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary w-100" tabindex="4">Login</button>
                                </div>
                            </div>
                        </form>
                        <br/>
                        <p class="card-text mb-2">
                            New on our platform? <a href="{{ route('register') }}" class="text-primary">Create an Account</a>
                        </p>

                    </div>
                </div>
                <!-- /Login basic -->
            </div>
        </div>
    </div>
</div>
@endsection