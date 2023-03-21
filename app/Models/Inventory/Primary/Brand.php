<?php

namespace App\Models\Inventory\Primary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class Brand extends Model {

    use HasFactory;

    public function saveManufactureLocation($data){

        $brand = $data['brand'];

        $brand_id = 0;

        DB::beginTransaction();

        try{

            if($brand['brand_id'] == '#Auto#'){

                unset($brand['brand_id']);

                DB::table('brand')->insert($brand);
                $brand_id = DB::getPdo()->lastInsertId();

            }else{

               $brand_id = $brand['brand_id'];
               DB::table('brand')->where('brand_id', $brand_id)->update($brand);
            }

            DB::commit();

            $process_result['brand_id'] = $brand_id;
            $process_result['process_status'] = TRUE;
            $process_result['front_end_message'] = "Saving Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        }catch(\Exception $e){

            DB::rollback();

            $process_result['brand_id'] = $brand_id;
            $process_result['process_status'] = FALSE;
            $process_result['front_end_message'] = $e->getMessage();
            $process_result['back_end_message'] = 'Purchaing Category <br> ' . $e->getLine();

            return $process_result;
        }
    }

    public function getBrand($brand_id){

        $result = DB::table('brand')->where('brand_id', $brand_id)->get();

        return $result;
    }


}
