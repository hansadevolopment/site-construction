<?php

namespace App\Http\Controllers\SiteMM\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SiteMM\Master\OverheadCostItem;
use App\Models\SiteMM\Master\Unit;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Carbon;

class OverheadCostController extends Controller {

    public function loadView(){

        $data['unit'] = Unit::where('active', 1)->get();
        $data['attributes'] = $this->getOverheadCostAttributes(NULL, NULL);

        return view('SiteMM.Master.overhead_cost')->with('OC', $data);
    }

    private function getOverheadCostAttributes($process, $request){

        $attributes['oci_id'] = '#Auto#';
        $attributes['oci_name'] = '';
        $attributes['unit_id'] = '0';
        $attributes['rental'] = 0;
        $attributes['active'] = 1;
        $attributes['remark'] = '';

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        if( ($process['validation_result'] == TRUE) && ($process['process_status'] == TRUE)){

            $elqOverheadCostItem = OverheadCostItem::where('oci_id', $process['oci_id'])->first();
            if($elqOverheadCostItem->count() >= 1) {

                $attributes['oci_id'] = $elqOverheadCostItem->oci_id;
                $attributes['oci_name'] = $elqOverheadCostItem->oci_name;
                $attributes['unit_id'] = $elqOverheadCostItem->unit_id;
                $attributes['rental'] = $elqOverheadCostItem->rental;
                $attributes['active'] = $elqOverheadCostItem->active;
                $attributes['remark'] = $elqOverheadCostItem->remark;
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

                $attributes['oci_id'] = $inputs['oci_id'];
                $attributes['oci_name'] = $inputs['oci_name'];
                $attributes['unit_id'] = $inputs['unit_id'];
                $attributes['rental'] = $inputs['rental'];
                $attributes['active'] = $inputs['active'];
                $attributes['remark'] = $inputs['remark'];
            }

            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';
        }

        return $attributes;
    }

    public function processOverheadCost(Request $request){

        $oci_validation_result = $this->validateOverheadCostItem($request);

        if($oci_validation_result['validation_result'] == TRUE){

			$saving_process_result = $this->saveOverheadCostItem($request);

			$saving_process_result['validation_result'] = $oci_validation_result['validation_result'];
			$saving_process_result['validation_messages'] = $oci_validation_result['validation_messages'];

            $data['attributes'] = $this->getOverheadCostAttributes($saving_process_result, $request);

		}else{

			$oci_validation_result['oci_id'] = $request->oci_id;
			$oci_validation_result['process_status'] = FALSE;

            $data['attributes'] = $this->getOverheadCostAttributes($oci_validation_result, $request);
		}

        $data['unit'] = Unit::where('active', 1)->get();

        return view('SiteMM.Master.overhead_cost')->with('OC', $data);
    }

    private function validateOverheadCostItem($request){

        //try{

            $inputs['oci_id'] = $request->oci_id;
            $inputs['oci_name'] = $request->oci_name;
            $inputs['unit_id'] = $request->unit_id;
            $inputs['remark'] = $request->remark;

            $rules['oci_id'] = array('required');
            $rules['oci_name'] = array('required', 'max:50');
            $rules['unit_id'] = array('required', 'max:50');
            $rules['remark'] = array( 'max:100');

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

    private function saveOverheadCostItem($request){

        //try{

            $objOverheadCostItem = new OverheadCostItem();

            $oc_item['oc_item'] = $this->getOverheadCostItemArray($request);
            $saving_process_result = $objOverheadCostItem->saveOverheadCostItem($oc_item);

            return $saving_process_result;

        // }catch(\Exception $e){

        //     $process_result['oci_id'] = $request->oci_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'item Controller -> item Saving Process <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

    private function getOverheadCostItemArray($request){

        $oc_item['oci_id'] = $request->oci_id;
        $oc_item['oci_name'] = $request->oci_name;
        $oc_item['unit_id'] = $request->unit_id;
        $oc_item['rental'] = $request->rental;
        $oc_item['active'] = $request->active;
        $oc_item['remark'] = $request->remark;

        if( OverheadCostItem::where('oci_id', $request->oci_id)->exists() ){

            $oc_item['updated_by'] = Auth::id();
            $oc_item['updated_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }else{

            $oc_item['saved_by'] = Auth::id();
            $oc_item['saved_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        return $oc_item;
    }

    public function openOverhead(Request $request){

        $process_result['oci_id'] = $request->oci_id;
        $process_result['process_status'] = TRUE;
        $process_result['validation_result'] = TRUE;
        $process_result['validation_messages'] =  new MessageBag();
        $process_result['front_end_message'] = '';
        $process_result['back_end_message'] = '';

        $data['attributes'] = $this->getOverheadCostAttributes($process_result, $request);
        $data['unit'] = Unit::where('active', 1)->get();

        return view('SiteMM.Master.overhead_cost')->with('OC', $data);
    }



}
