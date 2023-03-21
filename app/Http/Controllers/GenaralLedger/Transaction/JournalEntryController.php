<?php

namespace App\Http\Controllers\GenaralLedger\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;

use App\Models\GL\Transaction\JournalEntry;
use App\Models\GL\Primary\SubAccounts;

use App\Rules\ZeroValidation;

class JournalEntryController extends Controller {

    public function getJournalEntry(){

        $objSubAccounts = new SubAccounts();
        $objJournalEntry = new JournalEntry();

        $data['account_type'] =  $objSubAccounts->getAccountTypes();
        $data['sub_accounts'] = $objSubAccounts->getSubAccounts();
        $data['data_table'] = $objJournalEntry->getTmpJournalEntry();
        $data['attributes'] = $this->getJournalEntryAtrributes(NULL, NULL);

        return view('gl.transaction.journal_entry')->with('JE', $data);
    }

    private function getJournalEntryAtrributes($process, $request){

        $attributes['je_id'] = '#Auto#';
        $attributes['je_date'] = date("Y/m/d");
		$attributes['remark'] = '';

		$attributes['process_status'] = FALSE;
		$attributes['process_message'] = '';
		$attributes['validation_messages'] = new MessageBag();

		if((is_null($process) == TRUE) && (is_null($request) == TRUE)){

            return $attributes;
        }

        $input = $request->input();
        if(is_null($input) == FALSE){

            $attributes['je_id'] = $input['je_id'];
            $attributes['je_date'] = $input['je_date'];
            $attributes['remark'] = $input['remark'];
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
    
    public function journalEntryProcess(Request $request){

        if( $request->submit == 'Add' ){


            $journal_entry_validation_result = $this->journalEntryValidationProcess($request);
            if( $journal_entry_validation_result['validation_result'] == TRUE ){

                $je_saving_result = $this->saveJournalEntry($request);

                $je_saving_result['validation_result'] = $journal_entry_validation_result['validation_result'];
                $je_saving_result['validation_messages'] = $journal_entry_validation_result['validation_messages'];

                // echo 'Pass ----- <br>';
                // echo '<pre>';
                // print_r($je_saving_result);
                // echo '</pre>';

                $data['attributes'] = $this->getJournalEntryAtrributes($je_saving_result, $request);

            }else{

			    $journal_entry_validation_result['process_status'] = FALSE;

                // echo 'Fail ----- <br>';
                // echo '<pre>';
                // print_r($journal_entry_validation_result);
                // echo '</pre>';

			    $data['attributes'] = $this->getJournalEntryAtrributes($journal_entry_validation_result, $request);
            }
        }

        if( $request->submit == 'GL Post' ){


        }


        $objSubAccounts = new SubAccounts();
        $objJournalEntry = new JournalEntry();

        $data['account_type'] =  $objSubAccounts->getAccountTypes();
        $data['sub_accounts'] = $objSubAccounts->getSubAccounts();
        $data['data_table'] = $objJournalEntry->getTmpJournalEntry();
        

        return view('gl.transaction.journal_entry')->with('JE', $data);
    }

    private function journalEntryValidationProcess($request){

        //try{

			$front_end_message = " ";

			$input['JE Date'] = $request->je_date;
            $input['Remark'] = $request->remark;
	        $input['Account'] = $request->sub_account;
			$input['Account Type'] = $request->account_type;
			$input['Amount']= $request->amount;
            
			$rules['JE Date'] = array('required', 'date');
            $rules['Remark'] = array('required', 'max:500');
	        $rules['Account'] = array( new ZeroValidation('Account', $request->account));
			$rules['Account Type'] = array( new ZeroValidation('Account Type', $request->account_type));
			$rules['Amount'] = array('required', 'numeric');
           
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

    private function saveJournalEntry($request){

        $objSubAccounts = new SubAccounts();
        $objJournalEntry = new JournalEntry();


        $je_array['je_id'] = $request->je_id;
        $je_array['je_date'] = $request->je_date;
        $je_array['remark'] = $request->remark;
        $je_array['sa_id'] = $request->sub_account;
        $je_array['sa_name'] = $objSubAccounts->getSubAccountName($request->sub_account);
        $je_array['acc_id'] = $request->account_type;
        $je_array['amount'] = $request->amount;
        $je_array['saved_by'] = Auth::id();
        $je_array['saved_on'] = now();

        $data['journal_entry'] = $je_array;

        $savingResult = $objJournalEntry->addJournalEntry($data);

        return $savingResult;
    }

    private function glPost($request){


    }


}
