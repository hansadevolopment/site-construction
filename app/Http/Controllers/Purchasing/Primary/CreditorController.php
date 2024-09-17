<?php

namespace App\Http\Controllers\Purchasing\Primary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Purchasing\Primary\Creditor;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Carbon;

class CreditorController extends Controller {

    public function loadView(){

        $data['attributes'] = $this->getCreditorAttributes(NULL, NULL);

        return view('Purchasing.Primary.creditor')->with('CR', $data);
    }

    private function getCreditorAttributes($process, $request){

        $attributes['creditor_id'] = '#Auto#';
        $attributes['creditor_name'] = '';
        $attributes['address'] = '';
        $attributes['contact_persons'] = '';
        $attributes['contact_numbers'] = '';
        $attributes['fax_numbers'] = '';
        $attributes['email'] = '';
        $attributes['credit_limit'] = 0;
        $attributes['active'] = '1';

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        if( ($process['validation_result'] == TRUE) && ($process['process_status'] == TRUE)){

            $purchasing_category_table = Creditor::where('creditor_id', $process['creditor_id'])->get();
            foreach ($purchasing_category_table as $row) {

                $attributes['creditor_id'] = $row->creditor_id;
                $attributes['creditor_name'] = $row->creditor_name;
                $attributes['address'] = $row->address;
                $attributes['contact_persons'] = $row->contact_persons;
                $attributes['contact_numbers'] = $row->contact_numbers;
                $attributes['fax_numbers'] = $row->fax_numbers;
                $attributes['email'] = $row->email;
                $attributes['credit_limit'] = $row->credit_limit;
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
                $attributes['address'] = $inputs['address'];
                $attributes['contact_persons'] = $inputs['contact_persons'];
                $attributes['contact_numbers'] = $inputs['contact_numbers'];
                $attributes['fax_numbers'] = $inputs['fax_numbers'];
                $attributes['email'] = $inputs['email'];
                $attributes['credit_limit'] = $inputs['credit_limit'];
                $attributes['active'] = $inputs['active'];
            }

            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';
        }

        return $attributes;
    }

    public function saveCreditor(Request $request){

        $validation_result = $this->CreditorValidationProcess($request);

        if( $validation_result['validation_result'] == TRUE){

            $saving_process = $this->CreditorSavingProcess($request);

            $saving_process['validation_result'] = $validation_result['validation_result'];
			$saving_process['validation_messages'] = $validation_result['validation_messages'];

            $data['attributes'] = $this->getCreditorAttributes($saving_process, $request);

        }else{

            $validation_result['creditor_id'] = $request->creditor_id;
			$validation_result['process_status'] = FALSE;

            $data['attributes'] = $this->getCreditorAttributes($validation_result, $request);
        }

        return view('Purchasing.Primary.creditor')->with('CR', $data);
    }

    private function CreditorValidationProcess($request){

        try{

            $inputs['creditor_id'] = $request->creditor_id;
            $inputs['creditor_name'] = $request->creditor_name;
            $inputs['address'] = $request->address;
            $inputs['contact_persons'] = $request->contact_persons;
            $inputs['contact_numbers'] = $request->contact_numbers;
            $inputs['fax_numbers'] = $request->fax_numbers;
            $inputs['email'] = $request->email;
            $inputs['credit_limit'] = $request->credit_limit;
            $inputs['active'] = $request->active;

            $rules['creditor_id'] = array('required');
            $rules['creditor_name'] = array('required', 'max:50');
            $rules['address'] = array('required', 'max:100');
            $rules['contact_persons'] = array('required', 'max:100');
            $rules['contact_numbers'] = array('required', 'max:60');
            $rules['fax_numbers'] = array('max:50');
            $rules['email'] = array('max:50');
            $rules['credit_limit'] = array('numeric');
            $rules['active'] = array('max:50');

            $front_end_message = '';

            $validator = Validator::make($inputs, $rules);
            $validation_result = $validator->passes();
            if($validation_result == FALSE){

                $front_end_message = 'Please Check Your Inputs';
            }

            $process_result['validation_result'] = $validator->passes();
            $process_result['validation_messages'] =  $validator->errors();
            $process_result['front_end_message'] = $front_end_message;
            $process_result['back_end_message'] =  'Creditor - Validation Process ';

            return $process_result;

        }catch(\Exception $e){

            $process_result['validation_result'] = FALSE;
            $process_result['validation_messages'] = new MessageBag();
            $process_result['front_end_message'] =  $e->getMessage();
            $process_result['back_end_message'] =  'Creditor - Validation Function Fault';

            return $process_result;
        }
    }

    private function CreditorSavingProcess($request){

        //try{

            $objCreditor = new Creditor();

            $data['creditor'] = $this->getCreditorTable($request);
            $saving_process_result = $objCreditor->saveCreditor($data);

            return $saving_process_result;

        // }catch(\Exception $e){

        //     $process_result['creditor_id'] = $request->creditor_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'Creditor -> Creditor Process <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

    private function getCreditorTable($request){

        $creditor['creditor_id'] = $request->creditor_id;
        $creditor['creditor_name'] = $request->creditor_name;
        $creditor['address'] = $request->address;
        $creditor['contact_persons'] = $request->contact_persons;
        $creditor['contact_numbers'] = $request->contact_numbers;
        $creditor['fax_numbers'] = $request->fax_numbers;
        $creditor['email'] = $request->email;
        $creditor['credit_limit'] = $request->credit_limit;
        $creditor['active'] = $request->active;

        if( Creditor::where('creditor_id', $request->creditor_id)->exists() ){

            $creditor['updated_by'] = Auth::id();
            $creditor['updated_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }else{

            $creditor['saved_by'] = Auth::id();
            $creditor['saved_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        return $creditor;
    }


}
