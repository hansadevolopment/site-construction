<?php

namespace App\Models\SiteMM\SiteForcast;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

use App\Models\SiteMM\Master\LabourCategory;
use App\Models\SiteMM\Master\Unit;

class SiteLabour extends Model {

    use HasFactory;

    protected $table = 'sap_labour';

    protected $primaryKey = 'sap_labour_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $dateFormat = 'U';

    public function getLabourCategory(){

        return LabourCategory::where('lc_id', $this->lc_id)->first();
    }

    public function getUnit(){

        return Unit::where('unit_id', $this->unit_id)->first();
    }

    public function saveSiteLabour($data){

        $sap_labour = $data['sap_labour'];

        DB::beginTransaction();

        //try{

            //dd( $sap_labour );

            if( $sap_labour['sc_id'] == 1 ){

                $upsert_flag = SiteLabour::where('site_id', $sap_labour['site_id'])->where('task_id', $sap_labour['task_id'])
                                            ->where('sub_task_id', $sap_labour['sub_task_id'])->where('lc_id', $sap_labour['lc_id'])
                                            ->exists();
            }

            if( ($sap_labour['sc_id'] == 2) || ($sap_labour['sc_id'] == 3) ){

                $upsert_flag = SiteLabour::where('site_id', $sap_labour['site_id'])->where('task_id', $sap_labour['task_id'])
                                            ->where('sub_task_id', $sap_labour['sub_task_id'])->where('unit_id', $sap_labour['unit_id'])
                                            ->exists();
            }


            if( $upsert_flag ){

                if( $sap_labour['sc_id'] == 1 ){

                    DB::table('sap_labour')->where('site_id', $sap_labour['site_id'])
                                            ->where('task_id', $sap_labour['task_id'])
                                            ->where('sub_task_id', $sap_labour['sub_task_id'])
                                            ->where('lc_id', $sap_labour['lc_id'])
                                            ->update($sap_labour);
                }

                if( ($sap_labour['sc_id'] == 2) || ($sap_labour['sc_id'] == 3) ){

                    DB::table('sap_labour')->where('site_id', $sap_labour['site_id'])
                                            ->where('task_id', $sap_labour['task_id'])
                                            ->where('sub_task_id', $sap_labour['sub_task_id'])
                                            ->where('unit_id', $sap_labour['unit_id'])
                                            ->update($sap_labour);
                }

            }else{

                DB::table('sap_labour')->insert($sap_labour);
            }


            if( $sap_labour['sc_id'] == 1 ){

                DB::table('sap_labour')->where('site_id', $sap_labour['site_id'])
                                     ->where('task_id', $sap_labour['task_id'])
                                     ->where('sub_task_id', $sap_labour['sub_task_id'])
                                     ->where('lc_id', $sap_labour['lc_id'])
                                     ->where('days', 0)
                                     ->delete();
            }


            if( ($sap_labour['sc_id'] == 2) || ($sap_labour['sc_id'] == 3) ){

                DB::table('sap_labour')->where('site_id', $sap_labour['site_id'])
                                            ->where('task_id', $sap_labour['task_id'])
                                            ->where('sub_task_id', $sap_labour['sub_task_id'])
                                            ->where('unit_id', $sap_labour['unit_id'])
                                            ->where('days', 0)
                                            ->delete();
            }

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
