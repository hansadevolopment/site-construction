<?php

namespace App\Models\Purchase\Primary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class Creditor extends Model {

    use HasFactory;

    public function saveCreditor($data){
        
        $creditor_id = '0';

        $creditor = $data['creditor'];

        DB::beginTransaction();

        try{

            if($creditor['creditor_id'] == '#Auto#'){

                unset($creditor['creditor_id']);

                DB::table('creditor')->insert($creditor);
                $creditor_id = DB::getPdo()->lastInsertId();

            }else{

               $creditor_id = $creditor['creditor_id'];
               DB::table('creditor')->where('creditor_id', $creditor_id)->update($creditor);
            }

            DB::commit();

            $process_result['creditor_id'] = $creditor_id;
            $process_result['process_status'] = TRUE;
            $process_result['front_end_message'] = "Saving Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        }catch(\Exception $e){

            DB::rollback();

            $process_result['creditor_id'] = $creditor_id;
            $process_result['process_status'] = FALSE;
            $process_result['front_end_message'] = $e->getMessage();
            $process_result['back_end_message'] = 'Creditor <br> ' . $e->getLine();

            return $process_result;
        }
    }

    public function getCreditor($creditor_id){

        $result = DB::table('creditor')->where('creditor_id', $creditor_id)->get();

        return $result;
    }

    public function getActiveCreditorList(){

        $result = DB::table('creditor')->where('active', 1)->get();

        return $result;
    }

}
