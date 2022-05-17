@extends('layouts.front')

@section('content')
<section class="login-signup">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-6">
        <nav class="comment-log-reg-tabmenu">
          <div class="nav nav-tabs" id="nav-tab" role="tablist">
              <a class="nav-item nav-link" id="nav-reg-tab" data-toggle="tab" href="#nav-reg" role="tab"
              aria-controls="nav-reg" aria-selected="false">
              {{ $langg->lang198 }}
            </a>
            <a class="nav-item nav-link login active" href="{{route('user.login')}}">
              {{ $langg->lang197 }}
            </a>
          </div>
        </nav>
        <div class="tab-content">
            {{--Registration section starts..............--}}
          <div  id="nav-reg" role="tabpanel">
            <div class="login-area signup-area">
              <div class="header-area">
                <h4 class="title">{{ $langg->lang181 }}</h4>
                  {{--Success message--}}
                  <p class="alert alert-success alert-dismissible fade show" id="reg_success_msg" style="display: none">
                      <strong>Confirmation code has been sent to your phone.</strong>
                      <button type="button" class="close" data-dismiss="alert">&times;</button>
                  </p>
                  {{--Error message--}}
                  <p class="alert alert-danger alert-dismissible fade show" id="reg_error_msg" style="display: none">
                      <strong>Error!</strong> Something went wrong,Try again.
                      <button type="button" class="close" data-dismiss="alert">&times;</button>
                  </p>
                  {{--Un register message--}}
                  @if(session()->has('redirect'))
                      <div class="alert alert-danger alert-dismissible fade show">
                          {{ session()->get('redirect') }}
                          <button type="button" class="close" data-dismiss="alert">&times;</button>
                          {{session()->forget('redirect')}}
                      </div>
                  @endif
                  {{-- Already-registered number message--}}
                  <p class="alert alert-warning alert-dismissible fade show" id="reg_warning_msg" style="display: none">
                      <strong>You are already registered! Try with another number.</strong>
                      <button type="button" class="close" data-dismiss="alert">&times;</button>
                  </p>
                  {{--validation error message--}}
                  @if(session()->has('error'))
                      <div class="alert alert-danger">
                          {{ session()->get('error') }}
                      </div>
                  @endif
              </div>
              <div class="login-form signup-form">
                @include('includes.admin.form-login')
                <form action="{{route('user-register-only')}}" method="POST">
                  {{ csrf_field() }}

                  <div class="form-input">
                    <input type="text" class="User Name" name="name" placeholder="{{ $langg->lang182 }}" required="">
                    <i class="icofont-user-alt-5"></i>
                  </div>

                  <div class="form-input">
                    <input type="email" class="User Name" name="email" placeholder="{{ $langg->lang183 }}" required="">
                    <i class="icofont-email"></i>
                  </div>

                  <div class="form-input" style="overflow: hidden !important; margin-right: 70px !important;">
                    <input type="text" name="phone" id="reg_phone" placeholder="{{ $langg->lang184 }}"
                           required="">
                    <i class="icofont-phone"></i>
                  </div>

                  <a class="btn btn-success" id="send_btn" style="float: right; margin-top: -60px;">Send</a>
                  
                  <div class="form-input">
                    <input type="text" class="User Name" id="user_otp" name="otp" placeholder="Confirmation code.."
                             required="">
                    <i class="icofont-code"></i>
                  </div>

                  <div class="form-input">
                    <input type="text" class="User Name" name="address" placeholder="{{ $langg->lang185 }}" required="">
                    <i class="icofont-location-pin"></i>
                  </div>
                    @if(session()->has('checkout'))
                        <input type="hidden" name="checkout" value="1">
                    @endif
                  <input class="mprocessdata" type="hidden" value="{{ $langg->lang188 }}">
                  <button type="submit" class="submit-btn" id="reg_btn" disabled style="background-color: gray">{{
                  $langg->lang189
                  }}</button>
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

      /*Registration section starts*/
      $("#send_btn").click(function(){
        var phone = $('#reg_phone').val();
        if(phone.length == 11){
            $.ajax({
              type: "get",
              url: "{{route('user-register.send-otp')}}",
              data: { number: phone },
              success: function(msg) {
                  if (msg == "success"){
                      //Show dismissible success message
                      $('#reg_success_msg').removeAttr('style');
                  }else if(msg == "registered"){
                      $('#reg_warning_msg').removeAttr('style');
                  }else{
                      $('#reg_error_msg').removeAttr('style');
                  }
              }
          })
        }else{
          alert("Please enter valid phone number!");
        }
      })

      //Active register button
        $('#user_otp').on('input',function () {
            $('#reg_btn').removeAttr('disabled');
            $('#reg_btn').removeAttr('style');
        })

});
</script>
@endsection
