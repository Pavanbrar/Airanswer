<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\DB;
use App\Models\Faq;
use Illuminate\Support\Facades\Validator;


class FaqController extends BaseController
{
    public function add(Request $request)
    {
        $validator =  Validator::make($request->all(), [
          
            'question' => 'required|string|max:255',
            'answer' => 'required|string|max:255',
          
            
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => "false",
                "code" => "422",
                "message" => $validator->errors()
            ]);
        }

        $faq = new Faq;
        $faq->question = $request->input('question');
        $faq->answer = $request->input('answer');
        $faq->save();
        return apiResponse(true, 200, "data added successfully");
    }

    public function update(Request $request, $id)
    {
        $faq_update = Faq::find($id);
        if ($faq_update) {

            $faq_update->question = $request->input('question');
            $faq_update->answer = $request->input('answer');
            $faq_update->save();
            return apiResponse(true, 200, "data updated successfully");
        } else {
            return apiResponse(false, 201, "id  not found");
        }
    }

    public function getFaqId($id)
    {
      
        $faq_data = DB::table('faq')->select('*')->where('id', '=', $id)->get();
        if (count($faq_data) > 0) {
            return apiResponse(true, 200, "Faq data feteched", $faq_data);
        } else {
            return apiResponse(false, 201, "Faq data not found", $faq_data);
        }
    }

    public function faqAll()
    {
        $faq = Faq::all();
        return apiResponse(true, 200, "data fetch successfully",$faq);
    }

    public function delete($id)
    {

        $faqDelete = Faq::find($id);

        if ($faqDelete) {
            $faqDelete->delete();
            return apiResponse(true, 200, "data deleted successfully");
        } else {
            return apiResponse(false, 201, "id  not found");
        }
    }
}