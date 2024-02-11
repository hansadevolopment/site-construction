<?php

namespace App\Rules\SiteMM\SiteOperation;

use Illuminate\Contracts\Validation\Rule;

class SiteWorkingHoursValidation implements Rule {

    protected $message = NULL;
    protected $total_working_hours = 0;
    protected $site_total_working_hours = 0;

    public function __construct($total_working_hours,  $site_total_working_hours){

        $this->total_working_hours = $total_working_hours;
        $this->site_total_working_hours = $site_total_working_hours;
    }

    public function passes($attribute, $value){

        if( $this->total_working_hours == $this->site_total_working_hours){

            return TRUE;

        }else{

            $this->message = 'Working Hours not match.';
            return FALSE;
        }

    }

    public function message(){

        return $this->message;
    }
}
