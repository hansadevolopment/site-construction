<?php

namespace App\Rules\Inventory\Transaction\IRN;

use Illuminate\Contracts\Validation\Rule;

use App\Models\Inventory\Transaction\ItemRequestNote; 

class IrnCancelValidation implements Rule {

    protected $message = '';

    public function __construct(){

    }

    public function passes($attribute, $value){

        $objIrn = new ItemRequestNote();
        
        $result = $objIrn->isCancelItemRequestNote($value);

        if($result == 1){

            $this->message = 'This Item Request Note '. $value .' was cancelled.';
            return FALSE;
        }else{

            return TRUE;
        }
    }

    public function message(){

        return $this->message;
    }
    
}
