<?php

namespace App\Http\Controllers\SiteMM\SiteOperation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SiteMM\Master\Site;
use App\Models\SiteMM\Master\Item;
use App\Models\SiteMM\Master\Unit;

use App\Models\SiteMM\Master\CostSection;
use App\Models\SiteMM\Master\OverheadCostItem;
use App\Models\SiteMM\SiteForcast\SiteTask;
use App\Models\SiteMM\SiteForcast\SiteSubTask;
use App\Models\SiteMM\SiteOperation\DailyProgress;
use App\Models\SiteMM\SiteOperation\DailyProgressDetail;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Carbon;

use App\Rules\ZeroValidation;
use App\Rules\CurrencyValidation;
use App\Rules\SiteMM\SiteOperation\DprCancelValidation;

use App\Helpers\Common\InputHelper;

class DailyProgressReportController extends Controller {

    public function loadView(){

        $data['site'] = Site::where('active', 1)->get();
        $data['cost_section'] = CostSection::where('active', 1)->whereIn('cs_id', [1, 3])->get();
        $data['item'] = array();
        $data['site_task'] = array();
        $data['site_sub_task'] = array();
        $data['attributes'] = $this->getDailyProgressReportAttributes(NULL, NULL);

        return view('SiteMM.SiteOperation.dpr')->with('DPR', $data);
    }

    private function getDailyProgressReportAttributes($process, $request){

        $attributes['dpr_id'] = '#Auto#';
        $attributes['dpr_date'] = Carbon::today()->toDateString();
        $attributes['cs_id'] = '0';
        $attributes['site_id'] = '0';
        $attributes['task_id'] = '0';
        $attributes['sub_task_id'] = '0';
        $attributes['remark'] = '';
        $attributes['item_id'] = '0';
        $attributes['unit'] = '';
        $attributes['price'] = 0;
        $attributes['quantity'] = 0;
        $attributes['dpr_detail'] = array();
        $attributes['dpr_total'] = 0;

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        if( ($process['validation_result'] == TRUE) && ($process['process_status'] == TRUE)){

            $elqDpr = DailyProgress::where('dpr_id', $process['dpr_id'] )->first();
            $elqDprDetail = DailyProgressDetail::where('dpr_id', $process['dpr_id'])->get();
            $dpr_total = 0;

            foreach($elqDprDetail as $key => $value){

                $unit_id = Item::where('item_id', $value->item_id)->value('unit_id');

                $value->ono = ($key+1);
                $value->unit_name = Unit::where('unit_id', $unit_id)->value('unit_name');
                if( $elqDpr->cs_id == 1 ){
                    $value->item_name = Item::where('item_id', $value->item_id)->value('item_name');
                }else{
                    $value->item_name = OverheadCostItem::where('oci_id', $value->item_id)->value('oci_name');
                }
                $dpr_total = $dpr_total + $value->amount;
            }

            $attributes['dpr_id'] = $elqDpr->dpr_id;
            $attributes['dpr_date'] = $elqDpr->dpr_date;
            $attributes['cs_id'] = $elqDpr->cs_id;
            $attributes['site_id'] = $elqDpr->site_id;
            $attributes['task_id'] = $elqDpr->task_id;
            $attributes['sub_task_id'] = $elqDpr->sub_task_id;
            $attributes['remark'] = $elqDpr->remark;
            $attributes['item_id'] = '0';
            $attributes['unit'] = '';
            $attributes['price'] = 0;
            $attributes['quantity'] = 0;
            $attributes['dpr_detail'] = $elqDprDetail;
            $attributes['dpr_total'] = $elqDprDetail->sum('amount');

            $attributes['validation_messages'] = $process['validation_messages'];

            if($request->submit == 'Cancel'){

                $message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
                $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';

            }else{

                $message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
                $attributes['process_message'] = '<div class="alert alert-success" role="alert"> '. $message .' </div> ';
            }

        }else{

            $inputs = $request->input();
            if(is_null($inputs) == FALSE){

                $attributes['dpr_id'] = $inputs['dpr_id'];
                $attributes['dpr_date'] = $inputs['dpr_date'];
                $attributes['cs_id'] = $inputs['cs_id'];
                $attributes['site_id'] = $inputs['site_id'];
                $attributes['task_id'] = $inputs['task_id'];
                $attributes['sub_task_id'] = $inputs['sub_task_id'];
                $attributes['remark'] = $inputs['remark'];
                $attributes['item_id'] = $inputs['item_id'];
                $attributes['unit'] = $inputs['unit'];
                $attributes['price'] = $inputs['price'];
                $attributes['quantity'] = $inputs['quantity'];
            }

            $elqDprDetail = DailyProgressDetail::where('dpr_id', $request->dpr_id)->get();
            $dpr_total = 0;
            foreach($elqDprDetail as $key => $value){

                $value->ono = ($key+1);

                if( $attributes['cs_id'] == 1 ){

                    $unit_id = Item::where('item_id', $value->item_id)->value('unit_id');
                    $value->item_name = Item::where('item_id', $value->item_id)->value('item_name');
                }else{

                    $unit_id = OverheadCostItem::where('oci_id', $value->item_id)->value('unit_id');
                    $value->item_name = OverheadCostItem::where('oci_id', $value->item_id)->value('oci_name');
                }

                $value->unit_name = Unit::where('unit_id', $unit_id)->value('unit_name');
                $dpr_total = $dpr_total + $value->amount;
            }

            $attributes['dpr_detail'] = $elqDprDetail;
            $attributes['dpr_total'] = $elqDprDetail->sum('amount');
            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';
        }

        return $attributes;
    }

    public function processDPR(Request $request){

        if($request->submit == 'Reset'){
            $data['attributes'] = $this->getDailyProgressReportAttributes(NULL, NULL);
        }

        if($request->submit == 'Add'){

            $dpr_validation_result = $this->validateDPR($request);
            if($dpr_validation_result['validation_result'] == TRUE){

                $saving_process_result = $this->addDailyProgress($request);

                $saving_process_result['validation_result'] = $dpr_validation_result['validation_result'];
                $saving_process_result['validation_messages'] = $dpr_validation_result['validation_messages'];

                $data['attributes'] = $this->getDailyProgressReportAttributes($saving_process_result, $request);

            }else{

                $dpr_validation_result['process_status'] = FALSE;
                $data['attributes'] = $this->getDailyProgressReportAttributes($dpr_validation_result, $request);
            }
        }

        if($request->submit == 'Cancel'){

            $dpr_validation_result = $this->validateDPR($request);
            if($dpr_validation_result['validation_result'] == TRUE){

                $saving_process_result = $this->cancelDailyProgress($request);

                $saving_process_result['validation_result'] = TRUE;
                $saving_process_result['validation_messages'] = new MessageBag();

                $data['attributes'] = $this->getDailyProgressReportAttributes($saving_process_result, $request);

            }else{

                $dpr_validation_result['process_status'] = FALSE;
                $data['attributes'] = $this->getDailyProgressReportAttributes($dpr_validation_result, $request);
            }
        }

        if( $request->cs_id == 1 ){
            $data['item'] = Item::where('active', 1)->get();
        }elseif( $request->cs_id == 3 ){
            $data['item'] = OverheadCostItem::where('active', 1)->get();
        }else{
        }

        $data['site'] = Site::where('active', 1)->get();
        $data['cost_section'] = CostSection::where('active', 1)->whereIn('cs_id', [1, 3])->get();
        $data['site_task'] = SiteTask::where('active', 1)->where('site_id', $request->site_id)->get();
        $data['site_sub_task'] = SiteSubTask::where('active', 1)->where('task_id', $request->task_id)->get();

        return view('SiteMM.SiteOperation.dpr')->with('DPR', $data);
    }

    private function validateDPR($request){

        //try{

            $inputs['dpr_id'] = $request->dpr_id;
            $inputs['dpr_date'] = $request->dpr_date;
            $inputs['cs_id'] = $request->cs_id;
            $inputs['site_id'] = $request->site_id;
            $inputs['task_id'] = $request->task_id;
            $inputs['sub_task_id'] = $request->sub_task_id;
            $inputs['item_id'] = $request->item_id;
            $inputs['price'] = InputHelper::currencyToNumber($request->price);
            $inputs['quantity'] = $request->quantity;
            $inputs['remark'] = $request->remark;

            $rules['dpr_id'] = array('required', new DprCancelValidation('save'));
            $rules['dpr_date'] = array('required', 'date');
            $rules['cs_id'] = array( new ZeroValidation('Cost Section', $request->cs_id));
            $rules['site_id'] = array( new ZeroValidation('Site', $request->site_id));
            $rules['task_id'] = array( new ZeroValidation('Task', $request->task_id));
            $rules['sub_task_id'] = array( new ZeroValidation('Sub Task', $request->sub_task_id));
            if( ($request->cs_id == 1)  || ($request->cs_id == 0) ){
                $rules['item_id'] = array( new ZeroValidation('Item', $request->item_id));
            }elseif( $request->cs_id == 3 ){
                $rules['item_id'] = array( new ZeroValidation('Overhead Cost Item', $request->item_id));
            }
            $rules['price'] = array('required', 'numeric', new CurrencyValidation(1));
            $rules['quantity'] = array('required', 'numeric', new CurrencyValidation(1));
            $rules['remark'] = array( 'max:100');

            $front_end_message = '';

            if($request->submit == 'Cancel'){

                $i['dpr_id'] = $request->dpr_id;
                $r['dpr_id'] = array('required', new DprCancelValidation('cancel'));

                $validator = Validator::make($i, $r);

            }else{

                $validator = Validator::make($inputs, $rules);
            }

            $validation_result = $validator->passes();
            if($validation_result == FALSE){

                $front_end_message = 'Please Check Your Inputs';
            }

            $process_result['validation_result'] = $validator->passes();
            $process_result['validation_messages'] =  $validator->errors();
            $process_result['front_end_message'] = $front_end_message;
            $process_result['back_end_message'] =  'Daily Progress Report Controller - Validation Process ';

            return $process_result;

        // }catch(\Exception $e){

        //     $process_result['validation_result'] = FALSE;
        //     $process_result['validation_messages'] = new MessageBag();
        //     $process_result['front_end_message'] =  $e->getMessage();
        //     $process_result['back_end_message'] =  'Daily Progress Report Controller - Validation Function Fault';

		// 	return $process_result;
        // }
    }

    private function addDailyProgress($request){

        //try{

            $objDailyProgress = new DailyProgress();

            $dpr['dpr'] = $this->getDailyProgressArray($request);
            $dpr['dpr_detail'] = $this->getDailyProgressDetailArray($request);
            $saving_process_result = $objDailyProgress->addDailyProgressNote($dpr);

            return $saving_process_result;

        // }catch(\Exception $e){

        //     $process_result['dpr_id'] = $request->dpr_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'Daily Progress Report Controller -> DPR Saving Process <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

    private function getDailyProgressArray($request){

        $dpr['dpr_id'] = $request->dpr_id;
        $dpr['dpr_date'] = $request->dpr_date;
        $dpr['cs_id'] = $request->cs_id;
        $dpr['site_id'] = $request->site_id;
        $dpr['task_id'] = $request->task_id;
        $dpr['sub_task_id'] = $request->sub_task_id;
        $dpr['total_amount'] = 0;
        $dpr['remark'] = $request->remark;
        $dpr['cancel'] = 0;

        if( $request->dpr_id == '#Auto#' ){

            $dpr['saved_by'] = Auth::id();
            $dpr['saved_on'] = Carbon::now()->format('Y-m-d H:i:s');

        }else{

            $dpr['updated_by'] = Auth::id();
            $dpr['updated_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        return $dpr;
    }

    private function getDailyProgressDetailArray($request){

        $dpr_detail['dpr_id'] = 0;
        $dpr_detail['item_id'] = $request->item_id;
        $dpr_detail['price'] = str_replace(",","",$request->price);
        $dpr_detail['quantity'] = $request->quantity;
        $dpr_detail['amount'] = floatval(str_replace(",","",$request->price)) * $request->quantity;

        if($request->dpr_id == '#Auto#'){

            $dpr_detail['saved_by'] = Auth::id();
            $dpr_detail['saved_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }else{

            $dpr_detail['saved_by'] = Auth::id();
            $dpr_detail['saved_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        return $dpr_detail;
    }

    private function cancelDailyProgress($request){

        //try{

            $objDailyProgress = new DailyProgress();

            $dpr_cancel['dpr_id'] = $request->dpr_id;
            $dpr_cancel['cancel'] = 1;
            $dpr_cancel['cancel_by'] = Auth::id();
            $dpr_cancel['cancel_on'] = Carbon::now()->format('Y-m-d H:i:s');

            $saving_process_result = $objDailyProgress->cancelDailyProgressNote($dpr_cancel);

            return $saving_process_result;

        // }catch(\Exception $e){

        //     $process_result['dpr_id'] = $request->dpr_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'Daily Progress Report Controller -> DPR Saving Process <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

    public function openDPR(Request $request){

        $elqDailyProgress = DailyProgress::where('dpr_id', $request->open_dpr_id )->first();
        if( is_null($elqDailyProgress) == FALSE ){

            $attributes['dpr_id'] = $elqDailyProgress->dpr_id;
            $attributes['dpr_date'] = $elqDailyProgress->dpr_date;
            $attributes['cs_id'] = $elqDailyProgress->cs_id;
            $attributes['site_id'] = $elqDailyProgress->site_id;
            $attributes['task_id'] = $elqDailyProgress->task_id;
            $attributes['sub_task_id'] = $elqDailyProgress->sub_task_id;
            $attributes['remark'] = $elqDailyProgress->remark;
            $attributes['item_id'] = '0';
            $attributes['unit'] = '';
            $attributes['price'] = 0;
            $attributes['quantity'] = 0;
            $attributes['dpr_total'] = $elqDailyProgress->dpr_total;
            $attributes['dpr_detail'] = $elqDailyProgress->getDprDetail;

            $attributes['validation_messages'] = new MessageBag();;
            $attributes['process_message'] = '';

            $dpr_total = 0;
            foreach($elqDailyProgress->getDprDetail as $key => $value){

                $unit_id = Item::where('item_id', $value->item_id)->value('unit_id');

                $value->ono = ($key+1);
                $value->unit_name = Unit::where('unit_id', $unit_id)->value('unit_name');
                if( $elqDailyProgress->cs_id == 1 ){
                    $value->item_name = Item::where('item_id', $value->item_id)->value('item_name');
                }else{
                    $value->item_name = OverheadCostItem::where('oci_id', $value->item_id)->value('oci_name');
                }
                $dpr_total = $dpr_total + $value->amount;
            }
        }

        $data['attributes'] = $attributes;
        $data['item'] = array();
        $data['site'] = Site::where('active', 1)->get();
        $data['site_task'] = SiteTask::where('active', 1)->where('site_id', $attributes['task_id'])->get();
        $data['site_sub_task'] = SiteSubTask::where('active', 1)->where('task_id', $attributes['sub_task_id'])->get();
        $data['cost_section'] = CostSection::where('active', 1)->whereIn('cs_id', [1, 3])->get();

        return view('SiteMM.SiteOperation.dpr')->with('DPR', $data);
    }

}
