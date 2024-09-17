<?php

namespace App\Rules\GL\Primary;

use Illuminate\Contracts\Validation\Rule;

use Illuminate\Support\Str;
use App\Models\GL\Primary\MainAccount;

class AccountTypeChangeValidation implements Rule {

    protected $ma_id = NULL;

    public function __construct($ma_id){

        $this->ma_id = $ma_id;
    }

    public function passes($attribute, $value){

        $exists_result = MainAccount::where('ma_id', $this->ma_id)->exists();
        if( $exists_result ){

            $main_account_id = Str::substr($this->ma_id, 0, 1);
            if( $value == $main_account_id){

                return TRUE;

            }else{

                return FALSE;
            }

        }else{

            return TRUE;
        }
    }

    public function message(){

        return "Couldn't change the Account Type.";
    }
}
