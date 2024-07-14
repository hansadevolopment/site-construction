<?php

namespace App\Models\Purchasing\Primary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class PurchasingLocation extends Model {

    use HasFactory;

    protected $table = 'purchasing_location';

    protected $primaryKey = 'pl_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $dateFormat = 'U';

    public function savePurchaingLocation($data){

        $pl = $data['pl'];

        $pl_id = 0;

        DB::beginTransaction();

        try{

            if($pl['pl_id'] == '#Auto#'){

                unset($pl['pl_id']);

                DB::table('purchasing_location')->insert($pl);
                $pl_id = DB::getPdo()->lastInsertId();

            }else{

               $pl_id = $pl['pl_id'];
               DB::table('purchasing_location')->where('pl_id', $pl_id)->update($pl);
            }

            DB::commit();

            $process_result['pl_id'] = $pl_id;
            $process_result['process_status'] = TRUE;
            $process_result['front_end_message'] = "Saving Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        }catch(\Exception $e){

            DB::rollback();

            $process_result['pl_id'] = $pl_id;
            $process_result['process_status'] = FALSE;
            $process_result['front_end_message'] = $e->getMessage();
            $process_result['back_end_message'] = 'Purchaing Location <br> ' . $e->getLine();

            return $process_result;
        }

    }

}
