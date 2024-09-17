<?php

namespace App\Http\Controllers\Purchasing\Primary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Purchasing\Primary\PurchasingLocation;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Carbon;

class PurchasingLocationController extends Controller {

    public function loadView(){

        $data['attributes'] = $this->getPurchasingLocationAttributes(NULL, NULL);

        return view('Purchasing.Primary.purchasing_location')->with('PC', $data);
    }

    private function getPurchasingLocationAttributes($process, $request){

        $attributes['pl_id'] = '#Auto#';
        $attributes['pl_name'] = '';
        $attributes['active'] = '1';

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        if( ($process['validation_result'] == TRUE) && ($process['process_status'] == TRUE)){

            $elqResult = PurchasingLocation::where('pl_id', $process['pl_id'])->get();
            foreach ($elqResult as $row) {

                $attributes['pl_id'] = $row->pl_id;
                $attributes['pl_name'] = $row->pl_name;
                $attributes['active'] = $row->active;
            }

            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-success" role="alert"> '. $message .' </div> ';

        }else{

            $inputs = $request->input();
            if(is_null($inputs) == FALSE){

                $attributes['pl_id'] = $inputs['pl_id'];
                $attributes['pl_name'] = $inputs['pl_name'];
                $attributes['active'] = $inputs['active'];
            }

            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';
        }

        return $attributes;
    }

    public function savePurchasingLocation(Request $request){

        $validation_result = $this->purchasingLocationValidationProcess($request);

        if( $validation_result['validation_result'] == TRUE){

            $saving_process = $this->purchasingLocationSavingProcess($request);

            $saving_process['validation_result'] = $validation_result['validation_result'];
			$saving_process['validation_messages'] = $validation_result['validation_messages'];

            $data['attributes'] = $this->getPurchasingLocationAttributes($saving_process, $request);

        }else{

            $validation_result['pl_id'] = $request->pl_id;
			$validation_result['process_status'] = FALSE;

            $data['attributes'] = $this->getPurchasingLocationAttributes($validation_result, $request);
        }

        return view('Purchasing.Primary.purchasing_location')->with('PC', $data);
    }

    private function purchasingLocationValidationProcess($request){

        try{

            $inputs['pl_id'] = $request->pl_id;
            $inputs['pl_name'] = $request->pl_name;

            $rules['pl_id'] = array('required');
            $rules['pl_name'] = array('required', 'max:50');

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

        //try{

            $objPurchasingLocation = new PurchasingLocation();

            $data['pl'] = $this->getPurchasingLocationTable($request);
            $saving_process_result = $objPurchasingLocation->savePurchaingLocation($data);

            return $saving_process_result;

        // }catch(\Exception $e){

        //     $process_result['pl_id'] = $request->pl_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'Purchasing Location -> Purchasing Location Process <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

    private function getPurchasingLocationTable($request){

        $purchasing_location['pl_id'] = $request->pl_id;
        $purchasing_location['pl_name'] = $request->pl_name;
        $purchasing_location['active'] = $request->active;

        if( PurchasingLocation::where('pl_id', $request->pl_id)->exists() ){

            $purchasing_location['updated_by'] = Auth::id();
            $purchasing_location['updated_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }else{

            $purchasing_location['saved_by'] = Auth::id();
            $purchasing_location['saved_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        return $purchasing_location;
    }

}
