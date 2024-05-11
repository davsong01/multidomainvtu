@extends('layouts.app')
@section('title', 'Edit ' .$customerlevel->name)
@section('content')
<!-- Content wrapper -->
 <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-12 mb-2 mt-1">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb p-0 mb-0">
                                    <li class="breadcrumb-item"><a href="{{ route('dashboard', request()->array)}}"><i class="bx bx-home-alt"></i></a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="{{ route('customerlevel.index',request()->array ) }}">Customer Levels</a>
                                    </li>
                                    <li class="breadcrumb-item active">Edit {{ $customerlevel->name }}
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Basic Inputs start -->
                <section id="basic-input">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Edit {{ $customerlevel->name }}</h4>
                                    {{-- <p>Add new category</p> --}}
                                    @include('layouts.alerts')
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <form action="{{route('customerlevel.update', array_merge(request()->array, ['customerlevel' => $customerlevel->id]))}}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                             <div class="row">
                                                <div class="col-md-6">
                                                    <fieldset class="form-group">
                                                        <label for="name">Name</label>
                                                        <input type="text" class="form-control" id="name" name="name" value="{{ $customerlevel->name ?? old('name') }}" placeholder="Enter name" required>
                                                    </fieldset>
                                                     <fieldset class="form-group">
                                                        <label for="upgrade_amount">Upgrade Amount</label>
                                                        <input type="text" class="form-control" id="upgrade_amount" name="upgrade_amount" value="{{ $customerlevel->upgrade_amount ??  old('upgrade_amount') }}" placeholder="Enter Upgrade amount" required>
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-6">
                                                    <fieldset class="form-group">
                                                        <label for="make_default">Make Default Level</label>
                                                        <select class="form-control" name="make_default" id="make_default" required>
                                                            <option value="">Select</option>
                                                            <option value="yes" {{ $customerlevel->make_default == 'yes' ? 'selected' : '' }}>Yes
                                                            </option>
                                                            <option value="no" {{ $customerlevel->make_default == 'make_default' ? 'selected' : '' }}>No
                                                            </option>
                                                        </select>
                                                    </fieldset>
                                                    <fieldset class="form-group">
                                                        <label for="order">Order</label>
                                                        <input type="number" class="form-control" name="order" value="{{ $customerlevel->order ?? old('order') }}" placeholder="Enter order" id="order" required>
                                                    </fieldset>
                                                </div>
                                                
                                                <div class="col-md-12">
                                                <button class="btn btn-primary" type="submit">Update</button>
    
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
@section('page-script')
<script src="{{ asset('app-assets/js/scripts/pages/dashboard-analytics.js') }}"></script>

@endsection