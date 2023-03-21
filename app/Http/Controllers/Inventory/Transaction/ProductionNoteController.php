<?php

namespace App\Http\Controllers\Inventory\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Inventory\Primary\ItemMaster;
use App\Models\Inventory\Primary\ManufactureLocation;
use App\Models\Inventory\Transaction\ProductionNote;

use App\Rules\ZeroValidation;
use App\Rules\CurrencyValidation;
use App\Rules\Inventory\Transaction\pn\pnCancelValidation;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;

class ProductionNoteController extends Controller {

    public function loadView(){

        $objItemMaster = new ItemMaster();
        $objManufacturLocation = new ManufactureLocation();

        $data['item'] = $objItemMaster->getActiveItemList();
        $data['manufacture_location'] = $objManufacturLocation->getActiveManufactureLocationList();
        $data['attributes'] = $this->getPnAttributes(NULL, NULL);

        return view('inventory.transaction.production_note')->with('PN', $data);
    }

    private function getPnAttributes($process, $request){

        $attributes['pn_id'] = '#Auto#';
        $attributes['pn_date'] = '';
        $attributes['manufacture_location_id'] = '0';
        $attributes['remark'] = '';
        $attributes['item_id'] = '0';
        $attributes['quantity'] = '0';
  
        $attributes['pn_detail'] = array();

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        if( ($process['validation_result'] == TRUE) && ($process['process_result'] == TRUE)){

           $objProductionNote = new ProductionNote();

            $pn_table = $objProductionNote->getProductionNote($process['pn_id']);
            $pn_detail_table = $objProductionNote->getProductionNoteDetail($process['pn_id']);

            foreach ($pn_table as $row) {
                  
                $attributes['pn_id'] = $row->pn_id;
                $attributes['pn_date'] = $row->pn_date;
                $attributes['manufacture_location_id'] = $row->manufacture_location_id;
                $attributes['remark'] = $row->remark;
               
            }

            $attributes['pn_detail'] = $pn_detail_table;

            $attributes['process_message'] = '<div class="alert alert-success" role="alert"> '. $process['front_end_message'] .'</div> ';
            $attributes['validation_messages'] = new MessageBag();

        }else{

            $objProductionNote = new ProductionNote();

            $inputs = $request->input();
            if(is_null($inputs) == FALSE){
    
                $attributes['pn_id'] = $inputs['pn_id'];
                $attributes['pn_date'] = $inputs['pn_date'];
                $attributes['manufacture_location_id'] = $inputs['manufacture_location_id'];
                $attributes['remark'] = $inputs['remark'];

                $attributes['item_id'] = $inputs['item_id'];
                $attributes['quantity'] = $inputs['quantity'];

                $attributes['pn_detail'] =  $objProductionNote->getProductionNoteDetail($inputs['pn_id']);
            }

            $message = $process['front_end_message'] . ' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';

            $attributes['validation_messages'] = $process['validation_messages'];
        }
      
        return $attributes;
    }


    public function productionNoteProcess(Request $request){

        // Reset
        if($request->submit == 'Reset'){

            $data['attributes'] = $this->getPnAttributes(NULL, NULL);
        }

        if( ($request->submit == 'Save') || ($request->submit == 'Add') ){

            $pn_validation_result = $this->savingValidationProcess($request);
            if( $pn_validation_result['validation_result'] == TRUE){

                $pn_saving_result = $this->saveProductionNote($request);

                $pn_saving_result['validation_result'] = $pn_validation_result['validation_result'];
                $pn_saving_result['validation_messages'] = $pn_validation_result['validation_messages'];

                $data['attributes'] = $this->getPnAttributes($pn_saving_result, $request);

            }else{

                $data['attributes'] = $this->getPnAttributes($pn_validation_result, $request);
            }
        }

        $objItemMaster = new ItemMaster();
        $objManufacturLocation = new ManufactureLocation();

        $data['item'] = $objItemMaster->getActiveItemList();
        $data['manufacture_location'] = $objManufacturLocation->getActiveManufactureLocationList();

        return view('inventory.transaction.production_note')->with('PN', $data);
    }

    private function savingValidationProcess($request){

        //try{

            $front_end_message = '';

            $inputs['pn_id'] = $request->pn_id;
            $inputs['pn_date'] = $request->pn_date;
            $inputs['Manufacture Location'] = $request->manufacture_location_id;
            $inputs['remark'] = $request->remark;

            $rules['pn_id'] = array('required', new pnCancelValidation());
            $rules['pn_date'] = array('required', 'date');
            $rules['Manufacture Location'] = array( new ZeroValidation('Manufacture Location', $request->manufacture_location_id));
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

            $process['pn_id'] = $request->pn_id;
            $process['validation_result'] = $validation_result;
            $process['front_end_message'] = $front_end_message;
            $process['back_end_message'] = '';
            $process['validation_messages'] =  $validator->errors();

            return $process;

        // }catch(\Exception $e){ 

        //     $process['pn_id'] = $request->pn_id;
        //     $process['validation_result'] = FALSE;
        //     $process['front_end_message'] =  $e->getMessage();
        //     $process['back_end_message'] =  'pn Controller : pn Validation Process ' . $e->getLine();;
        //     $process['validation_messages'] = new MessageBag();

        //     return $process;
        // }
    }

    private function saveProductionNote($request){

        //try{

            $objProductionNote = new ProductionNote();

            $data['pn'] = $this->getProductionNoteTable($request);
            $data['pn_detail'] = NULL;

            if($request->submit == 'Add'){

                $data['pn_detail'] = $this->getItemRequestNoteDetailTable($request);
            }

            $pn_saving_process_result = $objProductionNote->productionNoteSavingProcess($data);

            return $pn_saving_process_result;

        // }catch(\Exception $e){

        //     $process['pn_id'] = $request->pn_id;
        //     $process['process_result'] = FALSE;
        //     $process['front_end_message'] = $e->getMessage();
        //     $process['back_end_message'] = " Item Request Note Controller : Item Request Note Saving Process " . $e->getLine();

        //     return $process;
        // }

    }

    private function getProductionNoteTable($request){

        $arr['pn_id'] = $request->pn_id;
        $arr['pn_date'] = $request->pn_date;
        $arr['manufacture_location_id'] = $request->manufacture_location_id;
        $arr['remark'] = $request->remark;
        $arr['cancel_reason'] = '';
        $arr['saved_by'] = Auth::id();
        $arr['saved_on'] = Now();
        $arr['saved_ip'] = '-';

        return $arr;
    }

    public function getItemRequestNoteDetailTable($request){

        $objItemMaster = new ItemMaster();

        $arr['pn_id'] = $request->pn_id;
        $arr['item_id'] = $request->item_id;
        $arr['item_name'] = $objItemMaster->getItemName($request->item_id);
        $arr['quantity'] =  $request->quantity;

        return $arr;
    }



    
}
