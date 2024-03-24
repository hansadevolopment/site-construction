<?php

namespace App\Http\Controllers\GL\Primary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\GL\Primary\ControllAccount;
use App\Models\GL\Primary\SubAccount;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

use App\Rules\ZeroValidation;

class SubAccountController extends Controller {

    public function loadView(){

        $data['controll_account'] = ControllAccount::all();
        $data['attributes'] = $this->getSubAccountAttributes(NULL, NULL);

        return view('GL.primary.sub_account')->with('SA', $data);
    }

    private function getSubAccountAttributes($process, $request){

        $attributes['ca_id'] = '0';
        $attributes['sa_id'] = '#Auto#';
        $attributes['sa_name'] = '';

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        if( ($process['validation_result'] == TRUE) && ($process['process_status'] == TRUE)){

            $elqSubAccount = SubAccount::where('sa_id', $process['sa_id'])->first();
            if($elqSubAccount->count() >= 1) {

                $attributes['ca_id'] = $elqSubAccount->ca_id;
                $attributes['sa_id'] = $elqSubAccount->sa_id;
                $attributes['sa_name'] = $elqSubAccount->sa_name;
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

                $attributes['ca_id'] = $inputs['ca_id'];
                $attributes['sa_id'] = $inputs['sa_id'];
                $attributes['sa_name'] = $inputs['sa_name'];
            }

            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';
        }

        return $attributes;

    }

    public function saveSubAccount(Request $request){

        if( $request->submit == 'Reset' ){

            $data['attributes'] = $this->getSubAccountAttributes(NULL, NULL);
        }

        if( $request->submit == 'Save' ){

            $ca_validation_result = $this->validateSubAccount($request);
            if($ca_validation_result['validation_result'] == TRUE){

                $saving_process_result = $this->saveAccount($request);

                $saving_process_result['validation_result'] = $ca_validation_result['validation_result'];
                $saving_process_result['validation_messages'] = $ca_validation_result['validation_messages'];

                $data['attributes'] = $this->getSubAccountAttributes($saving_process_result, $request);

            }else{

                $ca_validation_result['ma_id'] = $request->ma_id;
                $ca_validation_result['process_status'] = FALSE;

                $data['attributes'] = $this->getSubAccountAttributes($ca_validation_result, $request);
            }
        }

        $data['controll_account'] = ControllAccount::all();
        return view('GL.primary.sub_account')->with('SA', $data);
    }

    private function validateSubAccount($request){

        //try{

            $inputs['ca_id'] = $request->ca_id;
            $inputs['sa_id'] = $request->sa_id;
            $inputs['sa_name'] = $request->sa_name;

            $rules['ca_id'] = array( new ZeroValidation('Controll Account', $request->ca_id));
            $rules['sa_id'] = array('required');
            $rules['sa_name'] = array('required', 'max:50');

            $front_end_message = '';

            $validator = Validator::make($inputs, $rules);
            $validation_result = $validator->passes();
            if($validation_result == FALSE){

                $front_end_message = 'Please Check Your Inputs';
            }

            $process_result['validation_result'] = $validator->passes();
            $process_result['validation_messages'] =  $validator->errors();
            $process_result['front_end_message'] = $front_end_message;
            $process_result['back_end_message'] =  'Sub Account Controller - Validation Process ';

            return $process_result;

        // }catch(\Exception $e){

        //     $process_result['validation_result'] = FALSE;
        //     $process_result['validation_messages'] = new MessageBag();
        //     $process_result['front_end_message'] =  $e->getMessage();
        //     $process_result['back_end_message'] =  'Sub Account Controller - Validation Function Fault';

		// 	return $process_result;
        // }
    }

    private function saveAccount($request){

        //try{

            $objSubAccount = new SubAccount();

            $sub_account['sub_account'] = $this->getSubAccountArray($request);
            $saving_process_result = $objSubAccount->saveSubAccount($sub_account);

            return $saving_process_result;

        // }catch(\Exception $e){

        //     $process_result['ma_id'] = $request->ma_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'Controll Account Controller -> Account Saving Process <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

    private function getSubAccountArray($request){

        if( $request->sa_id == '#Auto#' ){

            $last_serial = $this->getLastSubAccountId($request);

            $number_formatter = new \NumberFormatter('de_DE', \NumberFormatter::DECIMAL);
            $number_formatter->setPattern("00000");
            $sa_id = $request->ca_id . $number_formatter->format($last_serial);

        }else{

            $sa_id = $request->sa_id;
        }

        $sub_account['ca_id'] = $request->ca_id;
        $sub_account['sa_id'] = $sa_id;
        $sub_account['sa_name'] = $request->sa_name;

        if( SubAccount::where('sa_id', $sa_id)->exists() ){

            $sub_account['updated_by'] = Auth::id();
            $sub_account['updated_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }else{

            $sub_account['saved_by'] = Auth::id();
            $sub_account['saved_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        return $sub_account;
    }

    private function getLastSubAccountId($request){

        $sa_id = SubAccount::where('ca_id', $request->ca_id)->orderBy('sa_id', 'desc')->value('sa_id');
        if( is_null($sa_id) ){

            return 1;

        }else{

            $sa_id = Str::substr($sa_id, 7, Str::length($sa_id));
            $sa_id = intval($sa_id);
            return $sa_id + 1;
        }

    }

    public function openSubAccount(Request $request){

        $process_result['sa_id'] = $request->source_id;
        $process_result['process_status'] = TRUE;
        $process_result['validation_result'] = TRUE;
        $process_result['validation_messages'] =  new MessageBag();
        $process_result['front_end_message'] = '';
        $process_result['back_end_message'] = '';

        $data['controll_account'] = ControllAccount::all();
        $data['attributes'] = $this->getSubAccountAttributes($process_result, $request);

        return view('GL.primary.sub_account')->with('SA', $data);
    }



}
