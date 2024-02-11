<?php

namespace App\Http\Controllers\SiteMM\SiteForcast;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SiteMM\Master\Site;
use App\Models\SiteMM\Master\Status;
use App\Models\SiteMM\SiteForcast\SiteTask;
use App\Models\SiteMM\SiteForcast\SiteSubTask;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Carbon;

use App\Rules\ZeroValidation;

class SiteSubTaskController extends Controller {

    public function loadView(){

        $data['site'] = Site::where('active', 1)->get();
        $data['task'] = SiteTask::where('active', 1)->get();
        $data['status'] = Status::where('active', 1)->get();
        $data['attributes'] = $this->getSiteSubTaskAttributes(NULL, NULL);

        return view('SiteMM.SiteForcast.site_sub_task')->with('SubSite', $data);
    }

    private function getSiteSubTaskAttributes($process, $request){

        $attributes['sub_task_id'] = '#Auto#';
        $attributes['sub_task_name'] = '';
        $attributes['site_id'] = '0';
        $attributes['task_id'] = '0';
        $attributes['start_date'] = '';
        $attributes['end_date'] = '';
        $attributes['active'] = 1;
        $attributes['sub_contract'] = 0;
        $attributes['status_id'] = 1;
        $attributes['remark'] = '';

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        if( ($process['validation_result'] == TRUE) && ($process['process_status'] == TRUE)){

            $elqSiteSubTask = SiteSubTask::where('sub_task_id', $process['sub_task_id'])->first();
            if($elqSiteSubTask->count() >= 1) {

                $attributes['sub_task_id'] = $elqSiteSubTask->sub_task_id;
                $attributes['sub_task_name'] = $elqSiteSubTask->sub_task_name;
                $attributes['site_id'] = $elqSiteSubTask->site_id;
                $attributes['task_id'] = $elqSiteSubTask->task_id;
                $attributes['start_date'] = $elqSiteSubTask->start_date;
                $attributes['end_date'] = $elqSiteSubTask->end_date;
                $attributes['active'] = $elqSiteSubTask->active;
                $attributes['sub_contract'] = $elqSiteSubTask->sub_contract;
                $attributes['status_id'] = $elqSiteSubTask->status_id;
                $attributes['remark'] = $elqSiteSubTask->remark;
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

                $attributes['sub_task_id'] = $inputs['sub_task_id'];
                $attributes['sub_task_name'] = $inputs['sub_task_name'];
                $attributes['site_id'] = $inputs['site_id'];
                $attributes['task_id'] = $inputs['task_id'];
                $attributes['start_date'] = $inputs['start_date'];
                $attributes['end_date'] = $inputs['end_date'];
                $attributes['active'] = $inputs['active'];
                $attributes['sub_contract'] = $inputs['sub_contract'];
                $attributes['status_id'] = $inputs['status_id'];
                $attributes['remark'] = $inputs['remark'];
            }

            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';
        }

        return $attributes;
    }

    public function processSiteSubTask(Request $request){

        if( $request->submit == 'Reset' ){
            $data['attributes'] = $this->getSiteSubTaskAttributes(NULL, NULL);
        }

        if( $request->submit == 'Save' ){

            $site_validation_result = $this->validateSiteSubTask($request);
            if($site_validation_result['validation_result'] == TRUE){

                $saving_process_result = $this->saveSiteSubTask($request);

                $saving_process_result['validation_result'] = $site_validation_result['validation_result'];
                $saving_process_result['validation_messages'] = $site_validation_result['validation_messages'];

                $data['attributes'] = $this->getSiteSubTaskAttributes($saving_process_result, $request);

            }else{

                $site_validation_result['site_sub_task_id'] = $request->site_sub_task_id;
                $site_validation_result['process_status'] = FALSE;

                $data['attributes'] = $this->getSiteSubTaskAttributes($site_validation_result, $request);
            }
        }

        $data['site'] = Site::where('active', 1)->get();
        $data['task'] = SiteTask::where('site_id', $data['attributes']['site_id'])->get();
        $data['status'] = Status::where('active', 1)->get();

        return view('SiteMM.SiteForcast.site_sub_task')->with('SubSite', $data);
    }

    private function validateSiteSubTask($request){

        //try{

            $inputs['sub_task_id'] = $request->sub_task_id;
            $inputs['sub_task_name'] = $request->sub_task_name;
            $inputs['site_id'] = $request->site_id;
            $inputs['task_id'] = $request->task_id;
            $inputs['start_date'] = $request->start_date;
            $inputs['end_date'] = $request->end_date;
            $inputs['status_id'] = $request->status_id;
            $inputs['remark'] = $request->remark;

            $rules['sub_task_id'] = array('required');
            $rules['sub_task_name'] = array('required', 'max:150');
            $rules['site_id'] = array( new ZeroValidation('Site', $request->site_id));
            $rules['task_id'] = array( new ZeroValidation('Task', $request->task_id));
            $rules['start_date'] = array('required', 'date');
            $rules['end_date'] = array('required', 'date');
            $rules['status_id'] = array( new ZeroValidation('Status', $request->status_id));
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

    private function saveSiteSubTask($request){

        //try{

            $objSiteSubTask = new SiteSubTask();

            $site_sub_task['site_sub_task'] = $this->getSiteSubTaskArray($request);
            $saving_process_result = $objSiteSubTask->saveSiteSubTask($site_sub_task);

            return $saving_process_result;

        // }catch(\Exception $e){

        //     $process_result['site_id'] = $request->site_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'Site Controller -> Site Saving Process <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

    private function getSiteSubTaskArray($request){

        $site_sub_task['sub_task_id'] = $request->sub_task_id;
        $site_sub_task['sub_task_name'] = $request->sub_task_name;
        $site_sub_task['site_id'] = $request->site_id;
        $site_sub_task['task_id'] = $request->task_id;
        $site_sub_task['start_date'] = $request->start_date;
        $site_sub_task['end_date'] = $request->end_date;
        $site_sub_task['active'] = $request->active;
        $site_sub_task['sub_contract'] = $request->sub_contract;
        $site_sub_task['status_id'] = $request->status_id;
        $site_sub_task['remark'] = $request->remark;

        if( SiteSubTask::where('sub_task_id', $request->sub_task_id)->exists() ){

            $site_sub_task['updated_by'] = Auth::id();
            $site_sub_task['updated_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }else{

            $site_sub_task['saved_by'] = Auth::id();
            $site_sub_task['saved_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        return $site_sub_task;
    }

    public function openSubTask(Request $request){

        $process_result['sub_task_id'] = $request->open_sub_task_id;
        $process_result['validation_result'] = TRUE;
        $process_result['process_status'] = TRUE;
        $process_result['validation_messages'] =  new MessageBag();
        $process_result['front_end_message'] = 'Open';
        $process_result['back_end_message'] =  '';

        $data['site'] = Site::where('active', 1)->get();
        $data['task'] = SiteTask::where('site_id', $request->open_site_no)->get();
        $data['status'] = Status::where('active', 1)->get();

        $data['attributes'] = $this->getSiteSubTaskAttributes($process_result, $request);

        return view('SiteMM.SiteForcast.site_sub_task')->with('SubSite', $data);
    }

}
