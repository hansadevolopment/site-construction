<?php

namespace App\Http\Controllers\SiteMM\SiteOperation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SiteMM\Master\Site;
use App\Models\SiteMM\Master\CostSection;
use App\Models\SiteMM\Master\OverheadCostItem;
use App\Models\SiteMM\Master\Unit;

use App\Models\SiteMM\SiteForcast\SiteTask;
use App\Models\SiteMM\SiteForcast\SiteSubTask;
use App\Models\SiteMM\SiteOperation\PaymentVoucher;
use App\Models\SiteMM\SiteOperation\PaymentVoucherDetail;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Carbon;

use App\Rules\ZeroValidation;
use App\Rules\CurrencyValidation;
use App\Rules\SiteMM\SiteOperation\PaymentVoucherCancelValidation;

use App\Helpers\Common\InputHelper;

class PaymentVoucherController extends Controller {

    public function loadView(){

        $data['site'] = Site::where('active', 1)->get();
        $data['cost_section'] = CostSection::where('active', 1)->whereIn('cs_id', [2, 3])->get();
        $data['site_task'] = array();
        $data['site_sub_task'] = array();
        $data['overhead_cost_item'] = OverheadCostItem::where('active', 1)->get();
        $data['attributes'] = $this->getPaymentVoucherAttributes(NULL, NULL);

        return view('SiteMM.SiteOperation.payment_voucher')->with('PV', $data);
    }

    private function getPaymentVoucherAttributes($process, $request){

        $attributes['pv_id'] = '#Auto#';
        $attributes['pv_date'] = Carbon::today()->toDateString();
        $attributes['cs_id'] = '0';
        $attributes['site_id'] = '0';
        $attributes['task_id'] = '0';
        $attributes['sub_task_id'] = '0';
        $attributes['remark'] = '';
        $attributes['pv_detail'] = array();
        $attributes['pv_total'] = 0;

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        if( ($process['validation_result'] == TRUE) && ($process['process_status'] == TRUE)){

            $elqPv = PaymentVoucher::where('pv_id', $process['pv_id'] )->first();
            $elqPvDetail = PaymentVoucherDetail::where('pv_id', $process['pv_id'])->get();
            $pv_total = 0;

            foreach($elqPvDetail as $key => $value){

                $unit_id = OverheadCostItem::where('oci_id', $value->oci_id)->value('unit_id');

                $value->ono = ($key+1);
                $value->unit_name = Unit::where('unit_id', $unit_id)->value('unit_name');
                $value->item_name = OverheadCostItem::where('oci_id', $value->oci_id)->value('oci_name');
                $pv_total = $pv_total + $value->amount;
            }

            $attributes['pv_id'] = $elqPv->pv_id;
            $attributes['pv_date'] = $elqPv->pv_date;
            $attributes['site_id'] = $elqPv->site_id;
            $attributes['task_id'] = $elqPv->task_id;
            $attributes['sub_task_id'] = $elqPv->sub_task_id;
            $attributes['remark'] = $elqPv->remark;
            $attributes['pv_detail'] = $elqPvDetail;
            $attributes['pv_total'] = $elqPvDetail->sum('amount');

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

                $attributes['pv_id'] = $inputs['pv_id'];
                $attributes['pv_date'] = $inputs['pv_date'];
                $attributes['site_id'] = $inputs['site_id'];
                $attributes['task_id'] = $inputs['task_id'];
                $attributes['sub_task_id'] = $inputs['sub_task_id'];
                $attributes['remark'] = $inputs['remark'];
            }

            $pv_total = 0;
            $elqPvDetail = PaymentVoucherDetail::where('pv_id', $request->pv_id)->get();
            foreach($elqPvDetail as $key => $value){

                $unit_id = OverheadCostItem::where('oci_id', $value->oci_id)->value('unit_id');

                $value->ono = ($key+1);
                $value->unit_name = Unit::where('unit_id', $unit_id)->value('unit_name');
                $value->item_name = OverheadCostItem::where('oci_id', $value->oci_id)->value('oci_name');
                $pv_total = $pv_total + $value->amount;
            }

            $attributes['pv_detail'] = $elqPvDetail;
            $attributes['pv_total'] = $elqPvDetail->sum('amount');
            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';
        }

        return $attributes;

    }

    public function processPaymentVoucher(Request $request){

        if($request->submit == 'Reset'){

            $data['attributes'] = $this->getPaymentVoucherAttributes(NULL, NULL);
        }

        if($request->submit == 'Add'){

            $pv_validation_result = $this->validatePaymentVoucher($request);

            if($pv_validation_result['validation_result'] == TRUE){

                $saving_process_result = $this->addPaymentVoucher($request);
                $saving_process_result['validation_result'] = $pv_validation_result['validation_result'];
                $saving_process_result['validation_messages'] = $pv_validation_result['validation_messages'];
                $data['attributes'] = $this->getPaymentVoucherAttributes($saving_process_result, $request);

            }else{

                $pv_validation_result['process_status'] = FALSE;
                $data['attributes'] = $this->getPaymentVoucherAttributes($pv_validation_result, $request);
            }
        }

        if($request->submit == 'Cancel'){

            $pv_validation_result = $this->validatePaymentVoucher($request);
            if($pv_validation_result['validation_result'] == TRUE){

                $saving_process_result = $this->cancelPaymentVoucher($request);

                $saving_process_result['validation_result'] = TRUE;
                $saving_process_result['validation_messages'] = new MessageBag();

                $data['attributes'] = $this->getPaymentVoucherAttributes($saving_process_result, $request);

            }else{

                $pv_validation_result['process_status'] = FALSE;
                $data['attributes'] = $this->getPaymentVoucherAttributes($pv_validation_result, $request);
            }
        }

        $data['site'] = Site::where('active', 1)->get();
        $data['cost_section'] = CostSection::where('active', 1)->whereIn('cs_id', [2, 3])->get();
        $data['site_task'] = SiteTask::where('active', 1)->where('site_id', $request->site_id)->get();
        $data['site_sub_task'] = SiteSubTask::where('active', 1)->where('task_id', $request->task_id)->get();
        $data['overhead_cost_item'] = OverheadCostItem::where('active', 1)->get();

        return view('SiteMM.SiteOperation.payment_voucher')->with('PV', $data);
    }

    private function validatePaymentVoucher($request){

        //try{

            $inputs['pv_id'] = $request->pv_id;
            $inputs['pv_date'] = $request->pv_date;
            $inputs['site_id'] = $request->site_id;
            $inputs['task_id'] = $request->task_id;
            $inputs['sub_task_id'] = $request->sub_task_id;
            $inputs['remark'] = $request->remark;
            $inputs['oci_id'] = $request->oci_id;
            $inputs['quantity'] = $request->quantity;
            $inputs['price'] = InputHelper::currencyToNumber($request->price);

            $rules['pv_id'] = array('required', new PaymentVoucherCancelValidation('save'));
            $rules['pv_date'] = array('required', 'date');
            $rules['site_id'] = array( new ZeroValidation('Site', $request->site_id));
            $rules['task_id'] = array( new ZeroValidation('Task', $request->task_id));
            $rules['sub_task_id'] = array( new ZeroValidation('Sub Task', $request->sub_task_id));
            $rules['remark'] = array( 'max:100');
            $rules['oci_id'] = array( new ZeroValidation('Overhead Cost Item', $request->oci_id));
            $rules['quantity'] = array('required', 'numeric', new CurrencyValidation(1));
            $rules['price'] = array('required', 'numeric', new CurrencyValidation(1));

            $front_end_message = '';

            if($request->submit == 'Cancel'){

                $i['pv_id'] = $request->pv_id;
                $r['pv_id'] = array('required', new PaymentVoucherCancelValidation('cancel'));
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
            $process_result['back_end_message'] =  'Payment Voucher - Validation Process ';

            return $process_result;

        // }catch(\Exception $e){

        //     $process_result['validation_result'] = FALSE;
        //     $process_result['validation_messages'] = new MessageBag();
        //     $process_result['front_end_message'] =  $e->getMessage();
        //     $process_result['back_end_message'] =  'Payment Voucher - Validation Function Fault';

		// 	return $process_result;
        // }
    }

    private function addPaymentVoucher($request){

        //try{

            $objPaymentVoucher = new PaymentVoucher();

            $pv['ea'] = array();
            $pv['pv'] = $this->getPaymentVoucherArray($request);
            $pv['pv_detail'] = $this->getPaymentVoucherDetailArray($request);
            $saving_process_result = $objPaymentVoucher->savePaymentVoucher($pv);

            return $saving_process_result;

        // }catch(\Exception $e){

        //     $process_result['site_id'] = $request->site_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'Payment Voucher -> Saving Process <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

    private function getPaymentVoucherArray($request){

        $pv['pv_id'] = $request->pv_id;
        $pv['pv_date'] = $request->pv_date;
        $pv['site_id'] = $request->site_id;
        $pv['task_id'] = $request->task_id;
        $pv['sub_task_id'] = $request->sub_task_id;
        $pv['cs_id'] = 3;
        $pv['advance'] = 0;
        $pv['total_amount'] = 0;
        $pv['remark'] = $request->remark;
        $pv['cancel'] = 0;

        if( $request->pv_id == '#Auto#' ){

            $pv['saved_by'] = Auth::id();
            $pv['saved_on'] = Carbon::now()->format('Y-m-d H:i:s');

        }else{

            $pv['updated_by'] = Auth::id();
            $pv['updated_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        return $pv;
    }

    private function getPaymentVoucherDetailArray($request){

        $price = str_replace(",","",$request->price);
        $quantity = $request->quantity;

        $pv_detail['pv_id'] = 0;
        $pv_detail['oci_id'] = $request->oci_id;
        $pv_detail['employee_id'] = 0;
        $pv_detail['price'] = $price;
        $pv_detail['quantity'] = $quantity;
        $pv_detail['amount'] = $price * $quantity;

        if($request->pv_id == '#Auto#'){

            $pv_detail['saved_by'] = Auth::id();
            $pv_detail['saved_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }else{

            $pv_detail['saved_by'] = Auth::id();
            $pv_detail['saved_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        return $pv_detail;
    }

    private function cancelPaymentVoucher($request){

        //try{

            $objPaymentVoucher = new PaymentVoucher();

            $pv_cancel['pv_id'] = $request->pv_id;
            $pv_cancel['cancel'] = 1;
            $pv_cancel['cancel_by'] = Auth::id();
            $pv_cancel['cancel_on'] = Carbon::now()->format('Y-m-d H:i:s');

            $saving_process_result = $objPaymentVoucher->cancelPaymentVoucher($pv_cancel);

            return $saving_process_result;

        // }catch(\Exception $e){

        //     $process_result['pv_id'] = $request->pv_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'Payment Voucher Controller -> Payment Voucher Saving Process <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

    public function openPaymentVoucher(Request $request){

        $elqPv = PaymentVoucher::where('pv_id', $request->open_pv_id )->first();
        $elqPvDetail = PaymentVoucherDetail::where('pv_id', $request->open_pv_id)->get();
        $pv_total = 0;

        foreach($elqPvDetail as $key => $value){

            $unit_id = OverheadCostItem::where('oci_id', $value->oci_id)->value('unit_id');

            $value->ono = ($key+1);
            $value->unit_name = Unit::where('unit_id', $unit_id)->value('unit_name');
            $value->item_name = OverheadCostItem::where('oci_id', $value->oci_id)->value('oci_name');
            $pv_total = $pv_total + $value->amount;
        }

        $attributes['pv_id'] = $elqPv->pv_id;
        $attributes['pv_date'] = $elqPv->pv_date;
        $attributes['site_id'] = $elqPv->site_id;
        $attributes['task_id'] = $elqPv->task_id;
        $attributes['sub_task_id'] = $elqPv->sub_task_id;
        $attributes['remark'] = $elqPv->remark;
        $attributes['pv_detail'] = $elqPvDetail;
        $attributes['pv_total'] = $elqPvDetail->sum('amount');

        $attributes['validation_messages'] = new MessageBag();
        $attributes['process_message'] = "";

        $data['attributes'] = $attributes;
        $data['site'] = Site::where('active', 1)->get();
        $data['cost_section'] = CostSection::where('active', 1)->whereIn('cs_id', [2, 3])->get();
        $data['site_task'] = SiteTask::where('active', 1)->where('site_id', $elqPv->site_id)->get();
        $data['site_sub_task'] = SiteSubTask::where('active', 1)->where('task_id', $elqPv->task_id)->get();
        $data['overhead_cost_item'] = OverheadCostItem::where('active', 1)->get();

        return view('SiteMM.SiteOperation.payment_voucher')->with('PV', $data);

    }

}
