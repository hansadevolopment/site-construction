<?php

namespace App\Rules\Purchasing\Transaction\GRN;

use Illuminate\Contracts\Validation\Rule;

use App\Models\Purchase\Transaction\GoodReceiveNote;

class GrnCancelValidation implements Rule {

    protected $message = '';

    public function __construct(){

    }

    public function passes($attribute, $value){

        $objGrn = new GoodReceiveNote();
        
        $result = $objGrn->isCancelGoodReceiveNote($value);

        if($result == 1){

            $this->message = 'This Grn '. $value .' was cancelled.';
            return FALSE;
        }else{

            return TRUE;
        }

        
    }

    public function message(){

        return $this->message;
    }
    
   
}
