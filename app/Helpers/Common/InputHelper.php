<?php

namespace App\Helpers\Common;

class InputHelper{

    public static function currencyToNumber($number){

        $currencyValue = str_replace(",","", $number);
        if( (preg_match('/^\d+(\.\d{1,2})?$/', $number)) ){

            return floatval($currencyValue);

        }else{

            return floatval(0);
        }
    }

    public static function stringToNumber($number){

        if( is_numeric($number)){

            return floatval($number);

        }else{

            return 0;
        }
    }

}
