<?php

namespace App\Helpers\Common;

class InputHelper{

    public static function currencyToNumber($number){

        return floatval(str_replace("," , "", $number));
    }

}
