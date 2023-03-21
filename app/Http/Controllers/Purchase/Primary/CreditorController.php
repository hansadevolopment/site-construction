<?php

namespace App\Http\Controllers\Purchase\Primary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Purchase\Primary\Creditor;

use App\Rules\CurrencyValidation;
use App\Rules\ZeroValidation;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;

class CreditorController extends Controller {

    public function loadView(){

        $data['attributes'] = $this->getCreditorAttributes(NULL, NULL);

        return view('purchase.primary.creditor')->with('Creditor', $data);
    }

    private function getCreditorAttributes($process, $request){

        $attributes['creditor_id'] = '#Auto#';
        $attributes['creditor_name'] = '';
        $attributes['contact_number'] = '';
        $attributes['contact_persons'] = '';
        $attributes['emails'] = '';
        $attributes['fax'] = '';
        $attributes['address'] = '';
        $attributes['active'] = '1';

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        if( ($process['validation_result'] == TRUE) && ($process['process_status'] == TRUE)){

            $objCreditor = new Creditor();
            $creditor_table = $objCreditor->getCreditor($process['creditor_id']);
            foreach ($creditor_table as $row) {

                $attributes['creditor_id'] = $row->creditor_id;
                $attributes['creditor_name'] = $row->creditor_name;
                $attributes['contact_number'] = $row->contact_number;
                $attributes['contact_persons'] = $row->contact_persons;
                $attributes['emails'] = $row->emails;
                $attributes['fax'] = $row->fax;
                $attributes['address'] = $row->address;
                $attributes['active'] = $row->active;
            }

            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-success" role="alert"> '. $message .' </div> ';

        }else{

            $inputs = $request->input();
            if(is_null($inputs) == FALSE){

                $attributes['creditor_id'] = $inputs['creditor_id'];
                $attributes['creditor_name'] = $inputs['creditor_name'];
                $attributes['contact_number'] = $inputs['contact_number'];
                $attributes['contact_persons'] = $inputs['contact_persons'];
                $attributes['emails'] = $inputs['emails'];
                $attributes['fax'] = $inputs['fax'];
                $attributes['address'] = $inputs['address'];
                $attributes['active'] = $inputs['active'];
            }

            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';
        }
      
        return $attributes;
    }

    public function creditorProcess(Request $request){


        $creditor_validation_result = $this->creditorValidationProcess($request);

        if( $creditor_validation_result['validation_result'] == TRUE){

            $creditor_saving_process = $this->creditorSavingProcess($request);

            $creditor_saving_process['validation_result'] = $creditor_validation_result['validation_result'];
			$creditor_saving_process['validation_messages'] = $creditor_validation_result['validation_messages'];

            $data['attributes'] = $this->getCreditorAttributes($creditor_saving_process, $request);

        }else{

            $creditor_validation_result['creditor_id'] = $request->creditor_id;
			$creditor_validation_result['process_status'] = FALSE;

            $data['attributes'] = $this->getCreditorAttributes($creditor_validation_result, $request);
        }

        return view('purchase.primary.creditor')->with('Creditor', $data);
    }

    private function creditorValidationProcess($request){

        try{

            $inputs['creditor_id'] = $request->creditor_id;
            $inputs['creditor_name'] = $request->creditor_name;
            $inputs['contact_number'] = $request->contact_number;
            $inputs['contact_persons'] = $request->contact_persons;
            $inputs['emails'] = $request->emails;
            $inputs['fax'] = $request->fax;
            $inputs['address'] = $request->address;
            
            $rules['creditor_id'] = array('required');
            $rules['creditor_name'] = array('required', 'max:50');
            $rules['contact_number'] = array('required', 'max:100');
            $rules['contact_persons'] = array('required', 'max:100');
            $rules['emails'] = array('max:100');
            $rules['address'] = array('required', 'max:100');
            $rules['fax'] = array('max:100');

            $front_end_message = '';

            $validator = Validator::make($inputs, $rules);
            $validation_result = $validator->passes();
            if($validation_result == FALSE){

                $front_end_message = 'Please Check Your Inputs';
            }

            $process_result['validation_result'] = $validator->passes();
            $process_result['validation_messages'] =  $validator->errors();
            $process_result['front_end_message'] = $front_end_message;
            $process_result['back_end_message'] =  'Debtor Controller - Validation Process ';

            return $process_result;
            
        }catch(\Exception $e){

            $process_result['validation_result'] = FALSE;
            $process_result['validation_messages'] = new MessageBag();
            $process_result['front_end_message'] =  $e->getMessage();
            $process_result['back_end_message'] =  'Debtor Controller - Validation Function Fault';

            return $process_result;
        }
    }

    private function creditorSavingProcess($request){

        try{

            $objCreditor = new Creditor();

            $data['creditor'] = $this->getCreditorTable($request);

            $saving_process_result = $objCreditor->saveCreditor($data);

            return $saving_process_result;

        }catch(\Exception $e){

            $process_result['credit_id'] = $request->credit_id;
            $process_result['process_status'] = FALSE;
            $process_result['front_end_message'] = $e->getMessage();
            $process_result['back_end_message'] = 'Creditor Controller -> Creditor Saving Process <br> ' . $e->getLine();

            return $process_result;
        }

    }

    private function getCreditorTable($request){

        $creditor['creditor_id'] = $request->creditor_id;
        $creditor['creditor_name'] = $request->creditor_name;
        $creditor['contact_number'] = $request->contact_number;
        $creditor['contact_persons'] = $request->contact_persons;
        $creditor['emails'] = $request->emails;
        $creditor['fax'] = $request->fax;
        $creditor['address'] = $request->address;
        $creditor['active'] = $request->active;

        $creditor['updated_by'] = Auth::id();
        $creditor['updated_on'] = Now();
        $creditor['updated_ip'] = '-';

        return $creditor;
    }
    
}
