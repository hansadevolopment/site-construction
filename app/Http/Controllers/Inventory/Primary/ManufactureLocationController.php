<?php

namespace App\Http\Controllers\Inventory\Primary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Inventory\Primary\ManufactureLocation;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;

class ManufactureLocationController extends Controller {

    public function loadView(){

        $data['attributes'] = $this->getManufactureLocationAttributes(NULL, NULL);

        return view('inventory.primary.manufacture_location')->with('ML', $data);
    }

    private function getManufactureLocationAttributes($process, $request){

        $objManufactureLocation = new ManufactureLocation();

        $attributes['manufacture_location_id'] = '#Auto#';
        $attributes['manufacture_location_name'] = '';
        $attributes['active'] = '1';

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        if( ($process['validation_result'] == TRUE) && ($process['process_status'] == TRUE)){

            $manufacture_location_table = $objManufactureLocation->getManufactureLocation($process['manufacture_location_id']);
            foreach ($manufacture_location_table as $row) {

                $attributes['manufacture_location_id'] = $row->manufacture_location_id;
                $attributes['manufacture_location_name'] = $row->manufacture_location_name;
                $attributes['active'] = $row->active;
            }

            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-success" role="alert"> '. $message .' </div> ';

        }else{

            $inputs = $request->input();
            if(is_null($inputs) == FALSE){

                $attributes['manufacture_location_id'] = $inputs['manufacture_location_id'];
                $attributes['manufacture_location_name'] = $inputs['manufacture_location_name'];
                $attributes['active'] = $inputs['active'];
            }

            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';
        }
      
        return $attributes;
    }

    public function manufactureLocationProcess(Request $request){

        $manufacture_location_validation_result = $this->manufactureLocationValidationProcess($request);

        if( $manufacture_location_validation_result['validation_result'] == TRUE){

            $manufacture_location_saving_process = $this->manufactureLocationSavingProcess($request);

            $manufacture_location_saving_process['validation_result'] = $manufacture_location_validation_result['validation_result'];
			$manufacture_location_saving_process['validation_messages'] = $manufacture_location_validation_result['validation_messages'];

            $data['attributes'] = $this->getManufactureLocationAttributes($manufacture_location_saving_process, $request);

        }else{

            $manufacture_location_validation_result['manufacture_location_id'] = $request->manufacture_location_id;
			$manufacture_location_validation_result['process_status'] = FALSE;

            $data['attributes'] = $this->getManufactureLocationAttributes($manufacture_location_validation_result, $request);
        }

        return view('inventory.primary.manufacture_location')->with('ML', $data);
    }

    private function manufactureLocationValidationProcess($request){

        try{

            $inputs['manufacture_location_id'] = $request->manufacture_location_id;
            $inputs['manufacture_location_name'] = $request->manufacture_location_name;
            
            $rules['manufacture_location_id'] = array('required');
            $rules['manufacture_location_name'] = array('required', 'max:50');

            $front_end_message = '';

            $validator = Validator::make($inputs, $rules);
            $validation_result = $validator->passes();
            if($validation_result == FALSE){

                $front_end_message = 'Please Check Your Inputs';
            }

            $process_result['validation_result'] = $validator->passes();
            $process_result['validation_messages'] =  $validator->errors();
            $process_result['front_end_message'] = $front_end_message;
            $process_result['back_end_message'] =  'Manufacture Location - Validation Process ';

            return $process_result;
            
        }catch(\Exception $e){

            $process_result['validation_result'] = FALSE;
            $process_result['validation_messages'] = new MessageBag();
            $process_result['front_end_message'] =  $e->getMessage();
            $process_result['back_end_message'] =  'Manufacture Location - Validation Function Fault';

            return $process_result;
        }
    }

    private function manufactureLocationSavingProcess($request){

        try{

            $objManufactureLocation = new ManufactureLocation();

            $data['manufacture_location'] = $this->getManufactureLocationTable($request);

            $saving_process_result = $objManufactureLocation->saveManufactureLocation($data);

            return $saving_process_result;

        }catch(\Exception $e){

            $process_result['manufacture_location_id'] = $request->manufacture_location_id;
            $process_result['process_status'] = FALSE;
            $process_result['front_end_message'] = $e->getMessage();
            $process_result['back_end_message'] = 'Manufacture Location -> Manufacture Location Process <br> ' . $e->getLine();

            return $process_result;
        }
    }

    private function getManufactureLocationTable($request){

        $manufacture_location['manufacture_location_id'] = $request->manufacture_location_id;
        $manufacture_location['manufacture_location_name'] = $request->manufacture_location_name;
        $manufacture_location['active'] = $request->active;

        $manufacture_location['updated_by'] = Auth::id();
        $manufacture_location['updated_ip'] = '-';

        return $manufacture_location;
    }
    
    
}
