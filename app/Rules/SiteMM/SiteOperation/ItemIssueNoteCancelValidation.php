<?php

namespace App\Rules\SiteMM\SiteOperation;

use Illuminate\Contracts\Validation\Rule;

use Illuminate\Support\Facades\DB;
use App\Helpers\Database\EloquentHelper;

class ItemIssueNoteCancelValidation implements Rule {

    protected $return_message = '';
    protected $transaction_type = '';

    public function __construct($type){

        $this->transaction_type = $type;
    }

    public function passes($attribute, $value){

        if( $this->transaction_type == 'save' ){

            if( $value == '#Auto#'){

                return TRUE;

            }else{

                $result = DB::table('item_issue_note')->where('iin_id', $value)->value('cancel');
                if( EloquentHelper::recordExists($result) ){

                    if($result == TRUE){

                        $this->return_message = 'This Item issue note is Cancelled.';
                        return FALSE;
                    }else{

                        return TRUE;
                    }
                }else{

                    return TRUE;
                }
            }

        }else{


            if( $value == '#Auto#'){

                $this->return_message = 'Invalied Item issue note no.';
                return FALSE;

            }else{

                $result = DB::table('item_issue_note')->where('iin_id', $value)->value('cancel');
                if( EloquentHelper::recordExists($result) ){

                    if($result == TRUE){

                        $this->return_message = 'This record is already cancelled.';
                        return FALSE;

                    }else{

                        return TRUE;
                    }
                }else{

                    return TRUE;
                }
            }

        }
    }

    public function message(){

        return  $this->return_message;
    }
}
