<?php

namespace App\Http\Controllers\SiteMM\SiteOperation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SiteMM\Master\Site;
use App\Models\SiteMM\Master\Item;
use App\Models\SiteMM\Master\Unit;
use App\Models\SiteMM\SiteForcast\SiteTask;
use App\Models\SiteMM\SiteForcast\SiteSubTask;
use App\Models\SiteMM\SiteOperation\ItemIssueNote;
use App\Models\SiteMM\SiteOperation\ItemIssueNoteDetail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Carbon;

use App\Rules\ZeroValidation;
use App\Rules\CurrencyValidation;
use App\Rules\SiteMM\SiteOperation\ItemIssueNoteCancelValidation;

use App\Helpers\Common\InputHelper;

class ItemIssueNoteController extends Controller {

    public function loadView(){

        $data['site'] = Site::where('active', 1)->get();
        $data['item'] = Item::where('active', 1)->get();
        $data['site_task'] = array();
        $data['site_sub_task'] = array();
        $data['attributes'] = $this->getItemIssueNoteAttributes(NULL, NULL);

        return view('SiteMM.SiteOperation.item_issue_note')->with('IIN', $data);
    }

    private function getItemIssueNoteAttributes($process, $request){

        $attributes['iin_id'] = '#Auto#';
        $attributes['iin_date'] = Carbon::today()->toDateString();
        $attributes['site_id'] = '0';
        $attributes['task_id'] = '0';
        $attributes['sub_task_id'] = '0';
        $attributes['remark'] = '';
        $attributes['iin_detail'] = array();
        $attributes['iin_total'] = 0;

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        if( ($process['validation_result'] == TRUE) && ($process['process_status'] == TRUE)){

            $elqIin = ItemIssueNote::where('iin_id', $process['iin_id'] )->first();
            $elqIinDetail = ItemIssueNoteDetail::where('iin_id', $process['iin_id'])->get();
            $iin_total = 0;

            foreach($elqIinDetail as $key => $value){

                $unit_id = Item::where('item_id', $value->item_id)->value('unit_id');

                $value->ono = ($key+1);
                $value->unit_name = Unit::where('unit_id', $unit_id)->value('unit_name');
                $value->item_name = Item::where('item_id', $value->item_id)->value('item_name');
                $iin_total = $iin_total + $value->amount;
            }

            $attributes['iin_id'] = $elqIin->iin_id;
            $attributes['iin_date'] = $elqIin->iin_date;
            $attributes['site_id'] = $elqIin->site_id;
            $attributes['task_id'] = $elqIin->task_id;
            $attributes['sub_task_id'] = $elqIin->sub_task_id;
            $attributes['remark'] = $elqIin->remark;
            $attributes['iin_detail'] = $elqIinDetail;
            $attributes['iin_total'] = $elqIinDetail->sum('amount');

            $attributes['validation_messages'] = $process['validation_messages'];

            if($request->submit == 'Display'){

                $attributes['validation_messages'] = new MessageBag();
                $attributes['process_message'] = "";
            }else{

                $message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
                $attributes['process_message'] = '<div class="alert alert-success" role="alert"> '. $message .' </div> ';
            }

        }else{

            $inputs = $request->input();
            if(is_null($inputs) == FALSE){

                $attributes['iin_id'] = $inputs['iin_id'];
                $attributes['iin_date'] = $inputs['iin_date'];
                $attributes['site_id'] = $inputs['site_id'];
                $attributes['task_id'] = $inputs['task_id'];
                $attributes['sub_task_id'] = $inputs['sub_task_id'];
                $attributes['remark'] = $inputs['remark'];
            }

            $elqIinDetail = ItemIssueNoteDetail::where('iin_id', $inputs['iin_id'])->get();
            $iin_total = 0;
            foreach($elqIinDetail as $key => $value){

                $unit_id = Item::where('item_id', $value->item_id)->value('unit_id');

                $value->ono = ($key+1);
                $value->unit_name = Unit::where('unit_id', $unit_id)->value('unit_name');
                $value->item_name = Item::where('item_id', $value->item_id)->value('item_name');
                $iin_total = $iin_total + $value->amount;
            }

            $attributes['iin_detail'] = $elqIinDetail;
            $attributes['iin_total'] = $elqIinDetail->sum('amount');
            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';
        }

        return $attributes;
    }

    public function processItemIssueNote(Request $request){

        if($request->submit == 'Reset'){

            $data['attributes'] = $this->getItemIssueNoteAttributes(NULL, NULL);
        }

        if($request->submit == 'Add'){

            $iin_validation_result = $this->validateItemIssueNote($request);

            if($iin_validation_result['validation_result'] == TRUE){

                $saving_process_result = $this->addItem($request);

                $saving_process_result['validation_result'] = $iin_validation_result['validation_result'];
                $saving_process_result['validation_messages'] = $iin_validation_result['validation_messages'];

                $data['attributes'] = $this->getItemIssueNoteAttributes($saving_process_result, $request);

            }else{

                $iin_validation_result['process_status'] = FALSE;

                $data['attributes'] = $this->getItemIssueNoteAttributes($iin_validation_result, $request);
            }
        }

        if($request->submit == 'Cancel'){

            $iin_validation_result = $this->validateItemIssueNote($request);
            if($iin_validation_result['validation_result'] == TRUE){

                $saving_process_result = $this->cancelItemIssueNote($request);

                $saving_process_result['validation_result'] = TRUE;
                $saving_process_result['validation_messages'] = new MessageBag();

                $data['attributes'] = $this->getItemIssueNoteAttributes($saving_process_result, $request);

            }else{

                $iin_validation_result['process_status'] = FALSE;
                $data['attributes'] = $this->getItemIssueNoteAttributes($iin_validation_result, $request);
            }
        }

        $data['site'] = Site::where('active', 1)->get();
        $data['item'] = Item::where('active', 1)->get();
        $data['site_task'] = SiteTask::where('active', 1)->where('site_id', $request->site_id)->get();
        $data['site_sub_task'] = SiteSubTask::where('active', 1)->where('task_id', $request->task_id)->get();

        return view('SiteMM.SiteOperation.item_issue_note')->with('IIN', $data);
    }

    private function validateItemIssueNote($request){

        //try{

            $inputs['iin_id'] = $request->iin_id;
            $inputs['iin_date'] = $request->iin_date;
            $inputs['task_id'] = $request->task_id;
            $inputs['site_id'] = $request->site_id;
            $inputs['sub_task_id'] = $request->sub_task_id;
            $inputs['item_id'] = $request->item_id;
            $inputs['remark'] = $request->remark;
            $inputs['price'] = InputHelper::currencyToNumber($request->price);
            $inputs['quantity'] = $request->quantity;

            $rules['iin_id'] = array('required', new ItemIssueNoteCancelValidation('New'));
            $rules['iin_date'] = array('required', 'date');
            $rules['site_id'] = array( new ZeroValidation('Site', $request->site_id));
            $rules['task_id'] = array( new ZeroValidation('Task', $request->task_id));
            $rules['sub_task_id'] = array( new ZeroValidation('Sub Task', $request->sub_task_id));
            $rules['item_id'] = array( new ZeroValidation('Item', $request->item_id));
            $rules['remark'] = array( 'max:100');
            $rules['price'] = array('required', 'numeric', new CurrencyValidation(0));
            $rules['quantity'] = array('required', 'numeric', new CurrencyValidation(1));

            $front_end_message = '';

            if($request->submit == 'Cancel'){

                $i['iin_id'] = $request->iin_id;
                $r['iin_id'] = array('required', new ItemIssueNoteCancelValidation('Exist'));
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
            $process_result['back_end_message'] =  'Item Issue Note Controller - Validation Process ';

            return $process_result;

        // }catch(\Exception $e){

        //     $process_result['validation_result'] = FALSE;
        //     $process_result['validation_messages'] = new MessageBag();
        //     $process_result['front_end_message'] =  $e->getMessage();
        //     $process_result['back_end_message'] =  'Item Issue Note Controller - Validation Function Fault';

		// 	return $process_result;
        // }
    }

    private function addItem($request){

        //try{

            $objItemIssueNote = new ItemIssueNote();

            $iin['iin'] = $this->getItemIssueNoteArray($request);
            $iin['iin_detail'] = $this->getItemIssueNoteDetailArray($request);
            $saving_process_result = $objItemIssueNote->addItemIssueNote($iin);

            return $saving_process_result;

        // }catch(\Exception $e){

        //     $process_result['site_id'] = $request->site_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'Item Issue Note Controller -> Site Saving Process <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

    private function getItemIssueNoteArray($request){

        $iin['iin_id'] = $request->iin_id;
        $iin['iin_date'] = $request->iin_date;
        $iin['site_id'] = $request->site_id;
        $iin['task_id'] = $request->task_id;
        $iin['sub_task_id'] = $request->sub_task_id;
        $iin['total_amount'] = 0;
        $iin['remark'] = $request->remark;
        $iin['cancel'] = 0;

        if( $request->iin_id == '#Auto#' ){

            $iin['saved_by'] = Auth::id();
            $iin['saved_on'] = Carbon::now()->format('Y-m-d H:i:s');

        }else{

            $iin['updated_by'] = Auth::id();
            $iin['updated_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        return $iin;
    }

    private function getItemIssueNoteDetailArray($request){

        $iin_detail['iin_id'] = 0;
        $iin_detail['item_id'] = $request->item_id;
        $iin_detail['price'] = str_replace(",","",$request->price);
        $iin_detail['quantity'] = $request->quantity;
        $iin_detail['amount'] = floatval(str_replace(",","",$request->price)) * $request->quantity;

        if($request->iin_id == '#Auto#'){

            $iin_detail['saved_by'] = Auth::id();
            $iin_detail['saved_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }else{

            $iin_detail['saved_by'] = Auth::id();
            $iin_detail['saved_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        return $iin_detail;
    }

    private function cancelItemIssueNote($request){

        //try{

            $objItemIssueNote = new ItemIssueNote();

            $iin_cancel['iin_id'] = $request->iin_id;
            $iin_cancel['cancel'] = 1;
            $iin_cancel['cancel_by'] = Auth::id();
            $iin_cancel['cancel_on'] = Carbon::now()->format('Y-m-d H:i:s');

            $saving_process_result = $objItemIssueNote->cancelItemIssueNote($iin_cancel);

            return $saving_process_result;

        // }catch(\Exception $e){

        //     $process_result['iin_id'] = $request->iin_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'Payment Voucher Controller -> Payment Voucher Saving Process <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

    public function openItemIssueNote(Request $request){

        $elqIin = ItemIssueNote::where('iin_id', $request->iin_id )->first();
        $elqIinDetail = ItemIssueNoteDetail::where('iin_id', $request->iin_id)->get();
        $iin_total = 0;

        foreach($elqIinDetail as $key => $value){

            $unit_id = Item::where('item_id', $value->item_id)->value('unit_id');

            $value->ono = ($key+1);
            $value->unit_name = Unit::where('unit_id', $unit_id)->value('unit_name');
            $value->item_name = Item::where('item_id', $value->item_id)->value('item_name');
            $iin_total = $iin_total + $value->amount;
        }

        $attributes['iin_id'] = $elqIin->iin_id;
        $attributes['iin_date'] = $elqIin->iin_date;
        $attributes['site_id'] = $elqIin->site_id;
        $attributes['task_id'] = $elqIin->task_id;
        $attributes['sub_task_id'] = $elqIin->sub_task_id;
        $attributes['remark'] = $elqIin->remark;
        $attributes['iin_detail'] = $elqIinDetail;
        $attributes['iin_total'] = $elqIinDetail->sum('amount');

        $attributes['validation_messages'] = new MessageBag();
        $attributes['process_message'] = "";

        $data['attributes'] = $attributes;
        $data['site'] = Site::where('active', 1)->get();
        $data['item'] = Item::where('active', 1)->get();
        $data['site_task'] = SiteTask::where('active', 1)->where('site_id', $elqIin->site_id)->get();
        $data['site_sub_task'] = SiteSubTask::where('active', 1)->where('task_id', $elqIin->task_id)->get();

        return view('SiteMM.SiteOperation.item_issue_note')->with('IIN', $data);
    }


}
