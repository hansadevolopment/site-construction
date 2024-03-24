<?php

namespace App\Rules\GL\Transaction;

use Illuminate\Contracts\Validation\Rule;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JournalEntryGLPostValidation implements Rule {

    public function __construct(){

    }

    public function passes($attribute, $value){

        $tmpJournalEntry = DB::table('tmp_journal_entry')->where('saved_by', Auth::user()->id)->orderBy('acc_type_id')->get();

        $debit_amount = $tmpJournalEntry->where('acc_type_id', 1)->sum('amount');
        $credit_amount = $tmpJournalEntry->where('acc_type_id', 2)->sum('amount');

        if( $debit_amount == $credit_amount ){

            return TRUE;
        }else{

            return FALSE;
        }
    }


    public function message(){

        return 'Please check Debit Credit Amounts.';
    }
}
