<?php

namespace App\Models\SiteMM\SiteForcast;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class SiteSubTask extends Model {

    use HasFactory;

    protected $table = 'site_sub_task';

    protected $primaryKey = 'sub_task_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $dateFormat = 'U';


    public function saveSiteSubTask($data){

        $site_sub_task = $data['site_sub_task'];

        DB::beginTransaction();

        //try{

            if($site_sub_task['sub_task_id'] == '#Auto#'){

                unset($site_sub_task['sub_task_id']);

                DB::table('site_sub_task')->insert($site_sub_task);
                $sub_task_id = DB::getPdo()->lastInsertId();

            }else{

               $sub_task_id = $site_sub_task['sub_task_id'];
               DB::table('site_sub_task')->where('sub_task_id', $sub_task_id)->update($site_sub_task);
            }

            DB::commit();

            $process_result['sub_task_id'] = $sub_task_id;
            $process_result['process_status'] = TRUE;
            $process_result['front_end_message'] = "Saving Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        // }catch(\Exception $e){

        //     DB::rollback();

        //     $process_result['sub_task_id'] = $sub_task_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'site <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

    public function task(){

        return $this->belongsTo(SiteTask::class, 'task_id');
    }


}
