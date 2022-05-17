<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Generalsetting;
use App\Models\User;
use App\Classes\GeniusMailer;
use App\Models\Notification;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Validator;

class RegisterController extends Controller
{
    public function showRegisterForm()
    {
        return view('user.registration');
    }

    // Log in after customer creation from admin panel
    public function user_login_from_admin(Request $request)
    {
        if(isset($request->buttonreg) && $request->admin_request == "true"){
            $rules = [
                'name'   => 'required|string',
                'email'   => 'required|email|unique:users',
                'phone'   => 'required|numeric|unique:users|digits:11|min:0',
                'address'   => 'required|string'
            ];
            $validator = Validator::make(Input::all(), $rules);

            if ($validator->fails()) {
                return redirect()->back() ->withInput()->withErrors($validator);
            }
            
            $user = new User;
            $input = $request->all();
            $input['password'] = bcrypt(1234);
            $token = md5(time().$request->name.$request->phone);
            $input['verification_link'] = $token;
            $input['affilate_code'] = md5($request->name.$request->phone);
            $input['email_verified'] = 'Yes';

            $user->fill($input)->save();
            if ($user) {
                if (Auth::attempt(['phone' => $request->phone, 'password' => 1234])) {
                return app('App\Http\Controllers\User\UserController')->index();
                }
            }
            else{
                return redirect()->back()->with('errors', 'Try again!');
            }
        }
    }

    public function register(Request $request)
    {

        $gs = Generalsetting::findOrFail(1);

        if($gs->is_capcha == 1) {
            $value = session('captcha_string');
            if ($request->codes != $value) {
                return response()->json(array('errors' => [ 0 => 'Please enter Correct Capcha Code.' ]));    
            }            
        }


        //--- Validation Section

        $rules = [
                'email'   => 'required|email|unique:users',
                'password' => 'required|confirmed'
                ];
        $validator = Validator::make(Input::all(), $rules);
        
        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        //--- Validation Section Ends

            $user = new User;
            $input = $request->all();        
            $input['password'] = bcrypt($request['password']);
            $token = md5(time().$request->name.$request->email);
            $input['verification_link'] = $token;
            $input['affilate_code'] = md5($request->name.$request->email);

        if(!empty($request->vendor)) {
            //--- Validation Section
            $rules = [
            'shop_name' => 'unique:users',
            'shop_number'  => 'max:10'
            ];
            $customs = [
            'shop_name.unique' => 'This Shop Name has already been taken.',
            'shop_number.max'  => 'Shop Number Must Be Less Then 10 Digit.'
            ];

            $validator = Validator::make(Input::all(), $rules, $customs);
            if ($validator->fails()) {
                return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
            }
            $input['is_vendor'] = 1;

        }
              
        $user->fill($input)->save();

        if($gs->is_verification_email == 1) {
            $to = $request->email;
            $subject = 'Verify your email address.';
            $msg = "Dear Customer,<br> We noticed that you need to verify your email address. <a href=".url('user/register/verify/'.$token).">Simply click here to verify. </a>";
            //Sending Email To Customer
            if($gs->is_smtp == 1) {
                $data = [
                'to' => $to,
                'subject' => $subject,
                'body' => $msg,
                ];

                $mailer = new GeniusMailer();
                $mailer->sendCustomMail($data);
            }
            else
            {
                $headers = "From: ".$gs->from_name."<".$gs->from_email.">";
                mail($to, $subject, $msg, $headers);
            }
            return response()->json('We need to verify your email address. We have sent an email to '.$to.' to verify your email address. Please click link in that email to continue.');
        }
        else {

            $user->email_verified = 'Yes';
            $user->update();
            $notification = new Notification;
            $notification->user_id = $user->id;
            $notification->save();
            Auth::guard('web')->login($user); 
            return response()->json(1);
        }

    }

    //Send otp for registration
    public function sendOtp(Request $request)
    {
        //--- Validation Section
        $rules = [
            'number'   => 'required|numeric|digits:11',
        ];
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return "failed";
        }
        //--- Validation Section Ends

        //---Check number existence
        $user = DB::table('users')->where('phone', $request->number)->first();
        if(!empty($user)) {
            return "registered";
        }

        $to = $request->number;
        $fourRandomDigit = rand(1000, 9999);
        $request->Session()->put('fourRandomDigit', $fourRandomDigit);
        $request->Session()->put('user_number', $to);

        //Prepare data for sms
        $token = "ddf27746968425c62d0e9e5713ed1169";
        $message = "Your confirmation code for marnbazar.com registration is ". $fourRandomDigit;
        $url = "http://api.greenweb.com.bd/api.php";

        $data= array(
        'to'=>"$to",
        'message'=>"$message",
        'token'=>"$token"
        ); // Add parameters in key value
        $ch = curl_init(); // Initialize cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $smsresult = curl_exec($ch);
        curl_close($ch);

        return "success";
    }

    //User registration only
    public function userRegister(Request $request)
    {
        //--- Validation Section
        $rules = [
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users',
            'phone'   => 'required|numeric|digits:11',
            'otp'     => 'required|numeric|digits:4',
            'address' => 'required|string|max:255'
        ];
        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->getMessageBag());
        }
        //--- Validation Section Ends
        $user_otp = $request->otp;
        $user_number = $request->phone;
        $session_otp = $request->Session()->get('fourRandomDigit');
        $session_number = $request->Session()->get('user_number');

        if($user_otp == $session_otp && $user_number == $session_number) {
            $user = new User;
            $input = $request->all();
            $input['password'] = bcrypt(1234);
            $token = md5(time().$request->name.$request->phone);
            $input['verification_link'] = $token;
            $input['affilate_code'] = md5($request->name.$request->phone);
            $input['email_verified'] = 'Yes';

            $user->fill($input)->save();

            $notification = new Notification;
            $notification->user_id = $user->id;
            $notification->save();
            Auth::guard('web')->login($user);
            if ($request->checkout == 1) {
                return app('App\Http\Controllers\Front\CartController')->cart();
            }
            return app('App\Http\Controllers\User\UserController')->index();

        }else{
            return redirect()->back()->with('errors', 'Credentials doesn\'t match. Try again!');
        }
    }

    public function token($token)
    {
        $gs = Generalsetting::findOrFail(1);

        if($gs->is_verification_email == 1) {        
            $user = User::where('verification_link', '=', $token)->first();
            if(isset($user)) {
                $user->email_verified = 'Yes';
                $user->update();
                $notification = new Notification;
                $notification->user_id = $user->id;
                $notification->save();
                Auth::guard('web')->login($user); 
                return redirect()->route('user-dashboard')->with('success', 'Email Verified Successfully');
            }
        }
        else {
            return redirect()->back();    
        }
    }
}
