<?php

namespace App\Http\Controllers\Sales\Primary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Sales\Primary\Debtor;

use App\Rules\CurrencyValidation;
use App\Rules\ZeroValidation;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;

class DebtorController extends Controller {

    public function loadView(){

        $data['attributes'] = $this->getDebtorAttributes(NULL, NULL);

        return view('sales.primary.debtor')->with('Debtor', $data);
    }

    private function getDebtorAttributes($process, $request){

        $objDebtor = new Debtor();

        $attributes['debtor_id'] = '#Auto#';
        $attributes['debtor_name'] = '';
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

            $client_table = $objDebtor->getDebtor($process['debtor_id']);
            foreach ($client_table as $row) {

                $attributes['debtor_id'] = $row->debtor_id;
                $attributes['debtor_name'] = $row->debtor_name;
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

                $attributes['debtor_id'] = $inputs['debtor_id'];
                $attributes['debtor_name'] = $inputs['debtor_name'];
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

    public function debtorProcess(Request $request){

        $debtor_validation_result = $this->debtorValidationProcess($request);

        if( $debtor_validation_result['validation_result'] == TRUE){

            $debtor_saving_process = $this->debtorSavingProcess($request);

            $debtor_saving_process['validation_result'] = $debtor_validation_result['validation_result'];
			$debtor_saving_process['validation_messages'] = $debtor_validation_result['validation_messages'];

            $data['attributes'] = $this->getDebtorAttributes($debtor_saving_process, $request);

        }else{

            $debtor_validation_result['debtor_id'] = $request->debtor_id;
			$debtor_validation_result['process_status'] = FALSE;

            $data['attributes'] = $this->getDebtorAttributes($debtor_validation_result, $request);
        }

        return view('sales.primary.debtor')->with('Debtor', $data);
    }

    private function debtorValidationProcess($request){

        try{

            $inputs['debtor_id'] = $request->debtor_id;
            $inputs['debtor_name'] = $request->debtor_name;
            $inputs['contact_number'] = $request->contact_number;
            $inputs['contact_persons'] = $request->contact_persons;
            $inputs['emails'] = $request->emails;
            $inputs['fax'] = $request->fax;
            $inputs['address'] = $request->address;
            
            $rules['debtor_id'] = array('required');
            $rules['debtor_name'] = array('required', 'max:50');
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

    private function debtorSavingProcess($request){

        try{

            $objDebtor = new Debtor();

            $data['Debtor'] = $this->getDebtorTable($request);

            $saving_process_result = $objDebtor->saveDebtor($data);

            return $saving_process_result;

        }catch(\Exception $e){

            $process_result['debtor_id'] = $request->debtor_id;
            $process_result['process_status'] = FALSE;
            $process_result['front_end_message'] = $e->getMessage();
            $process_result['back_end_message'] = 'Debtor Controller -> Debtor Saving Process <br> ' . $e->getLine();

            return $process_result;
        }
    }

    private function getDebtorTable($request){

        $debtor['debtor_id'] = $request->debtor_id;
        $debtor['debtor_name'] = $request->debtor_name;
        $debtor['contact_number'] = $request->contact_number;
        $debtor['contact_persons'] = $request->contact_persons;
        $debtor['emails'] = $request->emails;
        $debtor['fax'] = $request->fax;
        $debtor['address'] = $request->address;
        $debtor['active'] = $request->active;

        $debtor['updated_by'] = Auth::id();
        $debtor['updated_ip'] = '-';

        return $debtor;
    }
    
}
