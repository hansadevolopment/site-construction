<?php

namespace App\Rules\SiteMM\SiteOperation;

use Illuminate\Contracts\Validation\Rule;

use Illuminate\Support\Facades\DB;
use App\Helpers\Database\EloquentHelper;

class PaymentVoucherCancelValidation implements Rule {

    public function __construct(){

    }

    public function passes($attribute, $value){

        $result = DB::table('payment_voucher')->where('pv_id', $value)->value('cancel');
        if( EloquentHelper::recordExists($result) ){

            return TRUE;
        }else{

            return FALSE;
        }

    }

    public function message(){

        return 'This payment voucher is Cancelled.';
    }

}
