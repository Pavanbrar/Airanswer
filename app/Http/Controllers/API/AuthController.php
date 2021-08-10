<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\User as UserResource;
use Mail;

class AuthController extends BaseController
{
    public function index()
    {
        $user = User::all();
        return $this->sendResponse(UserResource::collection($user), 'Posts fetched.');
    }

    public function adminLogin(Request $request)
    {
        if (empty($request->input('username'))) {
            return response()->json([
                "success" => "false",
                "code" => "422",
                "message" => 'Please enter valid data'
            ]);
        }
        if (($request->input('username') == 'admin') && ($request->input('password') == 'mind@123')) {
            return $this->sendResponse('Successfully', 'Successfully');
        } else {
            return $this->sendError('Please enter valid detail', []);
        }
    }

    public function register(Request $request)
    {
        $validator =  Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|string|email|max:255',
            'company_name' => 'required|string|min:6',
            'dob' => 'required|date|max:20',
            'password' => 'required|max:60',
            'gender' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => "false",
                "code" => "422",
                "message" => $validator->errors()
            ]);
        }


        $user = new User;
        if (!$user_detail = User::where('email', $request->input("email"))->first()) {
            $user_detail = User::where('phone', $request->input("phone"))->first();
        }
        $dob = $request->input('dob');
        $final_dob = date('Y-m-d', strtotime($dob));
        if ($user_detail) {
            User::where('id', $user_detail->id)->update(
                array(
                    'firstname' => $request->input("firstname"),
                    'lastname' => $request->input("lastname"),
                    'phone' => $request->input("phone"),
                    'company_name' => $request->input("company_name"),
                    'dob' =>$final_dob,
                    'device_type' => $request->input("device_type"),
                    'device_token' => $request->input("device_token"),
                    'gender' => $request->input("gender"),
                    'email' => $request->input("email"),
                    'password' => Hash::make($request->input("password"))
                )
            );
            $user->id =  $user_detail->id;
        } else {
            $user->firstname = $request->input("firstname");
            $user->lastname = $request->input("lastname");
            $user->phone = $request->input("phone");
            $user->company_name = $request->input("company_name");
            $user->dob = $final_dob;
            $user->device_type = $request->input("device_type");
            $user->device_token = $request->input("device_token");
            $user->gender = $request->input("gender");
            $user->email = $request->input("email");
            $user->password = Hash::make($request->input("password"));
            $user->save();
        }
        // $verification_otp = rand(1000, 9999); // verification otp
        $verification_otp = 1234; // verification otp
        $users_otp = DB::table('otp_verify')->select('*')->where([['user_id', '=', $user->id]])->first();
        if (!empty($users_otp)) {
            $update_time = DB::table('otp_verify')
                ->where('user_id', $users_otp->user_id)
                ->update([
                    'user_otp' => $verification_otp,
                    'expire_token' => '0',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
        } else {
            DB::table('otp_verify')->insert(['user_id' => $user->id, 'user_otp' => $verification_otp, 'expire_token' => '0', 'created_at' => date('Y-m-d H:i:s')]);
        }

        //Sent email verfication with pin to users...
        // emailTemplete($request, $verification_otp);
        // return apiResponse('true', '200', 'Otp send to your registered email address', $user);
        $success['token'] =  $user->createToken('api_token')->plainTextToken;
        $success['user_id'] =  $user->id;
        return $this->sendResponse($success, 'OTP has been send to your email. Please Verify your account');
    }

    // public function login(Request $request){

    //     $field = "";
    //     if (is_numeric($request->input('phone_or_email'))) {
    //         $field = "phone";
    //     } elseif (filter_var($request->input('phone_or_email'), FILTER_VALIDATE_EMAIL)) {
    //         $field = "email";
    //     }
    //     if (empty($field)) {
    //         return response()->json([
    //             'success' => 'false',
    //             'code' => '422',
    //             'message' => 'Please enter data/vaild data'
    //         ]);
    //     }

    //     $request->merge([$field => $request->input('phone_or_email')]);
    //     $validator = Validator::make($request->all(), [
    //         $field => 'required|max:60',
    //         'password' => 'required|max:60',
    //     ]);
    //     $fields = $request->validate([
    //         $field => 'required|string',
    //         'password' => 'required|string',
    //     ]);
    //     if ($validator->fails()) {
    //         return response()->json([
    //             'success' => 'false',
    //             'code' => '422',
    //             'message' => $validator->errors()
    //         ]);
    //     }
    //        //check email
    //     if($field == 'email'){
    //         $user = User::where('email',$fields['email'])->first();
    //     }else{
    //         $user = User::where('phone',$fields['phone'])->first();
    //     }
    //        //check password
    //        if(!$user || !Hash::check($fields['password'],$user->password)){
    //             return $this->sendError('Wrong Credientials.', ['error' => 'Unauthorised']);
    //        }
    //        //
    //        $token = $user->CreateToken('myapptoken')->plainTextToken;
    //        $response =[
    //            'user' => $user,
    //            'token' => $token
    //        ];
    //         if ($user->email_verified == '0') {

    //             return $this->sendError('Your account is not Register.', []);
    //         }
    //         if($user->email_verified == '1'){
    //             $verification_otp = 4567;
    //             $users_otp = DB::table('otp_verify')->select('*')->where([['user_id', '=', $user->id]])->first();
    //             if (!empty($users_otp)) {
    //                 $update_time = DB::table('otp_verify')
    //                                 ->where('user_id', $users_otp->user_id)
    //                                 ->update([
    //                                     'user_otp' => $verification_otp, 
    //                                     'expire_token' => '0', 
    //                                     'created_at' => date('Y-m-d H:i:s')
    //                                 ]);
    //             }else{
    //                 DB::table('otp_verify')->insert(['user_id' => $user->id, 'user_otp' => $verification_otp, 'expire_token' => '0', 'created_at' => date('Y-m-d H:i:s')]);
    //             }
    //         }
    //         return $this->sendResponse($response, 'OTP has been send to your phone/email acccount');

    // // }

    public function verifyPhoneOtp(Request $request)
    {

        $user_id = $request->user_id;
        $usr_otp = $request->otp;
        $users = User::select('*', 'id')->where([['id', '=', $user_id]])->first();
        if ($users) {
            $users_otp = DB::table('otp_verify')->select('*')->where([['user_id', '=', $users->id]])->first();
            if (!empty($users_otp)) {
                if ($users_otp->user_otp == $usr_otp) {
                    $created_at = strtotime("+10 minutes", strtotime($users_otp->created_at));
                    $current_date = strtotime(date('Y-m-d H:i:s'));
                    if ($created_at > $current_date) {
                        $emailAlreadyExist = DB::table('users')->select('*')->where([['email_verified', '=', '1']])->where([['id', '=', $users->id]])->first();
                        $emailAlreadyExist =  $users->email_verified;
                        if (!$emailAlreadyExist) {
                            $update_time = DB::table('otp_verify')
                                ->where('user_id', $users_otp->user_id)
                                ->update([
                                    'expire_token' => 1
                                ]);
                            $update_email = DB::table('users')
                                ->where('id', $user_id)
                                ->update([
                                    'email_verified' => '1',
                                    'status' => '1',
                                    'email_verified_at' => date('Y-m-d H:i:s'),
                                ]);
                            return $this->sendResponse('Account verified Successfully', 'Successfully');
                        } else {
                            $token = $users->CreateToken('myapptoken')->plainTextToken;
                            $response = [
                                'user' => $users,
                                'token' => $token
                            ];
                            return $this->sendResponse($response, 'Successfully Logged In.');
                        }
                    } else {
                        $update_time = DB::table('otp_verify')
                            ->where('user_id', $users_otp->user_id)
                            ->update([
                                'expire_token' => 1
                            ]);
                        return $this->sendError('PIN expired.', []);
                    }
                } else {
                    return $this->sendError('Invalid PIN.', []);
                }
            } else {
                return $this->sendError('Otp not exist.', []);
            }
        } else {
            return $this->sendError('User does not matched.', []);
        }
    }

    public function resendPin(Request $request)
    {
        $user_id = $request->user_id;
        $getuser = DB::table('users')->select('*')->where([['id', '=', $user_id]])->first();
        if ($getuser) {
            $userId = $getuser->id;
            $verification_otp = rand(1000, 9999);
            $update_otp = DB::table('otp_verify')
                ->where('user_id', $userId)
                ->update([
                    'user_otp' => $verification_otp,
                    'expire_token' => '1',
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            // mail//
            emailTemplete($request, $verification_otp);
            return $this->sendResponse('PIN resent to registered Email Address', 'Successfully');
        } else {
            return $this->sendError('User does not matched.', []);
        }
    }

    public function forget_password(Request $request)
    {
        $email = $request->email;
        $getuser = DB::table('users')->select('*')->where([['email', '=', $email]])->first();
        if ($getuser) {
            $userId = $getuser->id;
            //  $verification_otp = rand(1000,9999);
            $verification_otp = 1234;
            $update_otp = DB::table('otp_verify')
                ->where('user_id', $userId)
                ->update([
                    'user_otp' => $verification_otp,
                    'expire_token' => '0',
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            // Email

            emailTemplete($request, $verification_otp);
            return $this->sendResponse([], 'PIN sent to Email Address');
        } else {
            return $this->sendError('User does not matched');
        }
    }

    public function resetpassword(Request $request)
    {
        $user_otp = $request->otp;
        $new_password = $request->new_password;
        $email = $request->email;
        $checkotp = DB::table('otp_verify')->select('*')->where([['user_otp', '=', $user_otp]])->first();
        if ($checkotp) {
            $user_id = $checkotp->user_id;
            if ($new_password != '') {
                $getemail = DB::table('users')->select('email')->where([['email', '=', $email]])->first();
                if ($getemail) {
                    $userpass = DB::table('users')->select('password')->where([['id', '=', $user_id]])->first();
                    if (Hash::check($new_password, $userpass->password)) {
                        return $this->sendError('User new password is same as the old password. Please enter a different password');
                    }
                    $created_at = strtotime("+10 minutes", strtotime($checkotp->created_at));
                    $current_date = strtotime(date('Y-m-d H:i:s'));
                    if ($created_at > $current_date) {
                        $user_email = $getemail->email;
                        $update_password = DB::table('users')
                            ->where('email', $user_email)
                            ->update([
                                'password' => bcrypt($new_password)
                            ]);
                        return $this->sendResponse('Password Changed Successfully', []);
                    } else {
                        $update_time = DB::table('otp_verify')
                            ->where('user_id', $checkotp->user_id)
                            ->update([
                                'expire_token' => 0
                            ]);
                        return $this->sendError('PIN expired');
                    }
                } else {
                    return $this->sendError('Email Address is not matched');
                }
            } else {
                return $this->sendError('Password is required');
            }
        } else {
            return $this->sendError('PIN not matched');
        }
    }

    public function me(Request $request)
    {

        $user = $request->user();
        return apiResponse(true, 200, "User data feteched", $user);
    }


    public function get_user()
    {

        $users =  User::all();
        foreach ($users as $user) {
           $response =array( 'user_id' => $user->id,
            'username' => $user->username,
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'dob' => date("d-m-Y", strtotime($user->dob)),
            'phone' => $user->phone,
            'gender' => $user->gender,
            'email' => $user->email,
            'company_name' => $user->company_name,
            'status' => $user->status,
            'device_type' => $user->device_type,
            'device_token' => $user->device_token);
            $data [] =  $response;
        }
       
        return apiResponse(true, 200, "User data feteched", $data);
    }


    public function logout(Request $request)
    {
         $user=request()->user();
         $token = request()->user()->currentAccessToken()->token;
 
       // $remove_device_token= DB::table('users')->where('id', $user->id)->update(['device_token'=>'']);
        $remove_device_token= DB::table('device_token_table')->where(['user_id'=>$user->id,'token'=>$token])->delete();
        $remove_token= DB::table('personal_access_tokens')->where(['tokenable_id'=>$user->id,'token'=>$token])->delete();
     //   auth()->user()->tokens()->delete();
       

        return response(['success' => true, 'code' => 200, 'message' => "User successfully logout"]);
    }

    public function getUserId(Request $request,$id)
    {
    
        $user_data = DB::table('users')->select('*')->where('id', '=', $id)->get();
        if (count($user_data) > 0) {
            return apiResponse(true, 200, "User data feteched", $user_data);
        } else {
            return apiResponse(false, 201, "User data not found", $user_data);
        }
    }
    //temporiory apis//


    public function login(Request $request)
    {

        if (empty($request->input('phone_or_email_or_username'))) {
            return response()->json([
                'success' => false,
                'code' => 422,
                'message' => 'Email/phone/username is required'
            ]);
        }

        $field = "";
        if (is_numeric($request->input('phone_or_email_or_username'))) {
            $field = "phone";
        } elseif (filter_var($request->input('phone_or_email_or_username'), FILTER_VALIDATE_EMAIL)) {
            $field = "email";
        } elseif (ctype_alnum($request->input('phone_or_email_or_username'))) {
            $field = "username";
        }

        if (empty($field)) {
            return response()->json([
                'success' => false,
                'code' => 422,
                'message' => 'Invalid email/phone/username'
            ]);
        }
        if (empty($request->input('password'))) {
            return response()->json([
                'success' => false,
                'code' => 422,
                'message' => 'Password field required'
            ]);
        }
        $request->merge([$field => $request->input('phone_or_email_or_username')]);
        // $validator = Validator::make($request->all(), [
        // $field => 'required|max:60',
        // 'password' => 'required|max:60',
        // ]);
        $fields = $request->validate([
            $field => 'required|string',
            'password' => 'required|string',
        ]);
        // if ($validator->fails()) {
        // return response()->json([
        //     'success' => false,
        //     'code' => 422,
        //     'message' => $validator->errors()
        // ]);
        // }


        //check email
        if ($field == 'email') {
         

        //   User::where('email', $fields['email'])->first();
           $user =  User::where(DB::raw('BINARY `email`'),$fields['email'])->first();
           
        } elseif ($field == 'phone') {
            $user = User::where('phone', $fields['phone'])->first();
        } else {
          //  $user = User::where('username', $fields['username'])->first();
            $user =  User::where(DB::raw('BINARY `username`'),$fields['username'])->first();
        }
        //check password
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response(['success' => false, 'code' => 201, 'message' => "Wrong credentials"]);
        }
        //
        $token = $user->CreateToken('myapptoken')->plainTextToken;
        $response = [
            'user_id' => $user->id,
            'username' => $user->username,
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'dob' => date("d-m-Y", strtotime($user->dob)),
            'phone' => $user->phone,
            'gender' => $user->gender,
            'email' => $user->email,
            'company_name' => $user->company_name,
            'token' => $token
        ];

        $device_token=$request->input('device_token');
        // $update_device_token = DB::table('users')
        // ->where('id',$user->id)
        // ->update([
        //     'device_token' =>$device_token
         
        // ]); 
    
        foreach($user->tokens as $token){
            
            $token_for_personal =$token->token;
        
        }

     //insert device token and token in device_token_table//
    
        DB::table('device_token_table')->insert(['user_id'=>$user->id,'device_token'=>$device_token,'token'=>$token_for_personal]);

        return apiResponse(true, 200, "Logged in successfully", $response);
    }
    public function update(Request $request, $id)
    {
        $user = User::find($id);
      
       
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->username = $request->username;
        $user->dob = date("Y-m-d", strtotime($request->dob));
        $user->phone = $request->phone;
        $user->gender = $request->gender;
        $user->email = $request->email;
        $user->company_name = $request->company_name;
        $user->email = $request->email;
      
        $user->save();
        $response = [
            'user_id' => $user->id,
            'username' => $user->username,
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'dob' => date("d-m-Y", strtotime($user->dob)),
            'phone' => $user->phone,
            'gender' => $user->gender,
            'email' => $user->email,
            'company_name' => $user->company_name,
            'status' => $user->status,
            'device_type' => $user->device_type,
            'device_token' => $user->device_token,
        ];
        return apiResponse(true, 200, "User updated successfully", $response);
    }

    public function getUserbyId($id)
    {
        $user = User::find($id);
        $response = [
            'user_id' => $user->id,
            'username' => $user->username,
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'dob' => date("d-m-Y", strtotime($user->dob)),
            'phone' => $user->phone,
            'gender' => $user->gender,
            'email' => $user->email,
            'company_name' => $user->company_name,
            'status' => $user->status,
            'device_type' => $user->device_type,
            'device_token' => $user->device_token,
        ];
        return apiResponse(true, 200, "Get User data successfully", $response);
    }

    public function notification(Request $request)
    {


        $per_page = $request->input('per_page');
        $page_no = $request->input('page_no');
        if (isset($page_no)) {
            $pageno = $page_no;
        } else {
            $pageno = 1;
        }
        if (isset($per_page)) {
            $no_of_records_per_page = $per_page;
        } else {
            $no_of_records_per_page = 2;
        }
        $offset = ($pageno - 1) * $no_of_records_per_page;


        $notification = DB::table('notification')->select('*')->orderBy('id', 'desc')->skip($offset)->take($no_of_records_per_page)->get();
        if (count($notification) <= 0) {
            return apiResponse(false, 201, "Notifications not found", $notification);
        } else {

            $response_notification = array();
            $response_notification_array = array();

            foreach ($notification as $key => $value) {
                $response_notification['id'] = $value->id;
                $response_notification['user_id'] = $value->user_id;
                $response_notification['status']  = $value->status;
                $response_notification['msg']  = $value->msg;
                $response_notification['created_at'] = date("d-m-Y H:i:s", strtotime($value->created_at));
                $response_notification['updated_at'] = $value->updated_at;
                array_push($response_notification_array, $response_notification);
            }

            return apiResponse(true, 200, "Notifications fetched successfully", $response_notification_array);
        }
    }

    public function get_devices(Request $request)
    {

        $get_devices = DB::table('devices')->select('id', 'device_id', 'device_name', 'created_at', 'updated_at')->orderBy('id', 'desc')->get();

        if (count($get_devices) <= 0) {

            return apiResponse(false, 201, "Devices not found", $get_devices);
        } else {

            $response_devices = array();
            $response_get_devices_array = array();

            foreach ($get_devices as $key => $value) {
                $response_devices['id'] = $value->id;
                $response_devices['device_id'] = $value->device_id;
                $response_devices['device_name']  = $value->device_name;
                $response_devices['created_at'] = date("d-m-Y H:i:s", strtotime($value->created_at));
                $response_devices['updated_at'] = $value->updated_at;
                array_push($response_get_devices_array, $response_devices);
            }
            return apiResponse(true, 200, "Devices has been fetched successfully", $response_get_devices_array);
        }
    }

    public function useful_info(Request $request)
    {

        $useful_info = [


            [

                'id' => 4,
                'value' => 'covid-19',
                'url' => 'https://www.youtube.com/watch?v=i0ZabxXmH4Y',
                'video_id' => 'i0ZabxXmH4Y',
                'type' => 'video',
            ],
            [
                'id' => 3,
                'value' => 'covid 19 is airborne',
                'type' => 'text',
            ],
            [
                'id' => 2,
                'value' => 'demo video',
                'url' => 'https://youtube.com/watch?v=EngW7tLk6R8',
                'video_id' => 'EngW7tLk6R8',
                'type' => 'video',
            ],
            [
                'id' => 1,
                'value' => 'covid 19 is airborne',
                'type' => 'text',

            ],



        ];

        $per_page = $request->input('per_page');
        $page_no = $request->input('page_no');
        if (isset($page_no)) {
            $pageno = $page_no;
        } else {
            $pageno = 1;
        }
        if (isset($per_page)) {
            $no_of_records_per_page = $per_page;
        } else {
            $no_of_records_per_page = 2;
        }
        $offset = ($pageno - 1) * $no_of_records_per_page;


        $yourDataArray = array_slice($useful_info, $offset, $no_of_records_per_page);
        if (count($yourDataArray) <= 0) {
            return apiResponse(false, 201, "Useful information not found", $yourDataArray);
        } else {

            return apiResponse(true, 200, "Useful information fetched successfully", $yourDataArray);
        }
    }

    public function view_report(Request $request)
    {

        $device_id = $request->input('device_id');
        $dates = $request->input('date');
        $date = date('Y-m-d', strtotime($dates));


        $report = [


            [
                'id' => 3,
                'date' => '27-07-2021 00:00:00',
                'device' => [

                    "id" => 3,
                    "device_id" => "RTP6576576",
                    "device_name" => "device 3",
                    "created_at" => "27-07-2021 00:00:00",
                    "updated_at" => null


                ],
                'test_name' => 'Asthma test',
                "location" => "india"
            ],
            [
                'id' => 2,
                'date' => '14-07-2021 00:00:00',
                'device' => [

                    "id" => 2,
                    "device_id" => "FRT654576544",
                    "device_name" => "device 2",
                    "created_at" => "24-07-2021 00:00:00",
                    "updated_at" => null


                ],
                'test_name' => 'Covid-19 test',
                "location" => "america"
            ],
            [
                'id' => 1,
                'date' => '13-07-2021 00:00:00',
                'device' => [

                    "id" => 1,
                    "device_id" => "ASD23242342",
                    "device_name" => "device 1",
                    "created_at" => "21-07-2021 00:00:00",
                    "updated_at" => null


                ],
                'test_name' => 'Fungus test',
                "location" => "south africa"

            ],


        ];
        if ($date == '1970-01-011') {
            $dates = '';
        }
        if ($device_id != '' && $dates != '') {


            if ($device_id == 'ASD23242342' && $date == '2021-07-13') {

                $reports[] = $report[2];
                $report = $reports;
            } elseif ($device_id == 'FRT654576544' && $date == '2021-07-14') {
                $reports[] = $report[1];
                $report = $reports;
            } elseif ($device_id == 'RTP6576576' && $date == '2021-07-27') {
                $reports[] = $report[0];
                $report = $reports;
            } else {

                $report = [];
            }
        } elseif ($device_id != '' ||  $dates != '') {

            if ($device_id == 'ASD23242342' || $date == '2021-07-13') {

                $reports[] = $report[2];
                $report = $reports;
            } elseif ($device_id == 'FRT654576544' || $date == '2021-07-14') {
                $reports[] = $report[1];
                $report = $reports;
            } elseif ($device_id == 'RTP6576576' || $date == '2021-07-27') {
                $reports[] = $report[0];
                $report = $reports;
            } else {

                $report = [];
            }
        } else {

            $report = $report;
        }


        $per_page = $request->input('per_page');
        $page_no = $request->input('page_no');
        if (isset($page_no)) {
            $pageno = $page_no;
        } else {
            $pageno = 1;
        }
        if (isset($per_page)) {
            $no_of_records_per_page = $per_page;
        } else {
            $no_of_records_per_page = 2;
        }
        $offset = ($pageno - 1) * $no_of_records_per_page;
        if ($per_page == '' && $page_no == '') {
            $report_array = $report;
        } else {
            $report_array = array_slice($report, $offset, $no_of_records_per_page);
        }
        if (count($report_array) <= 0) {
            return apiResponse(false, 201, "Reports not found", $report_array);
        } else {

            return apiResponse(true, 200, "Report has been fetched successfully", $report_array);
        }
    }


    public function faq(Request $request)
    {


        $per_page = $request->input('per_page');
        $page_no = $request->input('page_no');
        if (isset($page_no)) {
            $pageno = $page_no;
        } else {
            $pageno = 1;
        }
        if (isset($per_page)) {
            $no_of_records_per_page = $per_page;
        } else {
            $no_of_records_per_page = 2;
        }
        $offset = ($pageno - 1) * $no_of_records_per_page;


        $faq = DB::table('faq')->select('*')->orderBy('id', 'desc')->skip($offset)->take($no_of_records_per_page)->get();
        if (count($faq) <= 0) {
            return apiResponse(false, 201, "Faq not found", $faq);
        } else {

            $response_faq = array();
            $response_faq_array = array();

            foreach ($faq as $key => $value) {
                $response_faq['id'] = $value->id;
                $response_faq['question'] = $value->question;
                $response_faq['answer']  = $value->answer;
                $response_faq['created_at'] = date("d-m-Y H:i:s", strtotime($value->created_at));
                $response_faq['updated_at'] = $value->updated_at;
                array_push($response_faq_array, $response_faq);
            }
            return apiResponse(true, 200, "Faq fetched successfully", $response_faq_array);
        }
    }

    public function tests(Request $request)
    {

        $tests = DB::table('tests')->select('*')->orderBy('id', 'desc')->get();
        if (count($tests) <= 0) {
            return apiResponse(false, 201, "Tests not found", $tests);
        } else {


            $response_test = array();
            $response_test_array = array();

            foreach ($tests as $key => $value) {
                $response_test['id'] = $value->id;
                $response_test['test'] = $value->test;
                $response_test['description']  = $value->description;
                $response_test['instructions']  = $value->instructions;
                $response_test['created_at'] = date("d-m-Y H:i:s", strtotime($value->created_at));
                $response_test['updated_at'] = $value->updated_at;
                array_push($response_test_array, $response_test);
            }


            return apiResponse(true, 200, "Tests fetched successfully", $response_test_array);
        }
    }
}
