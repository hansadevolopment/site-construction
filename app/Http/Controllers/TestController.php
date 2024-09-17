<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\SiteMM\Master\Employee;
use App\Models\SiteMM\SiteOperation\PaymentVoucher;
use Illuminate\Support\Facades\DB;

class TestController extends Controller {

    public function test(){

        $colGrnDtl = DB::table('grn_detail')->where('grn_id', 37)->get();
        $colGrnTaxDtl = DB::table('grn_tax_detail')->where('grn_id', 37)->get();

        $colGrnTaxDtlGroupBy = $colGrnTaxDtl->groupBy('grn_dtl_id');
        foreach($colGrnTaxDtlGroupBy as $taxKey => $taxValue){

            $grnDtlIdArray = $taxValue->pluck('grn_dtl_id');
            $grnDtlId = $grnDtlIdArray[0];

            $grossAmount = $colGrnDtl->where('grn_dtl_id', $grnDtlId);

            dump($grossAmount);
            //dump($taxValue->sum('tax_amount'));
        }

        //dump( $colGrnTaxDtl_GroupBy );
    }


    function getClassHierarchy($class) {
        $hierarchy = [];
        while ($class !== false) {
            $hierarchy[] = $class;
            $class = get_parent_class($class);
        }
        return array_reverse($hierarchy);
    }


    public function arrayElemantExists($inputArray){

        if( is_null($inputArray)) {

            dump('Null Value');
            return FALSE;
        }

        if( is_array($inputArray) ){

            if( count($inputArray) >= 1 ){

                dump('Count :- '. count($inputArray));
                return TRUE;

            }else{

                dump('Count is zero');
                return FALSE;
            }

        }else{

            dump('Not a Array');
            return FALSE;
        }
    }

}
