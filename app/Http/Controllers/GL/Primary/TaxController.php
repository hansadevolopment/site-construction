<?php

namespace App\Http\Controllers\GL\Primary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\GL\Primary\Tax;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Carbon;

use App\Rules\ZeroValidation;

class TaxController extends Controller {

    public function loadView(){

        $data['attributes'] = $this->getTaxAttributes(NULL, NULL);

        return view('GL.primary.tax')->with('Tax', $data);
    }

    private function getTaxAttributes($process, $request){

        $attributes['tax_id'] = '#Auto#';
        $attributes['tax_name'] = '';
        $attributes['short_name'] = '';
        $attributes['tax_rate'] = 0;
        $attributes['active'] = 1;

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        if( ($process['validation_result'] == TRUE) && ($process['process_status'] == TRUE)){

            $elqTax = Tax::where('tax_id', $process['tax_id'])->first();
            if($elqTax->count() >= 1) {

                $attributes['tax_id'] = $elqTax->tax_id;
                $attributes['tax_name'] = $elqTax->tax_name;
                $attributes['short_name'] = $elqTax->short_name;
                $attributes['tax_rate'] = $elqTax->tax_rate;
                $attributes['active'] = $elqTax->active;
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

                $attributes['tax_id'] = $inputs['tax_id'];
                $attributes['tax_name'] = $inputs['tax_name'];
                $attributes['short_name'] = $inputs['short_name'];
                $attributes['tax_rate'] = $inputs['tax_rate'];
                $attributes['active'] = $inputs['active'];
            }

            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';
        }

        return $attributes;
    }

    public function saveTax(Request $request){

        if( $request->submit == 'Reset' ){

            $data['attributes'] = $this->getTaxAttributes(NULL, NULL);
        }

        if( $request->submit == 'Save' ){

            $validation_result = $this->validateTax($request);
            if($validation_result['validation_result'] == TRUE){

                $process_result = $this->saveTaxInformation($request);

                $process_result['validation_result'] = $validation_result['validation_result'];
                $process_result['validation_messages'] = $validation_result['validation_messages'];

                $data['attributes'] = $this->getTaxAttributes($process_result, $request);

            }else{

                $validation_result['tax_id'] = $request->tax_id;
                $validation_result['process_status'] = FALSE;

                $data['attributes'] = $this->getTaxAttributes($validation_result, $request);
            }
        }

        return view('GL.primary.tax')->with('Tax', $data);
    }

    private function validateTax($request){

        //try{

            $inputs['tax_id'] = $request->tax_id;
            $inputs['tax_name'] = $request->tax_name;
            $inputs['short_name'] = $request->short_name;
            $inputs['tax_rate'] = $request->tax_rate;
            $inputs['active'] = $request->active;

            $rules['tax_id'] = array('required');
            $rules['tax_name'] = array('required', 'string', 'max:20');
            $rules['short_name'] = array('required','string','max:10');
            $rules['tax_rate'] = array('required','numeric','between:0,100.00');
            $rules['active'] = array('required','boolean', new ZeroValidation('Active', $request->active));

            $front_end_message = '';

            $validator = Validator::make($inputs, $rules);
            $validation_result = $validator->passes();
            if($validation_result == FALSE){

                $front_end_message = 'Please Check Your Inputs';
            }

            $process_result['validation_result'] = $validator->passes();
            $process_result['validation_messages'] =  $validator->errors();
            $process_result['front_end_message'] = $front_end_message;
            $process_result['back_end_message'] =  'Bank Controller - Validation Process ';

            return $process_result;

        // }catch(\Exception $e){

        //     $process_result['validation_result'] = FALSE;
        //     $process_result['validation_messages'] = new MessageBag();
        //     $process_result['front_end_message'] =  $e->getMessage();
        //     $process_result['back_end_message'] =  'Bank Controller - Validation Function Fault';

		// 	return $process_result;
        // }
    }

    private function saveTaxInformation($request){

        //try{

            $objTax = new Tax();

            $tax['tax'] = $this->getTaxArray($request);
            $process_result = $objTax->saveTax($tax);

            return $process_result;

        // }catch(\Exception $e){

        //     $process_result['tax_id'] = $request->tax_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'Tax Controller -> Account Saving Process <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

    private function getTaxArray($request){

        $tax['tax_id'] = $request->tax_id;
        $tax['tax_name'] = $request->tax_name;
        $tax['short_name'] = $request->short_name;
        $tax['tax_rate'] = $request->tax_rate;
        $tax['active'] = $request->active;

        if( Tax::where('tax_id', $request->tax_id)->exists() ){

            $tax['updated_by'] = Auth::id();
            $tax['updated_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }else{

            $tax['saved_by'] = Auth::id();
            $tax['saved_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        return $tax;
    }

    public function openTax(Request $request){

        $process_result['tax_id'] = $request->source_id;
        $process_result['process_status'] = TRUE;
        $process_result['validation_result'] = TRUE;
        $process_result['validation_messages'] =  new MessageBag();
        $process_result['front_end_message'] = '';
        $process_result['back_end_message'] = '';

        $data['attributes'] = $this->getTaxAttributes($process_result, $request);

        return view('GL.primary.tax')->with('Tax', $data);
    }


}
