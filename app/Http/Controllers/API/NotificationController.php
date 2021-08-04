<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\DB;
use App\Models\Faq;
use App\Models\StartTest;

class NotificationController extends BaseController
{

    public function __construct()
    {
      //  parent::__construct();
        date_default_timezone_set('Asia/Kolkata');
    }
 
    public function startTest(Request $request)
    {
        $deviceId=$request->input('deviceId');
        $location=$request->input('location');
        $testId=$request->input('testId');
        $user= request()->user();
        
        $checkuserTest=DB::table('start_test')->select('*')->where(['user_id'=>$user->id,'status'=>'true'])->get();
        
        if(count($checkuserTest)>0){
          
      
      
            $current_time=date('Y-m-d H:i:s');
            // if($current_time > $checkuserTest[0]->validity){

            //     $update_status = DB::table('start_test')
            //     ->where('user_id', $checkuserTest[0]->user_id)
            //     ->update([
            //         'status' =>'false',
            //        // 'validity' => date('Y-m-d H:i:s')
            //     ]); 
            //     $this->notifyUser($user->device_token); 
             
            // }
          
            return response()->json([
                'success'=>false,
                'code' =>201,
                'message' =>'User can start only one test at a time.',
              ]);
        }
        $test_array = [


    [
                'id' => 3,
                'date' => date('d-m-Y H:i:s'),
                'device' => [

                    "id" => 3,
                    "device_id" => "RTP6576576",
                    "device_name" => "device 3",
                    "created_at" => "27-07-2021 00:00:00",
                    "updated_at" => null


                ],
                'test_name' => 'Asthma test',
                "location" =>$location
            ],
            [
                'id' => 3,
                'date' => date('d-m-Y H:i:s'),
                'device' => [

                    "id" => 1,
                    "device_id" => "FRT654576544",
                    "device_name" => "device 2",
                    "created_at" => "24-07-2021 00:00:00",
                    "updated_at" => null


                ],
                'test_name' => 'Covid-19 test',
                "location" =>$location
            ],
            [
                'id' => 2,
                'date' =>date('d-m-Y H:i:s'),
                'device' => [

                    "id" => 1,
                    "device_id" => "ASD23242342",
                    "device_name" => "device 1",
                    "created_at" => "21-07-2021 00:00:00",
                    "updated_at" => null


                ],
                'test_name' => 'Fungus test',
                "location" =>$location

            ],


        ];
     
        if ($deviceId != '') {


            if ($deviceId == 'ASD23242342') {

                $test = $test_array[2];
              //  $test = $tests;
            } elseif ($deviceId == 'FRT654576544' ) {
                $test = $test_array[1];
              //  $test = $tests;
            } elseif ($deviceId == 'RTP6576576' ) {
                $test = $test_array[0];
              //  $test = $tests;
            } else {

                $test = [];
            }
        }  else {

            $test = [];
        }
       
    
        if (count($test) <= 0) {
           
            return response(['success' => false, 'code' => 201, 'message' => "Invalid test"]);
        } else {

          
            $testValidity=date('Y-m-d H:i:s',strtotime('+5 min')); 
         
      
            $start_test = new StartTest;
            $start_test->user_id=$user->id;
            $start_test->validity =$testValidity;
            $start_test->status ='true';
            $start_test->location =$test['location'];
            $start_test->deviceId = $test['device']['device_id'];
            $start_test->testId =$test['id'];
            $start_test->test_name =$test['test_name'];
            $start_test->save();

            $test['test_duration']=5;

           
            return apiResponse(true, 200, $test['test_name']." result will be available in next " .$test['test_duration']. " minutes.", $test);
                
              
        }

    
        
     
    }

    public function ongoingTest(Request $request)
    {
        
        $user= request()->user();
        
        $checkuserTest=DB::table('start_test')->select('*')->where(['user_id'=>$user->id,'status'=>'true'])->get();
       
        
        if(count($checkuserTest)>0){ 

            // $current_time=date('Y-m-d H:i:s');
            // if($current_time > $checkuserTest[0]->validity){

            //     $update_status = DB::table('start_test')
            //     ->where('user_id', $checkuserTest[0]->user_id)
            //     ->update([
            //         'status' =>'false',
            //        // 'validity' => date('Y-m-d H:i:s')
            //     ]);  
            //     $this->notifyUser($user->device_token);
            // }

            $test_array = [


                [
                    'id' => 3,
                    'date' => date('d-m-Y H:i:s',strtotime($checkuserTest[0]->created_at)),
                    'device' => [
    
                        "id" => 3,
                        "device_id" => "RTP6576576",
                        "device_name" => "device 3",
                        "created_at" => "27-07-2021 00:00:00",
                        "updated_at" => null
    
    
                    ],
                    'test_name' => 'Asthma test',
                    "location" => $checkuserTest[0]->location,
                    "test_duration"=>5
                ],
                [
                    'id' => 1,
                    'date' => date('d-m-Y H:i:s',strtotime($checkuserTest[0]->created_at)),
                    'device' => [
    
                        "id" => 2,
                        "device_id" => "FRT654576544",
                        "device_name" => "device 2",
                        "created_at" => "24-07-2021 00:00:00",
                        "updated_at" => null
    
    
                    ],
                    'test_name' => 'Covid-19 test',
                    "location" => $checkuserTest[0]->location,
                    "test_duration"=>5
                ],
                [
                    'id' => 2,
                    'date' => date('d-m-Y H:i:s',strtotime($checkuserTest[0]->created_at)),
                    'device' => [
    
                        "id" => 1,
                        "device_id" => "ASD23242342",
                        "device_name" => "device 1",
                        "created_at" => "21-07-2021 00:00:00",
                        "updated_at" => null
    
    
                    ],
                    'test_name' => 'Fungus test',
                    "location" => $checkuserTest[0]->location,
                    "test_duration"=>5
    
                ],
    
    
            ];
         
            if ($checkuserTest[0]->deviceId != '') {
    
    
                if ($checkuserTest[0]->deviceId == 'ASD23242342' ) {
    
                    $test = $test_array[2];
                  //  $test = $tests;
                } elseif ($checkuserTest[0]->deviceId == 'FRT654576544') {
                    $test = $test_array[1];
                  //  $test = $tests;
                } elseif ($checkuserTest[0]->deviceId == 'RTP6576576') {
                    $test = $test_array[0];
                  //  $test = $tests;
                } else {
    
                    $test = [];
                }
            }  else {
    
                $test = [];
            }
           
        
            if (count($test) <= 0) {
                
                  
            return response(['success' => false, 'code' => 201, 'message' => "Test result not found"]);
                
            } else {
               
                return apiResponse(true, 200, $test['test_name']." result will be available in next " .$test['test_duration']. " minutes.", $test);
                
              
            }
         
           
        }else{
            return response()->json([
                'success'=>false,
                'code' =>201,
                'message' =>'Test result not found.',
              ]);
        }
      

    
        
     
    }

    public function ongoingAllTest()
    {
        
        
        $checkuserTest=DB::table('start_test')->select('*')->where(['status'=>'true'])->get();
       
        
        if(count($checkuserTest)>0){ 

            // $current_time=date('Y-m-d H:i:s');
            // if($current_time > $checkuserTest[0]->validity){

            //     $update_status = DB::table('start_test')
            //     ->where('user_id', $checkuserTest[0]->user_id)
            //     ->update([
            //         'status' =>'false',
            //        // 'validity' => date('Y-m-d H:i:s')
            //     ]);  
            //     $this->notifyUser($user->device_token);
            // }

            $test_array = [


                [
                    'id' => 3,
                    'date' => date('d-m-Y H:i:s',strtotime($checkuserTest[0]->created_at)),
                    'device' => [
    
                        "id" => 3,
                        "device_id" => "RTP6576576",
                        "device_name" => "device 3",
                        "created_at" => "27-07-2021 00:00:00",
                        "updated_at" => null
    
    
                    ],
                    'test_name' => 'Asthma test',
                    "location" => $checkuserTest[0]->location,
                    "test_duration"=>5
                ],
                [
                    'id' => 1,
                    'date' => date('d-m-Y H:i:s',strtotime($checkuserTest[0]->created_at)),
                    'device' => [
    
                        "id" => 2,
                        "device_id" => "FRT654576544",
                        "device_name" => "device 2",
                        "created_at" => "24-07-2021 00:00:00",
                        "updated_at" => null
    
    
                    ],
                    'test_name' => 'Covid-19 test',
                    "location" => $checkuserTest[0]->location,
                    "test_duration"=>5
                ],
                [
                    'id' => 2,
                    'date' => date('d-m-Y H:i:s',strtotime($checkuserTest[0]->created_at)),
                    'device' => [
    
                        "id" => 1,
                        "device_id" => "ASD23242342",
                        "device_name" => "device 1",
                        "created_at" => "21-07-2021 00:00:00",
                        "updated_at" => null
    
    
                    ],
                    'test_name' => 'Fungus test',
                    "location" => $checkuserTest[0]->location,
                    "test_duration"=>5
    
                ],
    
    
            ];
         
            if ($checkuserTest[0]->deviceId != '') {
    
    
                if ($checkuserTest[0]->deviceId == 'ASD23242342' ) {
    
                    $test = $test_array[2];
                  //  $test = $tests;
                } elseif ($checkuserTest[0]->deviceId == 'FRT654576544') {
                    $test = $test_array[1];
                  //  $test = $tests;
                } elseif ($checkuserTest[0]->deviceId == 'RTP6576576') {
                    $test = $test_array[0];
                  //  $test = $tests;
                } else {
    
                    $test = [];
                }
            }  else {
    
                $test = [];
            }
           
        
            if (count($test) <= 0) {
                
                  
            return response(['success' => false, 'code' => 201, 'message' => "Test result not found"]);
                
            } else {
               
                return apiResponse(true, 200, $test['test_name']." result will be available in next " .$test['test_duration']. " minutes.", $test);
                
              
            }
         
           
        }else{
            return response()->json([
                'success'=>false,
                'code' =>201,
                'message' =>'Test result not found.',
              ]);
        }
      

    
        
     
    }

//   function send_notification_FCM($notification_id,$message) {

//     $accesstoken = 'AAAAZPcd4OM:APA91bEgLCqI30s2mWWf3a5KQDfDngexRhgLnV7DLesBGGhZOcop24btbh60a2V2_Gs7NK5Gpidz1pgNC_SJkdPO4MKz_aGHZsCY1LL5kkP8GdJDYtWGdhcqqjyvX1qTrRS2Bqn3xitw';
  

//     $URL = 'https://fcm.googleapis.com/fcm/send';


//         $post_data = '{
//             "to" : "' . $notification_id . '",
//             "data" : {
//               "body" : "",
//               "message" : "' . $message . '",
//             },
//             "notification" : {
//                  "body" : "' . $message . '",
//                  "message" : "' . $message . '",
//                 "icon" : "new",
//                 "sound" : "default"
//                 },

//           }';
//     //print_r($post_data);die;

//     $crl = curl_init();

//     $headr = array();
//     $headr[] = 'Content-type: application/json';
//     $headr[] = 'Authorization: ' . $accesstoken;
//     curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, false);

//     curl_setopt($crl, CURLOPT_URL, $URL);
//     curl_setopt($crl, CURLOPT_HTTPHEADER, $headr);

//     curl_setopt($crl, CURLOPT_POST, true);
//     curl_setopt($crl, CURLOPT_POSTFIELDS, $post_data);
//     curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);

//     $rest = curl_exec($crl);

//     if ($rest === false) {
//         // throw new Exception('Curl error: ' . curl_error($crl));
//         //print_r('Curl error: ' . curl_error($crl));
//         $result_noti = 0;
//     } else {

//         $result_noti = 1;
//     }

//     //curl_close($crl);
//     //print_r($result_noti);die;
//     return $result_noti;
// }

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
    
}
