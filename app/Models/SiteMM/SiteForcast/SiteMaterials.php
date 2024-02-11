<?php

namespace App\Models\SiteMM\SiteForcast;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

use App\Models\SiteMM\Master\Item;

class SiteMaterials extends Model {

    use HasFactory;

    protected $table = 'sap_material';

    protected $primaryKey = 'sap_material_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $dateFormat = 'U';

    public function getItem(){

        return Item::where('item_id', $this->item_id)->first();
    }

    public function saveSiteMaterials($data){

        $sap_material = $data['sap_material'];

        DB::beginTransaction();

        //try{

            $upsert_flag = SiteMaterials::where('site_id', $sap_material['site_id'])->where('task_id', $sap_material['task_id'])
                                        ->where('sub_task_id', $sap_material['sub_task_id'])->where('item_id', $sap_material['item_id'])
                                        ->exists();
            if( $upsert_flag ){

                DB::table('sap_material')->where('site_id', $sap_material['site_id'])
                                         ->where('task_id', $sap_material['task_id'])
                                         ->where('sub_task_id', $sap_material['sub_task_id'])
                                         ->where('item_id', $sap_material['item_id'])
                                         ->update($sap_material);

            }else{

                DB::table('sap_material')->insert($sap_material);
            }

            DB::table('sap_material')->where('site_id', $sap_material['site_id'])
                                     ->where('task_id', $sap_material['task_id'])
                                     ->where('sub_task_id', $sap_material['sub_task_id'])
                                     ->where('item_id', $sap_material['item_id'])
                                     ->where('quantity', 0)
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
