<?php

namespace App\Models\Sales\Primary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class SalesCategory extends Model {

    use HasFactory;

    public function saveSalesCategory($data){

        $sales_category = $data['sales_category'];

        $sales_category_id = 0;

        DB::beginTransaction();

        try{

            if($sales_category['sales_category_id'] == '#Auto#'){

                unset($sales_category['sales_category_id']);

                DB::table('sales_category')->insert($sales_category);
                $sales_category_id = DB::getPdo()->lastInsertId();

            }else{

               $sales_category_id = $sales_category['sales_category_id'];
               DB::table('sales_category')->where('sales_category_id', $sales_category_id)->update($sales_category);
            }

            DB::commit();

            $process_result['sales_category_id'] = $sales_category_id;
            $process_result['process_status'] = TRUE;
            $process_result['front_end_message'] = "Saving Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        }catch(\Exception $e){

            DB::rollback();

            $process_result['sales_category_id'] = $sales_category_id;
            $process_result['process_status'] = FALSE;
            $process_result['front_end_message'] = $e->getMessage();
            $process_result['back_end_message'] = 'Sales Category <br> ' . $e->getLine();

            return $process_result;
        }
    }

    public function getSalesCategory($sales_category_id){

        $result = DB::table('sales_category')->where('sales_category_id', $sales_category_id)->get();

        return $result;
    }


}
