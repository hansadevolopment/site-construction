<?php

namespace App\Models\SiteMM\SiteForcast;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

use App\Models\SiteMM\Master\OverheadCostItem;

class SiteOverheadCost extends Model {

    use HasFactory;

    protected $table = 'sap_overhead_cost';

    protected $primaryKey = 'sap_oc_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $dateFormat = 'U';

    public function getOverheadCost(){

        return OverheadCostItem::where('oci_id', $this->oci_id)->first();
    }

    public function saveSiteOverheadCost($data){

        $sap_overhead_cost = $data['sap_overhead_cost'];

        DB::beginTransaction();

        //try{

            $upsert_flag = SiteOverheadCost::where('site_id', $sap_overhead_cost['site_id'])->where('task_id', $sap_overhead_cost['task_id'])
                                            ->where('sub_task_id', $sap_overhead_cost['sub_task_id'])->where('oci_id', $sap_overhead_cost['oci_id'])
                                            ->exists();
            if( $upsert_flag ){

                DB::table('sap_overhead_cost')->where('site_id', $sap_overhead_cost['site_id'])
                                              ->where('task_id', $sap_overhead_cost['task_id'])
                                              ->where('sub_task_id', $sap_overhead_cost['sub_task_id'])
                                              ->where('oci_id', $sap_overhead_cost['oci_id'])
                                              ->update($sap_overhead_cost);

            }else{

                DB::table('sap_overhead_cost')->insert($sap_overhead_cost);
            }

            DB::table('sap_overhead_cost')->where('site_id', $sap_overhead_cost['site_id'])
                                          ->where('task_id', $sap_overhead_cost['task_id'])
                                          ->where('sub_task_id', $sap_overhead_cost['sub_task_id'])
                                          ->where('oci_id', $sap_overhead_cost['oci_id'])
                                          ->where('amount', 0)
                                          ->delete();

            DB::commit();

            $process_result['process_status'] = TRUE;
            $process_result['front_end_message'] = "Saving Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        // }catch(\Exception $e){

        //     DB::rollback();

        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'site <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

}
