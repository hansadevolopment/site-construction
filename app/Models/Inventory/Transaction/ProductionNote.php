<?php

namespace App\Models\Inventory\Transaction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class ProductionNote extends Model {

    use HasFactory;

    public function productionNoteSavingProcess($data){

        $pn_id = 0;

        $pn = $data['pn'];
        $pn_detail = $data['pn_detail'];

        DB::beginTransaction();

        //try{

            // Production Note
            if($pn['pn_id'] == '#Auto#'){

                unset($pn['pn_id']);

                DB::table('production_note')->insert($pn);
                $pn_id = DB::getPdo()->lastInsertId();

            }else{

               $pn_id = $pn['pn_id'];
               DB::table('production_note')->where('pn_id', $pn_id)->update($pn);
            }

            // Production Note Detail
            if(is_null($pn_detail) == FALSE){
                
                $item_exists = DB::table('production_note_detail')->where('pn_id', '=',  $pn_id)->where('item_id', '=', $pn_detail['item_id'])->exists();

                if ($item_exists == TRUE) {

                    $pn_detail['quantity'] = $pn_detail['quantity'] + $this->getProductionNoteQuantity($pn_id, $pn_detail['item_id']) ;

                    DB::table('production_note_detail')->where('pn_id', '=',  $pn_id)->where('item_id', '=', $pn_detail['item_id'])->update($pn_detail);
                }else{

                    $pn_detail['pn_id'] = $pn_id;
                    DB::table('production_note_detail')->insert($pn_detail);
                }
            }

            DB::commit();

            $process_result['pn_id'] = $pn_id;
            $process_result['process_result'] = TRUE;
            $process_result['front_end_message'] = "Saving Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        // }catch(\Exception $e){

        //     $process_result['pn_id'] = $data['pn_id'];
        //     $process_result['process_result'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'Production Note Model : Production Note Saving Process. <br> Line No. ' . $e->getLine();

        //     DB::rollback();

        //     return $process_result;
        // }

    }

    public function getProductionNote($pn_id){

        $result = DB::table('production_note')->where('pn_id', $pn_id)->get();

        return $result;
    }

    public function getProductionNoteDetail($pn_id){

        $result = DB::table('production_note_detail')->where('pn_id', $pn_id)->get();

        return $result;
    }

    public function getProductionNoteQuantity($pn_id, $item_id){

        $item_quantity = DB::table('production_note_detail')->where('pn_id', $pn_id)->where('item_id', $item_id)->value('quantity');

        return $item_quantity;
    }

    public function isCancelProductionNote($pn_id){

        $cancel = DB::table('production_note')->where('pn_id', $pn_id)->where('cancel', 1)->exists();

        return $cancel;
    }


}
