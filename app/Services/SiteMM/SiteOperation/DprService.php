<?php

namespace App\Services\SiteMM\SiteOperation;

use Illuminate\Support\Facades\DB;

class DprService{

    public static function getDprQuantityItemWise($site_id, $task_id, $sub_task_id, $item_id){

        $item_quantity = DB::table('dpr')
                                ->join('dpr_detail', 'dpr.dpr_id', 'dpr_detail.dpr_id' )
                                ->where('cancel', 0)
                                ->where('site_id', $site_id)
                                ->where('task_id', $task_id)
                                ->where('sub_task_id', $sub_task_id)
                                ->where('item_id', $item_id)
                                ->groupBy('dpr_detail.item_id')
                                ->sum('dpr_detail.quantity');
		return $item_quantity;
    }

}
