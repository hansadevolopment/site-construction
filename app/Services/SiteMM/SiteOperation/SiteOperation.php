<?php

namespace App\Services\SiteMM\SiteOperation;

use Illuminate\Support\Facades\DB;

class SiteOperation{

    public function getSiteOperationCost($site_id, $task_id, $sub_task_id){


        // $data = DB::table('item_issue_note')->where('cancel', 0)->where('site_id', $site_id)->get();

        $data = DB::table('item_issue_note')->where('cancel', 0)->get();

        dump(gettype($data));

        dump(get_class($data));

        dump($data->where('site_id', 8));

        dump($data->where('site_id', 8)->sum('total_amount'));

        dump($data[0]->iin_id);


        dd( $data );

    }

}
