<?php

namespace App\Http\Controllers\Admin;

use App\Classes\GeniusMailer;
use App\Http\Controllers\Controller;
use App\Models\Generalsetting;
use App\Models\Order;
use App\Models\OrderTrack;
use App\Models\User;
use App\Models\VendorOrder;
use Datatables;
use PDF;
use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Auth;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    //*** JSON Request
    public function datatables($status)
    {
        if ($status == 'pending') {
            $datas = Order::where('status', '=', 'pending')->get();
        } elseif ($status == 'processing') {
            $datas = Order::where('status', '=', 'processing')->get();
        } elseif ($status == 'completed') {
            $datas = Order::where('status', '=', 'completed')->get();
        } elseif ($status == 'declined') {
            $datas = Order::where('status', '=', 'declined')->get();
        } elseif ($status == 'on delivery') {
            $datas = Order::where('status', '=', 'on delivery')->get();
        } else {
            $datas = Order::orderBy('id', 'desc')->get();
        }

        //--- Integrating This Collection Into Datatables
        return Datatables::of($datas)
            ->editColumn('id', function (Order $data) {
                $id = '<a href="' . route('admin-order-invoice', $data->id) . '">' . $data->order_number . '</a>';
                return $id;
            })
            ->editColumn('pay_amount', function (Order $data) {
                return $data->currency_sign . round($data->pay_amount * $data->currency_value, 0);
            })
            ->addColumn('action', function (Order $data) {
                $orders = '<a href="javascript:;" data-href="' . route('admin-order-edit', $data->id) . '" class="delivery" data-toggle="modal" data-target="#modal1"><i class="fas fa-dollar-sign"></i> Delivery Status</a>';
                return '<div class="godropdown"><button class="go-dropdown-toggle"> Actions<i class="fas fa-chevron-down"></i></button><div class="action-list"><a href="' . route('admin-order-show', $data->id) . '" > <i class="fas fa-eye"></i> Details</a><a href="javascript:;" class="send" data-email="' . $data->customer_email . '" data-toggle="modal" data-target="#vendorform"><i class="fas fa-envelope"></i> Send</a><a href="javascript:;" data-href="' . route('admin-order-track', $data->id) . '" class="track" data-toggle="modal" data-target="#modal1"><i class="fas fa-truck"></i> Track Order</a>' . $orders . '</div></div>';
            })
            ->rawColumns(['id', 'action'])
            ->toJson(); //--- Returning Json Data To Client Side
    }

    public function index()
    {
        return view('admin.order.index');
    }

    public function edit($id)
    {
        $data = Order::find($id);
        return view('admin.order.delivery', compact('data'));
    }

    //*** POST Request
    public function update(Request $request, $id)
    {
        //--- Logic Section
        $data = Order::findOrFail($id);

        $input = $request->all();
        $dd=Auth::guard('admin')->user()->name;
        $text=$request->track_text;
        $input['track_text'] = $text."(Edited By-".$dd.")";
        if ($data->status == "completed") {

            // Then Save Without Changing it.
            $input['status'] = "completed";
            $data->update($input);
            //--- Logic Section Ends


            //--- Redirect Section
            $msg = 'Status Updated Successfully.';
            return response()->json($msg);
            //--- Redirect Section Ends

        } else {
            if ($input['status'] == "completed") {

                foreach ($data->vendororders as $vorder) {
                    $uprice = User::findOrFail($vorder->user_id);
                    $uprice->current_balance = $uprice->current_balance + $vorder->price;
                    $uprice->update();
                }

                $gs = Generalsetting::findOrFail(1);
                if ($gs->is_smtp == 1) {
                    $maildata = [
                        'to' => $data->customer_email,
                        'subject' => 'Your order ' . $data->order_number . ' is Confirmed!',
                        'body' => "Hello " . $data->customer_name . "," . "\n Thank you for shopping with us. We are looking forward to your next visit.",
                    ];

                    $mailer = new GeniusMailer();
                    $mailer->sendCustomMail($maildata);
                } else {
                    $to = $data->customer_email;
                    $subject = 'Your order ' . $data->order_number . ' is Confirmed!';
                    $msg = "Hello " . $data->customer_name . "," . "\n Thank you for shopping with us. We are looking forward to your next visit.";
                    $headers = "From: " . $gs->from_name . "<" . $gs->from_email . ">";
                    mail($to, $subject, $msg, $headers);
                }
            }
            if ($input['status'] == "declined") {
                $gs = Generalsetting::findOrFail(1);
                if ($gs->is_smtp == 1) {
                    $maildata = [
                        'to' => $data->customer_email,
                        'subject' => 'Your order ' . $data->order_number . ' is Declined!',
                        'body' => "Hello " . $data->customer_name . "," . "\n We are sorry for the inconvenience caused. We are looking forward to your next visit.",
                    ];
                    $mailer = new GeniusMailer();
                    $mailer->sendCustomMail($maildata);
                } else {
                    $to = $data->customer_email;
                    $subject = 'Your order ' . $data->order_number . ' is Declined!';
                    $msg = "Hello " . $data->customer_name . "," . "\n We are sorry for the inconvenience caused. We are looking forward to your next visit.";
                    $headers = "From: " . $gs->from_name . "<" . $gs->from_email . ">";
                    mail($to, $subject, $msg, $headers);
                }

            }

            $data->update($input);

            if ($request->track_text) {
                $title = ucwords($request->status);
                $ck = OrderTrack::where('order_id', '=', $id)->where('title', '=', $title)->first();
                if ($ck) {
                    $ck->order_id = $id;
                    $ck->title = $title;
                    $ck->text = $request->track_text."(Edited By-".$dd.")";
                    $ck->update();
                } else {
                    $data = new OrderTrack;
                    $data->order_id = $id;
                    $data->title = $title;
                    $ck->text = $request->track_text."(Edited By-".$dd.")";
                    $data->save();
                }


            }


            $order = VendorOrder::where('order_id', '=', $id)->update(['status' => $input['status']]);

            //--- Redirect Section
            $msg = 'Status Updated Successfully.';
            return response()->json($msg);
            //--- Redirect Section Ends

        }

        //--- Redirect Section          
        $msg = 'Status Updated Successfully.';
        return response()->json($msg);
        //--- Redirect Section Ends  

    }

    public function dailyProfitability()
    {


        $dailyProfitability = DB::table('orders')->select(array(
            DB::raw('DATE(`created_at`) as `date`'),

            /*DB::Raw('COUNT(id) as `num_items`'),*/
            DB::Raw('SUM(totalQty) as `num_items`'),
            DB::raw('SUM(pay_amount*currency_value) as total_pay_amount'),
            /*  DB::raw(
                  'COUNT(id) AS total,
                   COUNT(CASE status WHEN "completed" THEN 1 ELSE NULL END) AS completed,
                   COUNT(CASE status WHEN "declined" THEN 1 ELSE NULL END) AS declined

                   ')*/

        ))
            ->where('status', 'completed')
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();


        return view('admin.order.dailyProfitability', compact('dailyProfitability'));

    }

    public function MetricsbyMonth(){

        /*final git post*/

        $month_order = DB::select('
SELECT YEAR (`created_at`) AS `year`, MONTH (`created_at`) AS `month_order`,
 COUNT(id) AS numder_of_order,
  COUNT( CASE STATUS WHEN "completed" THEN 1 ELSE NULL END ) AS completed_order,
   COUNT( CASE STATUS WHEN "declined" THEN 1 ELSE NULL END ) AS cancel_order,
    COUNT(DISTINCT(user_id)) AS numder_of_customer, SUM(pay_amount * currency_value) AS total_pay_amount 
    FROM orders
    
     GROUP BY `month_order` ORDER BY `month_order` ASC
    ');



        return view('admin.order.month_order', compact('month_order'));

    }

    public function byDay()
    {


        /*
                $post = Order::whereYear('created_at', '=', Carbon::now()->year )
                    ->whereMonth('created_at', '=', Carbon::now()->month)
                    ->toSql();*/

        $year = Carbon::now()->year;
        $month = Carbon::now()->month;


        $days_order = DB::select('SELECT DATE(`created_at`) AS `date`,
 COUNT(id) AS numder_of_order,
  COUNT( CASE STATUS WHEN "completed" THEN 1 ELSE NULL END ) AS completed_order,
   COUNT( CASE STATUS WHEN "declined" THEN 1 ELSE NULL END ) AS cancel_order,
    COUNT(DISTINCT(user_id)) AS numder_of_customer, SUM(pay_amount * currency_value) AS total_pay_amount 
    FROM orders
     where year(`created_at`) = ' . $year . '     and month(`created_at`) = ' . $month . '
     GROUP BY `date` ORDER BY `date` ASC
    ');

        return view('admin.order.all-orders-by-day', compact('days_order'));
    }

    public function pending()
    {
        return view('admin.order.pending');
    }

    public function processing()
    {
        return view('admin.order.processing');
    }

    public function completed()
    {
        return view('admin.order.completed');
    }

    public function declined()
    {
        return view('admin.order.declined');
    }

    public function ondelivery()
    {
        return view('admin.order.ondelivery');
    }

    public function createCustomOrder()
    {
        return view('admin.order.create-custom-order');
    }

    public function getCustomerNumber($id)
    {
        $data = User::findOrFail($id);
        $number = $data->phone;
        return $number;
    }

    public function show($id)
    {

        if (!Order::where('id', $id)->exists()) {
            return redirect()->route('admin.dashboard')->with('unsuccess', __('Sorry the page does not exist.'));
        }
        $order = Order::findOrFail($id);
        $cart = unserialize(bzdecompress(utf8_decode($order->cart)));


        return view('admin.order.details', compact('order', 'cart'));
    }

    public function invoice($id)
    {
        $order = Order::findOrFail($id);
        $cart = unserialize(bzdecompress(utf8_decode($order->cart)));
        return view('admin.order.invoice', compact('order', 'cart'));
    }

    public function emailsub(Request $request)
    {
        $gs = Generalsetting::findOrFail(1);
        if ($gs->is_smtp == 1) {
            $data = 0;
            $datas = [
                'to' => $request->to,
                'subject' => $request->subject,
                'body' => $request->message,
            ];

            $mailer = new GeniusMailer();
            $mail = $mailer->sendCustomMail($datas);
            if ($mail) {
                $data = 1;
            }
        } else {
            $data = 0;
            $headers = "From: " . $gs->from_name . "<" . $gs->from_email . ">";
            $mail = mail($request->to, $request->subject, $request->message, $headers);
            if ($mail) {
                $data = 1;
            }
        }

        return response()->json($data);
    }

    public function printpage($id)
    {
        $order = Order::findOrFail($id);
        $cart = unserialize(bzdecompress(utf8_decode($order->cart)));
        return view('admin.order.print', compact('order', 'cart'));
    }

    public function printAllOrder()
    {
        $datas = Order::orderBy('id', 'desc')->get();
        return view('admin.order.index-print', compact('datas'));
    }

    public function license(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $cart = unserialize(bzdecompress(utf8_decode($order->cart)));
        $cart->items[$request->license_key]['license'] = $request->license;
        $order->cart = utf8_encode(bzcompress(serialize($cart), 9));
        $order->update();
        $msg = 'Successfully Changed The License Key.';
        return response()->json($msg);
    }
}