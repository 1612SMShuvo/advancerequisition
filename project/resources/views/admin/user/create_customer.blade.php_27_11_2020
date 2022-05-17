@extends('layouts.admin') 

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
												<a href="{{ route('create_customer_page') }}">{{ __("Create New Customers") }}</a>
											</li>
										</ul>
								</div>
							</div>
						</div>
						<div class="product-area">
							<div  id="nav-reg" role="tabpanel">
					            <div class="login-area signup-area">
					            	<br>
                            <div class="row">
                                <div class="col-lg-3">
                                </div>
                                <div class="col-lg-6 shade_box">
                                    <div class="mr-table allproduct">
										<div class="table-responsiv formbody">
						                    <div class="myform form ">			
								                <form action="{{route('user-create_&_login-only')}}" method="POST">
								                  {{ csrf_field() }}
								                  <center><h3>Create New Customer</h3></center>
								                  <hr>
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
									              </div>
									              
								                  <div class="form-group">
								                    <input type="text" class="form-control" name="name" placeholder="{{ $langg->lang182 }}" required="">
								                  </div>

								                  <div class="form-group">
								                    <input type="email" class="form-control" name="email" placeholder="{{ $langg->lang183 }}">
								                  </div>

								                  <div class="form-group">
								                    <input type="number" name="phone" class="form-control" id="reg_phone" pattern="[0-9]{11}" placeholder="{{ $langg->lang184 }}"
								                          required="">
								                  </div>

								                  <div class="form-group">
								                    <input type="text" class="form-control" name="address" placeholder="{{ $langg->lang185 }}">
								                  </div>
								                  
                                                   <input type="hidden" name="admin_request" value="true">
								                  <center><button type="submit" name="buttonreg" value="11" class="btn btn-success">{{
								                  $langg->lang189
								                  }}</button></center>
								                </form>
								            </div>
								        </div>
								    </div>    
                                </div>
                                <div class="col-lg-3">
                                </div>
                            </div><br><br>
				        </div>
					</div>
				</div>
			</div>
@endsection    