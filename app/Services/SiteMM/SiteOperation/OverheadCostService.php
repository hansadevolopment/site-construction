<?php

namespace App\Services\SiteMM\SiteOperation;

use Illuminate\Support\Facades\DB;

class OverheadCostService{

    public function getOverheadCostDetail($site_id, $task_id, $sub_task_id){

        // dd( $site_id, $task_id, $sub_task_id );

        if( ($site_id != 0) && ($task_id == 0) && ($sub_task_id == 0) ){

            $sql_query = " 	select			pv.pv_id, pv.pv_date, oci.oci_name, u.unit_name, pvd.price, pvd.quantity, pvd.amount
                            from			payment_voucher pv
                                                inner join payment_voucher_detail pvd on pv.pv_id = pvd.pv_id
                                                inner join overhead_cost_item oci on pvd.oci_id = oci.oci_id
                                                inner join unit u on u.unit_id = oci.unit_id
                            where			cancel = 0 && pv.site_id = ?
                            order by        pv.pv_date, oci.oci_name  ";

		    $result = DB::select($sql_query, [$site_id]);

        }elseif( ($site_id != 0) && ($task_id != 0) && ($sub_task_id == 0) ){

            $sql_query = " 	select			pv.pv_id, pv.pv_date, oci.oci_name, u.unit_name, pvd.price, pvd.quantity, pvd.amount
                            from			payment_voucher pv
                                                inner join payment_voucher_detail pvd on pv.pv_id = pvd.pv_id
                                                inner join overhead_cost_item oci on pvd.oci_id = oci.oci_id
                                                inner join unit u on u.unit_id = oci.unit_id
                            where			cancel = 0 && pv.site_id = ? && pv.task_id = ?
                            order by        pv.pv_date, oci.oci_name   ";

		    $result = DB::select($sql_query, [$site_id, $task_id]);

        }elseif( ($site_id != 0) && ($task_id != 0) && ($sub_task_id != 0) ){

            $sql_query = " 	select			pv.pv_id, pv.pv_date, oci.oci_name, u.unit_name, pvd.price, pvd.quantity, pvd.amount
                            from			payment_voucher pv
                                                inner join payment_voucher_detail pvd on pv.pv_id = pvd.pv_id
                                                inner join overhead_cost_item oci on pvd.oci_id = oci.oci_id
                                                inner join unit u on u.unit_id = oci.unit_id
                            where			cancel = 0 && pv.site_id = ? && pv.task_id = ? && pv.sub_task_id = ?
                            order by        pv.pv_date, oci.oci_name  ";

		    $result = DB::select($sql_query, [$site_id, $task_id, $sub_task_id]);

        }else{

        }

		return $result;
    }


}
