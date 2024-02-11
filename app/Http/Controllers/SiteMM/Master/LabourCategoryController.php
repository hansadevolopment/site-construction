<?php

namespace App\Http\Controllers\SiteMM\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SiteMM\Master\LabourCategory;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Carbon;

class LabourCategoryController extends Controller {

    public function loadView(){

        $data['attributes'] = $this->getLabourCategoryAttributes(NULL, NULL);

        return view('SiteMM.Master.labour_category')->with('LC', $data);
    }

    private function getLabourCategoryAttributes($process, $request){

        $attributes['lc_id'] = '#Auto#';
        $attributes['lc_name'] = '';
        $attributes['price'] = 0;
        $attributes['active'] = 1;

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        if( ($process['validation_result'] == TRUE) && ($process['process_status'] == TRUE)){

            $elqLabourCategory = LabourCategory::where('lc_id', $process['lc_id'])->first();
            if($elqLabourCategory->count() >= 1) {

                $attributes['lc_id'] = $elqLabourCategory->lc_id;
                $attributes['lc_name'] = $elqLabourCategory->lc_name;
                $attributes['price'] = $elqLabourCategory->price;
                $attributes['active'] = $elqLabourCategory->active;
                $attributes['remark'] = $elqLabourCategory->remark;
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

                $attributes['lc_id'] = $inputs['lc_id'];
                $attributes['lc_name'] = $inputs['lc_name'];
                $attributes['price'] = $inputs['price'];
                $attributes['active'] = $inputs['active'];
            }

            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';
        }

        return $attributes;
    }

    public function procesLlabourCategory(Request $request){

        $item_validation_result = $this->validateLabourCategory($request);

        if($item_validation_result['validation_result'] == TRUE){

			$saving_process_result = $this->saveItem($request);

			$saving_process_result['validation_result'] = $item_validation_result['validation_result'];
			$saving_process_result['validation_messages'] = $item_validation_result['validation_messages'];

            $data['attributes'] = $this->getLabourCategoryAttributes($saving_process_result, $request);

		}else{

			$item_validation_result['lc_id'] = $request->lc_id;
			$item_validation_result['process_status'] = FALSE;

            $data['attributes'] = $this->getLabourCategoryAttributes($item_validation_result, $request);
		}

        return view('SiteMM.Master.labour_category')->with('LC', $data);
    }

    private function validateLabourCategory($request){

        //try{

            $inputs['lc_id'] = $request->lc_id;
            $inputs['lc_name'] = $request->lc_name;
            $inputs['price'] = $request->price;

            $rules['lc_id'] = array('required');
            $rules['lc_name'] = array('required', 'max:50');
            $rules['price'] =array('required', 'numeric');

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

    private function saveItem($request){

        //try{

            $objLabourCategory = new LabourCategory();

            $labour_category['labour_category'] = $this->getLabourCategoryArray($request);
            $saving_process_result = $objLabourCategory->saveLabourCategory($labour_category);

            return $saving_process_result;

        // }catch(\Exception $e){

        //     $process_result['lc_id'] = $request->lc_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'item Controller -> item Saving Process <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

    private function getLabourCategoryArray($request){

        $item['lc_id'] = $request->lc_id;
        $item['lc_name'] = $request->lc_name;
        $item['price'] = $request->price;
        $item['active'] = $request->active;

        if( LabourCategory::where('lc_id', $request->lc_id)->exists() ){

            $item['updated_by'] = Auth::id();
            $item['updated_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }else{

            $item['saved_by'] = Auth::id();
            $item['saved_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        return $item;
    }


    public function getLabourCategoryForSapLabour(Request $request){

        $elqLabourCategory = LabourCategory::where('lc_id', $request->lc_id)->first();
        if($elqLabourCategory->count() >= 1){

            $labour_category['price'] = number_format($elqLabourCategory->price, 2);

        }else{

            $labour_category['price'] = number_format(0, 2);;
        }

        return $labour_category;
    }

    public function openLabourCategory(Request $request){

        $process_result['lc_id'] = $request->lc_id;
        $process_result['process_status'] = TRUE;
        $process_result['validation_result'] = TRUE;
        $process_result['validation_messages'] =  new MessageBag();
        $process_result['front_end_message'] = '';
        $process_result['back_end_message'] = '';

        $data['attributes'] = $this->getLabourCategoryAttributes($process_result, $request);

        return view('SiteMM.Master.labour_category')->with('LC', $data);
    }

}
