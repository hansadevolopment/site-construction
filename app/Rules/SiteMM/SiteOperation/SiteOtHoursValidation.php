<?php

namespace App\Rules\SiteMM\SiteOperation;

use Illuminate\Contracts\Validation\Rule;

class SiteOtHoursValidation implements Rule {

    protected $message = NULL;
    protected $total_ot_hours = 0;
    protected $site_total_ot_hours = 0;

    public function __construct($total_ot_hours,  $site_total_ot_hours){

        $this->total_ot_hours = $total_ot_hours;
        $this->site_total_ot_hours = $site_total_ot_hours;
    }

    public function passes($attribute, $value){

        if( $this->total_ot_hours == $this->site_total_ot_hours){

            return TRUE;

        }else{

            $this->message = 'Overtime Hours not match.';
            return FALSE;
        }

    }

    public function message(){

        return $this->message;
    }
}
