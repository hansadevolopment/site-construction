<?php

namespace App\Rules\SiteMM\SiteOperation;

use Illuminate\Contracts\Validation\Rule;

use Illuminate\Support\Facades\DB;
use App\Helpers\Database\EloquentHelper;

class EmployeeAdvanceIsCancalValidation implements Rule {

    public function __construct(){

    }

    public function passes($attribute, $value){

        $result = DB::table('employee_advance')->where('ea_id', $value)->value('cancel');
        if( is_null($result) ){

            return TRUE;

        }else{

            if($result == 1){

                return FALSE;

            }else{

                return TRUE;
            }
        }

    }

    public function message(){

        return 'This Employee Advance is Cancelled.';
    }
}
