<?php

namespace App\Http\Controllers\Inventory\Primary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Inventory\Primary\Brand;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;

class BrandController extends Controller {

    public function loadView(){

        $data['attributes'] = $this->getBrandAttributes(NULL, NULL);

        return view('inventory.primary.brand')->with('BR', $data);
    }

    private function getBrandAttributes($process, $request){

        $objBrand = new Brand();

        $attributes['brand_id'] = '#Auto#';
        $attributes['brand_name'] = '';
        $attributes['active'] = '1';

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        if( ($process['validation_result'] == TRUE) && ($process['process_status'] == TRUE)){

            $brand_table = $objBrand->getBrand($process['brand_id']);
            foreach ($brand_table as $row) {

                $attributes['brand_id'] = $row->brand_id;
                $attributes['brand_name'] = $row->brand_name;
                $attributes['active'] = $row->active;
            }

            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-success" role="alert"> '. $message .' </div> ';

        }else{

            $inputs = $request->input();
            if(is_null($inputs) == FALSE){

                $attributes['brand_id'] = $inputs['brand_id'];
                $attributes['brand_name'] = $inputs['brand_name'];
                $attributes['active'] = $inputs['active'];
            }

            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';
        }
      
        return $attributes;
    }

    public function brandProcess(Request $request){

        $brand_validation_result = $this->brandValidationProcess($request);

        if( $brand_validation_result['validation_result'] == TRUE){

            $brand_saving_process = $this->brandSavingProcess($request);

            $brand_saving_process['validation_result'] = $brand_validation_result['validation_result'];
			$brand_saving_process['validation_messages'] = $brand_validation_result['validation_messages'];

            $data['attributes'] = $this->getBrandAttributes($brand_saving_process, $request);

        }else{

            $brand_validation_result['brand_id'] = $request->brand_id;
			$brand_validation_result['process_status'] = FALSE;

            $data['attributes'] = $this->getBrandAttributes($brand_validation_result, $request);
        }

        return view('inventory.primary.brand')->with('BR', $data);
    }

    private function brandValidationProcess($request){

        try{

            $inputs['brand_id'] = $request->brand_id;
            $inputs['brand_name'] = $request->brand_name;
            
            $rules['brand_id'] = array('required');
            $rules['brand_name'] = array('required', 'max:50');

            $front_end_message = '';

            $validator = Validator::make($inputs, $rules);
            $validation_result = $validator->passes();
            if($validation_result == FALSE){

                $front_end_message = 'Please Check Your Inputs';
            }

            $process_result['validation_result'] = $validator->passes();
            $process_result['validation_messages'] =  $validator->errors();
            $process_result['front_end_message'] = $front_end_message;
            $process_result['back_end_message'] =  'Brand - Validation Process ';

            return $process_result;
            
        }catch(\Exception $e){

            $process_result['validation_result'] = FALSE;
            $process_result['validation_messages'] = new MessageBag();
            $process_result['front_end_message'] =  $e->getMessage();
            $process_result['back_end_message'] =  'Brand - Validation Function Fault';

            return $process_result;
        }
    }

    private function brandSavingProcess($request){

        try{

            $objBrand = new Brand();

            $data['brand'] = $this->getBrandTable($request);

            $saving_process_result = $objBrand->saveManufactureLocation($data);

            return $saving_process_result;

        }catch(\Exception $e){

            $process_result['brand_id'] = $request->brand_id;
            $process_result['process_status'] = FALSE;
            $process_result['front_end_message'] = $e->getMessage();
            $process_result['back_end_message'] = 'Brand -> Brand Process <br> ' . $e->getLine();

            return $process_result;
        }
    }

    private function getBrandTable($request){

        $brand['brand_id'] = $request->brand_id;
        $brand['brand_name'] = $request->brand_name;
        $brand['active'] = $request->active;

        $brand['updated_by'] = Auth::id();
        $brand['updated_ip'] = '-';

        return $brand;
    }
    
    
}
