<?php

namespace App\Http\Controllers\GL\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\GL\Primary\AccType;
use App\Models\GL\Primary\MainAccount;
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

        $data['main_account'] = MainAccount::all();
        $data['sub_account'] = SubAccount::all();
        $data['attributes'] = $this->getLedgerReportAttributes(NULL, NULL);

        return view('GL.report.ledger')->with('LR', $data);
    }

    public function getLedgerReportAttributes($process, $request){

        $attributes['ma_id'] = 0;
        $attributes['sa_id'] = 0;
        $attributes['ledger_report'] = array();

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        $input = $request->input();
        if(is_null($input) == FALSE){

            $attributes['ma_id'] = $input['ma_id'];
            $attributes['sa_id'] = $input['sa_id'];
            $attributes['ledger_report'] = array();
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


        $ledger_result = DB::table('general_ledger')
                            ->where('sa_id', $request->sa_id)
                            ->orderBy('gle_date')
                            ->orderBy('gl_entry_sub_id')
                            ->orderBy('acc_type')
                            ->get();

        $data['main_account'] = MainAccount::all();
        $data['sub_account'] = SubAccount::all();


        $attributes['validation_messages'] = new MessageBag();
        $attributes['process_message'] = '';
        $attributes['ma_id'] = $request->ma_id;
        $attributes['sa_id'] = $request->sa_id;
        $attributes['ledger_report'] = $ledger_result;

        $data['attributes'] = $attributes;

        //dd( $attributes );

        return view('GL.report.ledger')->with('LR', $data);

    }

}
