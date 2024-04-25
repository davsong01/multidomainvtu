@extends('layouts.auth')
@section('body')
<!-- left section-login -->

<div class="col-md-12 col-12 px-0">
    <div class="card disable-rounded-right d-flex justify-content-center">
        <div class="card-header pb-1">
            <div class="card-title">
                <h4 class="text-center mb-2">Welcome Back</h4>
            </div>
        </div>
        <div class="card-content">
            <div class="card-body">
                @include('layouts.alerts')
                <form action="{{ route('login', getMerchantDetails()->store_slug) }}" method="POST">
                    @csrf
                    <div class="row">

                    </div>
                    <div class="form-group mb-50">
                        <label class="text-bold-600" for="email">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email')}}" placeholder="Enter your email address"></div>
                    <div class="form-group">
                        <label class="text-bold-600" for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password">
                    </div>
                    <div class="form-group d-flex flex-md-row flex-column justify-content-between align-items-center">
                        <div class="text-left">
                            <div class="checkbox checkbox-sm">
                                <input type="checkbox" class="form-check-input" id="exampleCheck1">
                                <label class="checkboxsmall" for="exampleCheck1"><small>Keep me logged in</small></label>
                            </div>
                        </div>
                        <div class="text-right"><a href="{{ route('password.request',getMerchantDetails()->store_slug) }}" class="card-link"><small>Forgot Password?</small></a></div>
                    </div>
                    <button type="submit" class="btn btn-primary glow w-100 position-relative">Login<i id="icon-arrow" class="bx bx-right-arrow-alt"></i></button>
                </form>
                <hr>
                <div class="text-center"><small class="mr-25">Don't have an account?</small><a href="{{ route('register', getMerchantDetails()->store_slug) }}"><small>Sign up</small></a></div>
                @include('layouts.support')
            </div>
        </div>
    </div>
</div>
@endsection
