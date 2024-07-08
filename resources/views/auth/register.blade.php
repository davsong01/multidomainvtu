@extends('layouts.auth')
@section('title', 'Register')
@section('body')
    <!-- left section-login -->
    <div class="col-md-12 col-12 px-0">
        <div class="card disable-rounded-right d-flex justify-content-center">
            @include('layouts.auth_shop_details')
            <div class="card-header pb-1">
                <div class="card-title">
                    <h4 class="text-center mb-2">Create An Account</h4>
                </div>
            </div>
            <div class="card-content">
                <div class="card-body">
                    @include('layouts.alerts')
                    <form action="{{ route('register', request()->array) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="form-group mb-50 col-sm-6 col-12">
                                <label class="text-bold-600" for="firstName">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="firstName" name="first_name"
                                    value="{{ old('first_name') }}" placeholder="First name" required>
                            </div>
                            <div class="form-group col-sm-6 col-12">
                                <label class="text-bold-600" for="lastName">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="lastName" name="last_name" value="{{ old('last_name') }}" placeholder="Last name" required>
                            </div>
                            <div class="form-group col-sm-6 col-12 mb-50">
                                <label class="text-bold-600" for="email">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email"
                                value="{{ old('email') }}" placeholder="Enter your email address" required>
                            </div>
                            <div class="form-group col-sm-6 col-12 mb-50">
                                <label class="text-bold-600" for="phone">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="phone" name="phone"
                                value="{{ old('phone') }}" placeholder="Enter your phone number" required>
                            </div>
                            <div class="form-group col-sm-6 col-12 mb-50">
                                <label class="text-bold-600" for="username">Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="username" name="username"
                                    value="{{ old('username') }}" placeholder="Username" required>
                            </div>
                            <div class="form-group mb-50 col-sm-6 col-12 mb-50">
                                <label class="text-bold-600" for="ref">Referral Username (Optional)</label>
                                <input type="text" class="form-control" id="referral" name="referral"
                                    value="{{ request()->referral }}" placeholder="Enter referral id">
                            </div>
                            <div class="form-group mb-50 col-sm-6 col-12 mb-50">
                                <label class="text-bold-600" for="password">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Password" required>
                            </div>
                        </div>
                        @if(!empty(getSettings()->terms_and_conditions_link))
                        <div class="form-group mb-50 mt-2 ">
                            <div class="checkbox checkbox-success checkbox-glow">
                                <input type="checkbox" id="checkboxGlow3" name="privacy" required>
                                <label for="checkboxGlow3"><p>I agree to the <a target="_blank" href="{{ getSettings()->terms_and_conditions_link }}">TERMS OF SERVICE</a> of {{ request()->store_name }}</p></label>
                            </div>
                        </div>
                        @endif
                        <button type="submit" class="btn btn-primary glow w-100 position-relative">Register</button>
                    </form>
                    <hr>
                    <div class="text-center"><small class="mr-25">Already have an account?</small><a
                            href="{{ route('login', request()->array) }}"><small>Login</small></a></div>
                    @include('layouts.support')
                </div>
            </div>
        </div>
    </div>
@endsection
