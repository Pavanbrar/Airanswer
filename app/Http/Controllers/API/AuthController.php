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

    public function register(Request $request)
    {
        $validator =  Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|string|email|max:255',
            'company_name' => 'required|string|min:6',
            'dob' => 'required|max:20',
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
        if(!$user_detail = User::where('email',$request->input("email"))->first()){
            $user_detail = User::where('phone',$request->input("phone"))->first();
        }
        if($user_detail){
            User::where('id',$user_detail->id)->update(
                array(
                    'firstname' => $request->input("firstname"),
                    'lastname' => $request->input("lastname"),
                    'phone' => $request->input("phone"),
                    'company_name' => $request->input("company_name"),
                    'dob' => $request->input("dob"),
                    'device_type' => $request->input("device_type"),
                    'device_token' => $request->input("device_token"),
                    'gender' => $request->input("gender"),
                    'email' => $request->input("email"),
                    'password' => Hash::make($request->input("password"))
                ));
               $user->id =  $user_detail->id;
        }else{
            $user->firstname = $request->input("firstname");
            $user->lastname = $request->input("lastname");
            $user->phone = $request->input("phone");
            $user->company_name = $request->input("company_name");
            $user->dob = $request->input("dob");
            $user->device_type = $request->input("device_type");
            $user->device_token = $request->input("device_token");
            $user->gender = $request->input("gender");
            $user->email = $request->input("email");
            $user->password = Hash::make($request->input("password"));
            $user->save();
        }
            $verification_otp = rand(1000, 9999); // verification otp
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
            }else{
                DB::table('otp_verify')->insert(['user_id' => $user->id, 'user_otp' => $verification_otp, 'expire_token' => '0', 'created_at' => date('Y-m-d H:i:s')]);
            }
           
            //Sent email verfication with pin to users...
           // emailTemplete($request, $verification_otp);
            // return apiResponse('true', '200', 'Otp send to your registered email address', $user);
            $success['token'] =  $user->createToken('api_token')->plainTextToken;
            $success['user_id'] =  $user->id;
            return $this->sendResponse($success, 'OTP has been send to your email. Please Verify your account');
        
    }

    public function login(Request $request){

        $field = "";
        if (is_numeric($request->input('phone_or_email'))) {
            $field = "phone";
        } elseif (filter_var($request->input('phone_or_email'), FILTER_VALIDATE_EMAIL)) {
            $field = "email";
        }
        if (empty($field)) {
            return response()->json([
                'success' => 'false',
                'code' => '422',
                'message' => 'Please enter data/vaild data'
            ]);
        }

        $request->merge([$field => $request->input('phone_or_email')]);
        $validator = Validator::make($request->all(), [
            $field => 'required|max:60',
            'password' => 'required|max:60',
        ]);
        $fields = $request->validate([
            $field => 'required|string',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => 'false',
                'code' => '422',
                'message' => $validator->errors()
            ]);
        }
           //check email
        if($field == 'email'){
            $user = User::where('email',$fields['email'])->first();
        }else{
            $user = User::where('phone',$fields['phone'])->first();
        }
           //check password
           if(!$user || !Hash::check($fields['password'],$user->password)){
                return $this->sendError('Wrong Credientials.', ['error' => 'Unauthorised']);
           }
           //
           $token = $user->CreateToken('myapptoken')->plainTextToken;
           $response =[
               'user' => $user,
               'token' => $token
           ];
            if ($user->email_verified == '0') {
                return $this->sendError('Your account is not Register user.', []);
            }
            if($user->email_verified == '1'){
                $verification_otp = 4567;
                $users_otp = DB::table('otp_verify')->select('*')->where([['user_id', '=', $user->id]])->first();
                if (!empty($users_otp)) {
                    $update_time = DB::table('otp_verify')
                                    ->where('user_id', $users_otp->user_id)
                                    ->update([
                                        'user_otp' => $verification_otp, 
                                        'expire_token' => '0', 
                                        'created_at' => date('Y-m-d H:i:s')
                                    ]);
                }else{
                    DB::table('otp_verify')->insert(['user_id' => $user->id, 'user_otp' => $verification_otp, 'expire_token' => '0', 'created_at' => date('Y-m-d H:i:s')]);
                }
            }
            return $this->sendResponse($response, 'OTP has been send to your phone/email acccount');
        
    }

    public function verifyPhoneOtp(Request $request){
      
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
                            $response =[
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

    public function resendPin(Request $request){
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

    public function forget_password(Request $request){ 
        $email= $request->email;
        $getuser = DB::table('users')->select('*')->where([['email', '=',$email]])->first();
        if($getuser){
            $userId=$getuser->id;
            $verification_otp = rand(1000,9999);
           $update_otp = DB::table('otp_verify')
            ->where('user_id', $userId)
            ->update([
                'user_otp' => $verification_otp,
                'expire_token' => '0',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        // Email
            emailTemplete($request,$verification_otp);
          return $this->sendResponse('PIN sent to Email Address',[]);
    }else{
        return $this->sendError('User does not matched');
    }
  }

    public function resetpassword(Request $request){
        $user_otp = $request->otp;
        $new_password = $request->new_password;
        $email = $request->email;
        $checkotp = DB::table('otp_verify')->select('*')->where([['user_otp', '=',$user_otp]])->first();
        if($checkotp){
            $user_id=$checkotp->user_id;
            if($new_password!=''){
                $getemail = DB::table('users')->select('email')->where([['email', '=',$email]])->first();
                if($getemail){
                        $userpass = DB::table('users')->select('password')->where([['id', '=',$user_id]])->first();
                        if(Hash::check($new_password, $userpass->password)){
                            return $this->sendError('User new password is same as the old password. Please enter a different password');
                        }
                        $created_at=strtotime("+10 minutes",strtotime($checkotp->created_at));
                        $current_date = strtotime(date('Y-m-d H:i:s'));
                        if($created_at > $current_date){              
                            $user_email=$getemail->email;
                            $update_password = DB::table('users')
                                ->where('email', $user_email)
                                ->update([
                                    'password' => bcrypt($new_password)                        
                                ]);
                            return $this->sendResponse('Password Changed Successfully',[]);
                        }else{
                                $update_time = DB::table('otp_verify')
                                ->where('user_id', $checkotp->user_id)
                                ->update([
                                    'expire_token' =>0
                                ]);
                                return $this->sendError('PIN expired');
                        } 
                }else{
                    return $this->sendError('Email Address is not matched');
                }
            }else{
                return $this->sendError('Password is required');
                }
        }else{
            return $this->sendError('PIN not matched');
        }
    }

    public function me(Request $request){
        return $request->user();
    }

    public function logout(Request $request){
        $user = auth('api')->check(); 
         if(!$user){
            return $this->sendError('User Unauthorized',[]);
         }
          $user = auth('api')->user();
          DB::table('users')->where('id', $user->id)->update(['device_type'=>'','device_token'=>'']);
          return $this->sendResponse('Logout Successfully',[]);
    }
}
