<?php

namespace App\Http\Controllers\SiteMM\InquiryList;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SiteMM\Master\Site;
use App\Models\SiteMM\Master\Item;
use App\Models\SiteMM\Master\Unit;
use App\Models\SiteMM\Master\CostSection;

use App\Models\SiteMM\SiteForcast\SiteLabour;
use App\Models\SiteMM\SiteForcast\SiteTask;
use App\Models\SiteMM\SiteForcast\SiteSubTask;
use App\Models\SiteMM\SiteForcast\SiteMaterials;
use App\Models\SiteMM\SiteForcast\SiteOverheadCost;
use App\Models\SiteMM\SiteForcast\SiteProfit;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Carbon;

class SapInquiryController extends Controller {

    public function loadView(){

        $data['site'] = Site::where('active', 1)->get();
        $data['site_task'] = array();
        $data['site_sub_task'] = array();
        $data['cost_section'] = CostSection::all();
        $data['stsil_detail'] = array();
        $data['source'] = '';
        $data['source_name'] = 'Name';
        $data['attributes'] = $this->getSapInquiryAttributes(NULL, NULL);

        return view('SiteMM.InquiryList.sap_inquire')->with('stsil', $data);
    }

    private function getSapInquiryAttributes($process, $request){

        $attributes['site_id'] = '0';
        $attributes['task_id'] = '0';
        $attributes['sub_task_id'] = '0';
        $attributes['cs_id'] = '0';
        $attributes['source'] = '';

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        $inputs = $request->input();
        if(is_null($inputs) == FALSE){

            $attributes['site_id'] = $inputs['site_id'];
            $attributes['task_id'] = $inputs['task_id'];
            $attributes['sub_task_id'] = $inputs['sub_task_id'];
            $attributes['cs_id'] = $inputs['cs_id'];
        }

        return $attributes;
    }

    public function inquireSiteActionPlan(Request $request){

        $result = array();

        if( ($request->site_id != "0") && ($request->task_id == "0") && ($request->sub_task_id == "0") ){

            $data['source'] = 'site';
            if($request->cs_id == "1"){

                $result = SiteMaterials::where('site_id', $request->site_id)->get();
                $result->cost_section = "material";
                $result->class_name = "sap-material";
                $data['source_name']  = "Item Name";
            }

            if($request->cs_id == "2"){

                $result = SiteLabour::where('site_id', $request->site_id)->get();
                $result->cost_section = "labour";
                $result->class_name = "sap-labour";
                $data['source_name']  = "Labour Category";
            }

            if($request->cs_id == "3"){

                $result = SiteOverheadCost::where('site_id', $request->site_id)->get();
                $result->cost_section = "overhead";
                $result->class_name = "sap-overhead";
                $data['source_name']  = "Overhead Cost";
            }

            if($request->cs_id == "4"){

                $result = SiteProfit::where('site_id', $request->site_id)->get();
                $result->cost_section = "profit";
                $result->class_name = "sap-profit";
                $data['source_name']  = "profit";
            }

        }elseif( ($request->site_id != "0") && ($request->task_id != "0") && ($request->sub_task_id == "0") ){

            $data['source'] = 'task';
            if($request->cs_id == "1"){

                $result = SiteMaterials::where('site_id', $request->site_id)->where('task_id', $request->task_id)->get();
                $result->cost_section = "material";
                $result->class_name = "sap-material";
                $data['source_name']  = "material";
            }

            if($request->cs_id == "2"){

                $result = SiteLabour::where('site_id', $request->site_id)->where('task_id', $request->task_id)->get();
                $result->cost_section = "labour";
                $result->class_name = "sap-labour";
                $data['source_name']  = "labour";
            }

            if($request->cs_id == "3"){

                $result = SiteOverheadCost::where('site_id', $request->site_id)->where('task_id', $request->task_id)->get();
                $result->cost_section = "overhead";
                $result->class_name = "sap-overhead";
                $data['source_name']  = "overhead";
            }

            if($request->cs_id == "4"){

                $result = SiteProfit::where('site_id', $request->site_id)->where('task_id', $request->task_id)->get();
                $result->cost_section = "profit";
                $result->class_name = "sap-profit";
                $data['source_name']  = "profit";
            }

        }elseif( ($request->site_id != "0") && ($request->task_id != "0") && ($request->sub_task_id != "0") ){

            $data['source'] = 'sub_task';
            if($request->cs_id == "1"){

                $result = SiteMaterials::where('site_id', $request->site_id)->where('task_id', $request->task_id)->where('sub_task_id', $request->sub_task_id)->get();
                $result->cost_section = "material";
                $result->class_name = "sap-material";
                $data['source_name']  = "material";
            }

            if($request->cs_id == "2"){

                $result = SiteLabour::where('site_id', $request->site_id)->where('task_id', $request->task_id)->where('sub_task_id', $request->sub_task_id)->get();
                $result->cost_section = "labour";
                $result->class_name = "sap-labour";
                $data['source_name']  = "labour";
            }

            if($request->cs_id == "3"){

                $result = SiteOverheadCost::where('site_id', $request->site_id)->where('task_id', $request->task_id)->where('sub_task_id', $request->sub_task_id)->get();
                $result->cost_section = "overhead";
                $result->class_name = "sap-overhead";
                $data['source_name']  = "overhead";
            }

            if($request->cs_id == "4"){

                $result = SiteProfit::where('site_id', $request->site_id)->where('task_id', $request->task_id)->where('sub_task_id', $request->sub_task_id)->get();
                $result->cost_section = "profit";
                $result->class_name = "sap-profit";
                $data['source_name']  = "profit";
                $data['source_name']  = "profit";
            }

        }else{

        }

        //dd( $result );

        $data['site'] = Site::where('active', 1)->get();
        $data['item'] = Item::where('active', 1)->get();
        $data['site_task'] = SiteTask::where('active', 1)->where('site_id', $request->site_id)->get();
        $data['site_sub_task'] = array();
        $data['stsil_detail'] = $result;
        $data['cost_section'] = CostSection::all();
        $data['attributes'] = $this->getSapInquiryAttributes(NULL, $request);

        return view('SiteMM.InquiryList.sap_inquire')->with('stsil', $data);
    }


}
