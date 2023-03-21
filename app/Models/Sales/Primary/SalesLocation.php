<?php

namespace App\Models\Sales\Primary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class SalesLocation extends Model {

    use HasFactory;

    public function saveSalesLocation($data){

        $sales_location = $data['sales_location'];

        $sales_location_id = 0;

        DB::beginTransaction();

        try{

            if($sales_location['sales_location_id'] == '#Auto#'){

                unset($sales_location['sales_location_id']);

                DB::table('sales_location')->insert($sales_location);
                $sales_location_id = DB::getPdo()->lastInsertId();

            }else{

               $sales_location_id = $sales_location['sales_location_id'];
               DB::table('sales_location')->where('sales_location_id', $sales_location_id)->update($sales_location);
            }

            DB::commit();

            $process_result['sales_location_id'] = $sales_location_id;
            $process_result['process_status'] = TRUE;
            $process_result['front_end_message'] = "Saving Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        }catch(\Exception $e){

            DB::rollback();

            $process_result['sales_location_id'] = $sales_location_id;
            $process_result['process_status'] = FALSE;
            $process_result['front_end_message'] = $e->getMessage();
            $process_result['back_end_message'] = 'Sales Category <br> ' . $e->getLine();

            return $process_result;
        }
    }

    public function getSalesLocation($sales_location_id){

        $result = DB::table('sales_location')->where('sales_location_id', $sales_location_id)->get();

        return $result;
    }

    public function getActiveSalesLocationList(){

        $result = DB::table('sales_location')->where('active', 1)->get();

        return $result;
    }


}
