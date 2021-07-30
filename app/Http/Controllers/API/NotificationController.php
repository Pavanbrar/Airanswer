<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\DB;
use App\Models\Faq;
use App\Models\StartTest;

class NotificationController extends BaseController
{
    public function startTest(Request $request)
    {
        $deviceId=$request->input('deviceId');
        $location=$request->input('location');
        $testId=$request->input('testId');
        $user= request()->user();
        
        $checkuserTest=DB::table('start_test')->select('*')->where(['user_id'=>$user->id,'status'=>'true'])->get();
        
        if(count($checkuserTest)>0){
         //   $this->notifyUser();
            $current_time=date('Y-m-d H:i:s');
            if($current_time > $checkuserTest[0]->validity){

                $update_status = DB::table('start_test')
                ->where('user_id', $checkuserTest[0]->user_id)
                ->update([
                    'status' =>'false',
                   // 'validity' => date('Y-m-d H:i:s')
                ]);  
             
            }
          
            return response()->json([
                'success'=>false,
                'code' =>201,
                'message' =>'User can start only one test at a time.',
              ]);
        }
        $test_array = [


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
                'test_name' => 'asthma test',
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
                'test_name' => 'Covid Test',
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
                'test_name' => 'Thyroid Test',
                "location" => "south africa"

            ],


        ];
     
        if ($deviceId != '' && $testId != '' && $location!='') {


            if ($deviceId == 'ASD23242342' && $testId == '1' && $location =='south africa') {

                $test = $test_array[2];
              //  $test = $tests;
            } elseif ($deviceId == 'FRT654576544' && $testId == '2'  && $location =='america') {
                $test = $test_array[1];
              //  $test = $tests;
            } elseif ($deviceId == 'RTP6576576' && $testId == '3'  && $location =='india') {
                $test = $test_array[0];
              //  $test = $tests;
            } else {

                $test = [];
            }
        }  else {

            $test = [];
        }
       
    
        if (count($test) <= 0) {
            return apiResponse(false, 201, "Test not found", $test);
        } else {

            //date_default_timezone_set('Asia/Kolkata');
          
            $testValidity=date('Y-m-d H:i:s',strtotime('+5 min')); 
         
      
            $start_test = new StartTest;
            $start_test->user_id=$user->id;
            $start_test->validity =$testValidity;
            $start_test->status ='true';
            $start_test->location =$test['location'];
            $start_test->deviceId = $test['device']['device_id'];
            $start_test->testId =$test['id'];
            $start_test->save();

            return apiResponse(true, 200, "Test has been saved successfully", $test);
          
        }

    
        
     
    }

    public function ongoingTest(Request $request)
    {
        
        $user= request()->user();
        
        $checkuserTest=DB::table('start_test')->select('*')->where(['user_id'=>$user->id,'status'=>'true'])->get();
       
        
        if(count($checkuserTest)>0){ 

            $current_time=date('Y-m-d H:i:s');
            if($current_time > $checkuserTest[0]->validity){

                $update_status = DB::table('start_test')
                ->where('user_id', $checkuserTest[0]->user_id)
                ->update([
                    'status' =>'false',
                   // 'validity' => date('Y-m-d H:i:s')
                ]);  
            }

            $test_array = [


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
                    'test_name' => 'asthma test',
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
                    'test_name' => 'Covid Test',
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
                    'test_name' => 'Thyroid Test',
                    "location" => "south africa"
    
                ],
    
    
            ];
         
            if ($checkuserTest[0]->deviceId != '' && $checkuserTest[0]->testId != '' && $checkuserTest[0]->location !='') {
    
    
                if ($checkuserTest[0]->deviceId == 'ASD23242342' && $checkuserTest[0]->testId == '1' &&  $checkuserTest[0]->location =='south africa') {
    
                    $test = $test_array[2];
                  //  $test = $tests;
                } elseif ($checkuserTest[0]->deviceId == 'FRT654576544' && $checkuserTest[0]->testId == '2'  && $checkuserTest[0]->location =='america') {
                    $test = $test_array[1];
                  //  $test = $tests;
                } elseif ($checkuserTest[0]->deviceId == 'RTP6576576' && $checkuserTest[0]->testId == '3'  &&  $checkuserTest[0]->location =='india') {
                    $test = $test_array[0];
                  //  $test = $tests;
                } else {
    
                    $test = [];
                }
            }  else {
    
                $test = [];
            }
           
        
            if (count($test) <= 0) {
                return apiResponse(false, 201, "Ongoing test not found", $test);
            } else {
               
                return apiResponse(true, 200, "Ongoing test has been fetched successfully", $test);
              
            }
          
          
           
        }else{
            return response()->json([
                'success'=>false,
                'code' =>201,
                'message' =>'Ongoing test not found.',
              ]);
        }
      

    
        
     
    }

   
  function send_notification_FCM($notification_id,$message) {

    $accesstoken = 'AAAAZPcd4OM:APA91bEgLCqI30s2mWWf3a5KQDfDngexRhgLnV7DLesBGGhZOcop24btbh60a2V2_Gs7NK5Gpidz1pgNC_SJkdPO4MKz_aGHZsCY1LL5kkP8GdJDYtWGdhcqqjyvX1qTrRS2Bqn3xitw';
  

    $URL = 'https://fcm.googleapis.com/fcm/send';


        $post_data = '{
            "to" : "' . $notification_id . '",
            "data" : {
              "body" : "",
              "message" : "' . $message . '",
            },
            "notification" : {
                 "body" : "' . $message . '",
                 "message" : "' . $message . '",
                "icon" : "new",
                "sound" : "default"
                },

          }';
    //print_r($post_data);die;

    $crl = curl_init();

    $headr = array();
    $headr[] = 'Content-type: application/json';
    $headr[] = 'Authorization: ' . $accesstoken;
    curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, false);

    curl_setopt($crl, CURLOPT_URL, $URL);
    curl_setopt($crl, CURLOPT_HTTPHEADER, $headr);

    curl_setopt($crl, CURLOPT_POST, true);
    curl_setopt($crl, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);

    $rest = curl_exec($crl);

    if ($rest === false) {
        // throw new Exception('Curl error: ' . curl_error($crl));
        //print_r('Curl error: ' . curl_error($crl));
        $result_noti = 0;
    } else {

        $result_noti = 1;
    }

    //curl_close($crl);
    //print_r($result_noti);die;
    return $result_noti;
}

    // public function sendNotification()
    // {
      
    //     $user= request()->user();
    //     $firebaseToken =$user->device_token; 
    //     $SERVER_API_KEY = 'AAAAZPcd4OM:APA91bEgLCqI30s2mWWf3a5KQDfDngexRhgLnV7DLesBGGhZOcop24btbh60a2V2_Gs7NK5Gpidz1pgNC_SJkdPO4MKz_aGHZsCY1LL5kkP8GdJDYtWGdhcqqjyvX1qTrRS2Bqn3xitw';
  
    //     $data = [
    //         "to" => $firebaseToken,
    //         "notification" => [
    //             "title" =>'abc',
    //             "body" =>'Your test has been completed' 
    //         ]
    //     ];
    //     $dataString = json_encode($data);
    
    //     $headers = [
    //         'Authorization: key=' . $SERVER_API_KEY,
    //         'Content-Type: application/json',
    //     ];
    
    //     $ch = curl_init();
      
    //     curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
               
    //     $response = curl_exec($ch);
  
    //     print_r($response);
    // }
    public function notifyUser(){
 
        $user= request()->user();
      
        $notification_id =$user->device_token;
        $message = "Have good day!";
    
      
        $res = $this->send_notification_FCM($notification_id,$message);
      
        if($res == 1){
      
           echo 'success code';
      
        }else{
      
          echo 'fail code';
        }
         
      
     }

}
