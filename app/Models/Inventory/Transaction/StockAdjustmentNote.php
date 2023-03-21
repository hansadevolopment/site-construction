<?php

namespace App\Models\Inventory\Transaction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class StockAdjustmentNote extends Model {

    use HasFactory;

    public function sanSavingProcess($data){

        $san_id = 0;

        $san = $data['san'];

        DB::beginTransaction();

        //try{

            // Item Request Note
            if($san['san_id'] == '#Auto#'){

                unset($san['san_id']);

                DB::table('stock_adjustment_note')->insert($san);
                $san_id = DB::getPdo()->lastInsertId();

            }else{

               $san_id = $san['san_id'];
               DB::table('stock_adjustment_note')->where('san_id', $san_id)->update($san);
            }

            DB::commit();

            $process_result['san_id'] = $san_id;
            $process_result['process_result'] = TRUE;
            $process_result['front_end_message'] = "Saving Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        // }catch(\Exception $e){

        //     $process_result['san_id'] = $data['san_id'];
        //     $process_result['process_result'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'Stock Adjustment Note Model : Stock Adjustment Note Saving Process. <br> Line No. ' . $e->getLine();

        //     DB::rollback();

        //     return $process_result;
        // }

    }

    public function isCancelStockAdjustmentNote($san_id){

        $cancel = DB::table('stock_adjustment_note')->where('san_id', $san_id)->where('cancel', 1)->exists();

        return $cancel;
    }

}
