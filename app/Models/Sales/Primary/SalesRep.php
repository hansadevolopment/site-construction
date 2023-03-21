<?php

namespace App\Models\Sales\Primary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class SalesRep extends Model {

    use HasFactory;

    public function saveSalesLocation($data){

        $sales_rep = $data['sales_rep'];

        $sales_rep_id = 0;

        DB::beginTransaction();

        try{

            if($sales_rep['sales_rep_id'] == '#Auto#'){

                unset($sales_rep['sales_rep_id']);

                DB::table('sales_rep')->insert($sales_rep);
                $sales_rep_id = DB::getPdo()->lastInsertId();

            }else{

               $sales_rep_id = $sales_rep['sales_rep_id'];
               DB::table('sales_rep')->where('sales_rep_id', $sales_rep_id)->update($sales_rep);
            }

            DB::commit();

            $process_result['sales_rep_id'] = $sales_rep_id;
            $process_result['process_status'] = TRUE;
            $process_result['front_end_message'] = "Saving Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        }catch(\Exception $e){

            DB::rollback();

            $process_result['sales_rep_id'] = $sales_rep_id;
            $process_result['process_status'] = FALSE;
            $process_result['front_end_message'] = $e->getMessage();
            $process_result['back_end_message'] = 'Sales Category <br> ' . $e->getLine();

            return $process_result;
        }
    }

    public function getSalesRep($sales_rep_id){

        $result = DB::table('sales_rep')->where('sales_rep_id', $sales_rep_id)->get();

        return $result;
    }

}
