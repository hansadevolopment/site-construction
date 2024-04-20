<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class QuantityValidation implements Rule {

    protected $message = NULL;

    public function __construct(){

    }

    public function passes($attribute, $value){

        if( (preg_match('/^\d+(\.\d{1,3})?$/', $value)) && ($value != 0) ){

            return TRUE;

        }else{

            return FALSE;
        }

    }

    public function message(){

        return $this->message;
    }
}
