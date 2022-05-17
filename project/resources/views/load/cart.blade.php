@if(Session::has('cart'))
    <div class="dropdownmenu-wrapper">
        <div class="dropdown-cart-header">
        	<span class="item-no">
        		<span class="cart-quantity">
        {{ Session::has('cart') ? count(Session::get('cart')->items) : '0' }}
        		</span> {{ $langg->lang4 }}
        	</span>

            <a class="view-cart" href="{{ route('front.cart') }}">
                {{ $langg->lang5 }}
            </a>
        </div><!-- End .dropdown-cart-header -->

            @php $curr = App\Models\Currency::where('is_default','=',1)->first(); @endphp
            <div class="left-area">
                <div class="cart-table">
                    <table class="table">


                        <tbody>
                        @if(Session::has('cart'))

                            @foreach(Session::get('cart')->items as $product)

                                @php $discount_product =\DB::table('discounts')->where('product_id',$product['item']['id'])->where('status', '=', 1)->get()->first(); @endphp
                                <tr class="cremove{{ $product['item']['id'].$product['size'].$product['color'].str_replace(str_split(' ,'),'',$product['values']) }}">
                                    <td class="product-img">
                                        <div class="item">
                                            <img src="{{ $product['item']['photo'] ? asset('assets/images/products/'.$product['item']['photo']):asset('assets/images/noimage.png') }}" alt="">

                                        </div>
                                        <div class="item">

                                            <p class="name"><a href="{{ route('front.product', $product['item']['slug']) }}">{{mb_strlen($product['item']['name'],'utf-8') > 35 ? mb_substr($product['item']['name'],0,5,'utf-8').'...' : $product['item']['name']}}</a></p>
                                        </div>
                                        @if(!empty($discount_product))
                                        <div class="item alert-warning" style="font-size: 12px; display: inline-block; width: 100%; margin: 2px 0px;">{{'Purchase '}}{{$curr->sign}}{{$discount_product->conditional_price*$curr->value}}{{' & Get OFF '}}{{$discount_product->discount_type == 2 ? $curr->sign : '' }}{{$discount_product->discount*$curr->value}}{{$discount_product->discount_type == 1 ? '%' : '' }}{{'/Unit'}}</div>
                                        @endif
                                    </td>


                                    <td class="unit-price quantity">

                                        <p id="prc{{$product['item']['id'].$product['size'].$product['color'].str_replace(str_split(' ,'),'',$product['values'])}}">
                                            {{ App\Models\Product::convertPrice($product['price']) }}
                                        </p>

                                        @if($product['item']['type'] == 'Physical')

                                            <div class="qty">
                                                <ul>
                                                    <input type="hidden" class="prodid" value="{{$product['item']['id']}}"><input type="hidden" class="itemid" value="{{$product['item']['id'].$product['size'].$product['color'].str_replace(str_split(' ,'),'',$product['values'])}}"><input type="hidden" class="size_qty" value="{{$product['size_qty']}}">
                                                    <input type="hidden" class="size_price" value="{{$product['item']['price']}}">
                                                    <li><span class="qtminus1 reducing">
                                    <i class="icofont-minus"></i></span></li><li><span class="qttotal1" id="qty{{$product['item']['id'].$product['size'].$product['color'].str_replace(str_split(' ,'),'',$product['values'])}}">{{ $product['qty'] }}</span></li><li><span class="qtplus1 adding"><i class="icofont-plus"></i></span>
                                                    </li>
                                                </ul>
                                            </div>
                                        @endif

                                        @if(!empty($discount_product))
                                        <div class="item alert-warning" style="font-size: 12px; display: inline-block; width: 60%; margin: 2px 0px;">{{ 'Max. Qty '.$discount_product->max_quantity }}</div>
                                        @endif


                                    </td>

                                    @if($product['size_qty'])
                                        <input type="hidden" id="stock{{$product['item']['id'].$product['size'].$product['color'].str_replace(str_split(' ,'),'',$product['values'])}}" value="{{$product['size_qty']}}">
                                    @elseif($product['item']['type'] != 'Physical')
                                        <input type="hidden" id="stock{{$product['item']['id'].$product['size'].$product['color'].str_replace(str_split(' ,'),'',$product['values'])}}" value="1">
                                    @else
                                        <input type="hidden" id="stock{{$product['item']['id'].$product['size'].$product['color'].str_replace(str_split(' ,'),'',$product['values'])}}" value="{{$product['stock']}}">
                                    @endif


                                    <td>
                                        <span class="removecart cart-remove" data-class="cremove{{ $product['item']['id'].$product['size'].$product['color'].str_replace(str_split(' ,'),'',$product['values']) }}" data-href="{{ route('product.cart.remove',$product['item']['id'].$product['size'].$product['color'].str_replace(str_split(' ,'),'',$product['values'])) }}"><i class="icofont-ui-delete"></i> </span>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>



        <div class="dropdown-cart-total">
            <span>{{ $langg->lang6 }}</span>

            <span class="cart-total-price">
															<span class="cart-total">{{ Session::has('cart') ? App\Models\Product::convertPrice(Session::get('cart')->totalPrice) : '0.00' }}
															</span>
														</span>
        </div><!-- End .dropdown-cart-total -->

        <div class="dropdown-cart-action">
            <a href="{{ route('front.checkout') }}" class="mybtn1">{{ $langg->lang7 }}</a>
        </div><!-- End .dropdown-cart-total -->
    </div>
@else
    <p class="mt-1 pl-3 text-left">{{ $langg->lang8 }}</p>
@endif

<script>

</script>
