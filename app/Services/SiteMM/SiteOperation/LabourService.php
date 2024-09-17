<?php

namespace App\Services\SiteMM\SiteOperation;

use Illuminate\Support\Facades\DB;

class LabourService{

    public function getLabourDetail($site_id, $task_id, $sub_task_id){

        if( ($site_id != 0) && ($task_id == 0) && ($sub_task_id == 0) ){

            $sql_query = " 	select		es.es_id, es.es_date, es.employee_id, e.employee_name, '' as 'unit_name', 0 as 'price', total_hours, total_amount
                            from		employee_salary es
                                            inner join employee_salary_detail esd on es.es_id = esd.es_id
                                            inner join employee e on e.employee_id = es.employee_id
                            where		cancel = 0 && esd.site_id = ?
                            order by    es.es_date, e.employee_name  ";

		    $result = DB::select($sql_query, [$site_id]);

        }elseif( ($site_id != 0) && ($task_id != 0) && ($sub_task_id == 0) ){

            $sql_query = " 	select		es.es_id, es.es_date, es.employee_id, e.employee_name, '' as 'unit_name', 0 as 'price', total_hours, total_amount
                            from		employee_salary es
                                            inner join employee_salary_detail esd on es.es_id = esd.es_id
                                            inner join employee e on e.employee_id = es.employee_id
                            where		cancel = 0 && esd.site_id = ? && esd.task_id = ?
                            order by    es.es_date, e.employee_name   ";

		    $result = DB::select($sql_query, [$site_id, $task_id]);

        }elseif( ($site_id != 0) && ($task_id != 0) && ($sub_task_id != 0) ){

            $sql_query = " 	select		es.es_id, es.es_date, es.employee_id, e.employee_name, '' as 'unit_name', 0 as 'price', total_hours, total_amount
                            from		employee_salary es
                                            inner join employee_salary_detail esd on es.es_id = esd.es_id
                                            inner join employee e on e.employee_id = es.employee_id
                            where		cancel = 0 && esd.site_id = ? && esd.task_id = ? && esd.sub_task_id = ?
                            order by    es.es_date, e.employee_name  ";

		    $result = DB::select($sql_query, [$site_id, $task_id, $sub_task_id]);

        }else{

        }

		return $result;
    }


}
