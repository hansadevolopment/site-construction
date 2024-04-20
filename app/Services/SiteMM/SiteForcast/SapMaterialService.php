<?php

namespace App\Services\SiteMM\SiteForcast;

use Illuminate\Support\Facades\DB;

class SapMaterialService{

    public function getSapMaterialDetail($site_id, $task_id, $sub_task_id){

        if( ($site_id != 0) && ($task_id == 0) && ($sub_task_id == 0) ){

            $result = DB::table('sap_material')->where('site_id', $site_id)->get();

        }elseif( ($site_id != 0) && ($task_id != 0) && ($sub_task_id == 0) ){

            $result = DB::table('sap_material')->where('site_id', $site_id)->where('task_id', $task_id)->get();

        }elseif( ($site_id != 0) && ($task_id != 0) && ($sub_task_id != 0) ){

            $result = DB::table('sap_material')->where('site_id', $site_id)->where('task_id', $task_id)->where('sub_task_id', $sub_task_id)->get();

        }else{

        }

		return $result;
    }

}
