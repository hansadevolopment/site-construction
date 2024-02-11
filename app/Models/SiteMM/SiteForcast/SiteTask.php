<?php

namespace App\Models\SiteMM\SiteForcast;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\SiteMM\Master\Site;

use Illuminate\Support\Facades\DB;

class SiteTask extends Model {

    use HasFactory;

    protected $table = 'site_task';

    protected $primaryKey = 'task_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $dateFormat = 'U';

    public function saveSiteTask($data){

        $site_task = $data['site_task'];

        DB::beginTransaction();

        //try{

            if($site_task['task_id'] == '#Auto#'){

                unset($site_task['task_id']);

                DB::table('site_task')->insert($site_task);
                $task_id = DB::getPdo()->lastInsertId();

            }else{

               $task_id = $site_task['task_id'];
               DB::table('site_task')->where('task_id', $task_id)->update($site_task);
            }

            DB::commit();

            $process_result['task_id'] = $task_id;
            $process_result['process_status'] = TRUE;
            $process_result['front_end_message'] = "Saving Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        // }catch(\Exception $e){

        //     DB::rollback();

        //     $process_result['task_id'] = $task_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'site <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

    public function site(){

        return $this->belongsTo(Site::class, 'site_id');
    }

    public function subTask(){

        return $this->hasMany(SiteSubTask::class, 'task_id');
    }


}
