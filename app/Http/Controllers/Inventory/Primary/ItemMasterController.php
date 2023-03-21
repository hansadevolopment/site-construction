<?php

namespace App\Http\Controllers\Inventory\Primary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Inventory\Primary\Unit;
use App\Models\Inventory\Primary\ItemMaster;

use App\Rules\CurrencyValidation;
use App\Rules\ZeroValidation;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;

class ItemMasterController extends Controller {

    public function loadView(){

        $objUnit = new Unit();

        $data['Unit'] = $objUnit->getActiveUnitList();
        $data['attributes'] = $this->getItemMasterAttributes(NULL, NULL);

        return view('inventory.primary.item_master')->with('Item', $data);
    }
    
    private function getItemMasterAttributes($process, $request){

        $attributes['item_id'] = '#Auto#';
        $attributes['item_name'] = '';
        $attributes['unit'] = '0';
        $attributes['serial'] = '';
        $attributes['actual_quantity'] = '';
        $attributes['reorder_quantity'] = '';
        $attributes['item_price'] = '';
        $attributes['item_cost'] = '';
        $attributes['tax'] = '';
        $attributes['issue_method'] = '';
        $attributes['tax'] = '0';
        $attributes['receipe'] = '0';
        $attributes['active'] = '0';
        $attributes['remark'] = '';

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }


        if( ($process['validation_result'] == TRUE) && ($process['process_status'] == TRUE)){

            $objItemMaster = new ItemMaster();

            $item_table = $objItemMaster->getItem($process['item_id']);
            foreach ($item_table as $row) {

                $attributes['item_id'] = $row->item_id;
                $attributes['item_name'] = $row->item_name;
                $attributes['unit'] = $row->unit_id;
                $attributes['serial'] = $row->serial;
                $attributes['actual_quantity'] = $row->actual_quantity;
                $attributes['reorder_quantity'] = $row->reorder_quantity;
                $attributes['item_price'] = $row->item_price;
                $attributes['item_cost'] = $row->item_cost;
                $attributes['tax'] = $row->tax;
                $attributes['receipe'] = $row->receipe;
                $attributes['active'] = $row->active;
                $attributes['tax'] = $row->tax;
                $attributes['remark'] = $row->remark;
            }

            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-success" role="alert"> '. $message .' </div> ';

        }else{

            $inputs = $request->input();
            if(is_null($inputs) == FALSE){

                $attributes['item_id'] = $inputs['item_id'];
                $attributes['item_name'] = $inputs['item_name'];
                $attributes['unit'] = $inputs['unit'];
                $attributes['serial'] = $inputs['serial'];
                $attributes['actual_quantity'] = $inputs['actual_quantity'];
                $attributes['reorder_quantity'] = $inputs['reorder_quantity'];
                $attributes['item_price'] = $inputs['item_price'];
                $attributes['item_cost'] = $inputs['item_cost'];
                $attributes['tax'] = $inputs['tax'];
                $attributes['receipe'] = $inputs['receipe'];
                $attributes['active'] = $inputs['active'];
                $attributes['tax'] = $inputs['tax'];
                $attributes['remark'] = $inputs['remark'];    
            }

            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';
        }

        return $attributes;
    }

    public function itemMasterProcess(Request $request){

        $item_validation_result = $this->itemValidationProcess($request);

        if($item_validation_result['validation_result'] == TRUE){

			$saving_process_result = $this->itemSavingProcess($request);

			$saving_process_result['validation_result'] = $item_validation_result['validation_result'];
			$saving_process_result['validation_messages'] = $item_validation_result['validation_messages'];

            $data['attributes'] = $this->getItemMasterAttributes($saving_process_result, $request);

		}else{

			$item_validation_result['item_id'] = $request->item_id;
			$item_validation_result['process_status'] = FALSE;

            $data['attributes'] = $this->getItemMasterAttributes($item_validation_result, $request);
		}

        $objUnit = new Unit();

        $data['Unit'] = $objUnit->getActiveUnitList();

        return view('inventory.primary.item_master')->with('Item', $data);

    }

    private function itemValidationProcess($request){

        //try{

            $inputs['item_id'] = $request->item_id;
            $inputs['Item Name'] = $request->item_name;
            $inputs['Unit'] = $request->unit;
            $inputs['reorder_quantity'] = $request->reorder_quantity;
            $inputs['item_price'] = $request->item_price;
            $inputs['item_cost'] = $request->item_cost;
            $inputs['remark'] = $request->remark;
            
            $rules['item_id'] = array('required');
            $rules['Item Name'] = array('required', 'max:50');
            $rules['Unit'] = array('required', new ZeroValidation('Unit', $request->unit));
            $rules['reorder_quantity'] = array('required', 'numeric', new CurrencyValidation(0));
            $rules['item_price'] = array('required', 'numeric', new CurrencyValidation(0));
            $rules['item_cost'] = array('required', 'numeric', new CurrencyValidation(0));
            $rules['remark'] = array('max:50');


            $front_end_message = '';

            $validator = Validator::make($inputs, $rules);
            $validation_result = $validator->passes();
            if($validation_result == FALSE){

                $front_end_message = 'Please Check Your Inputs';
            }

            // echo '<pre>';
            // print_r( $validator->errors() );
            // echo '</pre>';

            $process_result['validation_result'] = $validator->passes();
            $process_result['validation_messages'] =  $validator->errors();
            $process_result['front_end_message'] = $front_end_message;
            $process_result['back_end_message'] =  'Item Master Controller - Validation Process ';

            return $process_result;

        // }catch(\Exception $e){

        //     $process_result['validation_result'] = FALSE;
        //     $process_result['validation_messages'] = new MessageBag();
        //     $process_result['front_end_message'] =  $e->getMessage();
        //     $process_result['back_end_message'] =  'Item Master Controller - Validation Function Fault';

		// 	return $process_result;
        // }
    }

    private function itemSavingProcess($request){

        //try{

            $objItemMaster = new ItemMaster();

            $item['item'] = $this->getItemTable($request);
            $saving_process_result = $objItemMaster->itemSavingProcess($item);

            return $saving_process_result;

        // }catch(\Exception $e){

        //     $process_result['item_id'] = $request->item_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'Item Master Controller -> Item Master Saving Process <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

    private function getItemTable($request){

        $item['item_id'] = $request->item_id;
        $item['item_name'] = $request->item_name;
        $item['unit_id'] = $request->unit;
        $item['reorder_quantity'] = $request->reorder_quantity;
        $item['item_price'] = $request->item_price;
        $item['item_cost'] = $request->item_cost;
        $item['tax'] = $request->tax;
        $item['receipe'] = $request->receipe;
        $item['item_issue_method_id'] = 1;
        $item['remark'] = $request->remark;
        $item['active'] = $request->active;
        $item['updated_by'] = Auth::id();
        $item['updated_ip'] = '-';

        return $item;
    }

}
