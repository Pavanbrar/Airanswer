<?php

namespace App\Http\Controllers\API;

use App\Models\Users;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\User as UserResource;

class AuthController extends BaseController
{

    public function index()
    {
        $user = Users::all();
        return $this->sendResponse(UserResource::collection($user), 'Posts fetched.');
    }

    public function register(Request $request)
    {
        $validator =  Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
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


        $user = new Users;
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
        $verification_otp = rand(1000, 9999); // verification otp
        DB::table('otp_verify')->insert(['user_id' => $user->id, 'user_otp' => $verification_otp, 'expire_token' => '0', 'created_at' => date('Y-m-d H:i:s')]);
        //Sent email verfication with pin to users...
       // emailTemplete($request, $verification_otp);
       // return apiResponse('true', '200', 'Otp send to your registered email address', $user);
        $success['token'] =  $user->createToken('MyAuthApp')->plainTextToken;
        $success['name'] =  $user->name;
        return $this->sendResponse($success, 'User created successfully.');
    }

    public function login(Request $request)
    {
        $validator =  Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|max:60',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => "false",
                "code" => "422",
                "message" => $validator->errors()
            ]);
        }
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::users(); 
            $success['token'] =  $user->createToken('MyAuthApp')->plainTextToken; 
            $success['name'] =  $user->name;
   
            return $this->sendResponse($success, 'User signed in');
        } 
        else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
      
    }

    public function logout()
    {
        //auth()->user()->tokens()->delete();

        return [
            'message' => 'Tokens Revoked'
        ];
    }
}