@extends('layouts.app')
@section('page-css')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/vendors.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css') }}">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/vendors.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
    <!-- END: Vendor CSS-->
@endsection
@section('content')
    <!-- Content wrapper -->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <section id="table-success">
                <div class="card">
                    <div class="card-header">
                        <!-- head -->
                        <h5 class="card-title">Customers {{ isset($status) ? "($status)" : '' }}</h5>
                    </div>

                    <div class="card-body">
                        <div class="col-md-12">
                            <form action="{{ route('customers', request()->array) }}" method="GET">
                                {{-- @csrf --}}
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="email" class="form-label">Email Address</label>
                                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter email address" value="{{ \Request::get('email')}}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <input type="phone" class="form-control" id="phone" name="phone" placeholder="Enter phone number" value="{{ \Request::get('phone')}}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="status" class="form-label">Status</label>
                                            <select class="form-control" name="status" id="status">
                                                <option value="">Select</option>
                                                <option value="active" {{ \Request::get('status') == 'active' ? 'selected' : ''}}>Active</option>
                                                <option value="inactive" {{ \Request::get('status') == 'inactive' ? 'selected' : ''}}>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="customer_level" class="form-label">Customer Level</label>
                                            <select class="form-control" name="customer_level" id="customer_level">
                                                <option value="">Select</option>
                                                @foreach($customer_levels as $customer_level)
                                                <option value="{{ $customer_level->id }}" {{ \Request::get('customer_level') == $customer_level->id ? 'selected' : ''}}>{{ $customer_level->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="username" class="form-control" id="username" name="username" placeholder="Enter Username" value="{{ \Request::get('username')}}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="form-label" for="from">Joined From</label>
                                            <input type="date" class="form-control" value="{{ \Request::get('from')}}" name="from">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="form-label" for="to">To</label>
                                            <input type="date" class="form-control" value="{{ \Request::get('to')}}" name="to">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="" class="form-label"></label>
                                        <input type="submit" class="form-control btn btn-primary mt-1" value="Search">
                                    </div>
                                </div>
                            </form>
                            <hr>
                        </div>
                        <div class="table-responsive">
                            <table id="table-extended-success" class="table table-striped dataex-html5-selectors">
                                <thead>
                                    <tr>
                                        <th>Details</th>
                                        <th>Username</th>
                                        <th>Status</th>
                                        <th>Level</th>
                                        <th>Balance</th>
                                        <th>Joined</th>
                                        @if(hasAccess('customers.edit'))
                                        <th>Action</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($customers as $customer)
                                        <tr>
                                            <td>
                                                <P>
                                                    Name:<a target="_blank" href="{{ route('customers.edit', array_merge(request()->array, ['id' => $customer->id])) }}">
                                                        {{ $customer->firstname . ' ' . $customer->lastname }}</a> <br>
                                                    Email:  {{ $customer->email }} <br>
                                                    Phone Number: {{ $customer->phone }}
                                                </P>
                                            </td>
                                            
                                            <td>{{ $customer->username }}</td>
                                            <td> <small><strong>{{ ucfirst($customer->status) }}</strong></small></td>
                                            <td>{{ $customer->customer->level->name ?? 'N/A' }}</td>
                                            <td>
                                                Wallet: {!! getSettings()->currency !!}{{ number_format(walletBalance($customer)) }} <br>
                                                Referral:  {!! getSettings()->currency !!}{{ number_format(referralBalance($customer)) }} <br>
                                            </td>
                                            <td>
                                                Registerd: {{ $customer->created_at }} <br>
                                                Verified: {{ $customer->email_verified_at }}
                                            </td>
                                            @if(hasAccess('customers.edit'))
                                            <td>
                                                <a href="{{ route('customers.edit', array_merge(request()->array, ['id' => $customer->id])) }}"><button type="button" class="btn btn-info btn-sm mr-1 mb-1"><i class="fa fa-edit"></i><span class="align-middle ml-25">View</span></button>
                                                </a>
                                            </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{-- {{ $customers->render() }} --}}
                        </div>
                    </div>
                    <!-- datatable ends -->
                </div>
            </section>
        </div>
    </div>
@section('page-script')
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.print.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/pdfmake.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/vfs_fonts.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/datatables/datatable.js') }}"></script>
@endsection
