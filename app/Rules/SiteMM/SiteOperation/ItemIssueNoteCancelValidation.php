<?php

namespace App\Rules\SiteMM\SiteOperation;

use Illuminate\Contracts\Validation\Rule;

use Illuminate\Support\Facades\DB;
use App\Helpers\Database\EloquentHelper;

class ItemIssueNoteCancelValidation implements Rule {

    protected $transaction_type = '';

    public function __construct($type){

        $this->transaction_type = $type;
    }

    public function passes($attribute, $value){

        if( $this->transaction_type == 'New' ){

            $result = DB::table('item_issue_note')->where('iin_id', $value)->value('cancel');
            if( EloquentHelper::recordExists($result) ){

                return FALSE;
            }else{

                return TRUE;
            }

        }else{

            $result = DB::table('item_issue_note')->where('iin_id', $value)->value('cancel');
            if( EloquentHelper::recordExists($result) ){

                if($result == TRUE){

                    return FALSE;
                }else{

                    return TRUE;
                }

            }else{

                return TRUE;
            }

        }
    }

    public function message(){

        return 'This Item issue note is Cancelled.';
    }
}
