<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class QuantityValidation implements Rule {

    protected $message = NULL;

    public function __construct(){

    }

    public function passes($attribute, $value){

        if(is_numeric($value)){

            if($value > 0){

                return TRUE;

            }else{

                $this->message = 'The Amount must be grater than zero value.';
                return FALSE;
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
