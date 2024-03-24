<?php

namespace App\Http\Controllers\GL\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\GL\Primary\AccType;
use App\Models\GL\Primary\SubAccount;
use App\Models\GL\Transaction\JournalEntry;
use App\Models\GL\Transaction\TmpJournalEntry;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

use App\Helpers\Database\EloquentHelper;

use App\Rules\ZeroValidation;
use App\Rules\CurrencyValidation;
use App\Rules\GL\Transaction\JournalEntryGLPostValidation;

class JournalEntryController extends Controller{

    public function loadView(){

        $data['sub_account'] = SubAccount::all();
        $data['account_type'] = AccType::all();
        $data['attributes'] = $this->getJournalEntryAttributes(NULL, NULL);

        return view('GL.transaction.journal_entry')->with('JE', $data);
    }

    private function getJournalEntryAttributes($process, $request){

        $attributes['je_id'] = '#Auto#';
        $attributes['je_date'] = date('Y-m-d');
        $attributes['gl_post_id'] = '';
        $attributes['remark'] = '';
        $tmpJournalEntry = DB::table('tmp_journal_entry')->where('saved_by', Auth::user()->id)->orderBy('acc_type_id')->get();
        $attributes['total_debit_amount'] = $tmpJournalEntry->where('acc_type_id', 1)->sum('amount');
        $attributes['total_credit_amount'] = $tmpJournalEntry->where('acc_type_id', 2)->sum('amount');
        $attributes['je_detail'] = $tmpJournalEntry;

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }


        if( method_exists($request,'input') ){

            $input = $request->input();
            if(is_null($input) == FALSE){

                $attributes['je_id'] = $input['je_id'];
                $attributes['je_date'] = $input['je_date'];
                $attributes['remark'] = $input['remark'];
                $tmpJournalEntry = DB::table('tmp_journal_entry')->where('saved_by', Auth::user()->id)->orderBy('acc_type_id')->get();
                $attributes['total_debit_amount'] = $tmpJournalEntry->where('acc_type_id', 1)->sum('amount');
                $attributes['total_credit_amount'] = $tmpJournalEntry->where('acc_type_id', 2)->sum('amount');
                $attributes['je_detail'] = $tmpJournalEntry;
            }
        }


        if( ($process['validation_result'] == TRUE) && ($process['process_status'] == TRUE)){


            if( $request->submit == 'GL Post'){

                $attributes['je_id'] = $process['je_id'];
                $JournalEntry = DB::table('journal_entry')->where('je_id', $process['je_id'])->first();
                if( EloquentHelper::recordExists($JournalEntry)  ){

                    $attributes['je_date'] = $JournalEntry->je_date;
                    $attributes['remark'] = $JournalEntry->remark;
                }

                $JournalEntryDetail = DB::table('journal_entry_detail')->where('je_id', $process['je_id'])->orderBy('acc_type_id')->get();
                $attributes['je_detail'] = $JournalEntryDetail;
                $attributes['total_debit_amount'] = $JournalEntryDetail->where('acc_type_id', 1)->sum('amount');
                $attributes['total_credit_amount'] = $JournalEntryDetail->where('acc_type_id', 2)->sum('amount');
            }

            $attributes['process_status'] = TRUE;
			$attributes['validation_messages'] = new MessageBag();

            if( $process['front_end_message'] == '' ){

                $attributes['process_message'] = '';
            }else{

                $message = $process['front_end_message'];
                $attributes['process_message'] = '<div class="alert alert-success" role="alert"> '. $message .' </div> ';
            }

			return $attributes;

        }else{

            $attributes['process_status'] = FALSE;
			$attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';

			return $attributes;
        }

    }

    public function saveJournalEntry(Request $request){

        if( $request->submit == 'Reset' ){

            $data['attributes'] = $this->getJournalEntryAttributes(NULL, NULL);
        }

        if( $request->submit == 'Add' ){

            $journal_entry_validation_result = $this->validateJournalEntryProcess($request);
            if( $journal_entry_validation_result['validation_result'] == TRUE ){

                $je_saving_result = $this->saveTmpJournalEntry($request);

                $je_saving_result['validation_result'] = $journal_entry_validation_result['validation_result'];
                $je_saving_result['validation_messages'] = $journal_entry_validation_result['validation_messages'];

                $data['attributes'] = $this->getJournalEntryAttributes($je_saving_result, $request);

            }else{

			    $journal_entry_validation_result['process_status'] = FALSE;
			    $data['attributes'] = $this->getJournalEntryAttributes($journal_entry_validation_result, $request);
            }
        }

        if( $request->submit == 'GL Post'){

            $general_ledger_validation_result = $this->validateGeneralLedgerPostProcess($request);
            if( $general_ledger_validation_result['validation_result'] == TRUE ){

                $gl_saving_result = $this->postGenralLedger($request);

                $gl_saving_result['validation_result'] = $general_ledger_validation_result['validation_result'];
                $gl_saving_result['validation_messages'] = $general_ledger_validation_result['validation_messages'];

                $data['attributes'] = $this->getJournalEntryAttributes($gl_saving_result, $request);

            }else{

			    $general_ledger_validation_result['process_status'] = FALSE;
			    $data['attributes'] = $this->getJournalEntryAttributes($general_ledger_validation_result, $request);
            }
        }

        $data['sub_account'] = SubAccount::all();
        $data['account_type'] = AccType::all();

        return view('GL.transaction.journal_entry')->with('JE', $data);
    }

    private function validateJournalEntryProcess($request){

        //try{

			$front_end_message = " ";

			$input['JE Date'] = $request->je_date;
            $input['description'] = $request->description;
	        $input['Account'] = $request->sub_account;
			$input['Account Type'] = $request->acc_type;
			$input['Amount']= $request->amount;

			$rules['JE Date'] = array('required', 'date');
            $rules['description'] = array('required', 'max:50');
	        $rules['Account'] = array( new ZeroValidation('Account', $request->account));
			$rules['Account Type'] = array( new ZeroValidation('Account Type', $request->acc_type));
			$rules['Amount'] = array('required', 'numeric', new CurrencyValidation(0) );

			$validator = Validator::make($input, $rules);
	        $validation_result = $validator->passes();
	        if($validation_result == FALSE){

	            $front_end_message = 'Please Check Your Inputs';
	        }

	        $process_result['validation_result'] =  $validation_result;
	        $process_result['validation_messages'] =  $validator->errors();
	        $process_result['front_end_message'] = $front_end_message;
	        $process_result['back_end_message'] =  'Journal Entry Controller - Validation Process ';

	        return $process_result;

		// }catch(\Exception $e){

		// 	$process_result['validation_result'] = FALSE;
        //     $process_result['validation_messages'] = new MessageBag();
        //     $process_result['front_end_message'] =  $e->getMessage();
        //     $process_result['back_end_message'] =  'Journal Entry Controller - Validation Function Fault';

		// 	return $process_result;
		// }

    }

    private function saveTmpJournalEntry($request){

        $objTmpJournalEntry = new TmpJournalEntry();

        $je_array['je_id'] = $request->je_id;
        $je_array['je_date'] = $request->je_date;
        $je_array['description'] = $request->description;
        $je_array['sa_id'] = $request->account;
        $je_array['sa_name'] = SubAccount::where('sa_id', $request->account)->value('sa_name');
        $je_array['acc_type_id'] = $request->acc_type;
        $je_array['amount'] = $request->amount;
        $je_array['saved_by'] = Auth::id();
        $je_array['saved_on'] = now();

        $data['journal_entry'] = $je_array;
        $savingResult = $objTmpJournalEntry->addTmpJournalEntry($data);

        return $savingResult;
    }

    private function validateGeneralLedgerPostProcess($request){

         //try{

			$front_end_message = " ";

            $input['JE Id'] = $request->je_id;
			$input['JE Date'] = $request->je_date;
            $input['Remark'] = $request->remark;

            $rules['JE Id'] = array('required', new JournalEntryGLPostValidation());
			$rules['JE Date'] = array('required', 'date');
            $rules['Remark'] = array('max:100');

			$validator = Validator::make($input, $rules);
	        $validation_result = $validator->passes();
	        if($validation_result == FALSE){

	            $front_end_message = 'Please Check Your Inputs';
	        }

	        $process_result['validation_result'] =  $validation_result;
	        $process_result['validation_messages'] =  $validator->errors();
	        $process_result['front_end_message'] = $front_end_message;
	        $process_result['back_end_message'] =  'Journal Entry Controller - Validation Process ';

	        return $process_result;

		// }catch(\Exception $e){

		// 	$process_result['validation_result'] = FALSE;
        //     $process_result['validation_messages'] = new MessageBag();
        //     $process_result['front_end_message'] =  $e->getMessage();
        //     $process_result['back_end_message'] =  'Journal Entry Controller - Validation Function Fault';

		// 	return $process_result;
		// }

    }

    private function postGenralLedger($request){

        $objJournalEntry = new JournalEntry();

        $data['journal_entry'] = $this->getJournalEntry($request);
        $data['journal_entry_detail'] = $this->getJournalEntryDetail($request);
        $data['general_ledger_entry'] = $this->getGenaralLedgerEntry($request);

        $gl_saving_result = $objJournalEntry->saveJournalEntry($data);

        return $gl_saving_result;
    }

    private function getJournalEntry($request){

        $tmpJournalEntry = DB::table('tmp_journal_entry')->where('saved_by', Auth::user()->id)->orderBy('acc_type_id')->get();

        $journal_entry['je_id'] = $request->je_id;
        $journal_entry['je_date'] = $request->je_date;
        $journal_entry['remark'] = $request->remark;
        $journal_entry['debit_amount'] = $tmpJournalEntry->where('acc_type_id', 1)->sum('amount');
        $journal_entry['credit_amount'] = $tmpJournalEntry->where('acc_type_id', 2)->sum('amount');
        $journal_entry['saved_by'] = Auth::user()->id;
        $journal_entry['saved_on'] = Carbon::now()->format('Y-m-d H:i:s');

        return $journal_entry;
    }

    private function getJournalEntryDetail($request){

        $journal_entry_detail = array();

        $tmpJournalEntry = DB::table('tmp_journal_entry')->where('saved_by', Auth::user()->id)->orderBy('acc_type_id')->get();
        foreach($tmpJournalEntry as $rowJE => $valueJE){

            $journal_entry_detail[$rowJE]['je_id'] = '';
            $journal_entry_detail[$rowJE]['sa_id'] = $valueJE->sa_id;
            $journal_entry_detail[$rowJE]['sa_name'] = $valueJE->sa_name;
            $journal_entry_detail[$rowJE]['description'] = $valueJE->description;
            $journal_entry_detail[$rowJE]['acc_type_id'] = $valueJE->acc_type_id;
            $journal_entry_detail[$rowJE]['amount'] = $valueJE->amount;
        }

        return $journal_entry_detail;
    }

    private function getGenaralLedgerEntry($request){

        $gl = array();

        $tmpJournalEntry = DB::table('tmp_journal_entry')->where('saved_by', Auth::user()->id)->orderBy('acc_type_id')->get();
        foreach($tmpJournalEntry as $rowJE => $valueJE){

            $gl[$rowJE]['gl_entry_id'] = '';
            $gl[$rowJE]['gl_entry_sub_id'] = '';
            $gl[$rowJE]['gle_date'] = $request->je_date;
            $gl[$rowJE]['source'] = 'JE';
            $gl[$rowJE]['source_id'] = '';
            $gl[$rowJE]['sa_id'] = $valueJE->sa_id;
            $gl[$rowJE]['sa_name'] = $valueJE->sa_name;
            $gl[$rowJE]['description'] = $valueJE->description;
            $gl[$rowJE]['acc_type'] = $valueJE->acc_type_id;
            $gl[$rowJE]['amount'] = $valueJE->amount;
        }

        return $gl;
    }

    public function removeJournalEntry(Request $request){


        DB::table('tmp_journal_entry')->where('saved_by', Auth::user()->id)
                                      ->where('tmp_je_id', $request->tmp_je_id)
                                      ->delete();

        $attributes['je_id'] = '#Auto#';
        $attributes['je_date'] = date('Y-m-d');
        $attributes['remark'] = '';
        $attributes['gl_post_id'] = '';
        $tmpJournalEntry = DB::table('tmp_journal_entry')->where('saved_by', Auth::user()->id)->orderBy('acc_type_id')->get();
        $attributes['total_debit_amount'] = $tmpJournalEntry->where('acc_type_id', 1)->sum('amount');
        $attributes['total_credit_amount'] = $tmpJournalEntry->where('acc_type_id', 2)->sum('amount');
        $attributes['je_detail'] = $tmpJournalEntry;

        $attributes['process_status'] = TRUE;
        $attributes['validation_messages'] = new MessageBag();

        $message = 'Journal entry item was removed successfully.';
        $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';

        $data['attributes'] = $attributes;
        $data['sub_account'] = SubAccount::all();
        $data['account_type'] = AccType::all();

        return view('GL.transaction.journal_entry')->with('JE', $data);
    }

    public function openJournalEntry(Request $request){


        $process_result['je_id'] = $request->source_id;
        $process_result['process_status'] = TRUE;
        $process_result['front_end_message'] = "";
        $process_result['back_end_message'] = "";
        $process_result['validation_result'] = TRUE;
        $process_result['validation_messages'] = new MessageBag();

        $objRequest = new \stdClass();
        $objRequest->je_id = $request->source_id;
        $objRequest->je_date = '';
        $objRequest->remark = '';
        $objRequest->submit = 'GL Post';

        $data['sub_account'] = SubAccount::all();
        $data['account_type'] = AccType::all();
        $data['attributes'] = $this->getJournalEntryAttributes($process_result, $objRequest);

        return view('GL.transaction.journal_entry')->with('JE', $data);
    }

}
