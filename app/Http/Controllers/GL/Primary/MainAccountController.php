<?php

namespace App\Http\Controllers\GL\Primary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\GL\Primary\AccountType;
use App\Models\GL\Primary\MainAccount;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

use App\Rules\ZeroValidation;
use App\Rules\GL\Primary\AccountTypeChangeValidation;

class MainAccountController extends Controller{

    public function loadView(){

        $data['account_type'] = AccountType::all();
        $data['attributes'] = $this->getMainAccountAttributes(NULL, NULL);

        return view('GL.primary.main_account')->with('MA', $data);
    }

    private function getMainAccountAttributes($process, $request){

        $attributes['at_id'] = '0';
        $attributes['ma_id'] = '#Auto#';
        $attributes['ma_name'] = '';

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        if( ($process['validation_result'] == TRUE) && ($process['process_status'] == TRUE)){

            $elqMainAccount = MainAccount::where('ma_id', $process['ma_id'])->first();
            if($elqMainAccount->count() >= 1) {

                $attributes['ma_id'] = $elqMainAccount->ma_id;
                $attributes['at_id'] = $elqMainAccount->at_id;
                $attributes['ma_name'] = $elqMainAccount->ma_name;
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

                $attributes['ma_id'] = $inputs['ma_id'];
                $attributes['at_id'] = $inputs['at_id'];
                $attributes['ma_name'] = $inputs['ma_name'];
            }

            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';
        }

        return $attributes;
    }

    public function saveMainAccount(Request $request){

        if( $request->submit == 'Reset' ){

            $data['attributes'] = $this->getMainAccountAttributes(NULL, NULL);
        }

        if( $request->submit == 'Save' ){

            $main_validation_result = $this->validateMainAccount($request);
            if($main_validation_result['validation_result'] == TRUE){

                $saving_process_result = $this->saveAccount($request);

                $saving_process_result['validation_result'] = $main_validation_result['validation_result'];
                $saving_process_result['validation_messages'] = $main_validation_result['validation_messages'];

                $data['attributes'] = $this->getMainAccountAttributes($saving_process_result, $request);

            }else{

                $main_validation_result['ma_id'] = $request->ma_id;
                $main_validation_result['process_status'] = FALSE;

                $data['attributes'] = $this->getMainAccountAttributes($main_validation_result, $request);
            }
        }

        $data['account_type'] = AccountType::all();
        return view('GL.primary.main_account')->with('MA', $data);
    }

    private function validateMainAccount($request){

        //try{

            $inputs['at_id'] = $request->at_id;
            $inputs['ma_id'] = $request->ma_id;
            $inputs['ma_name'] = $request->ma_name;

            $rules['at_id'] = array( new ZeroValidation('Account Type', $request->at_id) , new AccountTypeChangeValidation($request->ma_id) );
            $rules['ma_id'] = array('required');
            $rules['ma_name'] = array('required', 'max:50');

            $front_end_message = '';

            $validator = Validator::make($inputs, $rules);
            $validation_result = $validator->passes();
            if($validation_result == FALSE){

                $front_end_message = 'Please Check Your Inputs';
            }

            $process_result['validation_result'] = $validator->passes();
            $process_result['validation_messages'] =  $validator->errors();
            $process_result['front_end_message'] = $front_end_message;
            $process_result['back_end_message'] =  'Main Account Controller - Validation Process ';

            return $process_result;

        // }catch(\Exception $e){

        //     $process_result['validation_result'] = FALSE;
        //     $process_result['validation_messages'] = new MessageBag();
        //     $process_result['front_end_message'] =  $e->getMessage();
        //     $process_result['back_end_message'] =  'Main Account Controller - Validation Function Fault';

		// 	return $process_result;
        // }
    }

    private function saveAccount($request){

        //try{

            $objMainAccount = new MainAccount();

            $main_account['main_account'] = $this->getMainAccountArray($request);
            $saving_process_result = $objMainAccount->saveMainAccount($main_account);

            return $saving_process_result;

        // }catch(\Exception $e){

        //     $process_result['ma_id'] = $request->ma_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'Main Account Controller -> Account Saving Process <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

    private function getMainAccountArray($request){

        if( $request->ma_id == '#Auto#' ){

            $last_serial = $this->getLastMainAccountId($request);

            $number_formatter = new \NumberFormatter('de_DE', \NumberFormatter::DECIMAL);
            $number_formatter->setPattern("00");
            $ma_id = $request->at_id . $number_formatter->format($last_serial);

        }else{

            $ma_id = $request->ma_id;
        }

        $main_account['ma_id'] = $ma_id;
        $main_account['at_id'] = $request->at_id;
        $main_account['ma_name'] = $request->ma_name;

        if( MainAccount::where('ma_id', $ma_id)->exists() ){

            $main_account['updated_by'] = Auth::id();
            $main_account['updated_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }else{

            $main_account['saved_by'] = Auth::id();
            $main_account['saved_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        return $main_account;
    }

    private function getLastMainAccountId($request){

        $ma_id = MainAccount::where('at_id', $request->at_id)->orderBy('ma_id', 'desc')->value('ma_id');
        if( is_null($ma_id) ){

            return 1;

        }else{

            $ma_id = Str::substr($ma_id, 1, Str::length($ma_id));
            $ma_id = intval($ma_id);
            return $ma_id + 1;
        }

    }

    public function openMainAccount(Request $request){

        $process_result['ma_id'] = $request->source_id;
        $process_result['process_status'] = TRUE;
        $process_result['validation_result'] = TRUE;
        $process_result['validation_messages'] =  new MessageBag();
        $process_result['front_end_message'] = '';
        $process_result['back_end_message'] = '';

        $data['account_type'] = AccountType::all();
        $data['attributes'] = $this->getMainAccountAttributes($process_result, $request);

        return view('GL.primary.main_account')->with('MA', $data);
    }

}
