<?php

namespace App\Services\Finance;

use Illuminate\Support\Facades\DB;

class TaxService{

    public static function getItemTaxCalculation($itemId, $grossTotal){

        $taxTotal = 0;

        $itemTaxResult = DB::table('item_tax')->where('item_id', $itemId)->get();
        foreach($itemTaxResult as $itemTaxRow => $itemTaxValue){

            $taxId = $itemTaxValue->tax_id;
            $taxResult = DB::table('tax')->where('tax_id', $taxId)->first();
            if( $taxResult ){

                $taxTotal = $grossTotal * ($taxResult->tax_rate / 100);

            }else{

                $taxTotal = 0;
            }
        }

        return $taxTotal;
    }

    public static function getItemTaxDetail($itemId){

        $itemTaxResult = DB::table('item_tax')->where('item_id', $itemId)->get();
        return $itemTaxResult;
    }


    public static function getOtherTaxCalculation($taxId, $grossAmount){

        $taxResult = DB::table('tax')->where('tax_id', $taxId)->exists();
        if($taxResult){

            $taxRate = DB::table('tax')->where('tax_id', $taxId)->value('tax_rate');
            $taxAmount = $grossAmount * ($taxRate / 100);

            return $taxAmount;

        }else{

            return $grossAmount;
        }
    }

}
