<?php

namespace App\Models\Purchase\Primary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class PurchasingCategory extends Model {

    use HasFactory;

    public function savePurchaingCategory($data){

        $purchasing_category = $data['purchasing_category'];

        $purchasing_category_id = 0;

        DB::beginTransaction();

        try{

            if($purchasing_category['purchasing_category_id'] == '#Auto#'){

                unset($purchasing_category['purchasing_category_id']);

                DB::table('purchasing_category')->insert($purchasing_category);
                $purchasing_category_id = DB::getPdo()->lastInsertId();

            }else{

               $purchasing_category_id = $purchasing_category['purchasing_category_id'];
               DB::table('purchasing_category')->where('purchasing_category_id', $purchasing_category_id)->update($purchasing_category);
            }

            DB::commit();

            $process_result['purchasing_category_id'] = $purchasing_category_id;
            $process_result['process_status'] = TRUE;
            $process_result['front_end_message'] = "Saving Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        }catch(\Exception $e){

            DB::rollback();

            $process_result['purchasing_category_id'] = $purchasing_category_id;
            $process_result['process_status'] = FALSE;
            $process_result['front_end_message'] = $e->getMessage();
            $process_result['back_end_message'] = 'Purchaing Category <br> ' . $e->getLine();

            return $process_result;
        }
    }

    public function getPurchaingCategory($purchasing_category_id){

        $result = DB::table('purchasing_category')->where('purchasing_category_id', $purchasing_category_id)->get();

        return $result;
    }

    public function getActivePurchasingCategoryList(){

        $result = DB::table('purchasing_category')->where('active', 1)->get();

        return $result;
    }

}
