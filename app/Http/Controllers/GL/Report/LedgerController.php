<?php

namespace App\Http\Controllers\GL\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\GL\Primary\AccType;
use App\Models\GL\Primary\ControllAccount;
use App\Models\GL\Primary\SubAccount;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

use App\Rules\ZeroValidation;
use App\Rules\CurrencyValidation;
use App\Rules\GL\Transaction\JournalEntryGLPostValidation;

class LedgerController extends Controller{

    public function loadView(){

        $data['controll_account'] = ControllAccount::all();
        $data['sub_account'] = SubAccount::all();
        $data['attributes'] = $this->getLedgerReportAttributes(NULL, NULL);

        return view('GL.report.ledger')->with('LR', $data);
    }

    public function getLedgerReportAttributes($process, $request){

        $attributes['ca_id'] = 0;
        $attributes['sa_id'] = 0;
        $attributes['ledger_report'] = array();
        $attributes['ledger_bottom'] = array();

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        $input = $request->input();
        if(is_null($input) == FALSE){

            $attributes['ca_id'] = $input['ca_id'];
            $attributes['sa_id'] = $input['sa_id'];
            $attributes['ledger_report'] = array();
            $attributes['ledger_bottom'] = array();
        }

        if( ($process['validation_result'] == TRUE) && ($process['process_status'] == TRUE)){

            $attributes['process_status'] = TRUE;
			$attributes['validation_messages'] = new MessageBag();

			$message = $process['front_end_message'];
			$attributes['process_message'] = '<div class="alert alert-success" role="alert"> '. $message .' </div> ';

			return $attributes;

        }else{

            $attributes['process_status'] = FALSE;
			$attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';

			return $attributes;
        }

    }

    public function generateLedger(Request $request){

        $validation_result = $this->validateLedgerReport($request);
        if($validation_result['validation_result'] == TRUE ){

            $ledger_result = DB::table('general_ledger')->where('sa_id', $request->sa_id)
                                                    ->orderBy('gle_date')
                                                    ->orderBy('gl_entry_sub_id')
                                                    ->orderBy('acc_type')
                                                    ->get();



            $ledger_bottom['debit_amount'] = $ledger_result->where('acc_type', 1)->sum('amount');
            $ledger_bottom['credit_amount'] = $ledger_result->where('acc_type', 2)->sum('amount');

            if( $ledger_bottom['debit_amount'] == $ledger_bottom['credit_amount'] ){

                $ledger_bottom['highest_amount'] = $ledger_bottom['debit_amount'];
                $ledger_bottom['balance_amount'] = 0;

            }elseif( $ledger_bottom['debit_amount'] > $ledger_bottom['credit_amount'] ){

                $ledger_bottom['highest_amount'] = $ledger_bottom['debit_amount'];
                $ledger_bottom['balance_amount'] = $ledger_bottom['debit_amount'] - $ledger_bottom['credit_amount'];

            }elseif( $ledger_bottom['credit_amount'] > $ledger_bottom['debit_amount'] ){

                $ledger_bottom['highest_amount'] = $ledger_bottom['credit_amount'];
                $ledger_bottom['balance_amount'] = $ledger_bottom['credit_amount'] - $ledger_bottom['debit_amount'];

            }else{

            }

            $attributes['validation_messages'] = new MessageBag();
            $attributes['process_message'] = '';
            $attributes['ca_id'] = $request->ca_id;
            $attributes['sa_id'] = $request->sa_id;
            $attributes['ledger_report'] = $ledger_result;
            $attributes['ledger_bottom'] = $ledger_bottom;

            $data['attributes'] = $attributes;

        }else{

            $validation_result['process_status'] = FALSE;

            $data['attributes'] = $this->getLedgerReportAttributes($validation_result, $request);
        }

        $data['controll_account'] = ControllAccount::all();
        $data['sub_account'] = SubAccount::all();

        return view('GL.report.ledger')->with('LR', $data);
    }


    private function validateLedgerReport($request){

        //try{

            //$inputs['at_id'] = $request->at_id;
            $inputs['sa_id'] = $request->sa_id;

            //$rules['at_id'] = array();
            $rules['sa_id'] = array( new ZeroValidation('Sub Account', $request->sa_id));

            $front_end_message = '';

            $validator = Validator::make($inputs, $rules);
            $validation_result = $validator->passes();
            if($validation_result == FALSE){

                $front_end_message = 'Please Check Your Inputs';
            }

            $process_result['validation_result'] = $validator->passes();
            $process_result['validation_messages'] =  $validator->errors();
            $process_result['front_end_message'] = $front_end_message;
            $process_result['back_end_message'] =  'Ledger Controller - Validation Process ';

            return $process_result;

        // }catch(\Exception $e){

        //     $process_result['validation_result'] = FALSE;
        //     $process_result['validation_messages'] = new MessageBag();
        //     $process_result['front_end_message'] =  $e->getMessage();
        //     $process_result['back_end_message'] =  'Ledger Controller - Validation Function Fault';

		// 	return $process_result;
        // }
    }

}
