<?php

namespace App\Http\Controllers\Purchasing\Primary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Purchasing\Primary\PurchasingCategory;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Carbon;

class PurchasingCategoryController extends Controller {

    public function loadView(){

        $data['attributes'] = $this->getPurchasingCategoryAttributes(NULL, NULL);

        return view('Purchasing.Primary.purchasing_category')->with('PC', $data);
    }

    private function getPurchasingCategoryAttributes($process, $request){

        $attributes['pc_id'] = '#Auto#';
        $attributes['pc_name'] = '';
        $attributes['active'] = '1';

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        if( ($process['validation_result'] == TRUE) && ($process['process_status'] == TRUE)){

            $purchasing_category_table = PurchasingCategory::where('pc_id', $process['pc_id'])->get();
            foreach ($purchasing_category_table as $row) {

                $attributes['pc_id'] = $row->pc_id;
                $attributes['pc_name'] = $row->pc_name;
                $attributes['active'] = $row->active;
            }

            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-success" role="alert"> '. $message .' </div> ';

        }else{

            $inputs = $request->input();
            if(is_null($inputs) == FALSE){

                $attributes['pc_id'] = $inputs['pc_id'];
                $attributes['pc_name'] = $inputs['pc_name'];
                $attributes['active'] = $inputs['active'];
            }

            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';
        }

        return $attributes;
    }

    public function savePurchaisngCategory(Request $request){

        $validation_result = $this->purchasingCategoryValidationProcess($request);

        if( $validation_result['validation_result'] == TRUE){

            $saving_process = $this->purchasingCategorySavingProcess($request);

            $saving_process['validation_result'] = $validation_result['validation_result'];
			$saving_process['validation_messages'] = $validation_result['validation_messages'];

            $data['attributes'] = $this->getPurchasingCategoryAttributes($saving_process, $request);

        }else{

            $validation_result['pc_id'] = $request->pc_id;
			$validation_result['process_status'] = FALSE;

            $data['attributes'] = $this->getPurchasingCategoryAttributes($validation_result, $request);
        }

        return view('Purchasing.Primary.purchasing_category')->with('PC', $data);
    }

    private function purchasingCategoryValidationProcess($request){

        try{

            $inputs['pc_id'] = $request->pc_id;
            $inputs['pc_name'] = $request->pc_name;

            $rules['pc_id'] = array('required');
            $rules['pc_name'] = array('required', 'max:50');

            $front_end_message = '';

            $validator = Validator::make($inputs, $rules);
            $validation_result = $validator->passes();
            if($validation_result == FALSE){

                $front_end_message = 'Please Check Your Inputs';
            }

            $process_result['validation_result'] = $validator->passes();
            $process_result['validation_messages'] =  $validator->errors();
            $process_result['front_end_message'] = $front_end_message;
            $process_result['back_end_message'] =  'Purchasing Category - Validation Process ';

            return $process_result;

        }catch(\Exception $e){

            $process_result['validation_result'] = FALSE;
            $process_result['validation_messages'] = new MessageBag();
            $process_result['front_end_message'] =  $e->getMessage();
            $process_result['back_end_message'] =  'Purchasing Category - Validation Function Fault';

            return $process_result;
        }
    }

    private function purchasingCategorySavingProcess($request){

        //try{

            $objPurchasingCategory = new PurchasingCategory();

            $data['pc'] = $this->getPurchasingCategoryTable($request);
            $saving_process_result = $objPurchasingCategory->savePurchaingCategory($data);

            return $saving_process_result;

        // }catch(\Exception $e){

        //     $process_result['pc_id'] = $request->pc_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'Purchasing Category -> Purchasing Category Process <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

    private function getPurchasingCategoryTable($request){

        $purchasing_category['pc_id'] = $request->pc_id;
        $purchasing_category['pc_name'] = $request->pc_name;
        $purchasing_category['active'] = $request->active;

        if( PurchasingCategory::where('pc_id', $request->pc_id)->exists() ){

            $purchasing_category['updated_by'] = Auth::id();
            $purchasing_category['updated_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }else{

            $purchasing_category['saved_by'] = Auth::id();
            $purchasing_category['saved_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        return $purchasing_category;
    }

}
