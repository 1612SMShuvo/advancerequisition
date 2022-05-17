			@if (count($prods) > 0)
					@foreach ($prods as $key => $prod)
						@php

$discount_product =\DB::table('discounts')->where('product_id',$prod->id)->where('status', '=', 1)->get()->first();
$curr = App\Models\Currency::where('is_default','=',1)->first();
						@endphp
						<div class="col-lg-2 col-md-2 col-6 remove-padding">


							<a href="{{ route('front.product', $prod->slug) }}" class="item">
								<div class="item-img">
									@if(!empty($prod->features))
										<div class="sell-area">
											@foreach($prod->features as $key => $data1)
											<span class="sale" style="background-color:{{ $prod->colors[$key] }}">{{ $prod->features[$key] }}</span>
											@endforeach
										</div>
									@endif
									<!-- 	<div class="extra-list">
											<ul>
												<li>
													@if(Auth::guard('web')->check())

													<span class="add-to-wish" data-href="{{ route('user-wishlist-add',$prod->id) }}" data-toggle="tooltip" data-placement="right" title="{{ $langg->lang54 }}" data-placement="right"><i class="icofont-heart-alt" ></i>
													</span>

													@else

													<span rel-toggle="tooltip" title="{{ $langg->lang54 }}" data-toggle="modal" id="wish-btn" data-target="#comment-log-reg" data-placement="right">
														<i class="icofont-heart-alt"></i>
													</span>

													@endif
												</li>
												<li>
												<span class="quick-view" rel-toggle="tooltip" title="{{ $langg->lang55 }}" href="javascript:;" data-href="{{ route('product.quick',$prod->id) }}" data-toggle="modal" data-target="#quickview" data-placement="right"> <i class="icofont-eye"></i>
												</span>
												</li>
												<li>
													<span class="add-to-compare" data-href="{{ route('product.compare.add',$prod->id) }}"  data-toggle="tooltip" data-placement="right" title="{{ $langg->lang57 }}" data-placement="right">
														<i class="icofont-exchange"></i>
													</span>
												</li>
											</ul>
										</div> -->
									<img class="img-fluid" src="{{ $prod->thumbnail ? asset('assets/images/thumbnails/'.$prod->thumbnail):asset('assets/images/noimage.png') }}" alt="">
								</div>
								@if(isset($discount_product->discount))
								<div class="dis-p">{{ $discount_product->discount*$curr->value }}{{ $discount_product->discount_type == 1 ? '%' : $curr->sign }}
								 <br> OFF*</div>
								@endif
								<div class="info">
									<div class="stars">
										<div class="ratings">
											<div class="empty-stars"></div>
											<div class="full-stars" style="width:{{App\Models\Rating::ratings($prod->id)}}%"></div>
										</div>
									</div>
									@if(isset($discount_product->discount_price))
									<h4 class="price">{{ $curr->sign }}{{ $discount_product->discount_price*$curr->value }} <del><small>{{ $prod->setCurrency() }}</small></del>
									</h4>
									@else
										<h4 class="price">{{ $prod->setCurrency() }}
										</h4>
									@endif

									<h5 class="name">{{ $prod->showName() }}</h5>
									<div class="item-cart-area">
										@if($prod->product_type == "affiliate")
											<span class="add-to-cart-btn affilate-btn"
												data-href="{{ route('affiliate.product', $prod->slug) }}"><i class="icofont-cart"></i>
												{{ $langg->lang251 }}
											</span>
										@else
											@if($prod->emptyStock())
											<span class="add-to-cart-btn cart-out-of-stock">
												<i class="icofont-close-circled"></i> {{ $langg->lang78 }}
											</span>
											@else
											<span class="add-to-cart add-to-cart-btn" data-href="{{ route('product.cart.add',$prod->id) }}">
												<i class="icofont-cart"></i> {{ $langg->lang56 }}
											</span>
											<span class="add-to-cart-quick add-to-cart-btn"
												data-href="{{ route('product.cart.quickadd',$prod->id) }}">
												<i class="icofont-cart"></i> {{ $langg->lang251 }}
											</span>
											@endif
										@endif
									</div>
								</div>
							</a>

						</div>
					@endforeach
				{{-- Disable pagination --}}
				{{-- <div class="col-lg-12">
					<div class="page-center mt-5">
						{!! $prods->appends(['search' => request()->input('search')])->links() !!}
					</div>
				</div> --}}
			@else
				{{-- <div class="col-lg-12">
					<div class="page-center mt-5 alert alert-warning alert-dismissible fade show" id="noData">
						<p class="text-center" id="noProduct">No more product!</p>
						<button type="button" class="close" data-dismiss="alert">&times;</button>
					</div>
				</div> --}}
			@endif


@if(isset($ajax_check))


<script type="text/javascript">

// Tooltip Section
    $('[data-toggle="tooltip"]').tooltip({
      });
      $('[data-toggle="tooltip"]').on('click',function(){
          $(this).tooltip('hide');
      });




      $('[rel-toggle="tooltip"]').tooltip();

      $('[rel-toggle="tooltip"]').on('click',function(){
          $(this).tooltip('hide');
      });


// Tooltip Section Ends

</script>

@endif