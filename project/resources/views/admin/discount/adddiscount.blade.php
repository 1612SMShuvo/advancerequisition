@extends('layouts.admin')

@section('styles')

<link href="{{asset('assets/admin/css/jquery-ui.css')}}" rel="stylesheet" type="text/css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />

@endsection


@section('content')

            <div class="content-area">

              <div class="mr-breadcrumb">
                <div class="row">
                  <div class="col-lg-12">
                      <h4 class="heading">{{ __('Add New Discount') }} <a class="add-btn" href="{{route('admin-discount-index')}}"><i class="fas fa-arrow-left"></i> {{ __('Back') }}</a></h4>
                      <ul class="links">
                        <li>
                          <a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }} </a>
                        </li>
                        <li>
                          <a href="{{ route('admin-discount-index') }}">&nbsp;{{ __('Discount') }}</a>
                        </li>
                        <li>
                          <a href="{{ route('admin-discount-add_page') }}">&nbsp;{{ __('Add New Discount') }}</a>
                        </li>
                      </ul>
                  </div>
                </div>
              </div>
              <div class="add-product-content1">
                <div class="row">
                  <div class="col-lg-12">
                    <div class="product-description">
                      <center>
                        {{--validation error message--}}
                                      @if ($errors->any())
                              <div class="alert alert-danger">
                                  <ul>
                                  @foreach ($errors->all() as $error)
                                  <li>
                                            <button type="button" class="close inline" data-dismiss="alert">&times;</button>{{ $error }}</li>
                                  @endforeach
                                  </ul>
                                </div><br>
                            @endif
                          </center>
                      <div class="body-area">
                        <div class="gocover" style="background: url({{asset('assets/images/'.$gs->admin_loader)}}) no-repeat scroll center center rgba(45, 45, 45, 0.5);"></div>
                        @include('includes.admin.form-both') 
                        <center><h2 class="heading">{{ __('Add Discount') }}</h2></center><hr>
                      <form action="{{route('admin-discount-store')}}" method="POST" enctype="multipart/form-data">
                        {{csrf_field()}}

                        <div class="row">
                          <div class="col-lg-4">
                            <div class="left-area">
                                <h4 class="heading">{{ __('Choose Product') }} *</h4>
                            </div>
                          </div>
                          <div class="col-lg-7">
                              <select id="productId" class="products" name="product_id" required="">
                                <option value="">{{ __('Choose a type') }}</option>
                                @foreach($datas as $data)
                                <option value="{{$data->id}}">{{$data->name}}</option>
                                @endforeach
                              </select>
                          </div>
                        </div>

                        <div class="row">
                          <div class="col-lg-4">
                            <div class="left-area">
                                <h4 class="heading">{{ __('Main Price Amount') }} *</h4>
                            </div>
                          </div>
                          <div class="col-lg-7">
                            <input type="text" class="input-field" id="price" name="price" placeholder="" required="" readonly>
                          </div>
                        </div>

                        <div class="row">
                          <div class="col-lg-4">
                            <div class="left-area">
                                <h4 class="heading">{{ __('Discount Type') }} *</h4>
                            </div>
                          </div>
                          <div class="col-lg-7">
                              <select id="type" name="discount_type" required="">
                                <option value="">{{ __('Choose a type') }}</option>
                                <option value="1">{{ __('Discount By Percentage') }}</option>
                                <option value="2" selected>{{ __('Discount By Amount') }}</option>
                              </select>
                          </div>
                        </div>

                        <div class="row">
                          <div class="col-lg-4">
                            <div class="left-area">
                                <h4 class="heading">{{ __('Discount(৳ or %)') }} *</h4>
                            </div>
                          </div>
                          <div class="col-lg-7">
                            <input type="text" class="input-field" id="discount" name="discount_amount" placeholder="Enter Discount in (৳)'" value="">
                          </div>
                        </div>

                        <div class="row">
                          <div class="col-lg-4">
                            <div class="left-area">
                                <h4 class="heading">{{ __('Discount Price') }} *</h4>
                            </div>
                          </div>
                          <div class="col-lg-7">
                            <input type="text" class="input-field" id="discount_price" name="discount_price" placeholder="{{ __('Enter Discount Price') }}" value="" readonly>
                          </div>
                        </div>

                        <div class="row">
                          <div class="col-lg-4">
                            <div class="left-area">
                                <h4 class="heading">{{ __('Conditional Price') }} *</h4>
                            </div>
                          </div>
                          <div class="col-lg-7">
                            <input type="text" class="input-field" name="conditional_price" placeholder="{{ __('Enter Conditional Price') }}" required="" value="">
                          </div>
                        </div>

                        <div class="row">
                          <div class="col-lg-4">
                            <div class="left-area">
                                <h4 class="heading">{{ __('Status') }} *</h4>
                            </div>
                          </div>
                          <div class="col-lg-7">
                              <select id="type" name="status" required="">
                                <option value="">{{ __('Choose a type') }}</option>
                                <option value="0">{{ __('Deactive') }}</option>
                                <option value="1">{{ __('Active') }}</option>
                              </select>
                          </div>
                        </div>

                        <div class="row">
                          <div class="col-lg-4">
                            <div class="left-area">
                                <h4 class="heading">{{ __('Maximum Units') }} *</h4>
                            </div>
                          </div>
                          <div class="col-lg-7">
                            <input type="text" class="input-field" name="max_quantity" placeholder="{{ __('Enter Maximum Units For A Single Order') }}" required="" value="">
                          </div>
                        </div>

                        <br>
                        <div class="row">
                          <div class="col-lg-4">
                            <div class="left-area">
                              
                            </div>
                          </div>
                          <div class="col-lg-7">
                            <button class="addProductSubmit-btn" type="submit">{{ __('Create Discount') }}</button>
                          </div>
                        </div>
                      </form>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

@endsection


@section('scripts')

<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

{{-- DROPDOWN PRODUCT LIST STARTS --}}
<script type="text/javascript">
  $(document).ready(function(){
    $("#productId").on('change', function(){
      var product_id =$(this).val();

      $.ajax({
        type:'get',
        url:'{{ route('admin-discount-findproduct') }}',
        data:{'id':product_id},
        success:function(data){
          $("#price").val(data);
        },
        error:function(){

        },
      });

    });

    $("#discount").on('input', function(){
          var discount = $(this).val();
          var type_val = $("#type").val();
          var org_price =   $("#price").val();
          if(type_val == 1){
            var discounted_price = Math.round(org_price - ((org_price * discount)/100));
            $("#discount_price").val(discounted_price);
          }else{
            var discounted_price = org_price - discount;
            $("#discount_price").val(discounted_price);
          }
    })
    $("#type").on('change', function(){
          var discount = $("#discount").val();
          var type_val = $("#type").val();
          var org_price =   $("#price").val();
          if(discount > 0){
            if(type_val == 1){
            var discounted_price = Math.round(org_price - ((org_price * discount)/100));
            $("#discount_price").val(discounted_price);
            }else{
              var discounted_price = org_price - discount;
              $("#discount_price").val(discounted_price);
            }
          }
          
    })
    
  });
</script>
{{-- DROPDOWN PRODUCT LIST ENDS--}}

{{-- DROPDOWN PRODUCT LIST STARTS --}}
    <script type="text/javascript">                 
                  
      $("#productId").select2({
            placeholder: "Select a Product....!!!!",
            allowClear: true
        });

    </script>    
{{-- DROPDOWN PRODUCT LIST ENDS--}}






@endsection   