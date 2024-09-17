<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CurrencyValidation implements Rule {

    protected $message = NULL;
    protected $avoid_zero_validation = NULL;

    public function __construct($para_value){

        $this->avoid_zero_validation = $para_value;
    }

    public function passes($attribute, $value){

        $currency_value = str_replace(",","",$value);

        if( (preg_match('/^\d+(\.\d{1,2})?$/', $currency_value)) && ($currency_value != 0)){

            return TRUE;

        }else{

            $this->message = 'Please enter valid amounts';
            return FALSE;
        }
    }

    public function message(){

        return $this->message;
    }
}
