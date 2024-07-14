<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

use Illuminate\Support\Facades\DB;

class CancelValidation implements Rule {

    protected $table = '';
    protected $column = '';
    protected $message = '';
    protected $type = '';

    public function __construct($table, $column, $type){

        $this->table = $table;
        $this->column = $column;
        $this->type = $type;
    }

    public function passes($attribute, $value){

        $isCancel = DB::table($this->table)->where($this->column, $value)->where('cancel', 1)->exists();

        if( $this->type == 'save' ){

            if( $value == '#Auto#'){

                return TRUE;

            }else{

                if( $isCancel ){

                    $this->message = 'This record is Cancelled.';
                    return FALSE;
                }else{

                    return TRUE;
                }
            }

        }else{

            if( $value == '#Auto#'){

                $this->message = 'Invalied No.';
                return FALSE;

            }else{

                if( $isCancel ){

                    $this->message = 'This record is already cancelled.';
                    return FALSE;

                }else{

                    return TRUE;
                }
            }
        }

    }

    public function message(){

        return  $this->message;
    }
}
