<!DOCTYPE html>
<html lang="en">
    {{-- {{dd(request()->all())}} --}}
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="{{ getSettings()->seo_description }}">
    <meta name="author" content="{{ request()->store_name }}">

    <!-- OG Meta Tags to improve the way the post looks when you share the page on Facebook, Twitter, LinkedIn -->
	<meta property="og:site_name" content="" /> <!-- website name -->
	<meta property="og:site" content="{{ request()->store_name }}" /> <!-- website link -->
	<meta property="og:title" content="{{ request()->store_name }}"/> <!-- title shown in the actual shared post -->
	<meta property="og:description" content="{{ getSettings()->seo_description }}" /> <!-- description shown in the actual shared post -->
	<meta property="og:image" content="{{ asset(getSettings()->logo) }}" /> <!-- image link, make sure it's jpg -->
	<meta property="og:url" content="{{ url('/') }}" /> <!-- where do you want your post to link to -->
	<meta name="twitter:card" content="{{ asset(getSettings()->logo) }}"> <!-- to have large image post format in Twitter -->

    <!-- Webpage Title -->
    <title>{{ request()->store_name }}</title>
    
    <!-- Styles -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link href="{{ asset('merchantlandingpage/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('merchantlandingpage/css/fontawesome-all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('merchantlandingpage/css/swiper.css') }}" rel="stylesheet">
	<link href="{{ asset('merchantlandingpage/css/styles.css') }}" rel="stylesheet">
	
	<!-- Favicon  -->
    <link rel="icon" href="{{ asset(getSettings()->favicon)}}">
    <style>
        .navbar .logo-image img {
            width: 80px !important;
            height: auto !important;
        }

        .counter .purecounter {
            font-size: 2.75rem;
            line-height: 0 !important;
        }

        header {
            padding-bottom: 2rem !important;
        }

        .f-image{
            width: 70px;
            height: auto;
        }

    </style>
</head>
<body>
    
    <!-- Navigation -->
    <nav id="navbar" class="navbar navbar-expand-lg fixed-top navbar-light" aria-label="Main navigation">
        <div class="container">

            <!-- Image Logo -->
            <a class="navbar-brand logo-image" href="{{route('welcome', request()->array )}}"><img src="{{ asset(getSettings()->logo)}}" alt="alternative"></a> 

            <!-- Text Logo - Use this if you don't have a graphic logo -->
            @if(empty(getSettings()->logo))
            <a class="navbar-brand logo-text" href="{{route('welcome', request()->array )}}">{{ request()->store_name }}</a>
            @endif

            
        </div> <!-- end of container -->
    </nav> <!-- end of navbar -->
    <!-- end of navigation -->

    <!-- Header -->
    <header id="header" class="header">
        <img class="decoration-star" src="{{ asset('merchantlandingpage/images/decoration-star.svg') }}" alt="alternative">
        <img class="decoration-star-2" src="{{ asset('merchantlandingpage/images/decoration-star.svg') }}" alt="alternative">
        <div class="container">
            <div class="row">
                <div class="col-lg-7 col-xl-7">
                    <div class="text-container">
                        <h1 class="h1-large">{{ request()->store_name }}</h1>
                        <p class="p-large">{!! getSettings()->seo_description !!}</p>
                        <a class="btn-solid-lg" href="{{route('login',request()->array)}}">Login</a>
                        <a class="btn-outline-lg" href="{{route('register',request()->array)}}">Register</a>
                    </div> <!-- end of text-container -->
                </div> <!-- end of col -->
                <div class="col-lg-5 col-xl-7">
                    <div class="image-container">
                        {{-- <img class="img-fluid" src="{{ asset('merchantlandingpage/images/header.png')}}" alt="alternative"> --}}
                    </div> <!-- end of image-container -->
                </div> <!-- end of col -->
            </div> <!-- end of row -->
        </div> <!-- end of container -->
    </header> <!-- end of header -->
    <!-- end of header -->


    <!-- Statistics -->
    <div class="counter">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    
                    <!-- Counter -->
                    <div class="counter-container">
                        <div class="counter-cell">
                            <div data-purecounter-start="0" data-purecounter-end="{{ $users * 100 }}" data-purecounter-duration="3" class="purecounter">+</div>
                            <div class="counter-info">Happy Customers</div>
                        </div> <!-- end of counter-cell -->
                        <div class="counter-cell">
                            <div data-purecounter-start="0" data-purecounter-end="{{ $categories }}" data-purecounter-duration="1.5" class="purecounter">+</div>
                            <div class="counter-info">Categories</div>
                        </div> <!-- end of counter-cell -->
                        <div class="counter-cell">
                            <div data-purecounter-start="0" data-purecounter-end="{{ $products * 10 }}" data-purecounter-duration="1.5" class="purecounter">{{ $products * 10 }}+</div>
                            <div class="counter-info">Products</div>
                        </div> <!-- end of counter-cell -->
                        <div class="counter-cell">
                            <div data-purecounter-start="0" data-purecounter-end="{{ $transactions * 100}}" data-purecounter-duration="1.5" class="purecounter">{{ $transactions * 25}}+</div>
                            <div class="counter-info">Transactions</div>
                        </div> <!-- end of counter-cell -->
                        
                    </div> <!-- end of counter-container -->
                    <!-- end of counter -->

                </div> <!-- end of col -->
            </div> <!-- end of row -->
        </div> <!-- end of container -->
    </div> <!-- end of counter -->
    <!-- end of statistics -->


    <!-- Introduction -->
    <div id="introduction" class="basic-1 bg-gray">
        <div class="container">
            <div class="row">
                <div class="col-xl-9">
                    <h2>YOUR ONE STOP SHOP FOR BILLS PAYMENT</h2>
                    <p>Introducing our all-in-one solution designed to simplify your financial management: <strong>{{ request()->store_name}}</strong>. Say goodbye to the hassle of juggling multiple platforms and due dates. With our user-friendly interface, you can effortlessly manage all your bills in one centralized location. From utilities to subscriptions, we've got you covered. Our secure platform ensures peace of mind with every transaction. Save time, stay organized, and take control of your finances today with our convenient one-stop shop for all your bill payment needs.</p>
                </div> <!-- end of col -->
            </div> <!-- end of row -->
        </div> <!-- end of container -->
    </div> <!-- end of basic-1 -->
    <!-- end of introduction -->
    <!-- Details 1 -->
    <div id="details" class="basic-2">
        <img class="decoration-star" src="{{ asset('merchantlandingpage/images/decoration-star.svg') }}" alt="alternative">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-xl-5">
                    <div class="image-container">
                        <img class="img-fluid" src="{{ asset('merchantlandingpage/images/details-1.jpg') }}" alt="alternative">
                    </div> <!-- end of image-container -->
                </div> <!-- end of col -->
                <div class="col-lg-6 col-xl-7">
                    <div class="text-container">
                        <h2>WHY CHOOSE US</h2>
                        <ul class="list-unstyled li-space-lg">
                            <li class="d-flex">
                                <i class="fas fa-square"></i>
                                <div class="flex-grow-1">Friendly Prices</div>
                            </li>
                            <li class="d-flex">
                                <i class="fas fa-square"></i>
                                <div class="flex-grow-1">Fast Delivery</div>
                            </li>
                            <li class="d-flex">
                                <i class="fas fa-square"></i>
                                <div class="flex-grow-1">High Return on Investment</div>
                            </li>
                            <li class="d-flex">
                                <i class="fas fa-square"></i>
                                <div class="flex-grow-1">Friendly Customer Support Team</div>
                            </li>
                        </ul>
                        <a class="btn-solid-reg" href="{{ route('register', request()->array) }}">Sign Up</a>
                    </div> <!-- end of text-container -->
                </div> <!-- end of col -->
            </div> <!-- end of row -->
        </div> <!-- end of container -->
    </div> <!-- end of basic-2 -->
    <!-- end of details 1 -->


    <!-- Services -->
    <div id="services" class="cards-1 bg-gray">
        <div class="container">
            <div class="row">
                <div class="col-lg-5">
                    <div class="text-container">
                        <h2>OUR PRODUCTS AND SERVICES</h2>
                        <p>Greatly hearted has who believe. Drift allow green son walls years for blush. Sir margaret drawings repeated recurred exercise laughing may you</p>
                        <p>Do repeated whatever to welcomed absolute no. Fat surprise although more words outlived</p>
                        <ul class="list-unstyled li-space-lg">
                            {{-- @foreach($categorys as $category)
                            <li class="d-flex">
                                <i class="fas fa-square"></i>
                                <div class="flex-grow-1">{{ $category->display_name }}</div>
                            </li>
                            @endforeach --}}
                        </ul>
                    </div> <!-- end of text-container -->
                </div> <!-- end of col -->
                <div class="col-lg-7">
                    <div class="card-container">
                        @foreach($productX as $product)
                        <!-- Card -->
                        <a style="text-decoration:none" href="{{ route('login', request()->array) }}">
                        <div class="card">
                            <div class="card-icon">
                                <img class="f-image" src="{{ $product->image }}" alt="">
                                {{-- <span class="fas fa-rocket"></span> --}}
                            </div>
                            <div class="card-body">
                                <h6 class="card-title">{{ $product->display_name }}</h6>
                            </div>
                        </div>
                        </a>
                        @endforeach
                        <!-- end of card -->

                        

                    </div> <!-- end of container -->
                </div> <!-- end of col -->
            </div> <!-- end of row -->
        </div> <!-- end of container -->
    </div> <!-- end of cards-1 -->
    <!-- end of services -->
    <!-- Projects -->
    
    <!-- Copyright -->
    <div class="copyright bg-gray">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <p class="p-small">Copyright © {{date('Y')}} <a href="{{ route('welcome', request()->array)}}"><strong>{{ request()->store_name }}.</strong></a> A subsidiary of <a href="{{env('KINGSVTU_URL')}}"><strong>KINGSVTU</strong></a></p>
                </div> <!-- end of col -->
            </div> <!-- enf of row -->
        </div> <!-- end of container -->
    </div> <!-- end of copyright --> 
    <!-- end of copyright -->
    

    <!-- Back To Top Button -->
    <button onclick="topFunction()" id="myBtn">
        <svg fill="red" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="m296-224-56-56 240-240 240 240-56 56-184-183-184 183Zm0-240-56-56 240-240 240 240-56 56-184-183-184 183Z"/></svg>
        {{-- <img src="{{ asset('merchantlandingpage/images/up-arrow.png')}}" alt="alternative"> --}}
    </button>
    <!-- end of back to top button -->
    	
    <!-- Scripts -->
    <script src="{{ asset('merchantlandingpage/js/bootstrap.min.js')}}"></script> <!-- Bootstrap framework -->
    <script src="{{ asset('merchantlandingpage/js/swiper.min.js')}}"></script> <!-- Swiper for image and text sliders -->
    <script src="{{ asset('merchantlandingpage/js/purecounter.min.js')}}"></script> <!-- Purecounter counter for statistics numbers -->
    <script src="{{ asset('merchantlandingpage/js/scripts.js')}}"></script> <!-- Custom scripts -->
</body>
</html>