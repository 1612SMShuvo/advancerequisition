@extends('layouts.front')

@section('content')
<section class="login-signup">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-6">
        <nav class="comment-log-reg-tabmenu">
          <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <a class="nav-item nav-link login active" id="nav-log-tab" data-toggle="tab" href="#nav-log" role="tab"
              aria-controls="nav-log" aria-selected="true">
              {{ $langg->lang197 }}
            </a>
          <a class="nav-item nav-link" href="{{route('user-register')}}">
              {{ $langg->lang198 }}
            </a>
          </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
          <div class="tab-pane fade show active" id="nav-log" role="tabpanel" aria-labelledby="nav-log-tab">
            <div class="login-area">
              <div class="header-area">
                <h4 class="title">{{ $langg->lang172 }}</h4>
                  {{--Success message--}}
                  <p class="alert alert-success alert-dismissible fade show" id="success_msg" style="display: none">
                      <strong>Confirmation code has been sent to your phone.</strong>
                      <button type="button" class="close" data-dismiss="alert">&times;</button>
                  </p>
                  {{--Error message--}}
                  <p class="alert alert-danger alert-dismissible fade show" id="error_msg" style="display: none">
                      <strong>Error!</strong> Something went wrong,Try again.
                      <button type="button" class="close" data-dismiss="alert">&times;</button>
                  </p>
                  @if(session()->has('errors'))
                      <div class="alert alert-danger">
                          {{ session()->get('errors') }}
                      </div>
                  @endif
                  @if(session()->has('checkout'))
                      <div class="alert alert-danger alert-dismissible fade show">
                          {{ session()->get('checkout') }}
                          <button type="button" class="close" data-dismiss="alert">&times;</button>
                      </div>
                  @endif
              </div>
              <div class="login-form signin-form">
                @include('includes.admin.form-login')
                <form action="{{ route('user.confirm.login') }}" method="POST">
                  {{ csrf_field() }}
                  <div class="form-input">
                    <input type="number" class="phone_number" id="phone_number" name="number" placeholder="Phone number 01XXX.."
                           required>
                    <i class="icofont-phone"></i>
                  </div>
                  <div class="form-input" id="otp" style="display: none">
                    <input type="number" name="otp" placeholder="Enter confirmation code" required>
                    <i class="icofont-pin"></i>
                  </div>

                  <input type="hidden" name="modal" value="1">
                  @if(session()->has('checkout'))
                    <input type="hidden" name="checkout" value="1">
                  @endif
                  <input class="mauthdata" type="hidden" value="{{ $langg->lang177 }}">
                  <button onclick="return false" class="submit-btn" style="background-color: gray" disabled>{{ $langg->lang178
                  }}</button>

                  @if(App\Models\Socialsetting::find(1)->f_check == 1 || App\Models\Socialsetting::find(1)->g_check ==
                  1)
                  <div class="social-area">
                    <h3 class="title">{{ $langg->lang179 }}</h3>
                    <p class="text">{{ $langg->lang180 }}</p>
                    <ul class="social-links">
                      @if(App\Models\Socialsetting::find(1)->f_check == 1)
                      <li>
                        <a href="{{ route('social-provider','facebook') }}">
                          <i class="fab fa-facebook-f"></i>
                        </a>
                      </li>
                      @endif
                      @if(App\Models\Socialsetting::find(1)->g_check == 1)
                      <li>
                        <a href="{{ route('social-provider','google') }}">
                          <i class="fab fa-google-plus-g"></i>
                        </a>
                      </li>
                      @endif
                    </ul>
                  </div>
                  @endif
                </form>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<script>
  $(document).ready(function(){
    //check phone number length
    $("#phone_number").on('input',function () {
      var number = $('.phone_number').val();
      if (number.length != 11) {
        $(".submit-btn").attr('disabled','true');
        $(".submit-btn").attr('style','background-color: gray');
      }else {
        $(".submit-btn").removeAttr('disabled');
        $(".submit-btn").removeAttr('style');
      }
      //Open otp form after click submit
      $(".submit-btn").click(function () {
          if ( $('.submit-btn').attr('onclick') == 'return false' ) {
              submitNumber();
              $("#otp").removeAttr('style');
              $(".submit-btn").removeAttr('onclick');
          }
      })
      //Send otp to number
      function submitNumber() {
        var phone = $('.phone_number').val();
        $.ajax({
          type: "get",
          url: "{{route('user.number.submit')}}",
          data: { number: phone },
          success: function(msg) {
              if (msg == "success"){
                  //Show dismissible success message
                  $('#success_msg').removeAttr('style');
              }else if(msg == "unRegister"){
                 window.location = "register";
              }else{
                  $('#error_msg').removeAttr('style');
              }
          }
        })
      }
    })

    /*Login section ends..........*/

   });
</script>
@endsection
