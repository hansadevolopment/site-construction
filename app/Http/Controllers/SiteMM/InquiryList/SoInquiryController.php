<?php

namespace App\Http\Controllers\SiteMM\InquiryList;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SiteMM\Master\Site;
use App\Models\SiteMM\Master\Item;
use App\Models\SiteMM\Master\Unit;

use App\Models\SiteMM\SiteForcast\SiteLabour;
use App\Models\SiteMM\SiteForcast\SiteTask;
use App\Models\SiteMM\SiteForcast\SiteSubTask;
use App\Models\SiteMM\SiteForcast\SiteProfit;

use App\Models\SiteMM\SiteOperation\SoInquire;

use App\Models\SiteMM\SiteOperation\ItemIssueNote;
use App\Models\SiteMM\SiteOperation\PaymentVoucher;
use App\Models\SiteMM\SiteOperation\EmployeeAdvance;
use App\Models\SiteMM\SiteOperation\EmployeeSalary;
use App\Models\SiteMM\SiteOperation\DailyProgress;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Carbon;

class SoInquiryController extends Controller {

    public function loadView(){

        $data['site'] = Site::where('active', 1)->get();
        $data['site_task'] = array();
        $data['site_sub_task'] = array();
        $data['so_inquire'] = SoInquire::get();
        $data['so_detail'] = array();
        $data['cost_section'] = '';
        $data['attributes'] = $this->getSiteOperationInquiryAttributes(NULL, NULL);

        return view('SiteMM.InquiryList.so_inquire')->with('so_inq', $data);
    }

    private function getSiteOperationInquiryAttributes($process, $request){

        $attributes['site_id'] = '0';
        $attributes['task_id'] = '0';
        $attributes['sub_task_id'] = '0';
        $attributes['cs_id'] = '0';
        $attributes['from_date'] = '';
        $attributes['to_date'] = '';

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

    public function getInquireSiteOperationResults(Request $request){

        $result = array();

        if($request->cs_id == "1"){

            $elqItemIssueNote = ItemIssueNote::get();

            if( (is_null($request->from_date) == false) &&  (is_null($request->to_date) == false) ){

                $elqItemIssueNote = $elqItemIssueNote->whereBetween('iin_date', [$request->from_date, $request->to_date]);
            }

            if( ($request->site_id != "0") && ($request->task_id == "0") && ($request->sub_task_id == "0") ){

                $elqItemIssueNote = $elqItemIssueNote->where('site_id', $request->site_id);

            }elseif( ($request->site_id != "0") && ($request->task_id != "0") && ($request->sub_task_id == "0") ){

                $elqItemIssueNote = $elqItemIssueNote->where('site_id', $request->site_id)->where('task_id', $request->task_id);

            }elseif( ($request->site_id != "0") && ($request->task_id != "0") && ($request->sub_task_id != "0") ){

                $elqItemIssueNote = $elqItemIssueNote->where('site_id', $request->site_id)->where('task_id', $request->task_id)->where('sub_task_id', $request->sub_task_id);

            }else{
            }

            $data['cost_section'] = 'material';
            $data['so_detail'] = $elqItemIssueNote;
        }

        if($request->cs_id == "2"){

            $elqPaymentVoucher = PaymentVoucher::get();

            if( (is_null($request->from_date) == false) &&  (is_null($request->to_date) == false) ){

                $elqPaymentVoucher = $elqPaymentVoucher->whereBetween('pv_date', [$request->from_date, $request->to_date]);
            }

            if( ($request->site_id != "0") && ($request->task_id == "0") && ($request->sub_task_id == "0") ){

                $elqPaymentVoucher = $elqPaymentVoucher->where('site_id', $request->site_id);

            }elseif( ($request->site_id != "0") && ($request->task_id != "0") && ($request->sub_task_id == "0") ){

                $elqPaymentVoucher = $elqPaymentVoucher->where('site_id', $request->site_id)->where('task_id', $request->task_id);

            }elseif( ($request->site_id != "0") && ($request->task_id != "0") && ($request->sub_task_id != "0") ){

                $elqPaymentVoucher = $elqPaymentVoucher->where('site_id', $request->site_id)->where('task_id', $request->task_id)->where('sub_task_id', $request->sub_task_id);

            }else{
            }

            $data['cost_section'] = 'overhead';
            $data['so_detail'] = $elqPaymentVoucher;
        }


        if($request->cs_id == "3"){

            $elqEmployeeAdvance = EmployeeAdvance::get();

            if( (is_null($request->from_date) == false) &&  (is_null($request->to_date) == false) ){

                $elqEmployeeAdvance = $elqEmployeeAdvance->whereBetween('ea_date', [$request->from_date, $request->to_date]);
            }

            if( ($request->site_id != "0") && ($request->task_id == "0") && ($request->sub_task_id == "0") ){

                $elqEmployeeAdvance = $elqEmployeeAdvance->where('site_id', $request->site_id);

            }elseif( ($request->site_id != "0") && ($request->task_id != "0") && ($request->sub_task_id == "0") ){

                $elqEmployeeAdvance = $elqEmployeeAdvance->where('site_id', $request->site_id)->where('task_id', $request->task_id);

            }elseif( ($request->site_id != "0") && ($request->task_id != "0") && ($request->sub_task_id != "0") ){

                $elqEmployeeAdvance = $elqEmployeeAdvance->where('site_id', $request->site_id)->where('task_id', $request->task_id)->where('sub_task_id', $request->sub_task_id);

            }else{
            }

            $data['cost_section'] = 'employee_advance';
            $data['so_detail'] = $elqEmployeeAdvance;
        }


        if($request->cs_id == "4"){

            $elqEmployeeSalary = EmployeeSalary::get();

            if( (is_null($request->from_date) == false) &&  (is_null($request->to_date) == false) ){

                $elqEmployeeSalary = $elqEmployeeSalary->whereBetween('es_date', [$request->from_date, $request->to_date]);
            }

            if( ($request->site_id != "0") && ($request->task_id == "0") && ($request->sub_task_id == "0") ){

                $elqEmployeeSalary = $elqEmployeeSalary->where('site_id', $request->site_id);

            }elseif( ($request->site_id != "0") && ($request->task_id != "0") && ($request->sub_task_id == "0") ){

                $elqEmployeeSalary = $elqEmployeeSalary->where('site_id', $request->site_id)->where('task_id', $request->task_id);

            }elseif( ($request->site_id != "0") && ($request->task_id != "0") && ($request->sub_task_id != "0") ){

                $elqEmployeeSalary = $elqEmployeeSalary->where('site_id', $request->site_id)->where('task_id', $request->task_id)->where('sub_task_id', $request->sub_task_id);

            }else{
            }

            $data['cost_section'] = 'employee_salary';
            $data['so_detail'] = $elqEmployeeSalary;
        }

        if($request->cs_id == "5"){

            $elqDailyProgress = DailyProgress::get();

            if( (is_null($request->from_date) == false) &&  (is_null($request->to_date) == false) ){

                $elqDailyProgress = $elqDailyProgress->whereBetween('dpr_date', [$request->from_date, $request->to_date]);
            }

            if( ($request->site_id != "0") && ($request->task_id == "0") && ($request->sub_task_id == "0") ){

                $elqDailyProgress = $elqDailyProgress->where('site_id', $request->site_id);

            }elseif( ($request->site_id != "0") && ($request->task_id != "0") && ($request->sub_task_id == "0") ){

                $elqDailyProgress = $elqDailyProgress->where('site_id', $request->site_id)->where('task_id', $request->task_id);

            }elseif( ($request->site_id != "0") && ($request->task_id != "0") && ($request->sub_task_id != "0") ){

                $elqDailyProgress = $elqDailyProgress->where('site_id', $request->site_id)->where('task_id', $request->task_id)->where('sub_task_id', $request->sub_task_id);

            }else{
            }

            $data['cost_section'] = 'daily_progress';
            $data['so_detail'] = $elqDailyProgress;
        }

        $data['site'] = Site::where('active', 1)->get();
        $data['item'] = Item::where('active', 1)->get();
        $data['site_task'] = SiteTask::where('active', 1)->where('site_id', $request->site_id)->get();
        $data['site_sub_task'] = SiteSubTask::where('active', 1)->where('site_id', $request->site_id)->where('task_id', $request->task_id)->get();
        $data['so_inquire'] = SoInquire::get();
        $data['attributes'] = $this->getSiteOperationInquiryAttributes(NULL, $request);

        return view('SiteMM.InquiryList.so_inquire')->with('so_inq', $data);
    }

}
