<?php

namespace App\Http\Controllers\SiteMM\InquiryList;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SiteMM\Master\Site;
use App\Models\SiteMM\Master\Item;
use App\Models\SiteMM\Master\Unit;
use App\Models\SiteMM\SiteForcast\SiteTask;
use App\Models\SiteMM\SiteForcast\SiteSubTask;
use App\Models\SiteMM\SiteForcast\SiteMaterials;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Carbon;

class SiteTaskSubTaskController extends Controller {

    public function loadView(){

        $data['site'] = Site::where('active', 1)->get();
        $data['item'] = Item::where('active', 1)->get();
        $data['site_task'] = array();
        $data['site_sub_task'] = array();
        $data['attributes'] = $this->getSiteTaskSubTaskInquiryAttributes(NULL, NULL);

        return view('SiteMM.InquiryList.site_task_subtask')->with('stsil', $data);
    }

    private function getSiteTaskSubTaskInquiryAttributes($process, $request){

        $attributes['site_id'] = '0';
        $attributes['task_id'] = '0';
        $attributes['sub_task_id'] = '0';
        $attributes['active'] = '1';
        $attributes['source'] = 'site';
        $attributes['stsil_detail'] = Site::get();

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        $attributes['site_id'] = $request->site_id;
        $attributes['active'] = $request->active;

        if( ($request->site_id != "0") && ($request->task_id == "0") ){

            $attributes['task_id'] = $request->task_id;
            $attributes['source'] = 'task';
            $attributes['stsil_detail'] = SiteTask::where('site_id', $request->site_id)->where('active', $request->active)->get();
        }

        if( ($request->site_id != "0") && ($request->task_id != "0") ){

            $attributes['task_id'] = $request->task_id;
            $attributes['source'] = 'sub-task';
            $attributes['stsil_detail'] = SiteSubTask::where('site_id', $request->site_id)->where('task_id', $request->task_id)->where('active', $request->active)->get();
        }

        return $attributes;
    }

    public function processSiteTaskSubTaskInquire(Request $request){

        $data['site'] = Site::where('active', 1)->get();
        $data['site_task'] = SiteTask::where('site_id', $request->site_id)->where('active', 1)->get();
        $data['site_sub_task'] = SiteSubTask::where('site_id', $request->site_id)->where('task_id', $request->task_id)->where('active', 1)->get();
        $data['attributes'] = $this->getSiteTaskSubTaskInquiryAttributes(array(), $request);

        return view('SiteMM.InquiryList.site_task_subtask')->with('stsil', $data);
    }


}
