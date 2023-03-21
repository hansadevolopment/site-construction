<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ZeroValidation implements Rule {

    protected $referance = NULL;
    protected $value = NULL;

    public function __construct($ref, $val){

        $this->referance = $ref;
        $this->value = $val;
    }

    public function passes($attribute, $value){

        settype($this->value, 'integer');

        if($this->value === 0){

            return FALSE;
        }else{

            return TRUE;
        }
        
    }

    public function message(){

        return ' Please select the ' . $this->referance;
        
    }

}
