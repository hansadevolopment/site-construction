<?php

namespace App\Http\Controllers\Purchasing\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use App\Models\SiteMM\Master\Item;
use App\Models\Purchasing\Primary\Creditor;
use App\Models\Purchasing\Primary\PurchasingCategory;
use App\Models\Purchasing\Primary\PurchasingLocation;
use App\Models\Purchasing\Transaction\GoodReceiveNote;
use App\Models\GL\Primary\Tax;

use App\Services\Finance\TaxService;

use App\Helpers\Common\InputHelper;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Carbon;

use App\Rules\CancelValidation;
use App\Rules\GLPostValidation;
use App\Rules\DiscountValidation;
use App\Rules\CurrencyValidation;
use App\Rules\QuantityValidation;

class GoodReceiveNoteController extends Controller {

    public function loadView(){

        $data['item'] = Item::where('active', 1)->get();
        $data['creditor'] = Creditor::where('active', 1)->get();
        $data['purchasing_category'] = PurchasingCategory::where('active', 1)->get();
        $data['purchasing_location'] = PurchasingLocation::where('active', 1)->get();
        $data['tax'] = Tax::where('active', 1)->get();
        $data['attributes'] = $this->getGrnAttributes(NULL, NULL);

        return view('Purchasing.Transaction.good_receive_note')->with('GRN', $data);
    }

    private function getGrnAttributes($process, $request){

        $attributes['grn_id'] = '#Auto#';
        $attributes['grn_date'] = date('Y-m-d');
        $attributes['creditor_id'] = 0;
        $attributes['pc_id'] = 0;
        $attributes['pl_id'] = 0;
        $attributes['gl_post_id'] = '';
        $attributes['grn_type'] = '1';
        $attributes['remark'] = '';
        $attributes['cancel'] = 0;

        $attributes['item_id'] = 0;
        $attributes['item_name'] = '';
        $attributes['item_description'] = '';
        $attributes['unit_price'] = '';
        $attributes['quantity'] = '';
        $attributes['discount_amount'] = '';

        $attributes['total_discount_amount'] = number_format(0, 2);
        $attributes['total_gross_amount'] = number_format(0, 2);
        $attributes['total_tax_amount'] = number_format(0, 2);
        $attributes['total_net_amount'] = number_format(0, 2);

        $attributes['btn_disable'] = 'disabled';

        $attributes['grn_detail'] = array();

        $attributes['validationMessages'] = new MessageBag();;
        $attributes['processMessage'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        $grnId = 0;
        if( method_exists($request,'input') ){

            $input = $request->input();
            if(is_null($input) == FALSE){

                $grnId = $input['grn_id'];
                $attributes['grn_id'] = $input['grn_id'];
                $attributes['grn_date'] = $input['grn_date'];
                $attributes['creditor_id'] = $input['creditor_id'];
                $attributes['pc_id'] = $input['pc_id'];
                $attributes['pl_id'] = $input['pl_id'];
                $attributes['gl_post_id'] = $input['gl_post_id'];
                $attributes['grn_type'] = $input['grn_type'];
                $attributes['remark'] = $input['remark'];

                if( $input['grn_type'] == 1 ){

                    $attributes['item_id'] = $input['item_id'];
                    $attributes['unit_price'] = $input['item_unit_price'];
                    $attributes['quantity'] = $input['item_quantity'];
                    $attributes['discount_amount'] = $input['item_discount_amount'];

                }else{

                    $attributes['item_description'] = $input['item_description'];
                    $attributes['unit_price'] = $input['desc_unit_price'];
                    $attributes['quantity'] = $input['desc_quantity'];
                    $attributes['discount_amount'] = $input['desc_discount_amount'];
                }
            }
        }

        if( isset($process['grnId']) ){

            $grnId = $process['grnId'];
        }

        $grnResult = DB::table('grn')->where('grn_id', $grnId)->first();
        if($grnResult){

            $attributes['grn_id'] = $grnResult->grn_id;
            $attributes['grn_date'] = $grnResult->grn_date;
            $attributes['creditor_id'] = $grnResult->creditor_id;
            $attributes['pc_id'] = $grnResult->pc_id;
            $attributes['pl_id'] = $grnResult->pl_id;
            $attributes['gl_post_id'] = $grnResult->gl_post_no;
            $attributes['grn_type'] = $grnResult->grn_type;
            $attributes['remark'] = $grnResult->remark;
            $attributes['cancel'] = $grnResult->cancel;

            $attributes['total_discount_amount'] = number_format($grnResult->discount_amount, 2);
            $attributes['total_gross_amount'] = number_format($grnResult->gross_amount, 2);
            $attributes['total_tax_amount'] = number_format($grnResult->tax_amount, 2);
            $attributes['total_net_amount'] = number_format($grnResult->net_amount, 2);

            if( $grnResult->gl_post_no > 0 ){

                $attributes['btn_disable'] = 'disabled';

            }elseif( $grnResult->cancel > 0 ){

                $attributes['btn_disable'] = 'disabled';

            }elseif( ($grnResult->gl_post_no == 0) || ($grnResult->cancel == 0) ){

                $attributes['btn_disable'] = '';

            }elseif( ($grnResult->gl_post_no > 0) || ($grnResult->cancel > 0) ){

                $attributes['btn_disable'] = 'disabled';
            }

            $attributes['grn_detail'] = DB::table('grn_detail')->where('grn_id', $grnId)->get();
        }

        if( ($process['validationResult'] == TRUE) && ($process['processStatus'] == TRUE)){

            $attributes['processStatus'] = TRUE;
			$attributes['validationMessages'] = new MessageBag();

            if( $process['frontEndMessage'] == '' ){

                $attributes['processMessage'] = '';
            }else{

                $message = $process['frontEndMessage'];
                $attributes['processMessage'] = '<div class="alert alert-success" role="alert"> '. $message .' </div> ';
            }

            if( isset($process['cancelStatus']) ){

                $message = $process['frontEndMessage'];
                $attributes['processMessage'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';
            }

			return $attributes;

        }else{

            $attributes['processStatus'] = FALSE;
			$attributes['validationMessages'] = $process['validationMessages'];

			$message = $process['frontEndMessage'] .' <br> ' . $process['back_end_message'];
            $attributes['processMessage'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';

			return $attributes;
        }

    }

    public function saveGrn(Request $request){

        if( $request->submit == 'Reset' ){

            $data['attributes'] = $this->getGrnAttributes(NULL, NULL);
        }

        if( ($request->submit == 'Add') || ($request->submit == 'Save') || ($request->submit == 'GL Post') ){

            $validationResult = $this->validateGrnProcess($request);
            if( $validationResult['validationResult'] == TRUE ){

                $savingResult = $this->saveGoodReceiveNote($request);

                $savingResult['validationResult'] = $validationResult['validationResult'];
                $savingResult['validationMessages'] = $validationResult['validationMessages'];

                $data['attributes'] = $this->getGrnAttributes($savingResult, $request);

            }else{

			    $validationResult['processStatus'] = FALSE;
			    $data['attributes'] = $this->getGrnAttributes($validationResult, $request);
            }
        }

        if($request->submit == 'Cancel'){

            $validationResult = $this->validateGrnProcess($request);
            if($validationResult['validationResult'] == TRUE){

                $processResult = $this->cancelGoodReceiveNote($request);

                $processResult['validationResult'] = TRUE;
                $processResult['validation_messages'] = new MessageBag();

                $data['attributes'] = $this->getGrnAttributes($processResult, $request);

            }else{

                $validationResult['process_status'] = FALSE;
                $data['attributes'] = $this->getGrnAttributes($validationResult, $request);
            }
        }

        $data['item'] = Item::where('active', 1)->get();
        $data['creditor'] = Creditor::where('active', 1)->get();
        $data['purchasing_category'] = PurchasingCategory::where('active', 1)->get();
        $data['purchasing_location'] = PurchasingLocation::where('active', 1)->get();
        $data['tax'] = Tax::where('active', 1)->get();

        return view('Purchasing.Transaction.good_receive_note')->with('GRN', $data);
    }

    private function validateGrnProcess($request){

        //try{

			$frontEndMessage = " ";

            $input['grn_id'] = $request->grn_id;
            $rule['grn_id'] = array('required', new CancelValidation('grn', 'grn_id', 'save'), new GLPostValidation('grn', 'grn_id', 'save'));

            if( ($request->submit == 'Save') || ($request->submit == 'GL Post') || ($request->submit == 'Add')   ){

                $input['grn_date'] = $request->grn_date;
                $input['creditor'] = $request->creditor_id;
                $input['purchase_category'] = $request->pc_id;
                $input['purchase_location'] = $request->pl_id;
                $input['remark'] = $request->remark;

                $rule['grn_date'] = array('required', 'date');
                $rule['creditor'] = array('required', 'exists:creditor,creditor_id');
                $rule['purchase_category'] = array('required', 'exists:purchasing_category,pc_id');
                $rule['purchase_location'] = array('required', 'exists:purchasing_location,pl_id');
                $rule['remark'] = array('max:50');

                if($request->submit == 'Add'){

                    if($request->grn_type == 1){

                        $input['item_id'] = $request->item_id;
                        $input['item_unit_price'] = $request->item_unit_price;
                        $input['item_quantity'] = $request->item_quantity;
                        $input['item_discount_amount'] = $request->item_discount_amount;

                        $rule['item_id'] = array('required', 'exists:item,item_id');
                        $rule['item_unit_price'] = array('required', new CurrencyValidation($request->item_unit_price));
                        $rule['item_quantity'] = array('required', new QuantityValidation($request->item_quantity));
                        $rule['item_discount_amount'] = array(new DiscountValidation());

                    }else{

                        $input['item_description'] = $request->item_description;
                        $input['desc_unit_price'] = $request->desc_unit_price;
                        $input['desc_quantity'] = $request->desc_quantity;
                        $input['desc_discount_amount'] = $request->desc_discount_amount;

                        $rule['item_description'] = array('required', 'max:100');
                        $rule['desc_unit_price'] = array('required', new CurrencyValidation($request->desc_unit_price));
                        $rule['desc_quantity'] = array('required', new QuantityValidation($request->desc_quantity));
                        $rule['desc_discount_amount'] = array(new DiscountValidation());
                    }
                }
            }

            $validator = Validator::make($input, $rule);
	        $validationResult = $validator->passes();
	        if($validationResult == FALSE){

	            $frontEndMessage = 'Please Check Your Inputs';
	        }

	        $processResult['validationResult'] =  $validationResult;
	        $processResult['validationMessages'] =  $validator->errors();
	        $processResult['frontEndMessage'] = $frontEndMessage;
	        $processResult['back_end_message'] =  'GRN Controller - Validation Process ';

            //dd( $processResult );

	        return $processResult;

		// }catch(\Exception $e){

		// 	$processResult['validationResult'] = FALSE;
        //     $processResult['validationMessages'] = new MessageBag();
        //     $processResult['frontEndMessage'] =  $e->getMessage();
        //     $processResult['back_end_message'] =  'GRN Controller - Validation Function Fault';

		// 	return $processResult;
		// }

    }

    private function saveGoodReceiveNote($request){

        //try{

            $objGrn = new GoodReceiveNote();

            $grn['grn'] = $this->getGoodReceiveNoteArray($request);

            if( $request->submit == 'Add'){

                $grn['grn_detail'] = $this->getGoodReceiveNoteDeatilArray($request);
                $grn['grn_tax'] = $this->getGoodReceiveNoteTaxArray($request);
                $grn['main_store'] = null;

                $colGrnTax = collect( $grn['grn_tax'] );
                $grnTaxAmount = $colGrnTax->sum('tax_amount');
                $grnGrossAmount = $grn['grn_detail']['gross_amount'];

                $grn['grn_detail']['tax_amount'] = $grnTaxAmount;
                $grn['grn_detail']['net_amount'] = $grnGrossAmount + $grnTaxAmount;
            }

            if( ($request->submit == 'Save') || ($request->submit == 'GL Post') ){

                $grn['grn_detail'] = null;
                $grn['grn_tax'] = null;
                $grn['main_store'] = null;

                if( $request->submit == 'GL Post' ){

                    $grn['main_store'] = $this->getMainStoreArray($request);
                }
            }

            $savingProcessResult = $objGrn->addGrnItem($grn);

            return $savingProcessResult;

        // }catch(\Exception $e){

        //     $processResult['grn_id'] = $request->grn_id;
        //     $processResult['processStatus'] = FALSE;
        //     $processResult['frontEndMessage'] = $e->getMessage();
        //     $processResult['back_end_message'] = 'Daily Progress Report Controller -> grn Saving Process <br> ' . $e->getLine();

        //     return $processResult;
        // }
    }

    private function getGoodReceiveNoteArray($request){

        $grn['grn_id'] = $request->grn_id;
        $grn['grn_date'] = $request->grn_date;
        $grn['creditor_id'] = $request->creditor_id;
        $grn['pc_id'] = $request->pc_id;
        $grn['pl_id'] = $request->pl_id;
        $grn['gl_post_no'] = 0;
        $grn['grn_type'] = $request->grn_type;
        $grn['remark'] = $request->remark;

        if( $request->submit == 'GL Post' ){

            $grn['gl_post_by'] = Auth::id();
            $grn['gl_post_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }else{

            $grn['gl_post_by'] = null;
            $grn['gl_post_on'] = null;
        }

        $grn['cancel'] = 0;
        $grn['cancel_by'] = null;
        $grn['cancel_on'] = null;

        if( GoodReceiveNote::where('grn_id', $request->grn_id)->exists() ){

            $grn['updated_by'] = Auth::id();
            $grn['updated_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }else{

            $grn['saved_by'] = Auth::id();
            $grn['saved_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        return $grn;
    }

    private function getGoodReceiveNoteDeatilArray($request){

        $grn_detail['grn_id'] = $request->grn_id;
        if($request->grn_type == 1){

            $unitPrice = InputHelper::currencyToNumber($request->item_unit_price);
            $quantity = $request->item_quantity;
            $discountAmount = InputHelper::currencyToNumber($request->item_discount_amount);

            $grn_detail['item_id'] = $request->item_id;
            $grn_detail['item_name'] = Item::where('item_id', $request->item_id)->value('item_name');
            $grn_detail['item_description'] = null;
            $grn_detail['unit_price'] = $unitPrice;
            $grn_detail['quantity'] = $quantity;
            $grn_detail['discount_amount'] = $discountAmount;

        }else{

            $unitPrice = InputHelper::currencyToNumber($request->desc_unit_price);
            $quantity = $request->desc_quantity;
            $discountAmount = InputHelper::currencyToNumber($request->desc_discount_amount);

            $grn_detail['item_id'] = 0;
            $grn_detail['item_name'] = '';
            $grn_detail['item_description'] = $request->item_description;
            $grn_detail['unit_price'] = $unitPrice;
            $grn_detail['quantity'] = $quantity;
            $grn_detail['discount_amount'] = $discountAmount;
        }

        $grossAmount = ($unitPrice * $quantity) - $discountAmount;
        $taxAmount = TaxService::getItemTaxCalculation($request->item_id, $grossAmount);
        $netAmount = $grossAmount + $taxAmount;

        $grn_detail['gross_amount'] = $grossAmount;
        $grn_detail['tax_amount'] = $taxAmount;
        $grn_detail['net_amount'] = $netAmount;

        return $grn_detail;
    }

    private function getGoodReceiveNoteTaxArray($request){

        $grn_tax_detail = array();

        if($request->grn_type == 1){

            $unitPrice = InputHelper::currencyToNumber($request->item_unit_price);
            $quantity = $request->item_quantity;
            $discountAmount = InputHelper::currencyToNumber($request->item_discount_amount);
            $grossAmount = ($unitPrice * $quantity) - $discountAmount;

            $taxAmount = TaxService::getItemTaxCalculation($request->item_id, $grossAmount);
            $itemTaxResult = TaxService::getItemTaxDetail($request->item_id);
            foreach($itemTaxResult as $itemTaxKey => $itemTaxValue){

                $grn_tax_detail[$itemTaxKey + 1]['grn_id'] = $request->grn_id;
                $grn_tax_detail[$itemTaxKey + 1]['item_id'] = $request->item_id;
                $grn_tax_detail[$itemTaxKey + 1]['tax_id'] =  $itemTaxValue->tax_id;
                $grn_tax_detail[$itemTaxKey + 1]['tax_amount'] = $taxAmount;
            }

        }else{

            if( isset($request->tax) && is_array($request->tax) ){

                $unitPrice = InputHelper::currencyToNumber($request->desc_unit_price);
                $quantity = $request->desc_quantity;
                $discountAmount = InputHelper::currencyToNumber($request->desc_discount_amount);
                $grossAmount = ($unitPrice * $quantity) - $discountAmount;

                foreach($request->tax as $rowKey => $rowValue){

                    $taxAmount = TaxService::getOtherTaxCalculation($rowValue, $grossAmount);

                    $grn_tax_detail[$rowKey + 1]['grn_id'] = $request->grn_id;
                    $grn_tax_detail[$rowKey + 1]['item_id'] = 0;
                    $grn_tax_detail[$rowKey + 1]['tax_id'] =  $rowValue;
                    $grn_tax_detail[$rowKey + 1]['tax_amount'] = $taxAmount;
                }
            }
        }

        return $grn_tax_detail;
    }

    private function getMainStoreArray($request){

        $main_store = array();

        $grnDtl = DB::table('grn_detail')->where('grn_id', $request->grn_id)->get();
        foreach($grnDtl as $rowKey => $rowValue){

            $main_store[$rowKey+1]['source'] = 'grn';
            $main_store[$rowKey+1]['source_id'] = $request->grn_id;
            $main_store[$rowKey+1]['item_id'] = $rowValue->item_id;
            $main_store[$rowKey+1]['item_name'] = $rowValue->item_name;
            $main_store[$rowKey+1]['unit_price'] = $rowValue->unit_price;
            $main_store[$rowKey+1]['quantity'] = $rowValue->quantity;
            $main_store[$rowKey+1]['quantity_balance'] = $rowValue->quantity;
            $main_store[$rowKey+1]['serial'] = 0;
            $main_store[$rowKey+1]['saved_by'] = Auth::id();
            $main_store[$rowKey+1]['saved_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        return $main_store;
    }

    public function removeGrnItem(Request $request){

        $grnDtlResult = DB::table('grn_detail')->where('grn_dtl_id',  $request->grnDtlId)->first();
        if( $grnDtlResult ){

            $objGrn = new GoodReceiveNote();
            $processResult = $objGrn->removeItem($request->grnId, $request->grnDtlId);

            $grnRequest = new \stdClass();
            $grnRequest->grnId = $grnDtlResult->grn_id;

            $processResult['validationResult'] = TRUE;

            $data['attributes'] = $this->getGrnAttributes($processResult, $grnRequest);

            $data['item'] = Item::where('active', 1)->get();
            $data['creditor'] = Creditor::where('active', 1)->get();
            $data['purchasing_category'] = PurchasingCategory::where('active', 1)->get();
            $data['purchasing_location'] = PurchasingLocation::where('active', 1)->get();
            $data['tax'] = Tax::where('active', 1)->get();

            return view('Purchasing.Transaction.good_receive_note')->with('GRN', $data);
        }
    }

    private function cancelGoodReceiveNote($request){

        //try{

            $objGrn = new GoodReceiveNote();

            $grn_cancel['grn_id'] = $request->grn_id;
            $grn_cancel['cancel'] = 1;
            $grn_cancel['cancel_by'] = Auth::id();
            $grn_cancel['cancel_on'] = Carbon::now()->format('Y-m-d H:i:s');

            $saving_process_result = $objGrn->cancelGoodReceiveNote($grn_cancel);

            return $saving_process_result;

        // }catch(\Exception $e){

        //     $processResult['pv_id'] = $request->pv_id;
        //     $processResult['process_status'] = FALSE;
        //     $processResult['front_end_message'] = $e->getMessage();
        //     $processResult['back_end_message'] = 'Payment Voucher Controller -> Payment Voucher Saving Process <br> ' . $e->getLine();

        //     return $processResult;
        // }
    }

}
