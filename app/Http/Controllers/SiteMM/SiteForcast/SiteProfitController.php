<?php

namespace App\Http\Controllers\SiteMM\SiteForcast;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SiteMM\Master\Site;
use App\Models\SiteMM\SiteForcast\SiteTask;
use App\Models\SiteMM\SiteForcast\SiteSubTask;
use App\Models\SiteMM\SiteForcast\SiteMaterials;
use App\Models\SiteMM\SiteForcast\SiteLabour;
use App\Models\SiteMM\SiteForcast\SiteOverheadCost;
use App\Models\SiteMM\SiteForcast\SiteProfit;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Carbon;

use App\Rules\ZeroValidation;
use App\Rules\CurrencyValidation;

use \stdClass;

class SiteProfitController extends Controller {

    public function loadView(){

        $data['site'] = Site::where('active', 1)->get();
        $data['site_task'] = array();
        $data['site_sub_task'] = array();
        $data['attributes'] = $this->getSiteActionPlanProfitAttributes(NULL, NULL);

        return view('SiteMM.SiteForcast.sap_profit')->with('sap_profit', $data);
    }

    private function getSiteActionPlanProfitAttributes($process, $request){

        $attributes['site_id'] = '0';
        $attributes['task_id'] = '0';
        $attributes['sub_task_id'] = '0';
        $attributes['material_cost'] = number_format(0, 2);
        $attributes['labour_cost'] = number_format(0, 2);
        $attributes['overhead_cost'] = number_format(0, 2);
        $attributes['total_cost'] = number_format(0, 2);
        $attributes['profit_value'] = number_format(0, 2);

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        $material_cost = $this->getMaterialCostTotal($request);
        $labour_cost = $this->getLabourCostTotal($request);
        $overhead_cost = $this->getOverheadCostTotal($request);

        $attributes['site_id'] = $request->site_id;
        $attributes['task_id'] = $request->task_id;
        $attributes['sub_task_id'] = $request->sub_task_id;
        $attributes['material_cost'] = number_format($material_cost, 2);
        $attributes['labour_cost'] = number_format($labour_cost, 2);
        $attributes['overhead_cost'] = number_format($overhead_cost, 2);
        $attributes['total_cost'] = number_format( ($material_cost + $labour_cost + $overhead_cost), 2 );
        $attributes['profit_value'] = number_format($request->profit_value, 2);

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

    public function addSapProfit(Request $request){

        $site_validation_result = $this->validateProfitProcessForSAP($request);
        if($site_validation_result['validation_result'] == TRUE){

            $saving_process_result = $this->saveSapProfit($request);

            $saving_process_result['validation_result'] = $site_validation_result['validation_result'];
            $saving_process_result['validation_messages'] = $site_validation_result['validation_messages'];

            $data['attributes'] = $this->getSiteActionPlanProfitAttributes($saving_process_result, $request);

        }else{

            $site_validation_result['process_status'] = FALSE;

            $data['attributes'] = $this->getSiteActionPlanProfitAttributes($site_validation_result, $request);
        }


        $data['site'] = Site::where('active', 1)->get();
        $data['site_task'] = SiteTask::where('active', 1)->where('site_id', $request->site_id)->get();
        $data['site_sub_task'] = SiteSubTask::where('active', 1)->where('task_id', $request->task_id)->get();

        return view('SiteMM.SiteForcast.sap_profit')->with('sap_profit', $data);

    }

    private function validateProfitProcessForSAP($request){

        //try{

            $inputs['task_id'] = $request->task_id;
            $inputs['site_id'] = $request->site_id;
            $inputs['sub_task_id'] = $request->sub_task_id;
            $inputs['profit_value'] = $request->profit_value;

            $rules['site_id'] = array( new ZeroValidation('Site', $request->site_id));
            $rules['task_id'] = array( new ZeroValidation('Task', $request->task_id));
            $rules['sub_task_id'] = array( new ZeroValidation('Sub Task', $request->sub_task_id));
            $rules['profit_value'] = array('required', 'numeric', new CurrencyValidation(1));

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

    private function saveSapProfit($request){

        //try{

            $objSiteProfit = new SiteProfit();

            $sap_profit['sap_profit'] = $this->getSapProfitArray($request);
            $saving_process_result = $objSiteProfit->saveSiteProfit($sap_profit);

            return $saving_process_result;

        // }catch(\Exception $e){

        //     $process_result['site_id'] = $request->site_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'Site Controller -> Site Saving Process <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

    private function getSapProfitArray($request){

        $material_cost = $this->getMaterialCostTotal($request);
        $labour_cost = $this->getLabourCostTotal($request);
        $overhead_cost = $this->getOverheadCostTotal($request);

        $sap_profit['site_id'] = $request->site_id;
        $sap_profit['task_id'] = $request->task_id;
        $sap_profit['sub_task_id'] = $request->sub_task_id;
        $sap_profit['material_cost'] = $material_cost;
        $sap_profit['labour_cost'] = $labour_cost;
        $sap_profit['overhead_cost'] = $overhead_cost;
        $sap_profit['total_cost'] =  ($material_cost + $labour_cost + $overhead_cost);
        $sap_profit['profit_value'] = str_replace(",","",$request->profit_value);

        $upsert_flag = SiteProfit::where('site_id', $request->site_id)
                                 ->where('task_id', $request->task_id)
                                 ->where('sub_task_id', $request->sub_task_id)
                                 ->exists();
        if( $upsert_flag ){

            $sap_profit['updated_by'] = Auth::id();
            $sap_profit['updated_on'] = Carbon::now()->format('Y-m-d H:i:s');

        }else{

            $sap_profit['saved_by'] = Auth::id();
            $sap_profit['saved_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        return $sap_profit;
    }

    public function getSiteWiseTotalCost(Request $request){

        $material_cost = $this->getMaterialCostTotal($request);
        $labour_cost = $this->getLabourCostTotal($request);
        $overhead_cost = $this->getOverheadCostTotal($request);

        $total['material_cost'] = number_format($material_cost, 2);
        $total['labour_cost'] = number_format($labour_cost, 2);
        $total['overhead_cost'] = number_format($overhead_cost, 2);
        $total['total_cost'] = number_format( ($material_cost + $labour_cost + $overhead_cost), 2 );

        return $total;
    }

    private function getMaterialCostTotal($request){

        $elqSiteMaterials = SiteMaterials::where('site_id', $request->site_id)->get();
        $material_cost_value = $elqSiteMaterials->sum('amount');

        if( $request->task_id != 0) {

            $elqSiteTaskMaterials = $elqSiteMaterials->where('task_id', $request->task_id);
            $material_cost_value = $elqSiteTaskMaterials->sum('amount');

            if( $request->sub_task_id != 0) {

                $elqSiteTaskSubTaskMaterials = $elqSiteMaterials->where('task_id', $request->task_id)->where('sub_task_id', $request->sub_task_id);
                $material_cost_value = $elqSiteTaskSubTaskMaterials->sum('amount');
            }
        }

        if( is_null($material_cost_value) == TRUE){

            $material_cost_value = 0;
        }

        return $material_cost_value;
    }

    private function getLabourCostTotal($request){

        $elqSiteLabour = SiteLabour::where('site_id', $request->site_id)->get();
        $labour_cost_value = $elqSiteLabour->sum('amount');

        if( $request->task_id != 0) {

            $elqSiteTaskMaterials = $elqSiteLabour->where('task_id', $request->task_id);
            $labour_cost_value = $elqSiteTaskMaterials->sum('amount');

            if( $request->sub_task_id != 0) {

                $elqSiteTaskSubTaskMaterials = $elqSiteLabour->where('task_id', $request->task_id)->where('sub_task_id', $request->sub_task_id);
                $labour_cost_value = $elqSiteTaskSubTaskMaterials->sum('amount');
            }

        }

        if( is_null($labour_cost_value) == TRUE){

            $labour_cost_value = 0;
        }

        return $labour_cost_value;
    }

    private function getOverheadCostTotal($request){

        $elqSiteOverheadCost = SiteOverheadCost::where('site_id', $request->site_id)->get();
        $overhead_cost_value = $elqSiteOverheadCost->sum('amount');

        if( $request->task_id != 0) {

            $elqSiteTaskMaterials = $elqSiteOverheadCost->where('task_id', $request->task_id);
            $overhead_cost_value = $elqSiteTaskMaterials->sum('amount');

            if( $request->sub_task_id != 0) {

                $elqSiteTaskSubTaskMaterials = $elqSiteOverheadCost->where('task_id', $request->task_id)->where('sub_task_id', $request->sub_task_id);
                $overhead_cost_value = $elqSiteTaskSubTaskMaterials->sum('amount');
            }
        }

        if( is_null($overhead_cost_value) == TRUE){

            $overhead_cost_value = 0;
        }

        return $overhead_cost_value;
    }


    public function openSapProfit(Request $request){

        $objRequest = new stdClass;

        $elqSiteProfit = SiteProfit::where('sap_profit_id', $request->open_sap_profit_id)->get();
        foreach($elqSiteProfit as $rowKey => $rowValue){

            $objRequest->site_id = $rowValue->site_id;
            $objRequest->task_id = $rowValue->task_id;
            $objRequest->sub_task_id = $rowValue->sub_task_id;
            $objRequest->profit_value = $rowValue->profit_value;
        }

        $material_cost = $this->getMaterialCostTotal($objRequest);
        $labour_cost = $this->getLabourCostTotal($objRequest);
        $overhead_cost = $this->getOverheadCostTotal($objRequest);

        $attributes['site_id'] = $objRequest->site_id;
        $attributes['task_id'] = $objRequest->task_id;
        $attributes['sub_task_id'] = $objRequest->sub_task_id;
        $attributes['material_cost'] = number_format($material_cost, 2);
        $attributes['labour_cost'] = number_format($labour_cost, 2);
        $attributes['overhead_cost'] = number_format($overhead_cost, 2);
        $attributes['total_cost'] = number_format( ($material_cost + $labour_cost + $overhead_cost), 2 );
        $attributes['profit_value'] = number_format($objRequest->profit_value, 2);
        $attributes['validation_messages'] = new MessageBag();
        $attributes['process_message'] = "";

        $data['attributes'] = $attributes;

        $data['site'] = Site::where('active', 1)->get();
        $data['site_task'] = SiteTask::where('active', 1)->where('site_id', $objRequest->site_id)->get();
        $data['site_sub_task'] = SiteSubTask::where('active', 1)->where('task_id', $objRequest->task_id)->get();

        return view('SiteMM.SiteForcast.sap_profit')->with('sap_profit', $data);

    }

}
