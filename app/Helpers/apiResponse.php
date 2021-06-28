<?php

if (!function_exists('apiResponse')) {
   
   
    function apiResponse($success,$code,$message,$data='')
      {
        return response()->json([
          'success' => $success,
          'code' => $code,
          'message' => $message,
          'data'=>$data,
        ]);
       }
}