<?php

namespace App\Http\Controllers\SiteMM\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SiteMM\Master\Unit;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Carbon;

class UnitController extends Controller {

    public function loadView(){

        $data['attributes'] = $this->getUnitAttributes(NULL, NULL);

        return view('SiteMM.Master.unit')->with('U', $data);
    }

    private function getUnitAttributes($process, $request){

        $attributes['unit_id'] = '#Auto#';
        $attributes['unit_name'] = '';
        $attributes['active'] = 1;

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        if( ($process['validation_result'] == TRUE) && ($process['process_status'] == TRUE)){

            $elqUnit = Unit::where('unit_id', $process['unit_id'])->first();
            if($elqUnit->count() >= 1) {

                $attributes['unit_id'] = $elqUnit->unit_id;
                $attributes['unit_name'] = $elqUnit->unit_name;
                $attributes['active'] = $elqUnit->active;
            }

            $attributes['validation_messages'] = $process['validation_messages'];

            if( $process['back_end_message'] == '' ){

                $message = '';
                $attributes['process_message'] = '';

            }else{

                $message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
                $attributes['process_message'] = '<div class="alert alert-success" role="alert"> '. $message .' </div> ';
            }

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

    public function processUnit(Request $request){

        $unit_validation_result = $this->validateUnit($request);

        if($unit_validation_result['validation_result'] == TRUE){

			$saving_process_result = $this->saveUnit($request);

			$saving_process_result['validation_result'] = $unit_validation_result['validation_result'];
			$saving_process_result['validation_messages'] = $unit_validation_result['validation_messages'];

            $data['attributes'] = $this->getUnitAttributes($saving_process_result, $request);

		}else{

			$unit_validation_result['unit_id'] = $request->unit_id;
			$unit_validation_result['process_status'] = FALSE;

            $data['attributes'] = $this->getUnitAttributes($unit_validation_result, $request);
		}

        $data['unit'] = Unit::where('active', 1)->get();

        return view('SiteMM.Master.unit')->with('U', $data);
    }

    private function validateUnit($request){

        //try{

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
            $process_result['back_end_message'] =  'Site Controller - Validation Process ';

            return $process_result;

        // }catch(\Exception $e){

        //     $process_result['validation_result'] = FALSE;
        //     $process_result['validation_messages'] = new MessageBag();
        //     $process_result['front_end_message'] =  $e->getMessage();
        //     $process_result['back_end_message'] =  'Site Controller - Validation Function Fault';

		// 	return $process_result;
        // }
    }

    private function saveUnit($request){

        //try{

            $objUnit = new Unit();

            $unit['unit'] = $this->getUnitArray($request);
            $saving_process_result = $objUnit->saveUnit($unit);

            return $saving_process_result;

        // }catch(\Exception $e){

        //     $process_result['unit_id'] = $request->unit_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'Unit Controller -> Unit Saving Process <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

    private function getUnitArray($request){

        $unit['unit_id'] = $request->unit_id;
        $unit['unit_name'] = $request->unit_name;
        $unit['active'] = $request->active;

        if( Unit::where('unit_id', $request->unit_id)->exists() ){

            $unit['updated_by'] = Auth::id();
            $unit['updated_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }else{

            $unit['saved_by'] = Auth::id();
            $unit['saved_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        return $unit;
    }

    public function openUnit(Request $request){

        $process_result['unit_id'] = $request->unit_id;
        $process_result['process_status'] = TRUE;
        $process_result['validation_result'] = TRUE;
        $process_result['validation_messages'] =  new MessageBag();
        $process_result['front_end_message'] = '';
        $process_result['back_end_message'] = '';

        $data['attributes'] = $this->getUnitAttributes($process_result, $request);
        $data['unit'] = Unit::where('active', 1)->get();

        return view('SiteMM.Master.unit')->with('U', $data);
    }


}
