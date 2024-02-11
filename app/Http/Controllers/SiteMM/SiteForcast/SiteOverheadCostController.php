<?php

namespace App\Http\Controllers\SiteMM\SiteForcast;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SiteMM\Master\Site;
use App\Models\SiteMM\Master\OverheadCostItem;

use App\Models\SiteMM\SiteForcast\SiteTask;
use App\Models\SiteMM\SiteForcast\SiteSubTask;
use App\Models\SiteMM\SiteForcast\SiteOverheadCost;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Carbon;

use App\Rules\ZeroValidation;
use App\Rules\CurrencyValidation;
use App\Rules\QuantityValidation;

class SiteOverheadCostController extends Controller {

    public function loadView(){

        $data['site'] = Site::where('active', 1)->get();
        $data['site_task'] = array();
        $data['site_sub_task'] = array();
        $data['overhead_cost'] = OverheadCostItem::where('active', 1)->get();
        $data['attributes'] = $this->getSiteActionPlanOverheadCostAttributes(NULL, NULL);

        return view('SiteMM.SiteForcast.sap_overhead_cost')->with('sap_overhead_cost', $data);
    }

    private function getSiteActionPlanOverheadCostAttributes($process, $request){

        $attributes['site_id'] = '0';
        $attributes['task_id'] = '0';
        $attributes['sub_task_id'] = '0';
        $attributes['oci_id'] = '0';
        $attributes['overhead_cost_detail'] = array();
        $attributes['overhead_cost_total'] = 0;

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        $overhead_cost_total = 0;
        $sap_overhead_cost_detail = SiteOverheadCost::where('site_id', $request->site_id)
                                             ->where('task_id', $request->task_id)
                                             ->where('sub_task_id', $request->sub_task_id)
                                             ->orderBy('updated_at', 'desc')
                                             ->get();
        $attributes['overhead_cost_detail'] = $sap_overhead_cost_detail;
        $attributes['overhead_cost_total'] = $sap_overhead_cost_detail->sum('amount');

        $inputs = $request->input();
        if(is_null($inputs) == FALSE){

            $attributes['site_id'] = $inputs['site_id'];
            $attributes['task_id'] = $inputs['task_id'];
            $attributes['sub_task_id'] = $inputs['sub_task_id'];
            $attributes['oci_id'] = $inputs['oci_id'];
            $attributes['quantity'] = $inputs['quantity'];
            $attributes['price'] = $inputs['price'];
        }

        if( ($process['validation_result'] == TRUE) && ($process['process_status'] == TRUE)){

            $attributes['validation_messages'] = $process['validation_messages'];

            if($request->submit == 'Display'){

                $attributes['validation_messages'] = new MessageBag();
                $attributes['process_message'] = "";
            }else{

                $message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
                $attributes['process_message'] = '<div class="alert alert-success" role="alert"> '. $message .' </div> ';
            }

        }else{

            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';
        }

        return $attributes;
    }

    public function addSapOverheadcost(Request $request){

        if($request->submit == 'Display'){

            $process_result = [
                                    "process_status" => true,
                                    "front_end_message" => "",
                                    "back_end_message" => "",
                                    "validation_result" => true,
                                    "validation_messages" => array()
                              ];

            $data['attributes'] = $this->getSiteActionPlanOverheadCostAttributes($process_result, $request);
        }

        if($request->submit == 'Add'){

            $site_validation_result = $this->validateOverheadCostForSAP($request);

            if($site_validation_result['validation_result'] == TRUE){

                $saving_process_result = $this->saveSapOverheadCost($request);

                $saving_process_result['validation_result'] = $site_validation_result['validation_result'];
                $saving_process_result['validation_messages'] = $site_validation_result['validation_messages'];

                $data['attributes'] = $this->getSiteActionPlanOverheadCostAttributes($saving_process_result, $request);

            }else{

                $site_validation_result['process_status'] = FALSE;

                $data['attributes'] = $this->getSiteActionPlanOverheadCostAttributes($site_validation_result, $request);
            }
        }

        $data['site'] = Site::where('active', 1)->get();
        $data['site_task'] = SiteTask::where('active', 1)->where('site_id', $request->site_id)->get();
        $data['site_sub_task'] = SiteSubTask::where('active', 1)->where('task_id', $request->task_id)->get();
        $data['overhead_cost'] = OverheadCostItem::where('active', 1)->get();

        return view('SiteMM.SiteForcast.sap_overhead_cost')->with('sap_overhead_cost', $data);

    }

    private function validateOverheadCostForSAP($request){

        //try{

            $inputs['task_id'] = $request->task_id;
            $inputs['site_id'] = $request->site_id;
            $inputs['sub_task_id'] = $request->sub_task_id;
            $inputs['oci_id'] = $request->oci_id;
            $inputs['quantity'] = $request->quantity;
            $inputs['price'] = $request->price;

            $rules['site_id'] = array( new ZeroValidation('Site', $request->site_id));
            $rules['task_id'] = array( new ZeroValidation('Task', $request->task_id));
            $rules['sub_task_id'] = array( new ZeroValidation('Sub Task', $request->sub_task_id));
            $rules['oci_id'] = array( new ZeroValidation('Overhead Cost', $request->oci_id));
            $rules['price'] = array('required', 'numeric', new CurrencyValidation(1));
            $rules['quantity'] = array('required', 'numeric', new QuantityValidation(1));

            $front_end_message = '';

            $validator = Validator::make($inputs, $rules);
            $validation_result = $validator->passes();
            if($validation_result == FALSE){

                $front_end_message = 'Please Check Your Inputs';
            }

            $process_result['validation_result'] = $validator->passes();
            $process_result['validation_messages'] =  $validator->errors();
            $process_result['front_end_message'] = $front_end_message;
            $process_result['back_end_message'] =  'Site Overhead Cost Controller - Validation Process ';

            return $process_result;

        // }catch(\Exception $e){

        //     $process_result['validation_result'] = FALSE;
        //     $process_result['validation_messages'] = new MessageBag();
        //     $process_result['front_end_message'] =  $e->getMessage();
        //     $process_result['back_end_message'] =  'Site Overhead Cost Controller - Validation Function Fault';

		// 	return $process_result;
        // }
    }

    private function saveSapOverheadCost($request){

        //try{

            $objSiteOverheadCost = new SiteOverheadCost();

            $sap_overhead_cost['sap_overhead_cost'] = $this->getSapOverheadCostArray($request);
            $saving_process_result = $objSiteOverheadCost->saveSiteOverheadCost($sap_overhead_cost);

            return $saving_process_result;

        // }catch(\Exception $e){

        //     $process_result['site_id'] = $request->site_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'Site Overhead Cost Controller -> Site Saving Process <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

    private function getSapOverheadCostArray($request){

        $sap_overhead_cost['site_id'] = $request->site_id;
        $sap_overhead_cost['task_id'] = $request->task_id;
        $sap_overhead_cost['sub_task_id'] = $request->sub_task_id;
        $sap_overhead_cost['oci_id'] = $request->oci_id;
        $sap_overhead_cost['quantity'] = $request->quantity;
        $sap_overhead_cost['price'] = $request->price;
        $sap_overhead_cost['amount'] = floatval($request->price * $request->quantity);

        $upsert_flag = SiteOverheadCost::where('site_id', $request->site_id)->where('task_id', $request->task_id)
                                       ->where('sub_task_id', $request->sub_task_id)->where('oci_id', $request->oci_id)
                                       ->exists();
        if( $upsert_flag ){

            $sap_overhead_cost['updated_by'] = Auth::id();
            $sap_overhead_cost['updated_on'] = Carbon::now()->format('Y-m-d H:i:s');

        }else{

            $sap_overhead_cost['saved_by'] = Auth::id();
            $sap_overhead_cost['saved_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        return $sap_overhead_cost;
    }

    public function openSapOverhead(Request $request){

        $site_id = 0;
        $task_id = 0;

        $sap_overhead_cost_detail = SiteOverheadCost::where('sap_oc_id', $request->open_sap_overhead_cost_id)->get();
        foreach( $sap_overhead_cost_detail as $key => $value ){

            $attributes['site_id'] = $site_id =  $value->site_id;
            $attributes['task_id'] = $task_id =  $value->task_id;
            $attributes['sub_task_id'] = $value->sub_task_id;
            $attributes['oci_id'] = $value->oci_id;
            $attributes['value'] = $value->amount;
        }

        $attributes['overhead_cost_detail'] = $sap_overhead_cost_detail;
        $attributes['overhead_cost_total'] = $sap_overhead_cost_detail->sum('amount');
        $attributes['validation_messages'] = new MessageBag();
        $attributes['process_message'] = "";

        $data['attributes'] = $attributes;
        $data['site'] = Site::where('active', 1)->get();
        $data['site_task'] = SiteTask::where('active', 1)->where('site_id', $site_id)->get();
        $data['site_sub_task'] = SiteSubTask::where('active', 1)->where('task_id', $task_id)->get();
        $data['overhead_cost'] = OverheadCostItem::where('active', 1)->get();

        return view('SiteMM.SiteForcast.sap_overhead_cost')->with('sap_overhead_cost', $data);

    }

}
