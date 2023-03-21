<?php

namespace App\Models\Inventory\Transaction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class ItemRequestNote extends Model {

    use HasFactory;

    public function irnSavingProcess($data){

        $irn_id = 0;

        $irn = $data['irn'];
        $irn_detail = $data['irn_detail'];

        DB::beginTransaction();

        //try{

            // Item Request Note
            if($irn['irn_id'] == '#Auto#'){

                unset($irn['irn_id']);

                DB::table('item_request_note')->insert($irn);
                $irn_id = DB::getPdo()->lastInsertId();

            }else{

               $irn_id = $irn['irn_id'];
               DB::table('item_request_note')->where('irn_id', $irn_id)->update($irn);
            }

            // Item Request Note Detail
            if(is_null($irn_detail) == FALSE){
                
                $item_exists = DB::table('item_request_note_detail')->where('irn_id', '=',  $irn_id)->where('item_id', '=', $irn_detail['item_id'])->exists();

                if ($item_exists == TRUE) {

                    $irn_detail['quantity'] = $irn_detail['quantity'] + $this->getItemRequestNoteQuantity($irn_id, $irn_detail['item_id']) ;

                    DB::table('item_request_note_detail')->where('irn_id', '=',  $irn_id)->where('item_id', '=', $irn_detail['item_id'])->update($irn_detail);
                }else{

                    $irn_detail['irn_id'] = $irn_id;
                    DB::table('item_request_note_detail')->insert($irn_detail);
                }
            }

            DB::commit();

            $process_result['irn_id'] = $irn_id;
            $process_result['process_result'] = TRUE;
            $process_result['front_end_message'] = "Saving Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        // }catch(\Exception $e){

        //     $process_result['irn_id'] = $data['irn']['irn_id'];
        //     $process_result['process_result'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'Item Request Note Model : Item Request Note Saving Process. <br> Line No. ' . $e->getLine();

        //     DB::rollback();

        //     return $process_result;
        // }

    }

    public function getItemRequestNote($irn_id){

        $result = DB::table('item_request_note')->where('irn_id', $irn_id)->get();

        return $result;
    }

    public function getItemRequestNoteDetail($irn_id){

        $result = DB::table('item_request_note_detail')->where('irn_id', $irn_id)->get();

        return $result;
    }

    public function getItemRequestNoteQuantity($irn_id, $item_id){

        $item_quantity = DB::table('item_request_note_detail')->where('irn_id', $irn_id)->where('item_id', $item_id)->value('quantity');

        return $item_quantity;
    }

    public function isCancelItemRequestNote($irn_id){

        $cancel = DB::table('item_request_note')->where('irn_id', $irn_id)->where('cancel', 1)->exists();

        return $cancel;
    }
}
