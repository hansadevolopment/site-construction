<?php

namespace App\Http\Controllers\SiteMM\SiteForcast;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SiteMM\Master\Site;
use App\Models\SiteMM\SiteForcast\SiteTask;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Carbon;

use App\Rules\ZeroValidation;

class SiteTaskController extends Controller {

    public function loadView(){

        $data['site'] = Site::where('active', 1)->get();
        $data['attributes'] = $this->getSiteTaskAttributes(NULL, NULL);

        return view('SiteMM.SiteForcast.site_task')->with('Site', $data);
    }

    private function getSiteTaskAttributes($process, $request){

        $attributes['task_id'] = '#Auto#';
        $attributes['task_name'] = '';
        $attributes['site_id'] = '0';
        $attributes['start_date'] = '';
        $attributes['end_date'] = '';
        $attributes['active'] = 1;
        $attributes['sub_contract'] = 0;
        $attributes['remark'] = '';

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        if( ($process['validation_result'] == TRUE) && ($process['process_status'] == TRUE)){

            $elqSiteTask = SiteTask::where('task_id', $process['task_id'])->first();
            if($elqSiteTask->count() >= 1) {

                $attributes['task_id'] = $elqSiteTask->task_id;
                $attributes['task_name'] = $elqSiteTask->task_name;
                $attributes['site_id'] = $elqSiteTask->site_id;
                $attributes['start_date'] = $elqSiteTask->start_date;
                $attributes['end_date'] = $elqSiteTask->end_date;
                $attributes['active'] = $elqSiteTask->active;
                $attributes['sub_contract'] = $elqSiteTask->sub_contract;
                $attributes['remark'] = $elqSiteTask->remark;
            }

            $attributes['validation_messages'] = $process['validation_messages'];

			if( $process['front_end_message'] == "Open" ){

                $message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
                $attributes['process_message'] = '';

            }else{

                $message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
                $attributes['process_message'] = '<div class="alert alert-success" role="alert"> '. $message .' </div> ';
            }

        }else{

            $inputs = $request->input();
            if(is_null($inputs) == FALSE){

                $attributes['task_id'] = $inputs['task_id'];
                $attributes['task_name'] = $inputs['task_name'];
                $attributes['site_id'] = $inputs['site_id'];
                $attributes['start_date'] = $inputs['start_date'];
                $attributes['end_date'] = $inputs['end_date'];
                $attributes['active'] = $inputs['active'];
                $attributes['sub_contract'] = $inputs['sub_contract'];
                $attributes['remark'] = $inputs['remark'];
            }

            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';
        }

        return $attributes;
    }

    public function processSiteTask(Request $request){

        if( $request->submit == 'Reset' ){
            $data['attributes'] = $this->getSiteTaskAttributes(NULL, NULL);
        }

        if( $request->submit == 'Save' ){
            $site_validation_result = $this->validateSiteTask($request);
            if($site_validation_result['validation_result'] == TRUE){

                $saving_process_result = $this->saveSiteTask($request);

                $saving_process_result['validation_result'] = $site_validation_result['validation_result'];
                $saving_process_result['validation_messages'] = $site_validation_result['validation_messages'];

                $data['attributes'] = $this->getSiteTaskAttributes($saving_process_result, $request);

            }else{

                $site_validation_result['item_id'] = $request->item_id;
                $site_validation_result['process_status'] = FALSE;

                $data['attributes'] = $this->getSiteTaskAttributes($site_validation_result, $request);
            }
        }

        $data['site'] = Site::where('active', 1)->get();

        return view('SiteMM.SiteForcast.site_task')->with('Site', $data);
    }

    private function validateSiteTask($request){

        //try{

            $inputs['task_id'] = $request->task_id;
            $inputs['task_name'] = $request->task_name;
            $inputs['site_id'] = $request->site_id;
            $inputs['start_date'] = $request->start_date;
            $inputs['end_date'] = $request->end_date;
            $inputs['remark'] = $request->remark;

            $rules['task_id'] = array('required');
            $rules['task_name'] = array('required', 'max:150');
            $rules['site_id'] = array( new ZeroValidation('Site', $request->site_id));
            $rules['start_date'] = array('required', 'date', 'nullable');
            $rules['end_date'] = array('required', 'date', 'nullable');
            $rules['remark'] = array( 'max:100');

            $front_end_message = '';

            $validator = Validator::make($inputs, $rules);
            $validation_result = $validator->passes();
            if($validation_result == FALSE){

                $front_end_message = 'Please Check Your Inputs';
            }

            $process_result['validation_result'] = $validation_result;
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

    private function saveSiteTask($request){

        //try{

            $objSiteTask = new SiteTask();

            $site['site_task'] = $this->getSiteTaskArray($request);
            $saving_process_result = $objSiteTask->saveSiteTask($site);

            return $saving_process_result;

        // }catch(\Exception $e){

        //     $process_result['site_id'] = $request->site_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'Site Controller -> Site Saving Process <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

    private function getSiteTaskArray($request){

        $site_task['task_id'] = $request->task_id;
        $site_task['task_name'] = $request->task_name;
        $site_task['site_id'] = $request->site_id;
        $site_task['start_date'] = $request->start_date;
        $site_task['end_date'] = $request->end_date;
        $site_task['active'] = $request->active;
        $site_task['sub_contract'] = $request->sub_contract;
        $site_task['remark'] = $request->remark;

        if( SiteTask::where('task_id', $request->task_id)->exists() ){

            $site_task['updated_by'] = Auth::id();
            $site_task['updated_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }else{

            $site_task['saved_by'] = Auth::id();
            $site_task['saved_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        return $site_task;
    }

    public function getTaskWiseSubTask(Request $request){

        $return_text = '';
        $elqSiteTask = SiteTask::where('site_id', $request->site_id)->where('task_id', $request->task_id)->first();

        foreach($elqSiteTask->subTask as $subTaskKey => $SubTaskValue){

            $return_text .= " <option value = '". $SubTaskValue->sub_task_id ."'>". $SubTaskValue->sub_task_name ."</option> ";
        }
        $return_text .= " <option value = '0' selected> Select the Sub Task </option> ";

        return  $return_text ;
    }

    public function openTask(Request $request){

        $process_result['task_id'] = $request->open_task_id;
        $process_result['validation_result'] = TRUE;
        $process_result['process_status'] = TRUE;
        $process_result['validation_messages'] =  new MessageBag();
        $process_result['front_end_message'] = 'Open';
        $process_result['back_end_message'] =  '';

        $data['site'] = Site::where('active', 1)->get();
        $data['attributes'] = $this->getSiteTaskAttributes($process_result, $request);

        return view('SiteMM.SiteForcast.site_task')->with('Site', $data);
    }

}
