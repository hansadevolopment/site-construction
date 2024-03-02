<?php

namespace App\Services\SiteMM\SiteOperation;

use Illuminate\Support\Facades\DB;

class MaterialService{

    public function getMaterialDetail($site_id, $task_id, $sub_task_id){

        if( ($site_id != 0) && ($task_id == 0) && ($sub_task_id == 0) ){

            $sql_query = " 	select		iin.iin_id, iin.iin_date, iid.item_id, i.item_name, u.unit_name, iid.price, iid.quantity, iid.amount
                            from		item_issue_note iin
                                            inner join item_issue_note_detail iid on iin.iin_id = iid.iin_id
                                            inner join item i on iid.item_id = i.item_id
                                            inner join unit u on i.unit_id = u.unit_id
                            where		cancel = 0 && site_id = ? && task_id = ?
                            order by	iin_date, i.item_name  ";

		    $result = DB::select($sql_query, [$site_id]);

        }elseif( ($site_id != 0) && ($task_id != 0) && ($sub_task_id == 0) ){

            $sql_query = " 	select		iin.iin_id, iin.iin_date, iid.item_id, i.item_name, u.unit_name, iid.price, iid.quantity, iid.amount
                            from		item_issue_note iin
                                            inner join item_issue_note_detail iid on iin.iin_id = iid.iin_id
                                            inner join item i on iid.item_id = i.item_id
                                            inner join unit u on i.unit_id = u.unit_id
                            where		cancel = 0 && site_id = ? && task_id = ?
                            order by	iin_date, i.item_name  ";

		    $result = DB::select($sql_query, [$site_id, $task_id]);

        }elseif( ($site_id != 0) && ($task_id != 0) && ($sub_task_id != 0) ){

            $sql_query = " 	select		iin.iin_id, iin.iin_date, iid.item_id, i.item_name, u.unit_name, iid.price, iid.quantity, iid.amount
                            from		item_issue_note iin
                                            inner join item_issue_note_detail iid on iin.iin_id = iid.iin_id
                                            inner join item i on iid.item_id = i.item_id
                                            inner join unit u on i.unit_id = u.unit_id
                            where		cancel = 0 && site_id = ? && task_id = ? && sub_task_id = ?
                            order by	iin_date, i.item_name  ";

		    $result = DB::select($sql_query, [$site_id, $task_id, $sub_task_id]);

        }else{

        }

		return $result;
    }

}
