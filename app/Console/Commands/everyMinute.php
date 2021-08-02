<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class everyMinute extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'minute:push';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'this will be for push notification';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Kolkata');
        
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

      
        $checkuserTest=DB::table('start_test')->select('*')->where(['status'=>'true'])->get();
        
        foreach($checkuserTest as $key=>$value){
            $current_time=date('Y-m-d H:i:s');
            if($current_time > $value->validity){

                    $update_status = DB::table('start_test')
                    ->where('user_id', $value->user_id)
                    ->update([
                        'status' =>'false',
                       
                    ]);  
                $device_tokens=DB::table('users')->select('device_token')->where(['id'=>$value->user_id])->get();   
                $this->notifyUser($device_tokens[0]->device_token);
            }
        }
        
    }

    public function notifyUser($device_token){
 
        $notification_id =$device_token;
        // $notification_id ='dSufHkOsRCelFwih0vpL_V:APA91bFuTPDk5IVu6oBeaEFcpLKCIUnlLJxswfjRhdDhFe9HcSge6G_EwWm6qoQJ7pVJCbP_42wz0MnvbN9yq99BZRN7MqOY4Y-x-dJs2nHZz-EdplE73QFlDTnw04NMGnw8KajsTGk_';
        $server_key ='AAAAZPcd4OM:APA91bEgLCqI30s2mWWf3a5KQDfDngexRhgLnV7DLesBGGhZOcop24btbh60a2V2_Gs7NK5Gpidz1pgNC_SJkdPO4MKz_aGHZsCY1LL5kkP8GdJDYtWGdhcqqjyvX1qTrRS2Bqn3xitw';
  
       // $user_token=""; // Token generated from Android device after setting up firebase
        $title="Notification";
        $n_msg="Your test has been completed";
        
        $ndata = array('title'=>$title,'body'=>$n_msg);
        
        $url = 'https://fcm.googleapis.com/fcm/send';
        
        $fields = array();
        $fields['notification'] = $ndata;
        
        $fields['to'] = $notification_id;
        $headers = array(
            'Content-Type:application/json',
          'Authorization:key='.$server_key
        
        );
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
    
        if ($result === FALSE) {
            die('FCM Send Error: ' . curl_error($ch));
        }
        curl_close($ch);
      
      
     }

}
