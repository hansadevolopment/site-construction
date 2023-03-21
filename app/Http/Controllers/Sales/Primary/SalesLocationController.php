<?php

namespace App\Http\Controllers\Sales\Primary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Sales\Primary\SalesLocation;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;

class SalesLocationController extends Controller {

    public function loadView(){

        $data['attributes'] = $this->getSalesLocationAttributes(NULL, NULL);

        return view('sales.primary.sales_location')->with('SL', $data);
    }

    private function getSalesLocationAttributes($process, $request){

        $objSalesLocation = new SalesLocation();

        $attributes['sales_location_id'] = '#Auto#';
        $attributes['sales_location_name'] = '';
        $attributes['active'] = '1';

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        if( ($process['validation_result'] == TRUE) && ($process['process_status'] == TRUE)){

            $sales_location_table = $objSalesLocation->getSalesLocation($process['sales_location_id']);
            foreach ($sales_location_table as $row) {

                $attributes['sales_location_id'] = $row->sales_location_id;
                $attributes['sales_location_name'] = $row->sales_location_name;
                $attributes['active'] = $row->active;
            }

            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-success" role="alert"> '. $message .' </div> ';

        }else{

            $inputs = $request->input();
            if(is_null($inputs) == FALSE){

                $attributes['sales_location_id'] = $inputs['sales_location_id'];
                $attributes['sales_location_name'] = $inputs['sales_location_name'];
                $attributes['active'] = $inputs['active'];
            }

            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';
        }
      
        return $attributes;
    }

    public function salesLocationProcess(Request $request){

        $sales_location_validation_result = $this->salesLocationValidationProcess($request);

        if( $sales_location_validation_result['validation_result'] == TRUE){

            $sales_location_saving_process = $this->salesLocationSavingProcess($request);

            $sales_location_saving_process['validation_result'] = $sales_location_validation_result['validation_result'];
			$sales_location_saving_process['validation_messages'] = $sales_location_validation_result['validation_messages'];

            $data['attributes'] = $this->getSalesLocationAttributes($sales_location_saving_process, $request);

        }else{

            $sales_location_validation_result['sales_location_id'] = $request->sales_location_id;
			$sales_location_validation_result['process_status'] = FALSE;

            $data['attributes'] = $this->getSalesLocationAttributes($sales_location_validation_result, $request);
        }

        return view('sales.primary.sales_location')->with('SL', $data);
    }

    private function salesLocationValidationProcess($request){

        try{

            $inputs['sales_location_id'] = $request->sales_location_id;
            $inputs['sales_location_name'] = $request->sales_location_name;
            
            $rules['sales_location_id'] = array('required');
            $rules['sales_location_name'] = array('required', 'max:50');

            $front_end_message = '';

            $validator = Validator::make($inputs, $rules);
            $validation_result = $validator->passes();
            if($validation_result == FALSE){

                $front_end_message = 'Please Check Your Inputs';
            }

            $process_result['validation_result'] = $validator->passes();
            $process_result['validation_messages'] =  $validator->errors();
            $process_result['front_end_message'] = $front_end_message;
            $process_result['back_end_message'] =  'Sales Location - Validation Process ';

            return $process_result;
            
        }catch(\Exception $e){

            $process_result['validation_result'] = FALSE;
            $process_result['validation_messages'] = new MessageBag();
            $process_result['front_end_message'] =  $e->getMessage();
            $process_result['back_end_message'] =  'Sales Location - Validation Function Fault';

            return $process_result;
        }
    }

    private function salesLocationSavingProcess($request){

        try{

            $objSalesLocation = new SalesLocation();

            $data['sales_location'] = $this->getSalesLocationTable($request);

            $saving_process_result = $objSalesLocation->saveSalesLocation($data);

            return $saving_process_result;

        }catch(\Exception $e){

            $process_result['sales_location_id'] = $request->sales_location_id;
            $process_result['process_status'] = FALSE;
            $process_result['front_end_message'] = $e->getMessage();
            $process_result['back_end_message'] = 'Sales Location -> Sales Location Process <br> ' . $e->getLine();

            return $process_result;
        }
    }

    private function getSalesLocationTable($request){

        $sales_location['sales_location_id'] = $request->sales_location_id;
        $sales_location['sales_location_name'] = $request->sales_location_name;
        $sales_location['active'] = $request->active;

        $sales_location['updated_by'] = Auth::id();
        $sales_location['updated_ip'] = '-';

        return $sales_location;
    }
    
}
