<?php

namespace App\Traits\Validations;

use \stdClass;

trait InputValidateTrait{

    public function validateInput($inputCollection){

        $objInput = new stdClass;

        foreach($inputCollection as $key => $value){

            $objInput->$key = $this->validateInputItem($value);
        }

        return $objInput;
    }

    private function validateInputItem($inputItem) {

        $inputResult = trim($inputItem);
        $inputResult = stripslashes($inputResult);
        $inputResult = htmlspecialchars($inputResult);

        return $inputResult;
    }

}
