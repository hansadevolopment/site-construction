<?php

namespace App\Models\SiteMM\SiteForcast;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class SiteProfit extends Model {

    use HasFactory;

    protected $table = 'sap_profit';

    protected $primaryKey = 'sap_profit_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $dateFormat = 'U';

    public function saveSiteProfit($data){

        $sap_profit = $data['sap_profit'];

        DB::beginTransaction();

        //try{

            $upsert_flag = SiteProfit::where('site_id', $sap_profit['site_id'])
                                     ->where('task_id', $sap_profit['task_id'])
                                     ->where('sub_task_id', $sap_profit['sub_task_id'])
                                     ->exists();
            if( $upsert_flag ){

                DB::table('sap_profit')->where('site_id', $sap_profit['site_id'])
                                       ->where('task_id', $sap_profit['task_id'])
                                       ->where('sub_task_id', $sap_profit['sub_task_id'])
                                       ->update($sap_profit);

            }else{

                DB::table('sap_profit')->insert($sap_profit);
            }

            // DB::table('sap_profit')->where('site_id', $sap_profit['site_id'])
            //                        ->where('task_id', $sap_profit['task_id'])
            //                        ->where('sub_task_id', $sap_profit['sub_task_id'])
            //                        ->where('profit_value', $sap_profit['profit_value'])
            //                        ->delete();

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
