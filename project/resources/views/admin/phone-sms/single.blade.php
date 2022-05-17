@extends('layouts.admin')

@section('scripts')

@endsection
@section('content')  
					<input type="hidden" id="headerdata" value="{{ __("CUSTOMER") }}">
					<div class="content-area">
						<div class="mr-breadcrumb">
							<div class="row">
								<div class="col-lg-12">
										<h4 class="heading">{{ __("Customers") }}</h4>
										<ul class="links">
											<li>
												<a href="{{ route('admin.dashboard') }}">{{ __("Dashboard") }} </a>
											</li>
											<li>
												<a href="{{ route('admin-user-index') }}">{{ __("Customers") }}</a>
											</li>
											<li>
												<a href="{{ route('create_customer_page') }}">{{ __("message") }}</a>
											</li>
										</ul>
								</div>
							</div>
						</div>


<!-- Include the default stylesheet -->
<!-- <link rel="stylesheet" type="text/css" href="https://cdn.rawgit.com/wenzhixin/multiple-select/e14b36de/multiple-select.css"> -->
<!-- Include plugin -->
<!-- <script src="https://cdn.rawgit.com/wenzhixin/multiple-select/e14b36de/multiple-select.js"></script>
 -->
						<div class="add-product-content1">
			                <div class="row">
			                  <div class="col-lg-12">
			                    <div class="product-description">
			                      <div class="body-area">
									  @include('includes.admin.form-error')
									  <form action="{{route('insertShowSingleSmsForm')}}" id="" method="POST">
											{{csrf_field()}}
											<div class="row justify-content-center">
												<div class="col-lg-3">
													<div class="left-area">
														<h4 class="heading">Select Name *
														</h4>
													</div>
												</div>
												<div class="col-lg-6">
													
													<!-- A multiple select element -->
													<select name="user_id[]" id="my-select" multiple="multiple">
														@foreach($users as $user)
														<option value="{{$user->id}}">{{$user->name}}</option>
														@endforeach
													</select>
												</div>
											</div>

											<div class="row justify-content-center">
												<div class="col-lg-3">
													<div class="left-area">
														<h4 class="heading">Text Messages </h4>
													</div>
												</div>
												<div class="col-lg-6">
													<textarea name="message" class="input-field" placeholder="Message"></textarea>
												</div>
											</div>

											<div class="row justify-content-center">
											  <div class="col-lg-3">
											    <div class="left-area">

											    </div>
											  </div>
											  <div class="col-lg-6">
											    <button class="addProductSubmit-btn" type="submit">Send Messages</button>
											  </div>
											</div>

										</form>
									 	

												

			                    </div>
			                  </div>
			                </div>
			              </div>	
						</div>
					</div>


					<script>
					    // Initialize multiple select on your regular select
					    $("#my-select").multipleSelect({
					        filter: true,
					        // minimumCountSelected: 2,
						  	placeholder: 'Filter & Select Name',
						  	// delimiter: ',',
						 	// formatSelectAll: () => 'A',
						  //   formatAllSelected: () => 'B',
						  //   formatCountSelected: () => 'c',
						  //   formatNoMatchesFound: () => 'd'
					    });
					</script>
					
@endsection    