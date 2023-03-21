<?php

namespace App\Http\Controllers\Inventory\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Inventory\Primary\ItemMaster;
use App\Models\Inventory\Primary\ManufactureLocation;
use App\Models\Sales\Primary\SalesLocation;
use App\Models\Inventory\Transaction\ItemRequestNote;

use App\Rules\ZeroValidation;
use App\Rules\CurrencyValidation;
use App\Rules\Inventory\Transaction\IRN\IrnCancelValidation;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;

class ItemRequestNoteController extends Controller {

    public function loadView($referance){

        $objItemMaster = new ItemMaster();
        $objManufacturLocation = new ManufactureLocation();
        $objSalesLocation = new SalesLocation();

        $data['item'] = $objItemMaster->getActiveItemList();
        $data['sales_location'] = $objSalesLocation->getActiveSalesLocationList();
        $data['manufacture_location'] = $objManufacturLocation->getActiveManufactureLocationList();
        $data['attributes'] = $this->getIrnAttributes(NULL, NULL, $referance);

        return view('inventory.transaction.item_request_note')->with('Irn', $data);
    }

    private function getIrnAttributes($process, $request, $referance){

        $attributes['irn_id'] = '#Auto#';
        $attributes['irn_date'] = '';
        $attributes['irn_referance'] = $referance;
        $attributes['location_id'] = '0';
        $attributes['remark'] = '';
        $attributes['item_id'] = '0';
        $attributes['quantity'] = '0';
  
        $attributes['irn_detail'] = array();

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        if( ($process['validation_result'] == TRUE) && ($process['process_result'] == TRUE)){

            $objIrn = new ItemRequestNote();

            $irn_table = $objIrn->getItemRequestNote($request->irn_id);
            $irn_detail_table = $objIrn->getItemRequestNoteDetail($request->irn_id);

            foreach ($irn_table as $row) {
                  
                $attributes['irn_id'] = $row->irn_id;
                $attributes['irn_date'] = $row->irn_date;
                $attributes['irn_referance'] = $row->irn_referance;
                $attributes['location_id'] = $row->location_id;
                $attributes['remark'] = $row->remark;
               
            }

            $attributes['irn_detail'] = $irn_detail_table;

            $attributes['process_message'] = '<div class="alert alert-success" role="alert"> '. $process['front_end_message'] .'. </div> ';
            $attributes['validation_messages'] = new MessageBag();

        }else{

            $objIrn = new ItemRequestNote();

            $inputs = $request->input();
            if(is_null($inputs) == FALSE){
    
                $attributes['irn_id'] = $inputs['irn_id'];
                $attributes['irn_date'] = $inputs['irn_date'];
                $attributes['irn_referance'] = $inputs['irn_referance'];
                $attributes['remark'] = $inputs['remark'];

                $attributes['item_id'] = $inputs['item_id'];
                $attributes['quantity'] = $inputs['quantity'];

                $attributes['irn_detail'] =  $objIrn->getItemRequestNoteDetail($inputs['irn_id']);
            }

            $message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .'. </div> ';

            $attributes['validation_messages'] = $process['validation_messages'];
        }
      
        return $attributes;
    }

    public function itemRequestNoteProcess(Request $request){

        // Reset
        if($request->submit == 'Reset'){

            $data['attributes'] = $this->getIrnAttributes(NULL, NULL, $request->irn_referance);
        }

        if( ($request->submit == 'Save') || ($request->submit == 'Add') ){

            $irn_validation_result = $this->savingValidationProcess($request);
            if( $irn_validation_result['validation_result'] == TRUE){

                $irn_saving_result = $this->saveItemRequestNote($request);

                $irn_saving_result['validation_result'] = $irn_validation_result['validation_result'];
                $irn_saving_result['validation_messages'] = $irn_validation_result['validation_messages'];

                $data['attributes'] = $this->getIrnAttributes($irn_saving_result, $request, $request->irn_referance);

            }else{

                $data['attributes'] = $this->getIrnAttributes($irn_validation_result, $request, $request->irn_referance);
            }

        }

        $objItemMaster = new ItemMaster();
        $objManufacturLocation = new ManufactureLocation();
        $objSalesLocation = new SalesLocation();

        $data['item'] = $objItemMaster->getActiveItemList();
        $data['sales_location'] = $objSalesLocation->getActiveSalesLocationList();
        $data['manufacture_location'] = $objManufacturLocation->getActiveManufactureLocationList();

        return view('inventory.transaction.item_request_note')->with('Irn', $data);
    }

    private function savingValidationProcess($request){

        //try{

            $front_end_message = '';

            $inputs['irn_id'] = $request->irn_id;
            $inputs['irn_date'] = $request->irn_date;
            $inputs[$request->irn_referance . 'Location'] = $request->location_id;
            $inputs['remark'] = $request->remark;

            $rules['irn_id'] = array('required', new IrnCancelValidation());
            $rules['irn_date'] = array('required', 'date');
            $rules[$request->irn_referance .  'Location'] =  array( new ZeroValidation($request->irn_referance .' Location', $request->location_id));
            $rules['remark'] = array('max:100');           

            if($request->submit == 'Add'){

                $inputs['item_id'] = $request->item_id;
                $inputs['quantity'] = $request->quantity;

                $rules['item_id'] =  array( new ZeroValidation('Item', $request->item_id));
                $rules['quantity'] = array('required', 'numeric', new CurrencyValidation(0));
            }

            $validator = Validator::make($inputs, $rules);

            $validation_result = $validator->passes();
            if($validation_result == FALSE){

                $front_end_message = 'Please Check Your Inputs';
            }

            $process['irn_id'] = $request->irn_id;
            $process['validation_result'] = $validation_result;
            $process['front_end_message'] = $front_end_message;
            $process['back_end_message'] = '';
            $process['validation_messages'] =  $validator->errors();

            return $process;

        // }catch(\Exception $e){ 

        //     $process['irn_id'] = $request->irn_id;
        //     $process['validation_result'] = FALSE;
        //     $process['front_end_message'] =  $e->getMessage();
        //     $process['back_end_message'] =  'iRN Controller : iRN Validation Process ' . $e->getLine();;
        //     $process['validation_messages'] = new MessageBag();

        //     return $process;
        // }
    }

    private function saveItemRequestNote($request){

        //try{

            $objIrn = new ItemRequestNote();

            $data['irn'] = $this->getItemRequestNoteTable($request);
            $data['irn_detail'] = NULL;

            if($request->submit == 'Add'){

                $data['irn_detail'] = $this->getItemRequestNoteDetailTable($request);
            }

            // Call To Model
            $irn_saving_process_result = $objIrn->irnSavingProcess($data);

            return $irn_saving_process_result;

        // }catch(\Exception $e){

        //     $process['irn_id'] = $request->irn_id;
        //     $process['process_result'] = FALSE;
        //     $process['front_end_message'] = $e->getMessage();
        //     $process['back_end_message'] = " Item Request Note Controller : Item Request Note Saving Process.  " . $e->getLine();

        //     return $process;
        // }

    }

    private function getItemRequestNoteTable($request){

        $arr['irn_id'] = $request->irn_id;
        $arr['irn_date'] = $request->irn_date;
        $arr['irn_referance'] = $request->irn_referance;
        $arr['location_id'] = $request->location_id;
        $arr['remark'] = $request->remark;
        $arr['cancel_reason'] = '';
        $arr['saved_by'] = Auth::id();
        $arr['saved_on'] = Now();
        $arr['saved_ip'] = '-';

        return $arr;
    }

    public function getItemRequestNoteDetailTable($request){

        $objItemMaster = new ItemMaster();

        $arr['irn_id'] = $request->irn_id;
        $arr['item_id'] = $request->item_id;
        $arr['item_name'] = $objItemMaster->getItemName($request->item_id);
        $arr['quantity'] =  $request->quantity;

        return $arr;
    }

    
}
