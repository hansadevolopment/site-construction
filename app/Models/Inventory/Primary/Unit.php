<?php

namespace App\Models\Inventory\Primary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class Unit extends Model {

    use HasFactory;

    public function saveUnit($data){

        $unit = $data['unit'];

        $unit_id = 0;

        DB::beginTransaction();

        try{

            if($unit['unit_id'] == '#Auto#'){

                unset($unit['unit_id']);

                DB::table('unit')->insert($unit);
                $unit_id = DB::getPdo()->lastInsertId();

            }else{

               $unit_id = $unit['unit_id'];
               DB::table('unit')->where('unit_id', $unit_id)->update($unit);
            }

            DB::commit();

            $process_result['unit_id'] = $unit_id;
            $process_result['process_status'] = TRUE;
            $process_result['front_end_message'] = "Saving Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        }catch(\Exception $e){

            DB::rollback();

            $process_result['unit_id'] = $unit_id;
            $process_result['process_status'] = FALSE;
            $process_result['front_end_message'] = $e->getMessage();
            $process_result['back_end_message'] = 'Unit <br> ' . $e->getLine();

            return $process_result;
        }
    }

    public function getunit($unit_id){

        $result = DB::table('unit')->where('unit_id', $unit_id)->get();

        return $result;
    }

    public function getActiveUnitList(){

        $result = DB::table('unit')->where('active', 1)->get();

        return $result;
    }


    
}
