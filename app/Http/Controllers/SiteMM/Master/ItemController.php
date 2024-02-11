<?php

namespace App\Http\Controllers\SiteMM\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SiteMM\Master\Item;
use App\Models\SiteMM\Master\Unit;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Carbon;

use App\Rules\ZeroValidation;
use App\Rules\CurrencyValidation;

class ItemController extends Controller {

    public function loadView(){

        $data['unit'] = Unit::where('active', 1)->get();
        $data['attributes'] = $this->getItemAttributes(NULL, NULL);

        return view('SiteMM.Master.item')->with('Item', $data);
    }

    private function getItemAttributes($process, $request){

        $attributes['item_id'] = '#Auto#';
        $attributes['item_name'] = '';
        $attributes['unit_id'] = '0';
        $attributes['price'] = 0;
        $attributes['active'] = 1;
        $attributes['remark'] = '';

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        if( ($process['validation_result'] == TRUE) && ($process['process_status'] == TRUE)){

            $elqItem = Item::where('item_id', $process['item_id'])->first();
            if($elqItem->count() >= 1) {

                $attributes['item_id'] = $elqItem->item_id;
                $attributes['item_name'] = $elqItem->item_name;
                $attributes['unit_id'] = $elqItem->unit_id;
                $attributes['price'] = $elqItem->price;
                $attributes['active'] = $elqItem->active;
                $attributes['remark'] = $elqItem->remark;
            }

            $attributes['validation_messages'] = $process['validation_messages'];

            if( $process['back_end_message'] == '' ){

                $message = '';
                $attributes['process_message'] = '';

            }else{

                $message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
                $attributes['process_message'] = '<div class="alert alert-success" role="alert"> '. $message .' </div> ';
            }

        }else{

            $inputs = $request->input();
            if(is_null($inputs) == FALSE){

                $attributes['item_id'] = $inputs['item_id'];
                $attributes['item_name'] = $inputs['item_name'];
                $attributes['unit_id'] = $inputs['unit_id'];
                $attributes['price'] = $inputs['price'];
                $attributes['active'] = $inputs['active'];
                $attributes['remark'] = $inputs['remark'];
            }

            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';
        }

        return $attributes;
    }

    public function processItem(Request $request){

        if( $request->submit == 'Reset' ){
            $data['attributes'] = $this->getItemAttributes(NULL, NULL);
        }

        if( $request->submit == 'Save' ){

            $item_validation_result = $this->validateItem($request);
            if($item_validation_result['validation_result'] == TRUE){

                $saving_process_result = $this->saveItem($request);

                $saving_process_result['validation_result'] = $item_validation_result['validation_result'];
                $saving_process_result['validation_messages'] = $item_validation_result['validation_messages'];

                $data['attributes'] = $this->getItemAttributes($saving_process_result, $request);

            }else{

                $item_validation_result['item_id'] = $request->item_id;
                $item_validation_result['process_status'] = FALSE;

                $data['attributes'] = $this->getItemAttributes($item_validation_result, $request);
            }
        }

        $data['unit'] = Unit::where('active', 1)->get();

        return view('SiteMM.Master.item')->with('Item', $data);
    }

    private function validateItem($request){

        //try{

            $inputs['item_id'] = $request->item_id;
            $inputs['item_name'] = $request->item_name;
            $inputs['unit_id'] = $request->unit_id;
            $inputs['price'] = $request->price;
            $inputs['remark'] = $request->remark;

            $rules['item_id'] = array('required');
            $rules['item_name'] = array('required', 'max:50');
            $rules['unit_id'] = array( new ZeroValidation('Unit', $request->unit_id));
            $rules['price'] = array('required', 'numeric', new CurrencyValidation(0));
            $rules['remark'] = array( 'max:100');

            $front_end_message = '';

            $validator = Validator::make($inputs, $rules);
            $validation_result = $validator->passes();
            if($validation_result == FALSE){

                $front_end_message = 'Please Check Your Inputs';
            }

            $process_result['validation_result'] = $validator->passes();
            $process_result['validation_messages'] =  $validator->errors();
            $process_result['front_end_message'] = $front_end_message;
            $process_result['back_end_message'] =  'Site Controller - Validation Process ';

            return $process_result;

        // }catch(\Exception $e){

        //     $process_result['validation_result'] = FALSE;
        //     $process_result['validation_messages'] = new MessageBag();
        //     $process_result['front_end_message'] =  $e->getMessage();
        //     $process_result['back_end_message'] =  'Site Controller - Validation Function Fault';

		// 	return $process_result;
        // }
    }

    private function saveItem($request){

        //try{

            $objItem = new Item();

            $item['item'] = $this->getItemArray($request);
            $saving_process_result = $objItem->saveItem($item);

            return $saving_process_result;

        // }catch(\Exception $e){

        //     $process_result['item_id'] = $request->item_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'item Controller -> item Saving Process <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

    private function getItemArray($request){

        $item['item_id'] = $request->item_id;
        $item['item_name'] = $request->item_name;
        $item['unit_id'] = $request->unit_id;
        $item['price'] = $request->price;
        $item['active'] = $request->active;
        $item['remark'] = $request->remark;

        if( Item::where('item_id', $request->item_id)->exists() ){

            $item['updated_by'] = Auth::id();
            $item['updated_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }else{

            $item['saved_by'] = Auth::id();
            $item['saved_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        return $item;
    }

    public function getItemForSapMaterial(Request $request){

        $elqItem = Item::where('item_id', $request->item_id)->first();

        $item_attributes['price'] = number_format($elqItem->price, 2);
        $item_attributes['unit'] = $elqItem->getUnit()->unit_name;

        return $item_attributes;
    }

    public function openItem(Request $request){

        $process_result['item_id'] = $request->item_id;
        $process_result['process_status'] = TRUE;
        $process_result['validation_result'] = TRUE;
        $process_result['validation_messages'] =  new MessageBag();
        $process_result['front_end_message'] = '';
        $process_result['back_end_message'] = '';

        $data['attributes'] = $this->getItemAttributes($process_result, $request);
        $data['unit'] = Unit::where('active', 1)->get();

        return view('SiteMM.Master.item')->with('Item', $data);
    }

}
