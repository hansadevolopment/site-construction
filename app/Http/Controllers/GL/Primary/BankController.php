<?php

namespace App\Http\Controllers\GL\Primary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\GL\Primary\Bank;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Carbon;

use App\Rules\ZeroValidation;

class BankController extends Controller{

    public function loadView(){

        $data['attributes'] = $this->getBankAttributes(NULL, NULL);

        return view('GL.primary.bank')->with('B', $data);
    }

    private function getBankAttributes($process, $request){

        $attributes['bank_id'] = '#Auto#';
        $attributes['bank_name'] = '';
        $attributes['active'] = 1;

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        if( ($process['validation_result'] == TRUE) && ($process['process_status'] == TRUE)){

            $elqBank = Bank::where('bank_id', $process['bank_id'])->first();
            if($elqBank->count() >= 1) {

                $attributes['bank_id'] = $elqBank->bank_id;
                $attributes['bank_name'] = $elqBank->bank_name;
                $attributes['active'] = $elqBank->active;
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

                $attributes['bank_id'] = $inputs['bank_id'];
                $attributes['bank_name'] = $inputs['bank_name'];
                $attributes['active'] = $inputs['active'];
            }

            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';
        }

        return $attributes;
    }

    public function saveBank(Request $request){

        if( $request->submit == 'Reset' ){

            $data['attributes'] = $this->getBankAttributes(NULL, NULL);
        }

        if( $request->submit == 'Save' ){

            $validation_result = $this->validateBank($request);
            if($validation_result['validation_result'] == TRUE){

                $process_result = $this->saveBankProcess($request);

                $process_result['validation_result'] = $validation_result['validation_result'];
                $process_result['validation_messages'] = $validation_result['validation_messages'];

                $data['attributes'] = $this->getBankAttributes($process_result, $request);

            }else{

                $validation_result['bank_id'] = $request->bank_id;
                $validation_result['process_status'] = FALSE;

                $data['attributes'] = $this->getBankAttributes($validation_result, $request);
            }
        }

        return view('GL.primary.bank')->with('B', $data);
    }

    private function validateBank($request){

        //try{

            $inputs['bank_id'] = $request->bank_id;
            $inputs['bank_name'] = $request->bank_name;
            $inputs['active'] = $request->active;

            $rules['bank_id'] = array('required');
            $rules['bank_name'] = array('required', 'max:50');
            $rules['active'] = array( new ZeroValidation('Active', $request->active));

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

    private function saveBankProcess($request){

        //try{

            $objBank = new Bank();

            $bank['bank'] = $this->getBankArray($request);
            $process_result = $objBank->saveBank($bank);

            return $process_result;

        // }catch(\Exception $e){

        //     $process_result['bank_id'] = $request->bank_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'Bank Controller -> Account Saving Process <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

    private function getBankArray($request){

        $bank['bank_id'] = $request->bank_id;
        $bank['bank_name'] = $request->bank_name;
        $bank['active'] = $request->active;

        if( Bank::where('bank_id', $request->bank_id)->exists() ){

            $bank['updated_by'] = Auth::id();
            $bank['updated_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }else{

            $bank['saved_by'] = Auth::id();
            $bank['saved_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        return $bank;
    }

    public function openBank(Request $request){

        $process_result['bank_id'] = $request->source_id;
        $process_result['process_status'] = TRUE;
        $process_result['validation_result'] = TRUE;
        $process_result['validation_messages'] =  new MessageBag();
        $process_result['front_end_message'] = '';
        $process_result['back_end_message'] = '';

        $data['attributes'] = $this->getBankAttributes($process_result, $request);
        return view('GL.primary.bank')->with('B', $data);
    }

}
