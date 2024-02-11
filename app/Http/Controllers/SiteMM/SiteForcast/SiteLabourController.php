<?php

namespace App\Http\Controllers\SiteMM\SiteForcast;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SiteMM\Master\Site;
use App\Models\SiteMM\Master\LabourCategory;
use App\Models\SiteMM\Master\Unit;
use App\Models\SiteMM\SiteForcast\SiteTask;
use App\Models\SiteMM\SiteForcast\SiteSubTask;
use App\Models\SiteMM\SiteForcast\SiteLabour;

use App\Http\Controllers\SiteMM\SiteOperation\EmployeeSalaryController;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Carbon;

use App\Rules\ZeroValidation;
use App\Rules\QuantityValidation;

class SiteLabourController extends Controller {

    public function loadView(){

        $objEmployeeSalaryController =  new EmployeeSalaryController();

        $data['salary_category'] = $objEmployeeSalaryController->getSalaryCategory();
        $data['site'] = Site::where('active', 1)->get();
        $data['labour'] = LabourCategory::where('active', 1)->get();
        $data['unit'] = Unit::where('active', 1)->get();
        $data['site_task'] = array();
        $data['site_sub_task'] = array();
        $data['attributes'] = $this->getSiteActionPlanLabourAttributes(NULL, NULL);

        return view('SiteMM.SiteForcast.sap_labour')->with('sap_labour', $data);
    }

    private function getSiteActionPlanLabourAttributes($process, $request){

        $attributes['sc_id'] = '0';
        $attributes['site_id'] = '0';
        $attributes['task_id'] = '0';
        $attributes['sub_task_id'] = '0';
        $attributes['remark'] = '';
        $attributes['labour_detail'] = array();
        $attributes['labour_total'] = 0;

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        $labour_total = 0;
        $sap_labour_detail = SiteLabour::where('site_id', $request->site_id)
                                       ->where('task_id', $request->task_id)
                                       ->where('sub_task_id', $request->sub_task_id)
                                       ->orderBy('updated_at', 'desc')
                                       ->get();
        foreach($sap_labour_detail as $key => $value){

            $value->ono = ($key+1);
            $labour_total = $labour_total + $value->amount;

            if($value->sc_id == 1){

                $value->lc_name = LabourCategory::where('lc_id', $value->lc_id)->value('lc_name');
            }

            if(($value->sc_id == 2) || ($value->sc_id == 3)){

                $value->lc_name = Unit::where('unit_id', $value->unit_id)->value('unit_name');
            }
        }
        $attributes['labour_detail'] = $sap_labour_detail;
        $attributes['labour_total'] = $labour_total;

        $inputs = $request->input();
        if(is_null($inputs) == FALSE){

            $attributes['sc_id'] = $inputs['sc_id'];
            $attributes['site_id'] = $inputs['site_id'];
            $attributes['task_id'] = $inputs['task_id'];
            $attributes['sub_task_id'] = $inputs['sub_task_id'];

            if( $inputs['sc_id'] == 1 ){

                $attributes['lc_id'] = $inputs['lc_id'];
                $attributes['price'] = $inputs['price'];
            }

            if( ($inputs['sc_id'] == 2) || ($inputs['sc_id'] == 3) ){

                $attributes['unit_id'] = $inputs['unit_id'];
                $attributes['unit_rate'] = $inputs['unit_rate'];
                $attributes['quantity'] = $inputs['quantity'];
            }
            $attributes['remark'] = $inputs['remark'];
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

    public function addSapLabour(Request $request){

        if($request->submit == 'Display'){

            $process_result = [
                                    "process_status" => true,
                                    "front_end_message" => "",
                                    "back_end_message" => "",
                                    "validation_result" => true,
                                    "validation_messages" => array()
                              ];

            $data['attributes'] = $this->getSiteActionPlanLabourAttributes($process_result, $request);
        }

        if($request->submit == 'Add'){

            $site_validation_result = $this->validateLabourForSapLabour($request);

            if($site_validation_result['validation_result'] == TRUE){

                $saving_process_result = $this->saveSapLabour($request);

                $saving_process_result['validation_result'] = $site_validation_result['validation_result'];
                $saving_process_result['validation_messages'] = $site_validation_result['validation_messages'];

                $data['attributes'] = $this->getSiteActionPlanLabourAttributes($saving_process_result, $request);

            }else{

                $site_validation_result['process_status'] = FALSE;

                $data['attributes'] = $this->getSiteActionPlanLabourAttributes($site_validation_result, $request);
            }
        }

        $objEmployeeSalaryController =  new EmployeeSalaryController();

        $data['salary_category'] = $objEmployeeSalaryController->getSalaryCategory();
        $data['unit'] = Unit::where('active', 1)->get();
        $data['site'] = Site::where('active', 1)->get();
        $data['labour'] = LabourCategory::where('active', 1)->get();
        $data['site_task'] = SiteTask::where('active', 1)->where('site_id', $request->site_id)->get();
        $data['site_sub_task'] = SiteSubTask::where('active', 1)->where('task_id', $request->task_id)->get();

        return view('SiteMM.SiteForcast.sap_labour')->with('sap_labour', $data);

    }

    private function validateLabourForSapLabour($request){

        //try{

            $inputs['sc_id'] = $request->sc_id;
            $inputs['task_id'] = $request->task_id;
            $inputs['site_id'] = $request->site_id;
            $inputs['sub_task_id'] = $request->sub_task_id;
            $inputs['status_id'] = $request->status_id;
            $inputs['remark'] = $request->remark;

            if($request->sc_id == 1){

                $inputs['lc_id'] = $request->lc_id;
                $inputs['days'] = $request->days;
            }

            if( ($request->sc_id == 2) || ($request->sc_id == 3)){

                $inputs['unit_id'] = $request->unit_id;
                $inputs['unit_rate'] = $request->unit_rate;
                $inputs['quantity'] = $request->quantity;
            }

            $rules['sc_id'] = array( new ZeroValidation('Salary Category', $request->sc_id));
            $rules['site_id'] = array( new ZeroValidation('Site', $request->site_id));
            $rules['task_id'] = array( new ZeroValidation('Task', $request->task_id));
            $rules['sub_task_id'] = array( new ZeroValidation('Sub Task', $request->sub_task_id));
            $rules['remark'] = array( 'max:100');

            if($request->sc_id == 1){

                $rules['lc_id'] = array( new ZeroValidation('Labour', $request->lc_id));
                $rules['days'] = array('required', 'numeric', new QuantityValidation());
            }

            if( ($request->sc_id == 2) || ($request->sc_id == 3)){

                $rules['unit_id'] = array( new ZeroValidation('Unit', $request->unit_id));
                $rules['unit_rate'] = array('required', 'numeric', new QuantityValidation(0));
                $rules['quantity'] = array('required', 'numeric', new QuantityValidation());
            }

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

    private function saveSapLabour($request){

        //try{

            $objSiteLabour = new SiteLabour();

            $sap_labour['sap_labour'] = $this->getSapLabourlArray($request);
            $saving_process_result = $objSiteLabour->saveSiteLabour($sap_labour);

            return $saving_process_result;

        // }catch(\Exception $e){

        //     $process_result['site_id'] = $request->site_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'Site Controller -> Site Saving Process <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

    private function getSapLabourlArray($request){

        $sap_labour['sc_id'] = $request->sc_id;
        $sap_labour['site_id'] = $request->site_id;
        $sap_labour['task_id'] = $request->task_id;
        $sap_labour['sub_task_id'] = $request->sub_task_id;

        if($request->sc_id == 1){

            $sap_labour['lc_id'] = $request->lc_id;
            $sap_labour['unit_id'] = 0;
            $sap_labour['price'] = str_replace(",","",$request->price);
            $sap_labour['days'] = $request->days;
            $sap_labour['amount'] = floatval(str_replace(",","",$request->price)) * $request->days;
        }

        if( ($request->sc_id == 2) || ($request->sc_id == 3)){

            $sap_labour['unit_id'] = $request->unit_id;
            $sap_labour['lc_id'] = 0;
            $sap_labour['price'] = str_replace(",","",$request->unit_rate);
            $sap_labour['days'] = $request->quantity;
            $sap_labour['amount'] = floatval(str_replace(",","",$request->unit_rate)) * $request->quantity;
        }

        $sap_labour['remark'] = $request->remark;

        $upsert_flag = SiteLabour::where('site_id', $request->site_id)->where('task_id', $request->task_id)
                                    ->where('sub_task_id', $request->sub_task_id)->where('lc_id', $request->lc_id)
                                    ->exists();
        if( $upsert_flag ){

            $sap_labour['updated_by'] = Auth::id();
            $sap_labour['updated_on'] = Carbon::now()->format('Y-m-d H:i:s');

        }else{

            $sap_labour['saved_by'] = Auth::id();
            $sap_labour['saved_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        return $sap_labour;
    }

    public function openSapLabour(Request $request){

        $site_id = 0;
        $task_id = 0;
        $labour_total = 0;

        $sap_labour_detail = SiteLabour::where('sap_labour_id', $request->open_sap_labour_id)->get();
        foreach($sap_labour_detail as $key => $value){

            $value->ono = ($key+1);
            $value->lc_name = LabourCategory::where('lc_id', $value->lc_id)->value('lc_name');
            $labour_total = $labour_total + $value->amount;

            $attributes['sc_id'] = $value->sc_id;
            $attributes['site_id'] = $site_id = $value->site_id;
            $attributes['task_id'] =  $task_id =  $value->task_id;
            $attributes['sub_task_id'] = $value->sub_task_id;

            if( $value->sc_id == 1 ){

                $attributes['lc_id'] = $value->lc_id;
                $attributes['price'] = $value->price;
                $attributes['days'] = $value->days;
            }

            if( ($value->sc_id == 2) || ($value->sc_id == 3) ){

                $attributes['unit_id'] = $value->unit_id;
                $attributes['unit_rate'] = $value->price;
                $attributes['quantity'] = $value->days;
            }

            $attributes['remark'] = $value->remark;
            $attributes['labour_total'] = $labour_total;

        }
        $attributes['labour_detail'] = $sap_labour_detail;
        $attributes['validation_messages'] = new MessageBag();
        $attributes['process_message'] = "";

        $objEmployeeSalaryController =  new EmployeeSalaryController();

        $data['attributes'] = $attributes;
        $data['unit'] = Unit::where('active', 1)->get();
        $data['salary_category'] = $objEmployeeSalaryController->getSalaryCategory();
        $data['site'] = Site::where('active', 1)->get();
        $data['labour'] = LabourCategory::where('active', 1)->get();
        $data['site_task'] = SiteTask::where('active', 1)->where('site_id', $site_id)->get();
        $data['site_sub_task'] = SiteSubTask::where('active', 1)->where('task_id', $task_id)->get();

        return view('SiteMM.SiteForcast.sap_labour')->with('sap_labour', $data);
    }


}
