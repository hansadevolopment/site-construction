<?php

namespace App\Models\Purchasing\Transaction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

use App\Helpers\Common\InputHelper;

class GoodReceiveNote extends Model {

    use HasFactory;

    protected $table = 'grn';

    protected $primaryKey = 'grn_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $dateFormat = 'U';

    public function addGrnItem($grnArray){

        $grn = $grnArray['grn'];
        $grn_detail = $grnArray['grn_detail'];
        $grn_tax = $grnArray['grn_tax'];
        $main_store = $grnArray['main_store'];

        $grnId = 0;
        $grnDtlId = 0;

        DB::beginTransaction();

        //try{

            // GRN
            if( $grn['grn_id'] == '#Auto#' ){

                unset($grn['grn_id']);
                DB::table('grn')->insert($grn);
                $grnId = DB::getPdo()->lastInsertId();

            }else{

                $grnId = $grn['grn_id'];
                DB::table('grn')->where('grn_id', $grn['grn_id'])->update($grn);
            }

            // GRN Detail
            if( is_null($grn_detail) == FALSE ){

                $grn_detail['grn_id'] = $grnId;
                if( $grn['grn_type'] == 1 ){

                    $upsertFlag = DB::table('grn_detail')->where('grn_id', $grnId)->where('item_id', $grn_detail['item_id'])->exists();
                    if( $upsertFlag ){

                        DB::table('grn_detail')->where('grn_id', $grnId)->where('item_id', $grn_detail['item_id'])->update($grn_detail);
                        $grnDtlId =  DB::table('grn_detail')->where('grn_id', $grnId)->where('item_description', $grn_detail['item_description'])->value('grn_dtl_id');

                    }else{

                        DB::table('grn_detail')->insert($grn_detail);
                        $grnDtlId = DB::getPdo()->lastInsertId();
                    }
                    DB::table('grn_detail')->where('grn_id', $grnId)->where('item_id', $grn_detail['item_id'])->where('quantity', 0)->delete();

                }else{

                    $upsertFlag = DB::table('grn_detail')->where('grn_id', $grnId)->where('item_description', $grn_detail['item_description'])->exists();
                    if( $upsertFlag ){

                        DB::table('grn_detail')->where('grn_id', $grnId)->where('item_description', $grn_detail['item_description'])->update($grn_detail);
                        $grnDtlId =  DB::table('grn_detail')->where('grn_id', $grnId)->where('item_description', $grn_detail['item_description'])->value('grn_dtl_id');

                    }else{

                        DB::table('grn_detail')->insert($grn_detail);
                        $grnDtlId = DB::getPdo()->lastInsertId();
                    }
                    DB::table('grn_detail')->where('grn_id', $grnId)->where('item_description', $grn_detail['item_description'])->where('quantity', 0)->delete();
                }
            }

            // Grn Tax
            if( is_null($grn_tax) == FALSE ){

                if( count($grn_tax) >= 1 ){

                    foreach($grn_tax as $grnTaxKey => $grnTaxValue){

                        if( $grnTaxValue['item_id'] != 0 ){

                            $taxEligible = DB::table('item_tax')->where('item_id', $grnTaxValue['item_id'])->where('tax_id', $grnTaxValue['tax_id'])->exists();
                            if($taxEligible){

                                $grnTaxValue['grn_id'] = $grnId;
                                $grnTaxValue['grn_dtl_id'] = $grnDtlId;
                                DB::table('grn_tax_detail')->insert($grnTaxValue);

                            }else{

                                DB::table('grn_tax_detail')->where('grn_id', $grnId)->where('item_id', $grn_tax['item_id'])->delete();
                            }

                        }else{

                            $grnTaxValue['grn_id'] = $grnId;
                            $grnTaxValue['grn_dtl_id'] = $grnDtlId;
                            DB::table('grn_tax_detail')->insert($grnTaxValue);
                        }
                    }
                }
            }

            //Get Grn Details
            $colGrnDtl = DB::table('grn_detail')->where('grn_id', $grnId)->get();
            $colGrnTaxDtl = DB::table('grn_tax_detail')->where('grn_id', $grnId)->get();

            // Update Grn Tax Amounts
            $colGrnTaxDtlGroupBy = $colGrnTaxDtl->groupBy('grn_dtl_id');
            foreach($colGrnTaxDtlGroupBy as $taxKey => $taxValue){

                $grnDtlIdArray = $taxValue->pluck('grn_dtl_id');
                $grnDtlId = $grnDtlIdArray[0];

                $grnDtlRow = $colGrnDtl->where('grn_dtl_id', $grnDtlId)->first();
                $grossAmount = InputHelper::stringToNumber($grnDtlRow->gross_amount);

                $taxAmount['tax_amount'] = $taxValue->sum('tax_amount');
                $taxAmount['net_amount'] = $grossAmount + $taxValue->sum('tax_amount');

                DB::table('grn_detail')->where('grn_id', $grnId)->where('grn_dtl_id', $grnDtlId)->update($taxAmount);
            }

            // Update Grn Amounts
            $grnAmount['discount_amount']= $colGrnDtl->sum('discount_amount');
            $grnAmount['gross_amount']= $colGrnDtl->sum('gross_amount');
            $grnAmount['tax_amount'] = $colGrnDtl->sum('tax_amount');
            $grnAmount['net_amount'] = $colGrnDtl->sum('net_amount');

            DB::table('grn')->where('grn_id', $grnId)->update($grnAmount);

            if( is_null($main_store) == FALSE ){

                foreach($main_store as $rowKey => $rowValue){

                    $itemSerial = DB::table('item')->where('item_id', $rowValue['item_id'] )->value('serial');
                    $itemSerial = $itemSerial + 1;
                    $rowValue['serial'] = $itemSerial;
                    DB::table('item')->where('item_id', $rowValue['item_id'] )->increment('serial');
                    DB::table('main_store')->insert($rowValue);

                    $glPost['gl_post_no'] = 1;
                    DB::table('grn')->where('grn_id', $grnId)->update($glPost);
                }
            }


            DB::commit();

            $processResult['grnId'] = $grnId;
            $processResult['processStatus'] = TRUE;
            $processResult['frontEndMessage'] = "Saving Process is Completed successfully.";
            $processResult['backEndMessage'] = "Commited.";

            return $processResult;

        // }catch(\Exception $e){

        //     DB::rollback();

        //     $processResult['grn_id'] = $grnArray['grn']['grn_id'];
        //     $processResult['processStatus'] = FALSE;
        //     $processResult['frontEndMessage'] = $e->getMessage();
        //     $processResult['backEndMessage'] = 'site <br> ' . $e->getLine();

        //     return $processResult;
        // }
    }

    public function removeItem($grnId, $grnDtlId){

        DB::beginTransaction();

        //try{

            DB::table('grn_tax_detail')->where('grn_dtl_id', $grnDtlId)->delete();
            DB::table('grn_detail')->where('grn_dtl_id', $grnDtlId)->delete();

            //Get Grn Details
            $colGrnDtl = DB::table('grn_detail')->where('grn_id', $grnId)->get();
            $colGrnTaxDtl = DB::table('grn_tax_detail')->where('grn_id', $grnId)->get();
            $colGrnTaxDtlGroupBy = $colGrnTaxDtl->groupBy('grn_dtl_id');

            // Update Grn Tax Amounts
            foreach($colGrnTaxDtlGroupBy as $taxKey => $taxValue){

                $grnDtlIdArray = $taxValue->pluck('grn_dtl_id');
                $grnDtlId = $grnDtlIdArray[0];

                $grnDtlRow = $colGrnDtl->where('grn_dtl_id', $grnDtlId)->first();
                $grossAmount = InputHelper::stringToNumber($grnDtlRow->gross_amount);

                $taxAmount['tax_amount'] = $taxValue->sum('tax_amount');
                $taxAmount['net_amount'] = $grossAmount + $taxValue->sum('tax_amount');

                DB::table('grn_detail')->where('grn_id', $grnId)->where('grn_dtl_id', $grnDtlId)->update($taxAmount);
            }

            // Update Grn Amounts
            $grnAmount['discount_amount']= $colGrnDtl->sum('discount_amount');
            $grnAmount['gross_amount']= $colGrnDtl->sum('gross_amount');
            $grnAmount['tax_amount'] = $colGrnDtl->sum('tax_amount');
            $grnAmount['net_amount'] = $colGrnDtl->sum('net_amount');

            DB::table('grn')->where('grn_id', $grnId)->update($grnAmount);

            DB::commit();

            $processResult['grnId'] = $grnId;
            $processResult['processStatus'] = TRUE;
            $processResult['frontEndMessage'] = 'Item is removed successfully.';
            $processResult['backEndMessage'] = "Commited.";

            return $processResult;


        // }catch(\Exception $e){

        //     DB::rollback();

        //     $processResult['grn_id'] = $grnId;
        //     $processResult['processStatus'] = FALSE;
        //     $processResult['frontEndMessage'] = $e->getMessage();
        //     $processResult['backEndMessage'] =  $e->getLine();

        //     return $processResult;
        // }
    }

    public function cancelGoodReceiveNote($grn_cancel){

        DB::beginTransaction();

        //try{

            $grnId = $grn_cancel['grn_id'];

            unset($grn_cancel['grn_id']);
            DB::table('grn')->where('grn_id', $grnId)->update($grn_cancel);

            DB::commit();

            $processResult['grnId'] = $grnId;
            $processResult['processStatus'] = TRUE;
            $processResult['frontEndMessage'] = "Cancel Process is Completed successfully.";
            $processResult['backEndMessage'] = "Commited.";

            return $processResult;

        // }catch(\Exception $e){

        //     DB::rollback();

        //     $processResult['grnId'] = $grn_cancel['grn_id'];
        //     $processResult['processStatus'] = FALSE;
        //     $processResult['frontEndMessage'] = $e->getMessage();
        //     $processResult['backEndMessage'] = 'site <br> ' . $e->getLine();

        //     return $processResult;
        // }
    }



}
