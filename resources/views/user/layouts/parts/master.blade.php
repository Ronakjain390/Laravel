<?php


$data = masterData();
$logo = '';
$favicon = '';
$copy = '';
$title = '';
if (count($data) > 0) {
  $logo = $data[0]->logo;
  $favicon = $data[0]->favicon;
  $copy = $data[0]->copyrighttext;
  $title = $data[0]->title;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  @if(isset($title))
  <title>{{ $title }}</title>
  @endif
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  @if(isset($favicon))
  <link rel="shortcut icon" href="{{asset('assets/images/generalsetting/'.$favicon)}}" />
  @endif
  <link rel="stylesheet" href="{{ asset('assets/user/bootstrap/css/bootstrap.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/user/style.css') }}" />
  <script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
  <script src="{{ asset('assets/user/bootstrap/js/bootstrap.js') }}"></script>
  <script src="https://cdn.ckeditor.com/ckeditor5/23.0.0/classic/ckeditor.js"></script>

  <link rel="stylesheet" href="{{ asset('assets/css/custom.css')  }}">
  <link rel="stylesheet" href="{{ asset('assets/css/destination.css')  }}">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />

  <!-- Owl stylesheet -->
  <!-- <link rel="stylesheet" href="{{ asset('assets/user/owl-carousel/owl.carousel.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/user/owl-carousel/owl.theme.css') }}">
  <script src="{{ asset('assets/user/owl-carousel/owl.carousel.js') }}"></script> -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" integrity="sha512-tS3S5qG0BlhnQROyJXvNjeEM4UpMXHrQfTGmbQ1gKmelCxlSEBUaxhRBj/EFTzpbP4RVSrpEikbmdJobCvhE3g==" crossorigin="anonymous" referrerpolicy="no-referrer">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css" integrity="sha512-sMXtMNL1zRzolHYKEujM2AqCLUR9F2C4/05cdbxjjLSRvMQIciEPCQZo++nk7go3BtSuK9kfa/s+a4f4i5pLkw==" crossorigin="anonymous" referrerpolicy="no-referrer">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js" integrity="sha512-bPs7Ae6pVvhOSiIcyUClR7/q2OAsRiovw4vAkX+zJbw3ShAeeqezq50RIIcIURq7Oa20rW2n2q+fyXBNcU9lrw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

  <!-- Owl stylesheet -->


  <!-- slitslider -->
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/user/slitslider/css/style.css') }}" />
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/user/slitslider/css/custom.css') }}" />
  <script type="text/javascript" src="{{ asset('assets/user/slitslider/js/modernizr.custom.79639.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/user/slitslider/js/jquery.ba-cond.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/user/slitslider/js/jquery.slitslider.js') }}"></script>
  <!-- slitslider -->

  <!-- EMI  -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <link rel="stylesheet" href="{{ asset('assets/css/emi.css')}}" type="text/css">
  <!-- EMI -->

  <!-- AREA CONVERTOR -->
  <link rel="stylesheet" href="{{ asset('assets/css/areaconverter.css')}}" type="text/css">


  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />
</head>

<body>
  <div class="top-header">
    <div class="header container user">
      <div class="header-container">
        <a href="{{ url('/') }}"><img src="{{asset('assets/images/generalsetting/'.$logo)}}" height="auto" width="200" alt="Realestate"></a>
        <input type="checkbox" id="check">
        <label for="check" class="checkbtn">
          <span class="glyphicon glyphicon-menu-hamburger"></span>
        </label>
        <ul class="HeaderLink">
          <li><a class="btn" href="{{ url('/') }}">Home</a></li>
          <li><a class="btn" href="{{ route('newhomes') }}">New Homes</a></li>
          <li>
            <form method="get" action="{{ route('RentProperties') }}">
              <input type="text" name="type" id="type" value="rent" hidden />
              <button type="submit" class="btn">
                RENT
              </button>
            </form>
          </li>
          <li>
            <form method="get" action="{{ route('BuyProperties') }}">
              <input type="text" name="type" id="type" value="sell" hidden />
              <button type="submit" class="btn">
                BUY
              </button>
            </form>
          </li>
          <li><a class="btn" href="{{ url('/newsblog') }}">News</a></li>
          <li><a class="btn" href="{{ url('/contactus') }}">Contact</a></li>
          <li><a class="btn" href="{{ url('/postproperty') }}">Add property</a></li>


          @if(Auth::user()== '' || Auth::user()->type == 1 || Auth::user()->type == 2)
          <li><a class="btn" href="{{ route('userLogin') }}">Login</a></li>
          <li>
            <div class="dropdown">
              <a class="btn dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                REGISTER
              </a>
              <ul class="dropdown-menu ">
                <li><a href="{{ route('agentRegister') }}">Register as Agent </a></li>
                <li><a href="{{ route('userRegister') }}">Register as User </a></li>
              </ul>
            </div>
          </li>
          @else
          <li>
            <div class="dropdown">
              <a class="btn dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                My ACCOUNT
              </a>
              <ul class="dropdown-menu ">
                <li><a href="{{ url('/profile') }}">Profile</a></li>
                <li>
                  <a class="dropdown-item" href="{{ route('customLogout') }}">
                    <i class="mdi mdi-logout me-2 text-primary"></i> Logout </a>
                </li>
              </ul>
            </div>
          </li>
          @endif
        </ul>
      </div>
    </div>
  </div>
  @if ($message = Session::get('emailSubscribe'))
  <div class="alert alert-success">
    <p>{{ $message }}</p>
  </div>
  @endif

  @yield('content')

  <div class="footer">
    <div class="container">
      <div class="row">
        <div class="col-md-4 col-sm-6 footer-section" id="footer">
          <h4>Information</h4>
          <?php
          $CmsPageData = CmsPages();
          ?>
          <ul class="row">
            <li class="col-sm-12 col-xs-6"><a href="{{ route('listagent') }}">Agents</a></li>
            <li class="col-sm-12 col-xs-6"><a href="{{ route('newsblog.index') }}">News</a></li>
            <li class="col-sm-12 col-xs-6"><a href="{{ url('/contactus') }}">Contact</a></li>
            <li class="col-sm-12 col-xs-6"><a href="{{ url('/faq') }}">Faq</a></li>
            <li class="col-sm-12 col-xs-6"><a href="{{ route('emicalc') }}">EMI Calculator</a></li>
            <li class="col-sm-12 col-xs-6"><a href="{{ route('areacalc') }}">Area Calculator</a></li>
            <li class="col-sm-12 col-xs-6"><a href="{{ route('SiteMap') }}">SiteMap</a></li>

            @foreach ($CmsPageData as $link)
            <li class="col-lg-6 col-sm-6 col-xs-3"><a href="{{$link->slug}}">{{$link->name}}</a></li>
            @endforeach
          </ul>
        </div>
        <div class="col-md-4 col-sm-6 footer-section">
          <h4>Newsletter</h4>
          <p>Get notified about the latest properties in our marketplace.</p>
          <form class="form-inline" method="get" action="{{ route('subscribeduser.store')}}" autocomplete="off" name="formPrevent">
            <input type="text" placeholder="Enter Your email address" class="footer-form form-control @error('email') is-invalid @enderror" required="" accept="" name="email">
            @error('email')
            <span class="invalid-feedback" role="alert">
              <strong>{{ $message }}</strong>
            </span>
            @enderror
            <button class="btn btn-primary" type="submit">Notify Me!</button>
          </form>
        </div>
        <?php
        $data = masterData();
        $contact = masterDataContact();
        ?>
        <div class="col-md-4 col-sm-6 footer-section" id="contact_us">
          <h4>Contact us</h4>

          @if(isset($data[0]->address) && isset($data[0]->country))
          <p><span class="glyphicon glyphicon-map-marker"></span> {{$data[0]->address .','. ucfirst($data[0]->country)}} <br></p>
          @endif
          @if(isset($data[0]->email))
          <p><span class="glyphicon glyphicon-envelope"></span> {{$data[0]->email }}<br></p>
          @endif
          @if(isset($data[0]->contact_number))
          <p><span class="glyphicon glyphicon-earphone"></span> {{$data[0]->contact_number }}</p>
          @endif
          </p>
          <div class="footer-icons"  id="social_links">
          <?php
          $SocialLinksData = SocialLinks();
          ?>
          <!-- <h4>Follow us</h4> -->
          @foreach ($SocialLinksData as $link)
          <a href="{{$link->url}}" target="blank"><i class="fa-brands fa-{{$link->name}} social-links"></i></a>
          @endforeach
        </div>
        </div>
      </div>
      @if(isset($data[0]->copyrightText))
      <p class="copyright footer-section">{{$data[0]->copyrightText}} </p>
      @endif
    </div>
  </div>

  <!-- /.modal -->
  <!-- <script src='https://cdn.jsdelivr.net/npm/botman-web-widget@0/build/js/widget.js'></script> -->
  <!-- <script>
    var botmanWidget = {
      aboutText: 'ssdsd',
      introMessage: "✋ Hi! I'm from Addweb"
    };
  </script> -->
  <!-- EMI -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/canvasjs/1.7.0/canvasjs.min.js" integrity="sha512-FJ2OYvUIXUqCcPf1stu+oTBlhn54W0UisZB/TNrZaVMHHhYvLBV9jMbvJYtvDe5x/WVaoXZ6KB+Uqe5hT2vlyA==" crossorigin="anonymous"></script>

  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.min.js" integrity="sha384-IDwe1+LCz02ROU9k972gdyvl+AESN10+x7tBKgc9I5HFtuNz0wWnPclzo6p9vxnk" crossorigin="anonymous"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.16.0/jquery.validate.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

  <script src="{{ asset('js/ckeeditor.js') }}"></script>
  <script src="https://cdn.ckeditor.com/ckeditor5/23.0.0/classic/ckeditor.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.5.17-beta.0/vue.js"></script>
  <script src="{{ asset('user/js/areaconverter.js') }}"> </script>

  <!-- <script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" integrity="sha512-tS3S5qG0BlhnQROyJXvNjeEM4UpMXHrQfTGmbQ1gKmelCxlSEBUaxhRBj/EFTzpbP4RVSrpEikbmdJobCvhE3g==" crossorigin="anonymous" referrerpolicy="no-referrer">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css" integrity="sha512-sMXtMNL1zRzolHYKEujM2AqCLUR9F2C4/05cdbxjjLSRvMQIciEPCQZo++nk7go3BtSuK9kfa/s+a4f4i5pLkw==" crossorigin="anonymous" referrerpolicy="no-referrer">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js" integrity="sha512-bPs7Ae6pVvhOSiIcyUClR7/q2OAsRiovw4vAkX+zJbw3ShAeeqezq50RIIcIURq7Oa20rW2n2q+fyXBNcU9lrw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        -->

  <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>

  <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=places&sensor=false&key={{env('GOOGLE_ADDRESS_KEY')}}&libraries=places&callback=initAutocomplete"></script>
  <script src="{{ asset('js/googleAddress.js')  }}"></script>
  <script src="{{ asset('js/checkbox.js') }}"></script>
  <script src="{{ asset('user/js/index.js') }}"> </script>
  <script src="{{ asset('assets/user/script.js') }}"></script>
  <script>
    $(function() {
      $("form[name='formPrevent']").validate({
        rules: {
          email: {
            required: true,
            email: true,
            accept: "[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}"
          },
        },
        messages: {
          email: {
            required: "Please enter a valid email address",
            email: "Email must contain @ and . ",
            accept: "Not a valid format."
          }
        },
        submitHandler: function(form) {
          form.submit();
        }
      });
    });
  </script>
  <script>
    (function(w, d, s, l, i) {
      w[l] = w[l] || [];
      w[l].push({
        'gtm.start': new Date().getTime(),
        event: 'gtm.js'
      });
      var f = d.getElementsByTagName(s)[0],
        j = d.createElement(s),
        dl = l != 'dataLayer' ? '&l=' + l : '';
      j.async = true;
      j.src =
        'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
      f.parentNode.insertBefore(j, f);
    })(window, document, 'script', 'dataLayer', 'GTM-NSLHKCP');
  </script>
</body>

</html>
<style>
  .header {
    height: 97px;
  }

  .header .header-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .header ul {
    margin-top: 15px;
    float: right;
    margin-right: 20px;
  }

  .header ul li {
    display: inline-block;
  }

  .header .checkbtn {
    font-size: 20px;
    color: black;
    /* margin-right: 40px; */
    cursor: pointer;
    display: none
  }

  .header #check {
    display: none;
  }

  @media (max-width: 1200px) {
    .header {
      height: 80px;
      position: fixed;
      width: 100%;
      background: white;
      z-index: 2000;
      top: 0;
    }

    .header .checkbtn {
      display: block
    }

    .header ul {
      position: fixed;
      width: 100%;
      height: 100vh;
      background: white;
      top: 65px;
      z-index: 1000;
      left: -100%;
      text-align: left;
      transition: all .5s;
    }

    .header ul li {
      display: block;
    }

    .header ul li a,
    .header ul li button {
      width: 100%;
      text-align: left;
    }

    .header #check:checked~ul {
      left: 0;
    }
  }

  .dropdown-menu li {
    width: 100%;
    padding: 0;
  }

  .dropdown-menu>li>a {
    /* margin-left: -40px !important; */
    border-bottom: 1px solid #eeeeee;
    font-size: 12px;
    font-weight: bold;
    padding: 10px 20px;
  }

  /* .btn {
    margin-bottom: 7px;
  } */

  .error {
    color: red;
  }

  .header button {
    color: #337ab7;
    font-size: 14px;
    font-weight: bold;
    background: transparent;
  }

  .HeaderLink a {
    color: black;
  }

  .HeaderLink button {
    color: black;
  }
</style>