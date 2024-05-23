<!DOCTYPE html>
<html lang="en">
<head>
<!-- basic -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- mobile metas -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="viewport" content="initial-scale=1, maximum-scale=1">
<!-- site metas -->
<title>BuyVTU</title>
<meta name="keywords" content="">
<meta name="description" content="">
<meta name="author" content=""> 
<!-- bootstrap css -->
<link rel="stylesheet" type="text/css" href="{{ asset('main-landing/css/bootstrap.min.css')}}">
<!-- style css -->
<link rel="stylesheet" type="text/css" href="{{ asset('main-landing/css/style.css')}}">
<!-- Responsive-->
<link rel="stylesheet" href="{{ asset('main-landing/css/responsive.css')}}">
<!-- fevicon -->
<link rel="icon" href="{{ asset('main-landing/images/img-1.png')}}" type="image/gif'" />
<!-- Scrollbar Custom CSS -->
<link rel="stylesheet" href="{{ asset('main-landing/css/jquery.mCustomScrollbar.min.css')}}">
<!-- Tweaks for older IEs-->
<link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
<!-- owl stylesheets --> 
<link rel="stylesheet" href="{{ asset('main-landing/css/owl.carousel.min.css')}}">
<link rel="stylesheet" href="{{ asset('main-landing/css/owl.theme.default.min.css')}}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css" media="screen">
<style>
  .icon_1{
    fill:#ecdf8f !important;
   
  }
  .box_main {
    background-color: black !important;
  }

  .box_main:hover {
    background-color: #000000c4 !important;
  }

  .icon{
    fill:#ecdf8f !important;
    height: 70px !important;
    width: 70px !important
  }
  .moremore_bt_1 a {
    border: 2px solid #ecdf8f !important;
  }

  @media (max-width: 575px) {
    .btn-primary {
      max-width: 100% !important;
      width: 100% !important;
    }
  }
   .btn-primary {
    background: black !important;
    border: black !important;
    color: #ecdf8f !important;
  }
  .moremore_bt a:hover {
    border: 2px solid #ac8585 !important;
  }

  .copyright_section {
    border-top: solid white 1px !important;
  }
</style>
</head>
<body>
  <!--header section start -->
    <div class="header_section">
      <div class="header_left">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
          <div class="logo"><a href="index.html"><img src="{{ asset('main-landing/images/logo.jpeg')}}"></a></div>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            {{-- <ul class="navbar-nav mr-auto">
              <li class="nav-item">
                <a class="nav-link" href="#">Home</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#about">About</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#services">Services</a>
              </li>
            </ul> --}}
          </div>
        </nav>
        <div class="banner_main">
          <h1 class="banner_taital"> BuyVTU<br><span style="font-weight: lighter;font-size: 28px;">THE WAY WE NOW TRADE</span><br></h1>
          <p class="banner_text">Your reliable VTU Portal for Data, Airtime, TV Subscriptions, Bills Payment and Exam Pins. </p><p  class="banner_text"><strong>DO YOU WISH TO HAVE A VTU SHOP UNDER BUYVTU.COM?</strong> Please contact the Business Manager via 09125335323 or click the button below</p>
          <div class="btn_main" style="padding-top: 10px;">
            <div class="contact_bt" style="margin-left: 0px;"><a href="{{ env('KINGSVTU_URL').'/'.'buyvtu' }}" target="_blank">Click Here</a></div>
          </div>
        </div>
      </div>
      <div class="header_right">
        <img src="{{ asset('main-landing/images/banner-img.png')}}" class="banner_img">
      </div>
    </div>
    <!--header section end -->
    <!--about section start -->
    <div class="services_section layout_padding" id="about">
      <div class="container">
        <div class="row">
          <div class="col-md-8">
            <h1 class="services_taital">Who we are</h1>
            <p class="services_text"><strong>Over 12 years experience in community business...</strong> <br><br>Our portal is automated, with instant delivery of products. Its our pride that we offer some of the best prices in VTU business. Our over a decade experience in IT and business gives us an edge over most of our competitors. We don’t just trade, we mentor entrepreneurs into becoming self-reliant in VTU business too. KINGSVTU.NG is a subsidiary of XPINOKINGS LIMITED (RC: 1870867) <br><br>Our head office is located in the Gold Coast, Queensland, Australia. We have dedicated servers and staff members in Abuja, Lagos, Akwa Ibom and Oyo, Nigeria. <br><br>Although the portals operate 24/7, our Nigeria-based Managers and Agents handle the support department from 8am to 8pm daily. </p>
          </div>
          <div class="col-md-4">
            <div><img src="{{ asset('main-landing/images/img-1.png')}}" class="image_1"></div>
          </div>
        </div>
      </div>
    </div>
    <!--about section end -->
    <!--services section start -->
    <div class="what_we_do_section layout_padding" id="services">
      <div class="container">
        <h1 class="what_taital">PRODUCTS AVAILABLE FOR YOU TO SELL TO YOUR CUSTOMERS</h1>
        <p class="what_text"></p>
        <div class="what_we_do_section_2">
          <div class="row">
          @foreach(landingPageCategories() as $category)
          <div class="col-lg-3 col-sm-6" style="margin-bottom: 20px;">
            <div class="box_main">
              <div class="icon_1">
                {!! $category['icon'] !!}
              </div>
              <h3 class="accounting_text">{{ $category['display_name']}}</h3>
              <p class="lorem_text"><br></p>
              {{-- <div style="color:white !important" class="moremore_bt_1"><a href="#">Explore</a></div> --}}
            </div>
          </div>
          @endforeach
        </div>
        </div>
      </div>
    </div>
    <!--services section end -->
    <!--project section start -->
  
    <div class="project_section_2 layout_padding" style="margin-bottom:20px">
      <div class="container">
        <div class="row">
          <div class="col-lg-4 col-sm-12"></div>
          <div class="col-lg-4 col-sm-12" style="display: flex;">
              <div class="moremore_bt" style="margin: auto;"><a href="{{ env('KINGSVTU_URL').'/'.'buyvtu' }}" target="_blank">Contact support </a></div>
          </div>
          <div class="col-lg-4 col-sm-12"></div>
        </div>
      </div>
    </div>
    <!--project section end -->
    <!--team section start -->
    {{-- <div class="team_section layout_padding" id="team">
      <div class="container">
        <h1 class="what_taital">Our Team and experts</h1>
        <p class="what_text_1">It is a long established fact that a reader will be distracted by the readable content of a </p>
        <div class="team_section_2 layout_padding">
          <div class="row">
            <div class="col-sm-3">
              <img src="{{ asset('main-landing/images/img-7.png')}}" class="image_7">
              <p class="readable_text">Readable</p>
              <p class="readable_text_1">Follow Us</p>
              <div class="social_icon">
                <ul>
                  <li><a href="#"><img src="{{ asset('main-landing/images/fb-icon.png')}}"></a></li>
                  <li><a href="#"><img src="{{ asset('main-landing/images/twitter-icon.png')}}"></a></li>
                  <li><a href="#"><img src="{{ asset('main-landing/images/linkedin-icon.png')}}"></a></li>
                </ul>
              </div>
            </div>
            <div class="col-sm-3">
              <img src="{{ asset('main-landing/images/img-8.png')}}" class="image_7">
              <p class="readable_text">Content</p>
              <p class="readable_text_1">Follow Us</p>
              <div class="social_icon">
                <ul>
                  <li><a href="#"><img src="{{ asset('main-landing/images/fb-icon.png')}}"></a></li>
                  <li><a href="#"><img src="{{ asset('main-landing/images/twitter-icon.png')}}"></a></li>
                  <li><a href="#"><img src="{{ asset('main-landing/images/linkedin-icon.png')}}"></a></li>
                </ul>
              </div>
            </div>
            <div class="col-sm-3">
              <img src="{{ asset('main-landing/images/img-9.png')}}" class="image_7">
              <p class="readable_text">Readable</p>
              <p class="readable_text_1">Follow Us</p>
              <div class="social_icon">
                <ul>
                  <li><a href="#"><img src="{{ asset('main-landing/images/fb-icon.png')}}"></a></li>
                  <li><a href="#"><img src="{{ asset('main-landing/images/twitter-icon.png')}}"></a></li>
                  <li><a href="#"><img src="{{ asset('main-landing/images/linkedin-icon.png')}}"></a></li>
                </ul>
              </div>
            </div>
            <div class="col-sm-3">
              <img src="{{ asset('main-landing/images/img-10.png')}}" class="image_7">
              <p class="readable_text">Content</p>
              <p class="readable_text_1">Follow Us</p>
              <div class="social_icon">
                <ul>
                  <li><a href="#"><img src="{{ asset('main-landing/images/fb-icon.png')}}"></a></li>
                  <li><a href="#"><img src="{{ asset('main-landing/images/twitter-icon.png')}}"></a></li>
                  <li><a href="#"><img src="{{ asset('main-landing/images/linkedin-icon.png')}}"></a></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div> --}}
    <!--team section end -->
    <!--client section start -->
    {{-- <div class="client_section layout_padding">
      <div class="container">
        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
          <ol class="carousel-indicators">
            <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
          </ol>
          <div class="carousel-inner">
            <div class="carousel-item active">
              <h1 class="what_taital">what is syas our clients</h1>
              <div class="client_section_2 layout_padding">
                <ul>
                  <li><img src="{{ asset('main-landing/images/round-1.png')}}" class="round_1"></li>
                  <li><img src="{{ asset('main-landing/images/img-11.png')}}" class="image_11"></li>
                  <li><img src="{{ asset('main-landing/images/round-2.png')}}" class="round_2"></li>
                </ul>
                <p class="dummy_text">It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem</p>
              </div>
            </div>
            <div class="carousel-item">
              <h1 class="what_taital">what is syas our clients</h1>
              <div class="client_section_2 layout_padding">
                <ul>
                  <li><img src="{{ asset('main-landing/images/round-1.png')}}" class="round_1"></li>
                  <li><img src="{{ asset('main-landing/images/img-11.png')}}" class="image_11"></li>
                  <li><img src="{{ asset('main-landing/images/round-2.png')}}" class="round_2"></li>
                </ul>
                <p class="dummy_text">It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem</p>
              </div>
            </div>
            <div class="carousel-item">
              <h1 class="what_taital">what is syas our clients</h1>
              <div class="client_section_2 layout_padding">
                <ul>
                  <li><img src="{{ asset('main-landing/images/round-1.png')}}" class="round_1"></li>
                  <li><img src="{{ asset('main-landing/images/img-11.png')}}" class="image_11"></li>
                  <li><img src="{{ asset('main-landing/images/round-2.png')}}" class="round_2"></li>
                </ul>
                <p class="dummy_text">It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div> --}}
    <!--client section end -->
    <!--footer section start -->
    <div class="footer_section layout_padding"style="padding-top: 0px;" >
      <div class="container">
        <!-- copyright section start -->
        <div class="copyright_section">
          <div class="copyright_text">Copyright {{ date('Y')}} All Right Reserved By <a href="{{ url('/')}}">{{config('app.name')}}</a></div>
        </div>
        <!-- copyright section end -->
      </div>
    </div>
    <!--footer section end -->
    <!-- Javascript files-->
    <script src="{{ asset('main-landing/js/jquery.min.js')}}"></script>
    <script src="{{ asset('main-landing/js/popper.min.js')}}"></script>
    <script src="{{ asset('main-landing/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{ asset('main-landing/js/jquery-3.0.0.min.js')}}"></script>
    <script src="{{ asset('main-landing/js/plugin.js')}}"></script>
    <!-- sidebar -->
    <script src="{{ asset('main-landing/js/jquery.mCustomScrollbar.concat.min.js')}}"></script>
    <script src="{{ asset('main-landing/js/custom.js')}}"></script>
    <!-- javascript --> 
    <script src="{{ asset('main-landing/js/owl.carousel.js')}}"></script>
    <script src="https:cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.js')}}"></script> 
</body>
</html>