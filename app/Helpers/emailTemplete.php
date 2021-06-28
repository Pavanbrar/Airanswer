
<?php

if (!function_exists('emailTemplete')) {
   
   
    function emailTemplete($request,$verification_otp)
      {
       
       Mail::send([], [], function ($message) use ($request,$verification_otp)  {
        $message->to($request->email)
          ->subject('air answsers')
         
          ->setBody('<h1>This is your otp '.$verification_otp.' </h1>', 'text/html'); // for HTML rich messages
      });
       }
}