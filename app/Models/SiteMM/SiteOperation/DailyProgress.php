<?php

namespace App\Models\SiteMM\SiteOperation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\SiteMM\SiteOperation\DailyProgressDetail;

use App\Models\SiteMM\Master\Site;
use App\Models\SiteMM\SiteForcast\SiteTask;
use App\Models\SiteMM\SiteForcast\SiteSubTask;
use Illuminate\Support\Facades\DB;

class DailyProgress extends Model {

    use HasFactory;

    protected $table = 'dpr';

    protected $primaryKey = 'dpr_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $dateFormat = 'U';

    public function getDprDetail(){

        return $this->hasMany(DailyProgressDetail::class, 'dpr_id');
    }

    public function getSite(){

        return Site::where('site_id', $this->site_id)->first();
    }

    public function getTask(){

        return SiteTask::where('task_id', $this->task_id)->first();
    }

    public function getSubTask(){

        return SiteSubTask::where('sub_task_id', $this->sub_task_id)->first();
    }

    public function addDailyProgressNote($data){

        $dpr = $data['dpr'];
        $dpr_detail = $data['dpr_detail'];

        DB::beginTransaction();

        //try{

            if( $dpr['dpr_id'] == '#Auto#' ){

                unset($dpr['dpr_id']);
                DB::table('dpr')->insert($dpr);
                $dpr_id = DB::getPdo()->lastInsertId();

            }else{

                $dpr_id = $dpr['dpr_id'];
                DB::table('dpr')->where('dpr_id', $dpr['dpr_id'])->update($dpr);
            }

            $dpr_detail['dpr_id'] = $dpr_id;
            $upsert_flag = DailyProgressDetail::where('dpr_id', $dpr_id)->where('item_id', $dpr_detail['item_id'])->exists();
            if( $upsert_flag ){

                DB::table('dpr_detail')->where('dpr_id', $dpr_id)->where('item_id', $dpr_detail['item_id'])->update($dpr_detail);
            }else{

                DB::table('dpr_detail')->insert($dpr_detail);
            }

            $dpr_total_amount = DB::table('dpr_detail')->where('dpr_id', $dpr_id)->sum('amount');
            DB::table('dpr')->where('dpr_id', $dpr_id)->update(['total_amount' => $dpr_total_amount]);

            DB::table('dpr_detail')->where('dpr_id', $dpr_id)->where('item_id', $dpr_detail['item_id'])->where('quantity', 0)->delete();

            DB::commit();

            $process_result['dpr_id'] = $dpr_id;
            $process_result['process_status'] = TRUE;
            $process_result['front_end_message'] = "Saving Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        // }catch(\Exception $e){

        //     DB::rollback();

        //     $process_result['dpr_id'] = $data['dpr']['dpr_id'];
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'site <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

    public function cancelDailyProgressNote($dpr_cancel){

        DB::beginTransaction();

        //try{


            $dpr_id = $dpr_cancel['dpr_id'];

            unset($dpr_cancel['dpr_id']);
            DB::table('dpr')->where('dpr_id', $dpr_id)->update($dpr_cancel);

            DB::commit();

            $process_result['dpr_id'] = $dpr_id;
            $process_result['process_status'] = TRUE;
            $process_result['front_end_message'] = "Cancel Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        // }catch(\Exception $e){

        //     DB::rollback();

        //     $process_result['dpr_id'] = $dpr_cancel['dpr_id'];
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'site <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

}
