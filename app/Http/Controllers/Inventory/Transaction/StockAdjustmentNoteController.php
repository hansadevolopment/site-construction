<?php

namespace App\Http\Controllers\Inventory\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Inventory\Primary\ItemMaster;
use App\Models\Inventory\Transaction\StockAdjustmentNote;

use App\Rules\ZeroValidation;
use App\Rules\CurrencyValidation;
use App\Rules\Inventory\Transaction\San\SanCancelValidation;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;

class StockAdjustmentNoteController extends Controller {

    public function loadView(){

        $objItemMaster = new ItemMaster();

        $data['item'] = $objItemMaster->getActiveItemList();
        $data['attributes'] = $this->getSanAttributes(NULL, NULL);

        return view('inventory.transaction.stock_adjustment_note')->with('SAN', $data);
    }

    private function getSanAttributes($process, $request){

        $attributes['san_id'] = '#Auto#';
        $attributes['san_date'] = '';
        $attributes['item_id'] = '0';
        $attributes['item_serial'] = '0';
        $attributes['actual_quantity'] = '0';
        $attributes['quantity'] = '0';
        $attributes['remark'] = '';

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        if( ($process['validation_result'] == TRUE) && ($process['process_result'] == TRUE)){

            $objSan = new StockAdjustmentNote();

            $san_table = $objSan->getStockAdjustmentNote($request->san_id);

            foreach ($san_table as $row) {
                  
                $attributes['san_id'] = $row->san_id;
                $attributes['san_date'] = $row->san_date;
                $attributes['item_id'] = '0';
                $attributes['item_serial'] = '0';
                $attributes['actual_quantity'] = '0';
                $attributes['quantity'] = '0';
                $attributes['remark'] = $row->remark;
            }

            $attributes['process_message'] = '<div class="alert alert-success" role="alert"> '. $process['front_end_message'] .' </div> ';
            $attributes['validation_messages'] = new MessageBag();

        }else{

            $inputs = $request->input();
            if(is_null($inputs) == FALSE){
    
                $attributes['san_id'] = $inputs['san_id'];
                $attributes['san_date'] = $inputs['san_date'];
                $attributes['item_id'] = $inputs['item_id'];
                $attributes['item_serial'] = $inputs['item_serial'];
                $attributes['actual_quantity'] = $inputs['actual_quantity'];
                $attributes['quantity'] = $inputs['quantity'];
                $attributes['remark'] = $inputs['remark'];
            }

            $message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';

            $attributes['validation_messages'] = $process['validation_messages'];
        }
      
        return $attributes;
    }

    public function stockAdjustmentNoteProcess(Request $request){

        // Reset
        if($request->submit == 'Reset'){

            $data['attributes'] = $this->getSanAttributes(NULL, NULL);
        }

        if( ($request->submit == 'Save') ){

            $san_validation_result = $this->savingValidationProcess($request);
            if( $san_validation_result['validation_result'] == TRUE){

                $san_saving_result = $this->saveStockAdjustmentNote($request);

                $san_saving_result['validation_result'] = $san_validation_result['validation_result'];
                $san_saving_result['validation_messages'] = $san_validation_result['validation_messages'];

                $data['attributes'] = $this->getSanAttributes($san_saving_result, $request);

            }else{

                $data['attributes'] = $this->getSanAttributes($san_validation_result, $request);
            }
        }

        $objItemMaster = new ItemMaster();
        $data['item'] = $objItemMaster->getActiveItemList();

        return view('inventory.transaction.stock_adjustment_note')->with('SAN', $data);
    }

    private function savingValidationProcess($request){

        //try{

            $front_end_message = '';

            $inputs['san_id'] = $request->san_id;
            $inputs['san_date'] = $request->san_date;
            $inputs['item_id'] = $request->item_id;
            $inputs['item_serial'] = $request->item_serial;
            $inputs['quantity'] = $request->quantity;
            $inputs['remark'] = $request->remark;

            $rules['san_id'] = array('required', new sanCancelValidation());
            $rules['san_date'] = array('required', 'date');
            $rules['item_id'] =  array( new ZeroValidation('Item', $request->item_id));
            $rules['item_serial'] =  array( new ZeroValidation('Item Serial', $request->item_serial));
            $rules['quantity'] = array('required', 'numeric', new CurrencyValidation(0));
            $rules['remark'] = array('max:100');           

            $validator = Validator::make($inputs, $rules);

            $validation_result = $validator->passes();
            if($validation_result == FALSE){

                $front_end_message = 'Please Check Your Inputs';
            }

            $process['san_id'] = $request->san_id;
            $process['validation_result'] = $validation_result;
            $process['front_end_message'] = $front_end_message;
            $process['back_end_message'] = '';
            $process['validation_messages'] =  $validator->errors();

            return $process;

        // }catch(\Exception $e){ 

        //     $process['san_id'] = $request->san_id;
        //     $process['validation_result'] = FALSE;
        //     $process['front_end_message'] =  $e->getMessage();
        //     $process['back_end_message'] =  'san Controller : san Validation Process ' . $e->getLine();;
        //     $process['validation_messages'] = new MessageBag();

        //     return $process;
        // }
    }

    private function saveStockAdjustmentNote($request){

        //try{

            $objSan = new StockAdjustmentNote();

            $data['san'] = $this->getStockAdjustmentNoteTable($request);
            
            $san_saving_process_result = $objSan->sanSavingProcess($data);

            return $san_saving_process_result;

        // }catch(\Exception $e){

        //     $process['san_id'] = $request->san_id;
        //     $process['process_result'] = FALSE;
        //     $process['front_end_message'] = $e->getMessage();
        //     $process['back_end_message'] = " Item Request Note Controller : Item Request Note Saving Process.  " . $e->getLine();

        //     return $process;
        // }

    }

    private function getStockAdjustmentNoteTable($request){

        $objItemMaster = new ItemMaster();

        $arr['san_id'] = $request->san_id;
        $arr['san_date'] = $request->san_date;
        $arr['item_id'] = $request->item_id;
        $arr['item_name'] = $objItemMaster->getItemName($request->item_id);
        $arr['item_serial'] = $request->item_serial;
        $arr['actual_quantity'] = $request->actual_quantity;
        $arr['quantity'] = $request->quantity;
        $arr['remark'] = $request->remark;
        $arr['cancel'] = 0;
        $arr['cancel_reason'] = '';
        $arr['saved_by'] = Auth::id();
        $arr['saved_on'] = Now();
        $arr['saved_ip'] = '-';

        return $arr;
    }
    
}
