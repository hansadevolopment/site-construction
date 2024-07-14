<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

use Illuminate\Support\Facades\DB;

class GLPostValidation implements Rule {

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

        $glPostResult = DB::table($this->table)->where($this->column, $value)->where('gl_post_no', 0)->doesntExist();

        if( $this->type == 'save' ){

            if( $value == '#Auto#'){

                return TRUE;

            }else{

                if( $glPostResult ){

                    $this->message = 'This record is already updated on gl module.';
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

                if( $glPostResult ){

                    $this->message = 'This record is already updated on gl module.';
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
