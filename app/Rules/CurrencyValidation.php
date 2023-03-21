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
        
        if(is_numeric($currency_value)){

            if($this->avoid_zero_validation == 0){

                if($currency_value > 0){

                    return TRUE;
    
                }else{
    
                    $this->message = 'The Amount must be grater than zero value.';
                    return FALSE;
                }
            }

			return TRUE;

		}else{

			$this->message = 'The Amount must be Numeric';
			return FALSE;
		}
    
    }

    public function message(){

        return $this->message;
    }

}
