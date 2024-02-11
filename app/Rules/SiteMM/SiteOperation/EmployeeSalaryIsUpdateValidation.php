<?php

namespace App\Rules\SiteMM\SiteOperation;

use Illuminate\Contracts\Validation\Rule;

use Illuminate\Support\Facades\DB;

class EmployeeSalaryIsUpdateValidation implements Rule {

    public function __construct(){

    }

    public function passes($attribute, $value) {

        $result = DB::table('employee_salary_advance_settlement_detail')->where('es_id', $value)->get();
        if($result->isNotEmpty()){

            return FALSE;
        }else{

            return TRUE;
        }
    }

    public function message(){

        return "This Employee Salary record couldn't be updated";
    }
}
