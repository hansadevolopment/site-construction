<?php

namespace App\Models\SiteMM\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class LabourCategory extends Model {

    use HasFactory;

    use HasFactory;

    protected $table = 'labour_category';

    protected $primaryKey = 'lc_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $dateFormat = 'U';

    public function saveLabourCategory($data){

        $labour_category = $data['labour_category'];

        DB::beginTransaction();

        //try{

            if($labour_category['lc_id'] == '#Auto#'){

                unset($labour_category['lc_id']);

                DB::table('labour_category')->insert($labour_category);
                $lc_id = DB::getPdo()->lastInsertId();

            }else{

               $lc_id = $labour_category['lc_id'];
               DB::table('labour_category')->where('lc_id', $lc_id)->update($labour_category);
            }

            DB::commit();

            $process_result['lc_id'] = $lc_id;
            $process_result['process_status'] = TRUE;
            $process_result['front_end_message'] = "Saving Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        // }catch(\Exception $e){

        //     DB::rollback();

        //     $process_result['lc_id'] = $lc_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'labour_category <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }


}
