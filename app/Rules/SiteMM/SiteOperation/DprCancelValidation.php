<?php

namespace App\Rules\SiteMM\SiteOperation;

use Illuminate\Contracts\Validation\Rule;

use Illuminate\Support\Facades\DB;
use App\Helpers\Database\EloquentHelper;

class DprCancelValidation implements Rule {

    public function __construct() {

    }

    public function passes($attribute, $value){

        $result = DB::table('dpr')->where('dpr_id', $value)->value('cancel');
        if( EloquentHelper::recordExists($result) ){

            return TRUE;
        }else{

            return FALSE;
        }
    }

    public function message(){

        return 'This Daily Progress record is Cancelled.';
    }
}
