<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class DiscountValidation implements Rule {

    protected $message = NULL;

    public function __construct(){

    }

    public function passes($attribute, $value){

        if( ($value === '') || ($value === 0) || ($value === '0') || (is_null($value) == TRUE) ){

            return TRUE;

        }else{

            $currencyValue = str_replace(",","",$value);

            if( (preg_match('/^\d+(\.\d{1,2})?$/', $currencyValue)) && ($currencyValue != 0)){

                return TRUE;

            }else{

                $this->message = 'Please enter valid amounts';
                return FALSE;
            }

        }

    }

    public function message(){

        return $this->message;
    }
}
