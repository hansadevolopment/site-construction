<?php

namespace App\Models\SiteMM\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

use App\Models\SiteMM\SiteForcast\SiteTask;

class Site extends Model {

    use HasFactory;

    protected $table = 'site';

    protected $primaryKey = 'site_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $dateFormat = 'U';

    public function task(){

        return $this->hasMany(SiteTask::class, 'site_id');
    }

    public function getTask(){

        return $this->hasMany(SiteTask::class, 'site_id');
    }

    public function saveSite($data){

        $site = $data['site'];

        DB::beginTransaction();

        //try{

            if($site['site_id'] == '#Auto#'){

                unset($site['site_id']);

                DB::table('site')->insert($site);
                $site_id = DB::getPdo()->lastInsertId();

            }else{

               $site_id = $site['site_id'];
               DB::table('site')->where('site_id', $site_id)->update($site);
            }

            DB::commit();

            $process_result['site_id'] = $site_id;
            $process_result['process_status'] = TRUE;
            $process_result['front_end_message'] = "Saving Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        // }catch(\Exception $e){

        //     DB::rollback();

        //     $process_result['site_id'] = $site_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'site <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

}
