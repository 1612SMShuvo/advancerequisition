@extends('layouts.admin') 

@section('content')  

<input type="hidden" id="headerdata" value="{{ __('ORDER') }}">

                    <div class="content-area">
                        <div class="mr-breadcrumb">
                            <div class="row">
                                <div class="col-lg-12">
                                        <h4 class="heading">{{ __('Create Custom Order') }}</h4>
                                        <ul class="links">
                                            <li>
                                                <a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }} </a>
                                            </li>
                                            <li>
                                                <a href="javascript:;">{{ __('Orders') }}</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('admin-order-declined') }}">{{ __('Create Custom Order') }}</a>
                                            </li>
                                        </ul>
                                </div>
                            </div>
                        </div>
                        <div class="product-area">
                            <br>
                            <br>
                            <div class="row">
                                <div class="col-lg-3">
                                </div>
                                <div class="col-lg-6 shade_box">
                                    <div class="mr-table allproduct">
                                        @include('includes.admin.form-success') 
                                        <div class="table-responsiv formbody">
                                            <div class="myform form ">
                                                <div class="logo mb-3">
                                                    <div class="col-md-12 text-center">
                                                        <h4>Login to Customer's Profile</h4>
                                                    </div>
                                                </div>
                                                <form action="{{ route('user.confirm.login') }}" method="post" name="login">
                                                    {{ csrf_field() }}
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1"><strong>Phone Number</strong> </label>
                                                        <input type="number" min="0" name="number"  class="form-control" id="phone_number" aria-describedby="emailHelp" placeholder="Phone number" required>
                                                    </div>
                                                    <input type="hidden" name="admin_request" value="true">

                                                    <br>
                                                    <center>
                                                        <div class="col-md-4 text-center ">
                                                            <button type="submit" class=" btn btn-block mybtn btn-primary tx-tfm">Login</button>
                                                        </div>
                                                    </center>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                </div>
                            </div>
                            <br><br><hr>
                        </div>
                    </div>




@endsection    

@section('scripts')
    <script>
        $( document ).ready(function(){
            var baseUrl = (window.location).href; // You can also use document.URL
            var koopId = baseUrl.substring(baseUrl.lastIndexOf('?') + 1);
            if(koopId > 0){
                var id = koopId;
                var newUrl = "/admin/get-customer-phone/"+id;
                $.ajax({
                    type: "GET",
                    url: newUrl,
                    success: function(data){
                        //$("input:number").val(data);
                        $("#phone_number").val(data);

                    }
                });
            }
        })
        
    </script>
@endsection   