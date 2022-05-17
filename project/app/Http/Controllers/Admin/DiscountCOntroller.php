<?php

namespace App\Http\Controllers\Admin;

use App\Models\Discounts;
use App\Models\Childcategory;
use App\Models\Subcategory;
use Datatables;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Gallery;
use App\Models\Attribute;
use App\Models\AttributeOption;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Validator;
use Image;
use DB;

class DiscountCOntroller extends Controller
{

   public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $sign = Currency::where('is_default','=',1)->first();
        $discounts = Discounts::orderBy('created_at');
        foreach ($discounts as $data) {
            $data->price = round($data->price * $sign->value , 2);
            $data->discount = round($data->discount * $sign->value , 2);
            $data->discount_price = round($data->discount_price * $sign->value , 2);
            $data->conditional_price = round($data->conditional_price * $sign->value , 2);
        }
        // dd($discounts);
        return view('admin.discount.index',compact('discounts'));
    }

    public function adddiscountpage()
    {
        $datas = DB::table('products')->get();
        return view('admin.discount.adddiscount',compact('datas'));
    }

    public function findproduct(Request $request)
    {
        $data = DB::table('products')->where('id',$request->id)->first();
        $currency=Currency::where('is_default','=',1)->first();
        $price=round($data->price * $currency->value);
        return response()->json($price);
    }

    public function storediscount(Request $request)
    {
         // dd($request->all());
        //--- Validation Section
        $rules = [
            'product_id' => 'unique:discounts|required',
            'price' => 'required',
            'discount_type'=>'required|max:30',
            'discount_amount'=>'required',
            'discount_price'=>'required',
            'conditional_price'=>'required',
            'status'=>'required|max:50',
            'max_quantity'=>'required|max:50'
        ];
        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return redirect()->back() ->withInput()->withErrors($validator);
        }

        $sign = Currency::where('is_default','=',1)->first();

        // if ($request->discount_type == 2) {
        //     $request->discount_price = $request->price - $request->discount_amount;
        // }else{
        //     $request->discount_price = $request->price - ($request->price * $request->discount_amount/100);
        // }

        $data =array();

        $product=DB::table('products')->where('id',$request->product_id)->first();
        $product_name=$product->name;
        $data['product_id']=$request->product_id;
        $data['product_name']=$product_name;
        $data['price']=($request->price / $sign->value);
        $data['discount_type']=$request->discount_type;
        $data['discount']=($request->discount_amount / $sign->value);
        $data['discount_price']=($request->discount_price / $sign->value);
        $data['conditional_price']=($request->conditional_price / $sign->value);
        $data['status']=$request->status;
        $data['max_quantity']=$request->max_quantity;

        $insert=DB::table('discounts')->insert($data);
        if ($insert) {
            return view('admin.discount.index');
        }
        
        else{
            // $flash_message_error="Can not Be Added Discount Details.....!!!";
            // return view('admin.discount.adddiscount');
            return redirect()->back();
        }  
    }

    public function datatables()
        {
            $sign = Currency::where('is_default','=',1)->first();
            $datas = Discounts::orderBy('created_at','DESC')->get();
            foreach ($datas as $dat) {
                $dat->price = round($dat->price * $sign->value , 2);
                $dat->discount = round($dat->discount * $sign->value , 2);
                $dat->discount_price = round($dat->discount_price * $sign->value , 2);
                $dat->conditional_price = round($dat->conditional_price * $sign->value , 2);
            }
             //--- Integrating This Collection Into Datatables
             return Datatables::of($datas)
                                ->editColumn('discount', function(Discounts $data) {
                                    if($data->discount_type=="1"){
                                        $discount = $data->discount.'%' ;
                                        return  $discount;
                                    }
                                    else{
                                        $discount = '৳'.$data->discount ;
                                        return  $discount;
                                    }
                                })
                                ->editColumn('status', function(Discounts $data) {
                                    $class = $data->status == 1 ? 'drop-success' : 'drop-danger';
                                $s = $data->status == 1 ? 'selected' : '';
                                $ns = $data->status == 0 ? 'selected' : '';
                                return '<div class="action-list"><select class="process select droplinks '.$class.'"><option data-val="1" value="'. route('admin-discount-status',['id1' => $data->id, 'id2' => 1]).'" '.$s.'>Activated</option><<option data-val="0" value="'. route('admin-discount-status',['id1' => $data->id, 'id2' => 0]).'" '.$ns.'>Deactivated</option>/select></div>';
                                })
                                ->addColumn('action', function(Discounts $data) {
                                    return '<div class="action-list"><a href="' . route('admin-discount-edit',$data->id) . '"> <i class="fas fa-edit"></i>Edit</a><a href="javascript:;" data-href="' . route('admin-discount-delete',$data->id) . '" data-toggle="modal" data-target="#confirm-delete" class="delete"><i class="fas fa-trash-alt"></i></a></div>';
                                }) 
                                ->rawColumns(['status','action'])
                                ->toJson(); //--- Returning Json Data To Client Side
        }

    public function status($id1,$id2)
        {
            $data = Discounts::findOrFail($id1);
            $data->status = $id2;
            $data->update();
        }

    public function destroy($id)
        {
            $data = Discounts::findOrFail($id);
            $data->delete();
            //--- Redirect Section     
            $msg = 'Data Deleted Successfully.';
            return response()->json($msg);      
            //--- Redirect Section Ends   
        }

    public function edit($id)
    {
        $data = Discounts::findOrFail($id);
        return view('admin.discount.editdiscount',compact('data'));
    }
    public function update(Request $request,$id)
    {
        // dd($request->all());
        //--- Validation Section
        $rules = [
            'product_id' => 'required',
            'price' => 'required',
            'discount_type'=>'required|max:30',
            'discount_amount'=>'required',
            'discount_price'=>'required',
            'conditional_price'=>'required',
            'status'=>'required|max:50',
            'max_quantity'=>'required|max:50'
        ];
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return redirect()->back() ->withInput()->withErrors($validator);
        }

        $sign = Currency::where('is_default','=',1)->first();

        // if ($request->discount_type == 2) {
        //     $request->discount_price = $request->price - $request->discount_amount;
        // }else{
        //     $request->discount_price = $request->price - ($request->price * $request->discount_amount/100);
        // }
            
        $data =array();

        
        $data['product_id']=$request->product_id;
        $data['product_name']=$request->product_name;
        $data['price']=($request->price / $sign->value);
        $data['discount_type']=$request->discount_type;
        $data['discount']=($request->discount_amount / $sign->value);
        $data['discount_price']=($request->discount_price / $sign->value);
        $data['conditional_price']=($request->conditional_price / $sign->value);
        $data['status']=$request->status;
        $data['max_quantity']=$request->max_quantity;

        $update=DB::table('discounts')->where('id',$id)->update($data);
        if ($update) {
            return view('admin.discount.index');
        }
        
        else{
            // $flash_message_error="Can not Be Added Discount Details.....!!!";
            // return view('admin.discount.adddiscount');
            return redirect()->back();
        }
    }
}
