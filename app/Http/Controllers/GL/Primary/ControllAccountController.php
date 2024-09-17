<?php

namespace App\Http\Controllers\GL\Primary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\GL\Primary\MainAccount;
use App\Models\GL\Primary\ControllAccount;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

use App\Rules\ZeroValidation;

class ControllAccountController extends Controller {

    public function loadView(){

        $data['main_account'] = MainAccount::all();
        $data['attributes'] = $this->getControllAccountAttributes(NULL, NULL);

        return view('GL.primary.controll_account')->with('CA', $data);
    }

    private function getControllAccountAttributes($process, $request){

        $attributes['ma_id'] = '0';
        $attributes['ca_id'] = '#Auto#';
        $attributes['ca_name'] = '';

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        if( ($process['validation_result'] == TRUE) && ($process['process_status'] == TRUE)){

            $elqControllAccount = ControllAccount::where('ca_id', $process['ca_id'])->first();
            if($elqControllAccount->count() >= 1) {

                $attributes['ma_id'] = $elqControllAccount->ma_id;
                $attributes['ca_id'] = $elqControllAccount->ca_id;
                $attributes['ca_name'] = $elqControllAccount->ca_name;
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
                $attributes['ca_id'] = $inputs['ca_id'];
                $attributes['ca_name'] = $inputs['ca_name'];
            }

            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';
        }

        return $attributes;
    }

    public function saveControllAccount(Request $request){

        if( $request->submit == 'Reset' ){

            $data['attributes'] = $this->getControllAccountAttributes(NULL, NULL);
        }

        if( $request->submit == 'Save' ){

            $ca_validation_result = $this->validateControllAccount($request);
            if($ca_validation_result['validation_result'] == TRUE){

                $saving_process_result = $this->saveAccount($request);

                $saving_process_result['validation_result'] = $ca_validation_result['validation_result'];
                $saving_process_result['validation_messages'] = $ca_validation_result['validation_messages'];

                $data['attributes'] = $this->getControllAccountAttributes($saving_process_result, $request);

            }else{

                $ca_validation_result['ma_id'] = $request->ma_id;
                $ca_validation_result['process_status'] = FALSE;

                $data['attributes'] = $this->getControllAccountAttributes($ca_validation_result, $request);
            }
        }

        $data['main_account'] = MainAccount::all();
        return view('GL.primary.controll_account')->with('CA', $data);
    }

    private function validateControllAccount($request){

        //try{

            $inputs['ma_id'] = $request->ma_id;
            $inputs['ca_id'] = $request->ca_id;
            $inputs['ca_name'] = $request->ca_name;

            $rules['ma_id'] = array( new ZeroValidation('Main Account', $request->ma_id));
            $rules['ca_id'] = array('required');
            $rules['ca_name'] = array('required', 'max:50');

            $front_end_message = '';

            $validator = Validator::make($inputs, $rules);
            $validation_result = $validator->passes();
            if($validation_result == FALSE){

                $front_end_message = 'Please Check Your Inputs';
            }

            $process_result['validation_result'] = $validator->passes();
            $process_result['validation_messages'] =  $validator->errors();
            $process_result['front_end_message'] = $front_end_message;
            $process_result['back_end_message'] =  'Controll Account Controller - Validation Process ';

            return $process_result;

        // }catch(\Exception $e){

        //     $process_result['validation_result'] = FALSE;
        //     $process_result['validation_messages'] = new MessageBag();
        //     $process_result['front_end_message'] =  $e->getMessage();
        //     $process_result['back_end_message'] =  'Controll Account Controller - Validation Function Fault';

		// 	return $process_result;
        // }
    }

    private function saveAccount($request){

        //try{

            $objControllAccount = new ControllAccount();

            $controll_account['controll_account'] = $this->getControllAccountArray($request);
            $saving_process_result = $objControllAccount->saveControllAccount($controll_account);

            return $saving_process_result;

        // }catch(\Exception $e){

        //     $process_result['ma_id'] = $request->ma_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'Controll Account Controller -> Account Saving Process <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

    private function getControllAccountArray($request){

        if( $request->ca_id == '#Auto#' ){

            $last_serial = $this->getLastControllAccountId($request);

            $number_formatter = new \NumberFormatter('de_DE', \NumberFormatter::DECIMAL);
            $number_formatter->setPattern("0000");
            $ca_id = $request->ma_id . $number_formatter->format($last_serial);

        }else{

            $ca_id = $request->ca_id;
        }

        $controll_account['ma_id'] = $request->ma_id;
        $controll_account['ca_id'] = $ca_id;
        $controll_account['ca_name'] = $request->ca_name;

        if( ControllAccount::where('ca_id', $ca_id)->exists() ){

            $controll_account['updated_by'] = Auth::id();
            $controll_account['updated_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }else{

            $controll_account['saved_by'] = Auth::id();
            $controll_account['saved_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        return $controll_account;
    }

    private function getLastControllAccountId($request){

        $ca_id = ControllAccount::where('ma_id', $request->ma_id)->orderBy('ca_id', 'desc')->value('ca_id');
        if( is_null($ca_id) ){

            return 1;

        }else{

            $ca_id = Str::substr($ca_id, 3, Str::length($ca_id));
            $ca_id = intval($ca_id);
            return $ca_id + 1;
        }

    }

    public function openControllAccount(Request $request){

        $process_result['ca_id'] = $request->source_id;
        $process_result['process_status'] = TRUE;
        $process_result['validation_result'] = TRUE;
        $process_result['validation_messages'] =  new MessageBag();
        $process_result['front_end_message'] = '';
        $process_result['back_end_message'] = '';

        $data['main_account'] = MainAccount::all();
        $data['attributes'] = $this->getControllAccountAttributes($process_result, $request);

        return view('GL.primary.controll_account')->with('CA', $data);
    }

}
