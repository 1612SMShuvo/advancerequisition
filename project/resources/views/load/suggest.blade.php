@foreach($prods as $prod)
@php
$discount_product =\DB::table('discounts')->where('product_id',$prod->id)->where('status', '=', 1)->get()->first();
$curr = App\Models\Currency::where('is_default','=',1)->first();
@endphp
	<div class="docname">
		<a href="{{ route('front.product', $prod->slug) }}">
			<img src="{{ asset('assets/images/thumbnails/'.$prod->thumbnail) }}" alt="">
			<div class="search-content">
				<p>{!! mb_strlen($prod->name,'utf-8') > 66 ? str_replace($slug,'<b>'.$slug.'</b>',mb_substr($prod->name,0,66,'utf-8')).'...' : str_replace($slug,'<b>'.$slug.'</b>',$prod->name)  !!} </p>
				<!-- <span style="font-size: 14px; font-weight:600; display:block;">{{ $prod->showPrice() }}</span> -->
				@if(isset($discount_product->discount_price))
				<h4 class="price" style="font-size: 14px; font-weight:600; display:block;">*{{ $curr->sign }}{{ $discount_product->discount_price*$curr->value }} <del><small>{{ $prod->setCurrency() }}</small></del>
				</h4>
				@else
					<h4 class="price" style="font-size: 14px; font-weight:600; display:block;">{{ $prod->setCurrency() }}
					</h4>
				@endif
			</div>
		</a>
	</div> 
@endforeach