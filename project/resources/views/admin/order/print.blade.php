<!DOCTYPE html>
<html>
<head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="keywords" content="{{$seo->meta_keys}}">
        <meta name="author" content="GeniusOcean">

        <title>{{$gs->title}}</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="{{asset('assets/print/bootstrap/dist/css/bootstrap.min.css')}}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset('assets/print/font-awesome/css/font-awesome.min.css')}}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="{{asset('assets/print/Ionicons/css/ionicons.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('assets/print/css/style.css')}}">
  <link href="{{asset('assets/print/css/print.css')}}" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
        <link rel="icon" type="image/png" href="{{asset('assets/images/'.$gs->favicon)}}"> 
  <style type="text/css">
@page { size: auto;  margin: 0mm; }
@page {
  size: A4;
  margin: 0;
}
@media print {
    html, body {
    width: 210mm;
    height: 287mm;
    }

    /*table tr,table td, table th {*/
    /*border: 1px solid #ddd !important;*/
    /*}*/
    table td, table th {
            padding: 0px 10px !important;
    
    }

    table thead{
    background-color:#232820 !important;
    -webkit-print-color-adjust: exact; color: white !important
    }
}

html {

}
::-webkit-scrollbar {
    width: 0px;  /* remove scrollbar space */
    background: transparent;  /* optional: just make scrollbar invisible */
}






  </style>
</head>
<body onload="window.print();">

    <div class="invoice-wrap">
            <div class="invoice__title">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="invoice__logo text-left">
                           <img src="{{ asset('assets/images/'.$gs->invoice_logo) }}" alt="woo commerce logo">
                        </div>
                    </div>
                </div>
            </div>
            <br>
           
            <div class="row">
                <div class="col-sm-12">
                    <div class="invoice_table">
                        <div class="mr-table">
                            <div class="table-responsive">
                                <table id="example2" class="table table-bordered dt-responsive" cellspacing="0"
                                    width="100%" >
                                    
                                    <tbody>
                                        
                                        <tr>
                                            <td width="50%">

                                                @if($order->dp == 0)

                                                <span><strong>{{ __('Name') }}</strong>: {{ $order->shipping_name == null ? $order->customer_name : $order->shipping_name}}</span><br>
                                                <span><strong>{{ __('Phone') }}</strong>: {{ $order->shipping_phone == null ? $order->customer_phone : $order->shipping_phone}}</span><br>
                                                <span><strong>{{ __('Address') }}</strong>: {{ $order->shipping_address == null ? $order->customer_address : $order->shipping_address }}</span><br>
                                                <span><strong>{{ __('Delivery Area') }}</strong>: {{ $order->shipping_city == null ? $order->customer_city : $order->shipping_city }}</span>

                                                @endif
                                                
                                            </td>


                                            <td>

                                                <span><strong>{{ __('Order Date') }} :</strong> {{ date('d-M-Y',strtotime($order->created_at)) }}</span><br>
                                                <span><strong>{{ __('Shipping Slot') }} :</strong> {{ (($order->shipping_time)) }}</span><br>
                                                <span><strong>{{  __('Order ID')}} :</strong> {{ $order->order_number }}</span><br>
                                                
                                                <span> <strong>{{ __('Payment Method') }} :</strong> {{$order->method}}</span>
                                              
                                            </td>
                                        </tr>
                                    </tbody>

                                   
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div><br>
            <div class="row">
                <div class="col-sm-12">
                    <div class="invoice_table">
                        <div class="mr-table">
                            <div class="table-responsive">
                                <table id="example2" class="table table-bordered dt-responsive" cellspacing="0"
                                    width="100%" >
                                    <thead style=" background-color:#232820 !important;
                                        -webkit-print-color-adjust: exact; color: white !important">
                                        <tr>
                                            <th>{{ __('Product') }}</th>
                                            <th>{{ __('Details') }}</th>
                                            <th>{{ __('Price/Quantity') }}</th>
                                            <th>{{ __('Quantity') }}</th>
                                            <th>{{ __('Total') }}</th>
                                        </tr>
                                    <thead>
                                    <tbody>
                                        @php
                                        $subtotal = 0;
                                        $tax = 0;
                                        @endphp
                                        @foreach($cart->items as $product)
                                        <tr>
                                            <td width="50%">
                                                @if($product['item']['user_id'] != 0)
                                                @php
                                                $user = App\Models\User::find($product['item']['user_id']);
                                                @endphp
                                                @if(isset($user))
                                                <a target="_blank"
                                                    href="{{ route('front.product', $product['item']['slug']) }}" style="color: #000;text-decoration: none !important;">{{ $product['item']['name']}}</a>
                                                @else
                                                <a href="javascript:;" style="color: #000;text-decoration: none !important;">{{$product['item']['name']}}</a>
                                                @endif

                                                @else
                                                <a href="javascript:;" style="color: #000;text-decoration: none !important;">{{ $product['item']['name']}}</a>

                                                @endif
                                            </td>


                                            <td>
                                                @if($product['size'])
                                               <p>
                                                    <strong>{{ __('Size') }} :</strong> {{str_replace('-',' ',$product['size'])}}
                                               </p>
                                               @endif
                                               @if($product['color'])
                                                <p>
                                                        <strong>{{ __('color') }} :</strong> <span
                                                        style="width: 40px; height: 20px; display: block; background: #{{$product['color']}};"></span>
                                                </p>
                                                @endif
                                            </td>
                                            <td>
                                                <p>
                                                        <strong>{{$order->currency_sign}}{{ round($product['item']['price'] * $order->currency_value , 2) }}</strong> 
                                                </p>
                                            </td>
                                            <td>
                                                <p>
                                                        &nbsp;<strong> {{$product['qty']}} {{ $product['item']['measure'] }}</strong>
                                                </p>
                                            </td>
                                               

                                                    @if(!empty($product['keys']))

                                                    @foreach( array_combine(explode(',', $product['keys']), explode(',', $product['values']))  as $key => $value)
                                                    <p>

                                                        <b>{{ ucwords(str_replace('_', ' ', $key))  }} : </b> {{ $value }} 

                                                    </p>
                                                    @endforeach

                                                    @endif
                                               
                                            </td>




                                      
                                            <td>{{$order->currency_sign}}{{ round($product['price'] * $order->currency_value , 0) }}
                                            </td>
                                            @php
                                            $subtotal += round($product['price'] * $order->currency_value, 0);
                                            @endphp

                                        </tr>

                                        @endforeach
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <td colspan="4" style="text-align: right; font-weight: bold;">{{ __('Subtotal') }}</td>
                                            <td style="background: #eee;font-weight: bold;">{{$order->currency_sign}}{{ round($subtotal, 0) }}</td>
                                        </tr>
                                        <!--@if($order->coupon_discount >0.059)-->
                                        <!--    <tr>-->
                                        <!--        <td colspan="5">There is no free products as you used coupon. Sorry..!!</td>-->
                                        <!--    </tr>-->
                                        <!--@else-->
                                        <!--    @if($subtotal>4999)-->
                                        <!--            <tr>-->
                                        <!--                <td colspan="4">Rupchanda Soybean Oil 2 ltr</td>-->
                                        <!--                <td style="background: #eee;font-weight: bold;">Free</td>-->
                                        <!--            </tr>-->
                                        <!--            <tr>-->
                                        <!--                <td colspan="4">Sugar 1 kg</td>-->
                                        <!--                <td style="background: #eee;font-weight: bold;">Free</td>-->
                                        <!--            </tr>-->
                                        <!--            <tr>-->
                                        <!--                <td colspan="4">Booter Beshon (Chick Peas Powder) 500 gm</td>-->
                                        <!--                <td style="background: #eee;font-weight: bold;">Free</td>-->
                                        <!--            </tr>-->
                                        <!--            @elseif($subtotal>3999)-->
                                        <!--            <tr>-->
                                        <!--                <td colspan="4">Chinigura Rice 1 kg</td>-->
                                        <!--                <td style="background: #eee;font-weight: bold;">Free</td>-->
                                        <!--            </tr>-->
                                        <!--            <tr>-->
                                        <!--                <td colspan="4">Ruchi Puffed Rice (Muri) 500 gm</td>-->
                                        <!--                <td style="background: #eee;font-weight: bold;">Free</td>-->
                                        <!--            </tr>-->
                                        <!--            <tr>-->
                                        <!--                <td colspan="4">Radhuni Haleem Mix 200 gm</td>-->
                                        <!--                <td style="background: #eee;font-weight: bold;">Free</td>-->
                                        <!--            </tr>-->
                                        <!--            @elseif($subtotal>2999)-->
                                        <!--            <tr>-->
                                        <!--                <td colspan="4">Onion (Deshi পেঁয়াজ ) 2 Kg</td>-->
                                        <!--                <td style="background: #eee;font-weight: bold;">Free</td>-->
                                        <!--            </tr>-->
                                        <!--            <tr>-->
                                        <!--                <td colspan="4">Garbanzo (Chola Boot Dal) 500gm</td>-->
                                        <!--                <td style="background: #eee;font-weight: bold;">Free</td>-->
                                        <!--            </tr>-->
                                        <!--            @elseif($subtotal>2499)-->
                                        <!--            <tr>-->
                                        <!--                <td colspan="4">Onion (Deshi পেঁয়াজ ) 1 Kg</td>-->
                                        <!--                <td style="background: #eee;font-weight: bold;">Free</td>-->
                                        <!--            </tr>-->
                                        <!--            <tr>-->
                                        <!--                <td colspan="4">Potato Regular (Net Weight ± 50 gm) 1 kg</td>-->
                                        <!--                <td style="background: #eee;font-weight: bold;">Free</td>-->
                                        <!--            </tr>-->
                                        <!--            <tr>-->
                                        <!--                <td colspan="4">Moshur Dal (Imported) 500 gm</td>-->
                                        <!--                <td style="background: #eee;font-weight: bold;">Free</td>-->
                                        <!--            </tr>-->
                                        <!--            @elseif($subtotal>1499)-->
                                        <!--            <tr>-->
                                        <!--                <td colspan="4">Potato Regular (Net Weight ± 50 gm) 1 kg</td>-->
                                        <!--                <td style="background: #eee;font-weight: bold;">Free</td>-->
                                        <!--            </tr>-->
                                        <!--            <tr>-->
                                        <!--                <td colspan="4">Chicken Eggs (Layer) 12 pcs</td>-->
                                        <!--                <td style="background: #eee;font-weight: bold;">Free</td>-->
                                        <!--            </tr>-->
                                        <!--            @else-->
                                        <!--            <tr>-->
                                        <!--                <td colspan="5">There is no free products. Sorry..!!</td>-->
                                        <!--            </tr>-->
                                        <!--    @endif-->
                                        <!--@endif-->
                                        @if($order->shipping_cost != 0)
                                                <tr>
                                                    <td colspan="4" style="text-align: right; font-weight: bold;">{{ __('Shipping cost') }}({{$order->currency_sign}})</td>
                                                    <td style="background-color:#eee !important;
        -webkit-print-color-adjust: exact; font-weight: bold;">{{ round($order->shipping_cost , 0) }}</td>
                                                </tr>
                                                @php $subtotal +=round($order->shipping_cost , 0)  @endphp
                                            {{-- @php 
                                            $price = round(($order->shipping_cost / $order->currency_value),0);
                                            @endphp
                                                @if(DB::table('shippings')->where('price','=',$price)->count() > 0)
                                                <tr>
                                                    <td colspan="2" style="font-weight: bold;">{{ DB::table('shippings')->where('price','=',$price)->first()->title }}({{$order->currency_sign}})</td>
                                                    <td style="font-weight: bold;">{{ round($order->shipping_cost , 0) }}</td>
                                                </tr>
                                                @endif--}}
                                        @endif 
                                            
                                        {{-- @if($order->packing_cost != 0)
                                            @php 
                                            $pprice = round(($order->packing_cost / $order->currency_value),2);
                                            @endphp
                                            @if(DB::table('packages')->where('price','=',$pprice)->count() > 0)
                                            <tr>
                                                <td colspan="2" style="font-weight:bold;">{{ DB::table('packages')->where('price','=',$pprice)->first()->title }}({{$order->currency_sign}})</td>
                                                <td style="background-color:#eee !important;
        -webkit-print-color-adjust: exact;font-weight:bold;">{{ round($order->packing_cost , 2) }}</td>
                                            </tr>
                                            @endif
                                        @endif --}}

                                        {{-- @if($order->tax != 0)
                                            <tr>
                                                <td colspan="2" style="font-weight:bold;">{{ __('TAX') }}({{$order->currency_sign}})</td>
                                                @php
                                                $tax = ($subtotal / 100) * $order->tax;
                                                @endphp
                                                <td style="font-weight:bold;">{{round($tax, 2)}}</td>
                                            </tr>
                                        @endif --}}
                                        @if($order->coupon_discount != null)
                                        <tr>
                                            <td colspan="4" style="font-weight: bold;">{{ __('Coupon Discount') }}({{$order->currency_sign}})</td>
                                            <td style="background-color:#eee !important;
        -webkit-print-color-adjust: exact;font-weight: bold;">{{round($order->coupon_discount, 0)}}</td>
                                            @php $subtotal -=round($order->coupon_discount , 0)  @endphp
                                        </tr>
                                        @endif
                                        <tr>
                                            <td colspan="3"></td>
                                            <td style="font-weight:bold; text-align: right;">{{ __('Total') }}</td>
                                        <td style="background-color:#eee !important;
        -webkit-print-color-adjust: exact; font-weight:bold;">{{$order->currency_sign}}{{ $subtotal }}</td>
                                            {{-- <td style="background-color:#eee !important;
        -webkit-print-color-adjust: exact; font-weight:bold;">{{$order->currency_sign}}{{ round($order->pay_amount * $order->currency_value , 0) }}</td> --}}
                                        </tr>
                                        @if($order->payment_status=="Pending")
                                        <tr>
                                            <td colspan="4">Payment Status: Cash On Delivery</td>
                                            <td style="color:red"><span><b><h3>Unpaid</b></h3></span></td>
                                        </tr>
                                        @else
                                        <tr>
                                            <td colspan="4">Payment Status: Online Payment</td>
                                            <td style="color:green;"><span><b><h3>Paid</b></h3></span></td>
                                        </tr>
                                        @endif
                                    </tfoot>
                                </table>
                                <p style="display: block; text-align: center;">Thank you for ordering from <b>advanceecommerce.com</b>. We offer a 7-day return/refund policy on all products. <br> If you have any complaint about this order, please call us at <b>01879186653</b> or email us at <b>support@advanceecommerce.com</b>.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<!-- ./wrapper -->

<script type="text/javascript">
setTimeout(function () {
        window.close();
      }, 500);
</script>

</body>
</html>