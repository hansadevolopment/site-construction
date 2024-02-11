<?php

namespace App\Rules\SiteMM\SiteOperation;

use Illuminate\Contracts\Validation\Rule;

use Illuminate\Support\Facades\DB;
use App\Helpers\Database\EloquentHelper;

class EmployeeAdvanceIsSettlementValidation implements Rule {

    public function __construct() {

    }

    public function passes($attribute, $value) {

        $result = DB::table('employee_advance')->where('ea_id', $value)->value('settle');
        if( EloquentHelper::recordExists($result) ){

            return TRUE;
        }else{

            return FALSE;
        }

    }

    public function message(){

        return 'This Employee Advance is Settled.';
    }
}
