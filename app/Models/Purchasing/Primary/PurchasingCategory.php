<?php

namespace App\Models\Purchasing\Primary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class PurchasingCategory extends Model{

    use HasFactory;

    protected $table = 'purchasing_category';

    protected $primaryKey = 'pc_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $dateFormat = 'U';

    public function savePurchaingCategory($data){

        $pc = $data['pc'];

        $pc_id = 0;

        DB::beginTransaction();

        try{

            if($pc['pc_id'] == '#Auto#'){

                unset($pc['pc_id']);

                DB::table('purchasing_category')->insert($pc);
                $pc_id = DB::getPdo()->lastInsertId();

            }else{

               $pc_id = $pc['pc_id'];
               DB::table('purchasing_category')->where('pc_id', $pc_id)->update($pc);
            }

            DB::commit();

            $process_result['pc_id'] = $pc_id;
            $process_result['process_status'] = TRUE;
            $process_result['front_end_message'] = "Saving Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        }catch(\Exception $e){

            DB::rollback();

            $process_result['pc_id'] = $pc_id;
            $process_result['process_status'] = FALSE;
            $process_result['front_end_message'] = $e->getMessage();
            $process_result['back_end_message'] = 'Purchaing Category <br> ' . $e->getLine();

            return $process_result;
        }

    }
}
