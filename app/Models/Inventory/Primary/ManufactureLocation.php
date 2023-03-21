<?php

namespace App\Models\Inventory\Primary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class ManufactureLocation extends Model {

    use HasFactory;

    public function saveManufactureLocation($data){

        $manufacture_location = $data['manufacture_location'];

        $manufacture_location_id = 0;

        DB::beginTransaction();

        try{

            if($manufacture_location['manufacture_location_id'] == '#Auto#'){

                unset($manufacture_location['manufacture_location_id']);

                DB::table('manufacture_location')->insert($manufacture_location);
                $manufacture_location_id = DB::getPdo()->lastInsertId();

            }else{

               $manufacture_location_id = $manufacture_location['manufacture_location_id'];
               DB::table('manufacture_location')->where('manufacture_location_id', $manufacture_location_id)->update($manufacture_location);
            }

            DB::commit();

            $process_result['manufacture_location_id'] = $manufacture_location_id;
            $process_result['process_status'] = TRUE;
            $process_result['front_end_message'] = "Saving Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        }catch(\Exception $e){

            DB::rollback();

            $process_result['manufacture_location_id'] = $manufacture_location_id;
            $process_result['process_status'] = FALSE;
            $process_result['front_end_message'] = $e->getMessage();
            $process_result['back_end_message'] = 'Purchaing Category <br> ' . $e->getLine();

            return $process_result;
        }
    }

    public function getManufactureLocation($manufacture_location_id){

        $result = DB::table('manufacture_location')->where('manufacture_location_id', $manufacture_location_id)->get();

        return $result;
    }

    public function getActiveManufactureLocationList(){

        $result = DB::table('manufacture_location')->where('active', 1)->get();

        return $result;
    }


}
