<?php

namespace App\Models\Sales\Primary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class Debtor extends Model {

    use HasFactory;

    public function saveDebtor($data){

        $Debtor = $data['Debtor'];

        $debtor_id = 0;

        DB::beginTransaction();

        try{

            if($Debtor['debtor_id'] == '#Auto#'){

                unset($Debtor['debtor_id']);

                DB::table('Debtor')->insert($Debtor);
                $debtor_id = DB::getPdo()->lastInsertId();

            }else{

               $debtor_id = $Debtor['debtor_id'];
               DB::table('Debtor')->where('debtor_id', $debtor_id)->update($Debtor);
            }

            DB::commit();

            $process_result['debtor_id'] = $debtor_id;
            $process_result['process_status'] = TRUE;
            $process_result['front_end_message'] = "Saving Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        }catch(\Exception $e){

            DB::rollback();

            $process_result['debtor_id'] = $debtor_id;
            $process_result['process_status'] = FALSE;
            $process_result['front_end_message'] = $e->getMessage();
            $process_result['back_end_message'] = 'Debtor <br> ' . $e->getLine();

            return $process_result;
        }
    }

    public function getDebtor($debtor_id){

        $result = DB::table('debtor')->where('debtor_id', $debtor_id)->get();

        return $result;
    }
}
