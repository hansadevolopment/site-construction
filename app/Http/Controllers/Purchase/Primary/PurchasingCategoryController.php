<?php

namespace App\Http\Controllers\Purchase\Primary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Purchase\Primary\PurchasingCategory;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;

class PurchasingCategoryController extends Controller {

    public function loadView(){

        $data['attributes'] = $this->getPurchasingCategoryAttributes(NULL, NULL);

        return view('purchase.primary.purchasing_category')->with('PC', $data);
    }

    private function getPurchasingCategoryAttributes($process, $request){

        $objPurchasingCategory = new PurchasingCategory();

        $attributes['purchasing_category_id'] = '#Auto#';
        $attributes['purchasing_category_name'] = '';
        $attributes['active'] = '1';

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        if( ($process['validation_result'] == TRUE) && ($process['process_status'] == TRUE)){

            $purchasing_category_table = $objPurchasingCategory->getPurchaingCategory($process['purchasing_category_id']);
            foreach ($purchasing_category_table as $row) {

                $attributes['purchasing_category_id'] = $row->purchasing_category_id;
                $attributes['purchasing_category_name'] = $row->purchasing_category_name;
                $attributes['active'] = $row->active;
            }

            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-success" role="alert"> '. $message .' </div> ';

        }else{

            $inputs = $request->input();
            if(is_null($inputs) == FALSE){

                $attributes['purchasing_category_id'] = $inputs['purchasing_category_id'];
                $attributes['purchasing_category_name'] = $inputs['purchasing_category_name'];
                $attributes['active'] = $inputs['active'];
            }

            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';
        }
      
        return $attributes;
    }

    public function purchasingCategoryProcess(Request $request){

        $purchasing_category_validation_result = $this->purchasingCategoryValidationProcess($request);

        if( $purchasing_category_validation_result['validation_result'] == TRUE){

            $purchasing_category_saving_process = $this->purchasingCategorySavingProcess($request);

            $purchasing_category_saving_process['validation_result'] = $purchasing_category_validation_result['validation_result'];
			$purchasing_category_saving_process['validation_messages'] = $purchasing_category_validation_result['validation_messages'];

            $data['attributes'] = $this->getPurchasingCategoryAttributes($purchasing_category_saving_process, $request);

        }else{

            $purchasing_category_validation_result['purchasing_category_id'] = $request->purchasing_category_id;
			$purchasing_category_validation_result['process_status'] = FALSE;

            $data['attributes'] = $this->getPurchasingCategoryAttributes($purchasing_category_validation_result, $request);
        }

        return view('purchase.primary.purchasing_category')->with('PC', $data);
    }

    private function purchasingCategoryValidationProcess($request){

        try{

            $inputs['purchasing_category_id'] = $request->purchasing_category_id;
            $inputs['purchasing_category_name'] = $request->purchasing_category_name;
            
            $rules['purchasing_category_id'] = array('required');
            $rules['purchasing_category_name'] = array('required', 'max:50');

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

        try{

            $objPurchasingCategory = new PurchasingCategory();

            $data['purchasing_category'] = $this->getPurchasingCategoryTable($request);

            $saving_process_result = $objPurchasingCategory->savePurchaingCategory($data);

            return $saving_process_result;

        }catch(\Exception $e){

            $process_result['purchasing_category_id'] = $request->purchasing_category_id;
            $process_result['process_status'] = FALSE;
            $process_result['front_end_message'] = $e->getMessage();
            $process_result['back_end_message'] = 'Purchasing Category -> Purchasing Category Process <br> ' . $e->getLine();

            return $process_result;
        }
    }

    private function getPurchasingCategoryTable($request){

        $purchasing_category['purchasing_category_id'] = $request->purchasing_category_id;
        $purchasing_category['purchasing_category_name'] = $request->purchasing_category_name;
        $purchasing_category['active'] = $request->active;

        $purchasing_category['updated_by'] = Auth::id();
        $purchasing_category['updated_ip'] = '-';

        return $purchasing_category;
    }
    
    
}
