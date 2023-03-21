<?php

namespace App\Rules\Inventory\Transaction\SAN;

use Illuminate\Contracts\Validation\Rule;

use App\Models\Inventory\Transaction\StockAdjustmentNote; 

class SanCancelValidation implements Rule {

    protected $message = '';

    public function __construct(){

    }

    public function passes($attribute, $value){

        $objSan = new StockAdjustmentNote();
        
        $result = $objSan->isCancelStockAdjustmentNote($value);

        if($result == 1){

            $this->message = 'This Stock Adjustment Note '. $value .' was cancelled.';
            return FALSE;
        }else{

            return TRUE;
        }
    }

    public function message(){

        return $this->message;
    }


}
