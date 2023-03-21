<?php

namespace App\Http\Controllers\Sales\Primary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Sales\Primary\SalesRep;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;

class SalesRepController extends Controller {

    public function loadView(){

        $data['attributes'] = $this->getSalesRepAttributes(NULL, NULL);

        return view('sales.primary.sales_rep')->with('SR', $data);
    }

    private function getSalesRepAttributes($process, $request){

        $objSalesRep = new SalesRep();

        $attributes['sales_rep_id'] = '#Auto#';
        $attributes['sales_rep_name'] = '';
        $attributes['contact_numbers'] = '';
        $attributes['emails'] = '';
        $attributes['fax'] = '';
        $attributes['address'] = '';
        $attributes['active'] = 1;

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        if( ($process['validation_result'] == TRUE) && ($process['process_status'] == TRUE)){

            $sales_rep_table = $objSalesRep->getSalesRep($process['sales_rep_id']);
            foreach ($sales_rep_table as $row) {

                $attributes['sales_rep_id'] = $row->sales_rep_id;
                $attributes['sales_rep_name'] = $row->sales_rep_name;
                $attributes['contact_numbers'] = $row->contact_numbers;
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

                $attributes['sales_rep_id'] = $inputs['sales_rep_id'];
                $attributes['sales_rep_name'] = $inputs['sales_rep_name'];
                $attributes['contact_numbers'] = $inputs['contact_numbers'];
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

    public function salesRepProcess(Request $request){

        $sales_rep_validation_result = $this->salesRepValidationProcess($request);

        if( $sales_rep_validation_result['validation_result'] == TRUE){

            $sales_rep_saving_process = $this->salesRepSavingProcess($request);

            $sales_rep_saving_process['validation_result'] = $sales_rep_validation_result['validation_result'];
			$sales_rep_saving_process['validation_messages'] = $sales_rep_validation_result['validation_messages'];

            $data['attributes'] = $this->getSalesRepAttributes($sales_rep_saving_process, $request);

        }else{

            $sales_rep_validation_result['sales_rep_id'] = $request->sales_rep_id;
			$sales_rep_validation_result['process_status'] = FALSE;

            $data['attributes'] = $this->getSalesRepAttributes($sales_rep_validation_result, $request);
        }

        return view('sales.primary.sales_rep')->with('SR', $data);
    }

    private function salesRepValidationProcess($request){

        try{

            $inputs['sales_rep_id'] = $request->sales_rep_id;
            $inputs['sales_rep_name'] = $request->sales_rep_name;
            $inputs['contact_numbers'] = $request->contact_numbers;
            $inputs['emails'] = $request->emails;
            $inputs['fax'] = $request->fax;
            $inputs['address'] = $request->address;
            
            $rules['sales_rep_id'] = array('required');
            $rules['sales_rep_name'] = array('required', 'max:50');
            $rules['contact_numbers'] = array('required', 'max:100');
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
            $process_result['back_end_message'] =  'Sales Rep - Validation Process ';

            return $process_result;
            
        }catch(\Exception $e){

            $process_result['validation_result'] = FALSE;
            $process_result['validation_messages'] = new MessageBag();
            $process_result['front_end_message'] =  $e->getMessage();
            $process_result['back_end_message'] =  'Sales Rep - Validation Function Fault';

            return $process_result;
        }
    }

    private function salesRepSavingProcess($request){

        try{

            $objSalesRep = new SalesRep();

            $data['sales_rep'] = $this->getSalesRepTable($request);

            $saving_process_result = $objSalesRep->saveSalesLocation($data);

            return $saving_process_result;

        }catch(\Exception $e){

            $process_result['sales_rep_id'] = $request->sales_rep_id;
            $process_result['process_status'] = FALSE;
            $process_result['front_end_message'] = $e->getMessage();
            $process_result['back_end_message'] = 'Sales Rep -> Sales Rep Process <br> ' . $e->getLine();

            return $process_result;
        }
    }

    private function getSalesRepTable($request){

        $sales_rep['sales_rep_id'] = $request->sales_rep_id;
        $sales_rep['sales_rep_name'] = $request->sales_rep_name;
        $sales_rep['contact_numbers'] = $request->contact_numbers;
        $sales_rep['emails'] = $request->emails;
        $sales_rep['fax'] = $request->fax;
        $sales_rep['address'] = $request->address;
        $sales_rep['active'] = $request->active;

        $sales_rep['updated_by'] = Auth::id();
        $sales_rep['updated_ip'] = '-';

        return $sales_rep;
    }
    
}
