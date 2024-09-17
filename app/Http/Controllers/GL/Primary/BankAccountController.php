<?php

namespace App\Http\Controllers\GL\Primary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\GL\Primary\BankAccount;
use App\Models\GL\Primary\Bank;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Carbon;

use App\Rules\ZeroValidation;

class BankAccountController extends Controller {

    public function loadView(){

        $data['bank'] = Bank::all();
        $data['attributes'] = $this->getBankAccountAttributes(NULL, NULL);

        return view('GL.primary.bank_account')->with('BA', $data);
    }

    private function getBankAccountAttributes($process, $request){

        $attributes['ba_id'] = '#Auto#';
        $attributes['ba_no'] = '';
        $attributes['branch_name'] = '';
        $attributes['short_name'] = '';
        $attributes['bank_id'] = "0";
        $attributes['active'] = 1;

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        if( ($process['validation_result'] == TRUE) && ($process['process_status'] == TRUE)){

            $elqBankAccount = BankAccount::where('bank_id', $process['ba_id'])->first();
            if($elqBankAccount->count() >= 1) {

                $attributes['ba_id'] = $elqBankAccount->ba_id;
                $attributes['ba_no'] = $elqBankAccount->ba_no;
                $attributes['branch_name'] = $elqBankAccount->branch_name;
                $attributes['short_name'] = $elqBankAccount->short_name;
                $attributes['bank_id'] = $elqBankAccount->bank_id;
                $attributes['active'] = $elqBankAccount->active;
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

                $attributes['ba_id'] = $inputs['ba_id'];
                $attributes['ba_no'] = $inputs['ba_no'];
                $attributes['branch_name'] = $inputs['branch_name'];
                $attributes['short_name'] = $inputs['short_name'];
                $attributes['bank_id'] = $inputs['bank_id'];
                $attributes['active'] = $inputs['active'];
            }

            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';
        }

        return $attributes;
    }

    public function saveBankAccount(Request $request){

        if( $request->submit == 'Reset' ){

            $data['attributes'] = $this->getBankAccountAttributes(NULL, NULL);
        }

        if( $request->submit == 'Save' ){

            $validation_result = $this->validateBankAccount($request);
            if($validation_result['validation_result'] == TRUE){

                $process_result = $this->saveBankAccountInformation($request);

                $process_result['validation_result'] = $validation_result['validation_result'];
                $process_result['validation_messages'] = $validation_result['validation_messages'];

                $data['attributes'] = $this->getBankAccountAttributes($process_result, $request);

            }else{

                $validation_result['bank_id'] = $request->bank_id;
                $validation_result['process_status'] = FALSE;

                $data['attributes'] = $this->getBankAccountAttributes($validation_result, $request);
            }
        }

        $data['bank'] = Bank::all();
        return view('GL.primary.bank_account')->with('BA', $data);
    }

    private function validateBankAccount($request){

        //try{

            $inputs['ba_id'] = $request->ba_id;
            $inputs['ba_no'] = $request->ba_no;
            $inputs['branch_name'] = $request->branch_name;
            $inputs['short_name'] = $request->short_name;
            $inputs['bank_id'] = $request->bank_id;
            $inputs['active'] = $request->active;

            $rules['ba_id'] = array('required');
            $rules['ba_no'] = array('required', 'string', 'max:20');
            $rules['branch_name'] = array('required','string','max:30');
            $rules['short_name'] = array('required','string','max:30');
            $rules['bank_id'] =  array('required','boolean', new ZeroValidation('Bank', $request->bank_id));
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

    private function saveBankAccountInformation($request){

        //try{

            $objBankAccount = new BankAccount();

            $bank_account['bank_account'] = $this->getBankAccountArray($request);
            $process_result = $objBankAccount->saveBankAccount($bank_account);

            return $process_result;

        // }catch(\Exception $e){

        //     $process_result['ba_id'] = $request->ba_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'Bank Account Controller -> Account Saving Process <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

    private function getBankAccountArray($request){

        $bank_account['ba_id'] = $request->ba_id;
        $bank_account['ba_no'] = $request->ba_no;
        $bank_account['branch_name'] = $request->branch_name;
        $bank_account['short_name'] = $request->short_name;
        $bank_account['bank_id'] = $request->bank_id;
        $bank_account['active'] = $request->active;

        if( BankAccount::where('ba_id', $request->ba_id)->exists() ){

            $bank_account['updated_by'] = Auth::id();
            $bank_account['updated_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }else{

            $bank_account['saved_by'] = Auth::id();
            $bank_account['saved_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        return $bank_account;
    }

    public function openBankAccount(Request $request){

        $process_result['ba_id'] = $request->source_id;
        $process_result['process_status'] = TRUE;
        $process_result['validation_result'] = TRUE;
        $process_result['validation_messages'] =  new MessageBag();
        $process_result['front_end_message'] = '';
        $process_result['back_end_message'] = '';

        $data['bank'] = Bank::all();
        $data['attributes'] = $this->getBankAccountAttributes($process_result, $request);

        return view('GL.primary.bank_account')->with('BA', $data);
    }


}
