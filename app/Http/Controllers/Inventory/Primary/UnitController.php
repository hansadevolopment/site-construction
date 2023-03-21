<?php

namespace App\Http\Controllers\Inventory\Primary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Inventory\Primary\Unit;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;

class UnitController extends Controller {

    public function loadView(){

        $data['attributes'] = $this->getUnitAttributes(NULL, NULL);

        return view('inventory.primary.unit')->with('U', $data);
    }

    private function getUnitAttributes($process, $request){

        $objUnit = new Unit();

        $attributes['unit_id'] = '#Auto#';
        $attributes['unit_name'] = '';
        $attributes['active'] = '1';

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        if( ($process['validation_result'] == TRUE) && ($process['process_status'] == TRUE)){

            $unit_table = $objUnit->getUnit($process['unit_id']);
            foreach ($unit_table as $row) {

                $attributes['unit_id'] = $row->unit_id;
                $attributes['unit_name'] = $row->unit_name;
                $attributes['active'] = $row->active;
            }

            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-success" role="alert"> '. $message .' </div> ';

        }else{

            $inputs = $request->input();
            if(is_null($inputs) == FALSE){

                $attributes['unit_id'] = $inputs['unit_id'];
                $attributes['unit_name'] = $inputs['unit_name'];
                $attributes['active'] = $inputs['active'];
            }

            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';
        }
      
        return $attributes;
    }

    public function unitProcess(Request $request){

        $unit_validation_result = $this->unitValidationProcess($request);

        if( $unit_validation_result['validation_result'] == TRUE){

            $unit_saving_process = $this->unitSavingProcess($request);

            $unit_saving_process['validation_result'] = $unit_validation_result['validation_result'];
			$unit_saving_process['validation_messages'] = $unit_validation_result['validation_messages'];

            $data['attributes'] = $this->getUnitAttributes($unit_saving_process, $request);

        }else{

            $unit_validation_result['unit_id'] = $request->unit_id;
			$unit_validation_result['process_status'] = FALSE;

            $data['attributes'] = $this->getUnitAttributes($unit_validation_result, $request);
        }

        return view('inventory.primary.unit')->with('U', $data);
    }

    private function unitValidationProcess($request){

        try{

            $inputs['unit_id'] = $request->unit_id;
            $inputs['unit_name'] = $request->unit_name;
            
            $rules['unit_id'] = array('required');
            $rules['unit_name'] = array('required', 'max:50');

            $front_end_message = '';

            $validator = Validator::make($inputs, $rules);
            $validation_result = $validator->passes();
            if($validation_result == FALSE){

                $front_end_message = 'Please Check Your Inputs';
            }

            $process_result['validation_result'] = $validator->passes();
            $process_result['validation_messages'] =  $validator->errors();
            $process_result['front_end_message'] = $front_end_message;
            $process_result['back_end_message'] =  'Unit - Validation Process ';

            return $process_result;
            
        }catch(\Exception $e){

            $process_result['validation_result'] = FALSE;
            $process_result['validation_messages'] = new MessageBag();
            $process_result['front_end_message'] =  $e->getMessage();
            $process_result['back_end_message'] =  'Unit - Validation Function Fault';

            return $process_result;
        }
    }

    private function unitSavingProcess($request){

        try{

            $objUnit = new Unit();

            $data['unit'] = $this->getUnitTable($request);

            $saving_process_result = $objUnit->saveUnit($data);

            return $saving_process_result;

        }catch(\Exception $e){

            $process_result['unit_id'] = $request->unit_id;
            $process_result['process_status'] = FALSE;
            $process_result['front_end_message'] = $e->getMessage();
            $process_result['back_end_message'] = 'Unit -> Unit Process <br> ' . $e->getLine();

            return $process_result;
        }
    }

    private function getUnitTable($request){

        $unit['unit_id'] = $request->unit_id;
        $unit['unit_name'] = $request->unit_name;
        $unit['active'] = $request->active;

        // $unit['updated_by'] = Auth::id();
        // $unit['updated_ip'] = '-';

        return $unit;
    }

    
}
