<?php

namespace App\Models\GL\Primary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class SubAccounts extends Model {

    use HasFactory;

    public function getAccountTypes(){

        $result = DB::table('acc_type')->orderBy('acc_id')->get();

		return $result;
    }

    public function getSubAccounts(){

        $result = DB::table('sub_accounts')->orderBy('sa_id')->get();

		return $result;
    }

    public function getSubAccountName($sa_id){

        $sub_account_name = DB::table('sub_accounts')->where('sa_id', $sa_id)->value('sa_name');

		return $sub_account_name;
    }


}
