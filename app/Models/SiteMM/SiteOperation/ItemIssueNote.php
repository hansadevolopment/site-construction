<?php

namespace App\Models\SiteMM\SiteOperation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\SiteMM\Master\Site;
use App\Models\SiteMM\SiteForcast\SiteTask;
use App\Models\SiteMM\SiteForcast\SiteSubTask;
use App\Models\SiteMM\SiteOperation\ItemIssueNoteDetail;

use Illuminate\Support\Facades\DB;

class ItemIssueNote extends Model {

    use HasFactory;

    protected $table = 'item_issue_note';

    protected $primaryKey = 'iin_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $dateFormat = 'U';

    public function iind(){

        return $this->hasMany(ItemIssueNoteDetail::class, 'iin_id');
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

    public function addItemIssueNote($data){

        $iin = $data['iin'];
        $iin_detail = $data['iin_detail'];

        DB::beginTransaction();

        //try{

            if( $iin['iin_id'] == '#Auto#' ){

                unset($iin['iin_id']);
                DB::table('item_issue_note')->insert($iin);
                $iin_id = DB::getPdo()->lastInsertId();

            }else{

                $iin_id = $iin['iin_id'];
                DB::table('item_issue_note')->where('iin_id', $iin['iin_id'])->update($iin);
            }

            $iin_detail['iin_id'] = $iin_id;
            $upsert_flag = ItemIssueNoteDetail::where('iin_id', $iin_id)->where('item_id', $iin_detail['item_id'])->exists();
            if( $upsert_flag ){

                DB::table('item_issue_note_detail')->where('iin_id', $iin_id)->where('item_id', $iin_detail['item_id'])->update($iin_detail);
            }else{

                DB::table('item_issue_note_detail')->insert($iin_detail);
            }

            $iin_total_amount = DB::table('item_issue_note_detail')->where('iin_id', $iin_id)->sum('amount');
            DB::table('item_issue_note')->where('iin_id', $iin_id)->update(['total_amount' => $iin_total_amount]);

            DB::table('item_issue_note_detail')->where('iin_id', $iin_id)->where('item_id', $iin_detail['item_id'])->where('quantity', 0)->delete();

            DB::commit();

            $process_result['iin_id'] = $iin_id;
            $process_result['process_status'] = TRUE;
            $process_result['front_end_message'] = "Saving Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        // }catch(\Exception $e){

        //     DB::rollback();

        //     $process_result['iin_id'] = $data['iin']['iin_id'];
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'site <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

    public function cancelItemIssueNote($iin_cancel){

        DB::beginTransaction();

        //try{

            $iin_id = $iin_cancel['iin_id'];

            unset($iin_cancel['iin_id']);
            DB::table('item_issue_note')->where('iin_id', $iin_id)->update($iin_cancel);

            DB::commit();

            $process_result['iin_id'] = $iin_id;
            $process_result['process_status'] = TRUE;
            $process_result['front_end_message'] = "Cancel Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        // }catch(\Exception $e){

        //     DB::rollback();

        //     $process_result['iin_id'] = $iin_cancel['iin_id'];
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'site <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

}
