<?php

namespace App\Models\Purchase\Primary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class PurchasingLocation extends Model {

    use HasFactory;

    public function savePurchaingLocation($data){

        $purchasing_location = $data['purchasing_location'];

        $purchasing_location_id = 0;

        DB::beginTransaction();

        try{

            if($purchasing_location['purchasing_location_id'] == '#Auto#'){

                unset($purchasing_location['purchasing_location_id']);

                DB::table('purchasing_location')->insert($purchasing_location);
                $purchasing_location_id = DB::getPdo()->lastInsertId();

            }else{

               $purchasing_location_id = $purchasing_location['purchasing_location_id'];
               DB::table('purchasing_location')->where('purchasing_location_id', $purchasing_location_id)->update($purchasing_location);
            }

            DB::commit();

            $process_result['purchasing_location_id'] = $purchasing_location_id;
            $process_result['process_status'] = TRUE;
            $process_result['front_end_message'] = "Saving Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        }catch(\Exception $e){

            DB::rollback();

            $process_result['purchasing_location_id'] = $purchasing_location_id;
            $process_result['process_status'] = FALSE;
            $process_result['front_end_message'] = $e->getMessage();
            $process_result['back_end_message'] = 'Purchaing Category <br> ' . $e->getLine();

            return $process_result;
        }
    }

    public function getPurchaingLocation($purchasing_location_id){

        $result = DB::table('purchasing_location')->where('purchasing_location_id', $purchasing_location_id)->get();

        return $result;
    }

    public function getActivePurchasingLocationList(){

        $result = DB::table('purchasing_location')->where('active', 1)->get();

        return $result;
    }


}
