<?php

namespace App\Http\Controllers;

use App\Classes\GeniusMailer;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Currency;
use App\Models\Generalsetting;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderTrack;
use App\Models\Pagesetting;
use App\Models\PaymentGateway;
use App\Models\Pickup;
use App\Models\Product;
use App\Models\User;
use App\Models\UserNotification;
use App\Models\VendorOrder;
use Auth;
use DB;
use Illuminate\Http\Request;
use Session;
use Validator;
use App\Library\SslCommerz\SslCommerzNotification;

class SslCommerzPaymentController extends Controller
{

    public function exampleEasyCheckout()
    {
        return view('exampleEasycheckout');
    }

    public function exampleHostedCheckout()
    {
        return view('exampleHosted');
    }

    public function index(Request $request)
    {
        # Here you have to receive all the order data to initate the payment.
        # Let's say, your oder transaction informations are saving in a table called "orders"
        # In "orders" table, order unique identity is "transaction_id". "status" field contain status of the transaction, "amount" is the order amount to be paid and "currency" is for storing Site Currency which will be checked with paid currency.

        $input = $request->all();

        if($request->pass_check) {
            $users = User::where('email', '=', $request->personal_email)->get();
            if(count($users) == 0) {
                if ($request->personal_pass == $request->personal_confirm) {
                    $user = new User;
                    $user->name = $request->personal_name; 
                    $user->email = $request->personal_email;   
                    $user->password = bcrypt($request->personal_pass);
                    $token = md5(time().$request->personal_name.$request->personal_email);
                    $user->verification_link = $token;
                    $user->affilate_code = md5($request->name.$request->email);
                    $user->email_verified = 'Yes';
                    $user->save();
                    Auth::guard('web')->login($user);                     
                }else{
                    return redirect()->back()->with('unsuccess', "Confirm Password Doesn't Match.");     
                }
            }
            else {
                return redirect()->back()->with('unsuccess', "This Email Already Exist.");  
            }
        }

        $gs = Generalsetting::findOrFail(1);
        
        if (!Session::has('cart')) {
            return redirect()->route('front.cart')->with('success', "You don't have any product to checkout.");
        }

        $order = Order::latest()->first();
        $orderNumber = str_replace(array('MB-'), '', $order->order_number);
        $newOrderNumber = $orderNumber + 1;

        $oldCart = Session::get('cart');
        $cart = new Cart($oldCart);
        if (Session::has('currency')) {
            $curr = Currency::find(Session::get('currency'));
        }
        else
            {
            $curr = Currency::where('is_default', '=', 1)->first();
        }

        if($request->discountItems) {
            $discountItems = json_decode($request->discountItems);
            foreach ($discountItems as $key => $value) {
                foreach ($cart->items as $cartKeys => $carts) {
                    if ($cartKeys == $key) {
                        $carts['item']['price'] = $value;
                        $carts['price'] = $value*$carts['qty'];
                        $cart->updatePrice($key,$carts['price']);
                    }
                }
            }
        }

        foreach($cart->items as $key => $prod)
        {
            if(!empty($prod['item']['license']) && !empty($prod['item']['license_qty'])) {
                foreach($prod['item']['license_qty']as $ttl => $dtl)
                {
                    if($dtl != 0) {
                        $dtl--;
                        $produc = Product::findOrFail($prod['item']['id']);
                        $temp = $produc->license_qty;
                        $temp[$ttl] = $dtl;
                        $final = implode(',', $temp);
                        $produc->license_qty = $final;
                        $produc->update();
                        $temp =  $produc->license;
                        $license = $temp[$ttl];
                         $oldCart = Session::has('cart') ? Session::get('cart') : null;
                         $cart = new Cart($oldCart);
                         $cart->updateLicense($prod['item']['id'], $license);  
                         Session::put('cart', $cart);
                        break;
                    }                    
                }
            }
        }
        $settings = Generalsetting::findOrFail(1);
        $order = new Order;
        $success_url = action('Front\PaymentController@payreturn');
        $item_name = $settings->title." Order";
        $item_number = str_random(4).time();
        $order['user_id'] = $request->user_id;
        $order['cart'] = utf8_encode(bzcompress(serialize($cart), 9));
        $order['totalQty'] = $request->totalQty;
        $order['pay_amount'] = round($request->total / $curr->value, 2);
        $order['method'] = $request->method;
        $order['shipping'] = $request->shipping;
        $order['pickup_location'] = $request->pickup_location;
        $order['customer_email'] = $request->email;
        $order['customer_name'] = $request->name;
        $order['shipping_cost'] = $request->shipping_cost;
        $order['packing_cost'] = $request->packing_cost;
        $order['tax'] = $request->tax;
        $order['customer_phone'] = $request->phone;
        // $OrderNum = $this->getLastOrder();
        // $newOrderNumber = $OrderNum + 1;
        $order['order_number'] = "MB-".$newOrderNumber;
        // $order['order_number'] = str_random(4).time();
        $order['customer_address'] = $request->address;
        $order['customer_country'] = "Bangladesh";
        $order['customer_city'] = $request->city;
        $order['customer_zip'] = null;
        $order['shipping_email'] = $request->shipping_email;
        $order['shipping_name'] = $request->shipping_name;
        $order['shipping_phone'] = $request->shipping_phone;
        $order['shipping_address'] = $request->shipping_address;
        $order['shipping_country'] = null;
        $order['shipping_city'] = $request->shipping_city;
        $order['shipping_zip'] = null;
        $order['order_note'] = $request->order_notes;
        // $order['txnid'] = $request->txn_id4;
        $order['coupon_code'] = $request->coupon_code;
        $order['coupon_discount'] = $request->discount_price ?? $request->coupon_discount;
        $order['dp'] = $request->dp;
        $order['payment_status'] = "Pending";
        $order['currency_sign'] = $curr->sign;
        $order['currency_value'] = $curr->value;
        $order['vendor_shipping_id'] = $request->vendor_shipping_id;
        $order['vendor_packing_id'] = $request->vendor_packing_id; 
        $order['created_at']=now()->addHours(6);  
        $ordertime= $order['created_at']->toTimeString();
        if($ordertime>15) {
            $order['shipping_time']= "Morning(9:30 AM To 11:30 AM)" ;
        }
        elseif ($ordertime>11) {
            $order['shipping_time']= "Evening(4:00 PM To 7:00 PM))" ;
        }
        else{
            $order['shipping_time']= "Mid Day(11:30 AM To 3:00 PM)" ;
        }     
        if (Session::has('affilate')) {
            $val = $request->total / $curr->value;
            $val = $val / 100;
            $sub = $val * $gs->affilate_charge;
            $user = User::findOrFail(Session::get('affilate'));
            $user->affilate_income += $sub;
            $user->update();
            $order['affilate_user'] = $user->name;
            $order['affilate_charge'] = $sub;
        }
        $order->save();

        $track = new OrderTrack;
        $track->title = 'Pending';
        $track->text = 'You have successfully placed your order.';
        $track->order_id = $order->id;
        $track->save();
        
        $notification = new Notification;
        $notification->order_id = $order->id;
        $notification->save();
        if($request->coupon_id != "") {
            $coupon = Coupon::findOrFail($request->coupon_id);
            $coupon->used++;
            if($coupon->times != null) {
                            $i = (int)$coupon->times;
                            $i--;
                            $coupon->times = (string)$i;
            }
                        $coupon->update();

        }

        foreach($cart->items as $prod)
        {
            $x = (string)$prod['size_qty'];
            if(!empty($x)) {
                $product = Product::findOrFail($prod['item']['id']);
                $x = (int)$x;
                $x = $x - $prod['qty'];
                $temp = $product->size_qty;
                $temp[$prod['size_key']] = $x;
                $temp1 = implode(',', $temp);
                $product->size_qty =  $temp1;
                $product->update();               
            }
        }


        foreach($cart->items as $prod)
        {
            $x = (string)$prod['stock'];
            if($x != null) {

                $product = Product::findOrFail($prod['item']['id']);
                $product->stock =  $prod['stock'];
                $product->update();  
                if($product->stock <= 5) {
                    $notification = new Notification;
                    $notification->product_id = $product->id;
                    $notification->save();                    
                }              
            }
        }

        $notf = null;

        foreach($cart->items as $prod)
        {
            if($prod['item']['user_id'] != 0) {
                $vorder =  new VendorOrder;
                $vorder->order_id = $order->id;
                $vorder->user_id = $prod['item']['user_id'];
                $notf[] = $prod['item']['user_id'];
                $vorder->qty = $prod['qty'];
                $vorder->price = $prod['price'];
                $vorder->order_number = $order->order_number;             
                $vorder->save();
            }

        }

        if(!empty($notf)) {
            $users = array_unique($notf);
            foreach ($users as $user) {
                $notification = new UserNotification;
                $notification->user_id = $user;
                $notification->order_number = $order->order_number;
                $notification->save();    
            }
        }

        Session::put('input', $input);
        Session::put('temporder', $order);
        Session::put('tempcart', $cart);
        Session::forget('cart');
        Session::forget('already');
        Session::forget('coupon');
        Session::forget('coupon_total');
        Session::forget('coupon_total1');
        Session::forget('coupon_percentage');

        Session::save();

        //Sending Email To Buyer
        if($gs->is_smtp == 1) {
            $data = [
            'to' => $request->email,
            'type' => "new_order",
            'cname' => $request->name,
            'oamount' => "",
            'aname' => "",
            'aemail' => "",
            'wtitle' => "",
            'onumber' => $order->order_number,
            ];

            $mailer = new GeniusMailer();
            $mailer->sendAutoOrderMail($data, $order->id);            
        }
        else
        {
            $to = $request->email;
            $subject = "Your Order Placed!!";
            $msg = "Hello ".$request->name."!\nYou have placed a new order.\nYour order number is ".$order->order_number.".Please wait for your delivery. \nThank you.";
            $headers = "From: ".$gs->from_name."<".$gs->from_email.">";
            mail($to, $subject, $msg, $headers);            
        }
        //Sending Email To Admin
        if($gs->is_smtp == 1) {
            $data = [
                'to' => Pagesetting::find(1)->contact_email,
                'subject' => "New Order Recieved!!",
                'body' => "Hello Admin!<br>Your store has received a new order.<br>Order Number is ".$order->order_number.".Please login to your panel to check. <br>Thank you.",
            ];

            $mailer = new GeniusMailer();
            $mailer->sendCustomMail($data);            
        }
        else
        {
            $to = Pagesetting::find(1)->contact_email;
            $subject = "New Order Recieved!!";
            $msg = "Hello Admin!\nYour store has recieved a new order.\nOrder Number is ".$order->order_number.".Please login to your panel to check. \nThank you.";
            $headers = "From: ".$gs->from_name."<".$gs->from_email.">";
            mail($to, $subject, $msg, $headers);
        }

        $post_data = array();
        $post_data['total_amount'] = $request->total; # You cant not pay less than 10
        $post_data['currency'] = "BDT";
        $post_data['tran_id'] = "MB-".$newOrderNumber; // tran_id must be unique

        # CUSTOMER INFORMATION
        $post_data['cus_name'] = $request->name;
        $post_data['cus_email'] = $request->email;
        $post_data['cus_add1'] = $request->address;
        $post_data['cus_add2'] = "";
        $post_data['cus_city'] = $request->city;
        $post_data['cus_state'] = "";
        $post_data['cus_postcode'] = "";
        $post_data['cus_country'] = "Bangladesh";
        $post_data['cus_phone'] = $request->phone;
        $post_data['cus_fax'] = "";

        # SHIPMENT INFORMATION
        $post_data['ship_name'] = $request->shipping_name;
        $post_data['ship_add1'] = $request->shipping_address;
        $post_data['ship_add2'] = "";
        $post_data['ship_city'] = $request->shipping_city;
        $post_data['ship_state'] = "";
        $post_data['ship_postcode'] = "";
        $post_data['ship_phone'] = $request->shipping_phone;
        $post_data['ship_country'] = "Bangladesh";

        $post_data['shipping_method'] = "NO";
        $post_data['num_of_item'] = $request->totalQty;

        $post_data['product_name'] = "Marn Bazar Goods";
        $post_data['product_category'] = "Goods";
        $post_data['product_profile'] = "Goods";

        $post_data['convenience_fee'] = $request->shipping_cost; // shipping cost
        $post_data['vat'] = $request->tax; // tax

        # OPTIONAL PARAMETERS
        $post_data['value_a'] = $request->user_id; //user id
        $post_data['value_b'] = $request->method;  // payment method
        $post_data['value_c'] = $request->shipping; // shipping
        $post_data['value_d'] = $request->pickup_location; // pickup location
        $post_data['value_e'] = $request->packing_cost; // packing_cost
        $post_data['value_f'] = $request->shipping_email; // shipping email
        // dd($post_data);

        // $order['order_note'] = $request->order_notes;
        // $order['coupon_code'] = $request->coupon_code;
        // $order['coupon_discount'] = $request->coupon_discount;
        // $order['dp'] = $request->dp;
        // $order['vendor_shipping_id'] = $request->vendor_shipping_id;
        // $order['vendor_packing_id'] = $request->vendor_packing_id;

        $sslc = new SslCommerzNotification();
        # initiate(Transaction Data , false: Redirect to SSLCOMMERZ gateway/ true: Show all the Payement gateway here )
        $payment_options = $sslc->makePayment($post_data, 'hosted');

        if (!is_array($payment_options)) {
            print_r($payment_options);
            $payment_options = array();
        }

    }

    public function payViaAjax(Request $request)
    {

        # Here you have to receive all the order data to initate the payment.
        # Lets your oder trnsaction informations are saving in a table called "orders"
        # In orders table order uniq identity is "transaction_id","status" field contain status of the transaction, "amount" is the order amount to be paid and "currency" is for storing Site Currency which will be checked with paid currency.

        $post_data = array();
        $post_data['total_amount'] = '10'; # You cant not pay less than 10
        $post_data['currency'] = "BDT";
        $post_data['tran_id'] = uniqid(); // tran_id must be unique

        # CUSTOMER INFORMATION
        $post_data['cus_name'] = 'Customer Name';
        $post_data['cus_email'] = 'customer@mail.com';
        $post_data['cus_add1'] = 'Customer Address';
        $post_data['cus_add2'] = "";
        $post_data['cus_city'] = "";
        $post_data['cus_state'] = "";
        $post_data['cus_postcode'] = "";
        $post_data['cus_country'] = "Bangladesh";
        $post_data['cus_phone'] = '8801XXXXXXXXX';
        $post_data['cus_fax'] = "";

        # SHIPMENT INFORMATION
        $post_data['ship_name'] = "Store Test";
        $post_data['ship_add1'] = "Dhaka";
        $post_data['ship_add2'] = "Dhaka";
        $post_data['ship_city'] = "Dhaka";
        $post_data['ship_state'] = "Dhaka";
        $post_data['ship_postcode'] = "1000";
        $post_data['ship_phone'] = "";
        $post_data['ship_country'] = "Bangladesh";

        $post_data['shipping_method'] = "NO";
        $post_data['product_name'] = "Computer";
        $post_data['product_category'] = "Goods";
        $post_data['product_profile'] = "physical-goods";

        # OPTIONAL PARAMETERS
        $post_data['value_a'] = "ref001";
        $post_data['value_b'] = "ref002";
        $post_data['value_c'] = "ref003";
        $post_data['value_d'] = "ref004";


        #Before  going to initiate the payment order status need to update as Pending.
        // $update_product = DB::table('orders')
        //     ->where('transaction_id', $post_data['tran_id'])
        //     ->updateOrInsert([
        //         'name' => $post_data['cus_name'],
        //         'email' => $post_data['cus_email'],
        //         'phone' => $post_data['cus_phone'],
        //         'amount' => $post_data['total_amount'],
        //         'status' => 'Pending',
        //         'address' => $post_data['cus_add1'],
        //         'transaction_id' => $post_data['tran_id'],
        //         'currency' => $post_data['currency']
        //     ]);

        $sslc = new SslCommerzNotification();
        # initiate(Transaction Data , false: Redirect to SSLCOMMERZ gateway/ true: Show all the Payement gateway here )
        $payment_options = $sslc->makePayment($post_data, 'checkout', 'json');

        if (!is_array($payment_options)) {
            print_r($payment_options);
            $payment_options = array();
        }

    }

    public function success(Request $request)
    {

        $input = Session::get('input');

        echo "Transaction is Successful";


        $tran_id = $request->input('tran_id');
        $amount = $request->input('amount');
        $currency = $request->input('currency');
        $txnid = $request->input('bank_tran_id');

        $sslc = new SslCommerzNotification();

        #Check order status in order tabel against the transaction id or order id.
        $order_detials = DB::table('orders')
            ->where('order_number', $tran_id)
            ->select('order_number', 'payment_status', 'pay_amount')->first();

        if ($order_detials->payment_status == 'Pending') {
            $validation = $sslc->orderValidate($request->all(), $tran_id, $amount, $currency);

            if ($validation == TRUE) {
                /*
                That means IPN did not work or IPN URL was not set in your merchant panel. Here you need to update order status
                in order table as Processing or Complete.
                Here you can also sent sms or email for successfull transaction to customer
                */
                $update_product = DB::table('orders')
                    ->where('order_number', $tran_id)
                    ->update(['payment_status' => 'Complete', 'txnid' => $txnid]);

                return redirect()->route('payment.return')->with('success', "Transaction is successfully Completed");

                // echo "<br >Transaction is successfully Completed";
            } else {
                /*
                That means IPN did not work or IPN URL was not set in your merchant panel and Transation validation failed.
                Here you need to update order status as Failed in order table.
                */
                $update_product = DB::table('orders')
                    ->where('order_number', $tran_id)
                    ->update(['payment_status' => 'Pending', 'txnid' => $txnid]);

                return redirect()->route('payment.return')->with('unsuccess', "Transaction validation Fail");
                // echo "validation Fail";
            }
        } else if ($order_detials->payment_status == 'Processing' || $order_detials->payment_status == 'Complete') {
            /*
             That means through IPN Order status already updated. Now you can just show the customer that transaction is completed. No need to udate database.
             */
             return redirect()->route('payment.return')->with('success', "Transaction is successfully Completed");
            // echo "Transaction is successfully Completed";
        } else {
            #That means something wrong happened. You can redirect customer to your product page.
            return redirect()->route('payment.return')->with('unsuccess', "Transaction validation Fail");
            // echo "Invalid Transaction";
        }


    }

    public function fail(Request $request)
    {
        $tran_id = $request->input('tran_id');

        $order_detials = DB::table('orders')
            ->where('order_number', $tran_id)
            ->select('order_number', 'payment_status', 'pay_amount')->first();

        if ($order_detials->payment_status == 'Pending') {
            $update_product = DB::table('orders')
                ->where('order_number', $tran_id)
                ->update(['payment_status' => 'Pending']);

            return redirect()->route('payment.return')->with('unsuccess', "Transaction is Falied");
            // echo "Transaction is Falied";
        } else if ($order_detials->payment_status == 'Processing' || $order_detials->payment_status == 'Complete') {
            return redirect()->route('payment.return')->with('success', "Transaction is already Successful");
            // echo "Transaction is already Successful";
        } else {
            return redirect()->route('payment.return')->with('unsuccess', "Transaction is Invalid");
            // echo "Transaction is Invalid";
        }

    }

    public function cancel(Request $request)
    {
        $tran_id = $request->input('tran_id');

        $order_detials = DB::table('orders')
            ->where('order_number', $tran_id)
            ->select('order_number', 'payment_status', 'pay_amount')->first();

        if ($order_detials->payment_status == 'Pending') {
            $update_product = DB::table('orders')
                ->where('order_number', $tran_id)
                ->update(['payment_status' => 'Pending']);

            return redirect()->route('payment.return')->with('unsuccess', "Transaction is Cancel");
            // echo "Transaction is Cancel";
        } else if ($order_detials->payment_status == 'Processing' || $order_detials->payment_status == 'Complete') {
            return redirect()->route('payment.return')->with('success', "Transaction is already Successful");
            // echo "Transaction is already Successful";
        } else {
            return redirect()->route('payment.return')->with('unsuccess', "Transaction is Invalid");
            // echo "Transaction is Invalid";
        }

    }

    public function ipn(Request $request)
    {
        #Received all the payement information from the gateway
        if ($request->input('tran_id')) #Check transation id is posted or not.
        {

            $tran_id = $request->input('tran_id');
            $txnid = $request->input('bank_tran_id');

            #Check order status in order tabel against the transaction id or order id.
            $order_details = DB::table('orders')
                ->where('order_number', $tran_id)
                ->select('order_number', 'payment_status', 'pay_amount')->first();

            if ($order_details->payment_status == 'Pending') {
                $sslc = new SslCommerzNotification();
                $validation = $sslc->orderValidate($request->all(), $tran_id, $order_details->pay_amount, "BDT");
                if ($validation == TRUE) {
                    /*
                    That means IPN worked. Here you need to update order status
                    in order table as Processing or Complete.
                    Here you can also sent sms or email for successful transaction to customer
                    */
                    $update_product = DB::table('orders')
                        ->where('order_number', $tran_id)
                        ->update(['payment_status' => 'Complete', 'txnid' => $txnid]);

                    return redirect()->route('payment.return')->with('success', "Transaction is successfully Completed");

                    // echo "Transaction is successfully Completed";
                } else {
                    /*
                    That means IPN worked, but Transation validation failed.
                    Here you need to update order status as Failed in order table.
                    */
                    $update_product = DB::table('orders')
                        ->where('order_number', $tran_id)
                        ->update(['payment_status' => 'Pending']);

                    return redirect()->route('payment.return')->with('unsuccess', "Transaction validation Fail");
                    // echo "Transaction validation Fail";
                }

            } else if ($order_details->payment_status == 'Processing' || $order_details->payment_status == 'Complete') {

                #That means Order status already updated. No need to udate database.
                return redirect()->route('payment.return')->with('success', "Transaction is already successfully Completed");
                // echo "Transaction is already successfully Completed";
            } else {
                #That means something wrong happened. You can redirect customer to your product page.
                return redirect()->route('payment.return')->with('unsuccess', "Transaction is Invalid");
                // echo "Invalid Transaction";
            }
        } else {
            return redirect()->route('payment.return')->with('unsuccess', "Transaction is Invalid");
            // echo "Invalid Transaction Data";
        }
    }

}
