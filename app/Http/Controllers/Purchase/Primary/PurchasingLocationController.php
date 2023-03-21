<?php

namespace App\Http\Controllers\Purchase\Primary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Purchase\Primary\PurchasingLocation;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;

class PurchasingLocationController extends Controller {

    public function loadView(){

        $data['attributes'] = $this->getPurchasingLocationAttributes(NULL, NULL);

        return view('purchase.primary.purchasing_location')->with('PL', $data);
    }

    private function getPurchasingLocationAttributes($process, $request){

        $objPurchasingLocation = new PurchasingLocation();

        $attributes['purchasing_location_id'] = '#Auto#';
        $attributes['purchasing_location_name'] = '';
        $attributes['active'] = '1';

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        if( ($process['validation_result'] == TRUE) && ($process['process_status'] == TRUE)){

            $purchasing_location_table = $objPurchasingLocation->getPurchaingLocation($process['purchasing_location_id']);
            foreach ($purchasing_location_table as $row) {

                $attributes['purchasing_location_id'] = $row->purchasing_location_id;
                $attributes['purchasing_location_name'] = $row->purchasing_location_name;
                $attributes['active'] = $row->active;
            }

            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-success" role="alert"> '. $message .' </div> ';

        }else{

            $inputs = $request->input();
            if(is_null($inputs) == FALSE){

                $attributes['purchasing_location_id'] = $inputs['purchasing_location_id'];
                $attributes['purchasing_location_name'] = $inputs['purchasing_location_name'];
                $attributes['active'] = $inputs['active'];
            }

            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';
        }
      
        return $attributes;
    }

    public function purchasingLocationProcess(Request $request){

        $purchasing_location_validation_result = $this->purchasingLocationValidationProcess($request);

        if( $purchasing_location_validation_result['validation_result'] == TRUE){

            $purchasing_location_saving_process = $this->purchasingLocationSavingProcess($request);

            $purchasing_location_saving_process['validation_result'] = $purchasing_location_validation_result['validation_result'];
			$purchasing_location_saving_process['validation_messages'] = $purchasing_location_validation_result['validation_messages'];

            $data['attributes'] = $this->getPurchasingLocationAttributes($purchasing_location_saving_process, $request);

        }else{

            $purchasing_location_validation_result['purchasing_location_id'] = $request->purchasing_location_id;
			$purchasing_location_validation_result['process_status'] = FALSE;

            $data['attributes'] = $this->getPurchasingLocationAttributes($purchasing_location_validation_result, $request);
        }

        return view('purchase.primary.purchasing_location')->with('PL', $data);
    }

    private function purchasingLocationValidationProcess($request){

        try{

            $inputs['purchasing_location_id'] = $request->purchasing_location_id;
            $inputs['purchasing_location_name'] = $request->purchasing_location_name;
            
            $rules['purchasing_location_id'] = array('required');
            $rules['purchasing_location_name'] = array('required', 'max:50');

            $front_end_message = '';

            $validator = Validator::make($inputs, $rules);
            $validation_result = $validator->passes();
            if($validation_result == FALSE){

                $front_end_message = 'Please Check Your Inputs';
            }

            $process_result['validation_result'] = $validator->passes();
            $process_result['validation_messages'] =  $validator->errors();
            $process_result['front_end_message'] = $front_end_message;
            $process_result['back_end_message'] =  'Purchasing Location - Validation Process ';

            return $process_result;
            
        }catch(\Exception $e){

            $process_result['validation_result'] = FALSE;
            $process_result['validation_messages'] = new MessageBag();
            $process_result['front_end_message'] =  $e->getMessage();
            $process_result['back_end_message'] =  'Purchasing Location - Validation Function Fault';

            return $process_result;
        }
    }

    private function purchasingLocationSavingProcess($request){

        try{

            $objPurchasingLocation = new PurchasingLocation();

            $data['purchasing_location'] = $this->getPurchasingLocationTable($request);

            $saving_process_result = $objPurchasingLocation->savePurchaingLocation($data);

            return $saving_process_result;

        }catch(\Exception $e){

            $process_result['purchasing_location_id'] = $request->purchasing_location_id;
            $process_result['process_status'] = FALSE;
            $process_result['front_end_message'] = $e->getMessage();
            $process_result['back_end_message'] = 'Purchasing Location -> Purchasing Location Process <br> ' . $e->getLine();

            return $process_result;
        }
    }

    private function getPurchasingLocationTable($request){

        $purchasing_location['purchasing_location_id'] = $request->purchasing_location_id;
        $purchasing_location['purchasing_location_name'] = $request->purchasing_location_name;
        $purchasing_location['active'] = $request->active;

        $purchasing_location['updated_by'] = Auth::id();
        $purchasing_location['updated_ip'] = '-';

        return $purchasing_location;
    }
    
}
