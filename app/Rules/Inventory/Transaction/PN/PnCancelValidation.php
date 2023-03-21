<?php

namespace App\Rules\Inventory\Transaction\PN;

use Illuminate\Contracts\Validation\Rule;

use App\Models\Inventory\Transaction\ProductionNote; 

class PnCancelValidation implements Rule {

    protected $message = '';

    public function __construct(){

    }

    public function passes($attribute, $value){

        $objProductionNote = new ProductionNote();
        
        $result = $objProductionNote->isCancelProductionNote($value);

        if($result == 1){

            $this->message = 'This Production Note '. $value .' was cancelled.';
            return FALSE;
        }else{

            return TRUE;
        }
    }

    public function message(){

        return $this->message;
    }
    
}
