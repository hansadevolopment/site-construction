<?php

namespace App\Http\Controllers\Sales\Primary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Sales\Primary\SalesCategory;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;

class SalesCategoryController extends Controller {

    public function loadView(){

        $data['attributes'] = $this->getSalesCategoryAttributes(NULL, NULL);

        return view('sales.primary.sales_category')->with('SC', $data);
    }

    private function getSalesCategoryAttributes($process, $request){

        $objSalesCategory = new SalesCategory();

        $attributes['sales_category_id'] = '#Auto#';
        $attributes['sales_category_name'] = '';
        $attributes['active'] = '1';

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        if( ($process['validation_result'] == TRUE) && ($process['process_status'] == TRUE)){

            $sales_category_table = $objSalesCategory->getSalesCategory($process['sales_category_id']);
            foreach ($sales_category_table as $row) {

                $attributes['sales_category_id'] = $row->sales_category_id;
                $attributes['sales_category_name'] = $row->sales_category_name;
                $attributes['active'] = $row->active;
            }

            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-success" role="alert"> '. $message .' </div> ';

        }else{

            $inputs = $request->input();
            if(is_null($inputs) == FALSE){

                $attributes['sales_category_id'] = $inputs['sales_category_id'];
                $attributes['sales_category_name'] = $inputs['sales_category_name'];
                $attributes['active'] = $inputs['active'];
            }

            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';
        }
      
        return $attributes;
    }

    public function salesCategoryProcess(Request $request){

        $sales_category_validation_result = $this->salesCategoryValidationProcess($request);

        if( $sales_category_validation_result['validation_result'] == TRUE){

            $sales_category_saving_process = $this->salesCategorySavingProcess($request);

            $sales_category_saving_process['validation_result'] = $sales_category_validation_result['validation_result'];
			$sales_category_saving_process['validation_messages'] = $sales_category_validation_result['validation_messages'];

            $data['attributes'] = $this->getSalesCategoryAttributes($sales_category_saving_process, $request);

        }else{

            $sales_category_validation_result['sales_category_id'] = $request->sales_category_id;
			$sales_category_validation_result['process_status'] = FALSE;

            $data['attributes'] = $this->getSalesCategoryAttributes($sales_category_validation_result, $request);
        }

        return view('sales.primary.sales_category')->with('SC', $data);
    }

    private function salesCategoryValidationProcess($request){

        try{

            $inputs['sales_category_id'] = $request->sales_category_id;
            $inputs['sales_category_name'] = $request->sales_category_name;
            
            $rules['sales_category_id'] = array('required');
            $rules['sales_category_name'] = array('required', 'max:50');

            $front_end_message = '';

            $validator = Validator::make($inputs, $rules);
            $validation_result = $validator->passes();
            if($validation_result == FALSE){

                $front_end_message = 'Please Check Your Inputs';
            }

            $process_result['validation_result'] = $validator->passes();
            $process_result['validation_messages'] =  $validator->errors();
            $process_result['front_end_message'] = $front_end_message;
            $process_result['back_end_message'] =  'Sales Category - Validation Process ';

            return $process_result;
            
        }catch(\Exception $e){

            $process_result['validation_result'] = FALSE;
            $process_result['validation_messages'] = new MessageBag();
            $process_result['front_end_message'] =  $e->getMessage();
            $process_result['back_end_message'] =  'Sales Category - Validation Function Fault';

            return $process_result;
        }
    }

    private function salesCategorySavingProcess($request){

        try{

            $objSalesCategory = new SalesCategory();

            $data['sales_category'] = $this->getSalesCategoryTable($request);

            $saving_process_result = $objSalesCategory->saveSalesCategory($data);

            return $saving_process_result;

        }catch(\Exception $e){

            $process_result['sales_category_id'] = $request->sales_category_id;
            $process_result['process_status'] = FALSE;
            $process_result['front_end_message'] = $e->getMessage();
            $process_result['back_end_message'] = 'Sales Category -> Sales Category Process <br> ' . $e->getLine();

            return $process_result;
        }
    }

    private function getSalesCategoryTable($request){

        $sales_category['sales_category_id'] = $request->sales_category_id;
        $sales_category['sales_category_name'] = $request->sales_category_name;
        $sales_category['active'] = $request->active;

        $sales_category['updated_by'] = Auth::id();
        $sales_category['updated_ip'] = '-';

        return $sales_category;
    }
    
    
}
