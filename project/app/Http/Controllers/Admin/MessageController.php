<?php

namespace App\Http\Controllers\Admin;

use App\Classes\GeniusMailer;
use Datatables;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Validator;
use App\Models\AdminUserConversation;
use App\Models\AdminUserMessage;
use App\Models\Message;
use App\Models\User;
use App\Models\UserNotification;
use App\Models\Generalsetting;
use Auth;


class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    //*** JSON Request
    public function datatables($type)
    {
        $datas = AdminUserConversation::where('type', '=', $type)->get();
        //--- Integrating This Collection Into Datatables
        return Datatables::of($datas)
            ->editColumn('created_at', function (AdminUserConversation $data) {
                $date = $data->created_at->diffForHumans();
                return $date;
            })
            ->addColumn('name', function (AdminUserConversation $data) {
                $name = $data->user->name;
                return $name;
            })
            ->addColumn('action', function (AdminUserConversation $data) {
                return '<div class="action-list"><a href="' . route('admin-message-show', $data->id) . '"> <i class="fas fa-eye"></i> Details</a><a href="javascript:;" data-href="' . route('admin-message-delete', $data->id) . '" data-toggle="modal" data-target="#confirm-delete" class="delete"><i class="fas fa-trash-alt"></i></a></div>';
            })
            ->rawColumns(['action'])
            ->toJson(); //--- Returning Json Data To Client Side
    }

    //*** GET Request
    public function index()
    {
        return view('admin.message.index');
    }

    //*** GET Request
    public function disputes()
    {
        return view('admin.message.dispute');
    }

    //*** GET Request
    public function message($id)
    {
        if (!AdminUserConversation::where('id', $id)->exists()) {
            return redirect()->route('admin.dashboard')->with('unsuccess', __('Sorry the page does not exist.'));
        }
        $conv = AdminUserConversation::findOrfail($id);
        return view('admin.message.create', compact('conv'));
    }

    //*** GET Request
    public function messageshow($id)
    {
        $conv = AdminUserConversation::findOrfail($id);
        return view('load.message', compact('conv'));
    }

    //*** GET Request
    public function messagedelete($id)
    {
        $conv = AdminUserConversation::findOrfail($id);
        if ($conv->messages->count() > 0) {
            foreach ($conv->messages as $key) {
                $key->delete();
            }
        }
        $conv->delete();
        //--- Redirect Section     
        $msg = 'Data Deleted Successfully.';
        return response()->json($msg);
        //--- Redirect Section Ends               
    }

    //*** POST Request
    public function postmessage(Request $request)
    {
        $msg = new AdminUserMessage();
        $input = $request->all();
        $msg->fill($input)->save();
        //--- Redirect Section     
        $msg = 'Message Sent!';
        return response()->json($msg);
        //--- Redirect Section Ends    
    }

    //*** POST Request
    public function usercontact(Request $request)
    {
        $data = 1;
        $admin = Auth::guard('admin')->user();
        $user = User::where('email', '=', $request->to)->first();
        if (empty($user)) {
            $data = 0;
            return response()->json($data);
        }
        $to = $request->to;
        $subject = $request->subject;
        $from = $admin->email;
        $msg = "Email: " . $from . "<br>Message: " . $request->message;
        $gs = Generalsetting::findOrFail(1);
        if ($gs->is_smtp == 1) {

            $datas = [
                'to' => $to,
                'subject' => $subject,
                'body' => $msg,
            ];
            $mailer = new GeniusMailer();
            $mailer->sendCustomMail($datas);
        } else {
            $headers = "From: " . $gs->from_name . "<" . $gs->from_email . ">";
            mail($to, $subject, $msg, $headers);
        }

        if ($request->type == 'Ticket') {
            $conv = AdminUserConversation::where('type', '=', 'Ticket')->where('user_id', '=', $user->id)->where('subject', '=', $subject)->first();
        } else {
            $conv = AdminUserConversation::where('type', '=', 'Dispute')->where('user_id', '=', $user->id)->where('subject', '=', $subject)->first();
        }
        if (isset($conv)) {
            $msg = new AdminUserMessage();
            $msg->conversation_id = $conv->id;
            $msg->message = $request->message;
            $msg->save();
            return response()->json($data);
        } else {
            $message = new AdminUserConversation();
            $message->subject = $subject;
            $message->user_id = $user->id;
            $message->message = $request->message;
            $message->order_number = $request->order;
            $message->type = $request->type;
            $message->save();
            $msg = new AdminUserMessage();
            $msg->conversation_id = $message->id;
            $msg->message = $request->message;
            $msg->save();
            return response()->json($data);
        }
    }

    public function showSingleSmsForm()
    {

        $users = User::select('id', 'name', 'phone')->get();

        return view('admin.phone-sms.single', compact('users'));
    }



    public function insertShowSingleSmsForm(Request $request)
    {
        
        // $requestData = $request->all();

        $requestData['conversation_id'] = 1;
        $requestData['message'] = $request->message;
        $requestData['array_user_id'] = implode(',', $request->user_id);


        Message::create($requestData);


        $response_return = $this->sms_multiple($request->user_id, $request->message);
        //TODO Please show success message when it is sent.
        return redirect()->back()->with('success','Message sent successfully!');

    }

    public function sms_multiple($user_id, $message_body)
    {



        $users = DB::table('users')
            ->select('id', 'name', 'phone')
            ->where('phone', '!=', null)
            ->where('phone', '!=', '')
            ->whereIn('id', $user_id)
            ->get();

        $json_smsdata = [];
        foreach ($users as $row) {


            $name = $row->name;
            $number = $row->phone;
            $message = rawurlencode("Hi $name,$message_body");

            $json_smsdata[]= ['to'=>$number,'message'=>$message];
        }
            

        $smsdata = json_encode($json_smsdata);


        $token = "ddf27746968425c62d0e9e5713ed1169";
        $smsdata = $smsdata;

        $url = "http://api.greenweb.com.bd/api2.php";


        $data = array(
            'smsdata' => "$smsdata",
            'token' => "$token"
        ); // Add parameters in key value
        $ch = curl_init(); // Initialize cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $smsresult = curl_exec($ch);

//Result

        ///echo $smsresult;

//Error Display
        echo curl_error($ch);

        return true;
    }
  

    public function insertShowSingleSmsForm1(Request $request)
    {


        //Prepare data for sms
        $token = "ddf27746968425c62d0e9e5713ed1169";
        $message = "Your confirmation code for marnbazar.com registration is ";
        $url = "http://api.greenweb.com.bd/api.php";

        $to = '01913705269';
        $data = array(
            'to' => "$to",
            'message' => "$message",
            'token' => "$token"
        ); // Add parameters in key value
        $ch = curl_init(); // Initialize cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $smsresult = curl_exec($ch);
        curl_close($ch);
        echo "success";
        exit();


    }


    public function insertShowSingleSmsForm2()
    {

        /* $users = DB::table('users')->select('id','name','phone')->limit(1)->get();

         echo '<pre>';
         print_r($users);
         exit();*/
        // প্রথমে আপনার সুবিধে মত করে loop এ ডেটাবেজ হতে ডেটা নিন অথবা অ্যাপ্লিকেশনের ইনপুট থেকে ডেটা নিন । ডেটাটি এরপর loop ব্যবহার করে ফরম্যাট করুন: এখানে while loop দেখানো হয়েছে আপনারা foreach loop ব্যবহার করতে পারেন ।

        $dblink = mysqli_connect("localhost", "root", "", "marnbazar_mve");
        /* If connection fails throw an error */
        if (mysqli_connect_errno()) {
            echo "Could  not connect to database: Error: " . mysqli_connect_error();
            exit();
        }


//চাইলে LIMIT দিয়ে  ৫০০ এসএমএস একসাথে ডেটাবেজ থেকে যাবে এমন লিমিট করে নিতে পারেন ।
        $sqlquery = "select `id`, `name`, `phone` from `users` limit 1";


        if ($result = mysqli_query($dblink, $sqlquery)) {
            /* fetch associative array */
            while ($row = mysqli_fetch_assoc($result)) {
                $name = $row["name"];
                $number = $row["phone"];

// আমরা উপরে loop করে ডেটাবেজ থেকে name এবং number কলামের ডেটা নিলাম, এখন dynamic ম্যাসেজ লিখুন rawurlencode() funtion must use করতে হবে
                $message = rawurlencode("Hi $name,
your message 
Regards
bdsms.net
");


                $genjsons = '{"to":"' . $number . '","message":"' . $message . '"}';

                $jsonsmsdata = "$genjsons";
                $jsonsmsdata = rtrim($jsonsmsdata, ',');
            }

        }

        $smsdata = '[' . $jsonsmsdata . ']';


// $smsdata হলো আমাদের কাংখিত format করা ডেটা যা এখন সেন্ড করা হবে ।


        //এবার এসএমএস প্রেরন করুন নিচে শুধু টোকেন বদল করবেন
        $token = "ddf27746968425c62d0e9e5713ed1169";
        $smsdata = $smsdata;

        $url = "http://api.greenweb.com.bd/api2.php";


        $data = array(
            'smsdata' => "$smsdata",
            'token' => "$token"
        ); // Add parameters in key value
        $ch = curl_init(); // Initialize cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $smsresult = curl_exec($ch);

//Result
        echo $smsresult;

//Error Display
        echo curl_error($ch);
    }
}