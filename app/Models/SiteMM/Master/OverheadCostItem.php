<?php

namespace App\Models\SiteMM\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

use App\Models\SiteMM\Master\Unit;

class OverheadCostItem extends Model {

    use HasFactory;

    protected $table = 'overhead_cost_item';

    protected $primaryKey = 'oci_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $dateFormat = 'U';

    public function saveOverheadCostItem($data){

        $oc_item = $data['oc_item'];

        DB::beginTransaction();

        //try{

            if($oc_item['oci_id'] == '#Auto#'){

                unset($oc_item['oci_id']);

                DB::table('overhead_cost_item')->insert($oc_item);
                $oci_id = DB::getPdo()->lastInsertId();

            }else{

               $oci_id = $oc_item['oci_id'];
               DB::table('overhead_cost_item')->where('oci_id', $oci_id)->update($oc_item);
            }

            DB::commit();

            $process_result['oci_id'] = $oci_id;
            $process_result['process_status'] = TRUE;
            $process_result['front_end_message'] = "Saving Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        // }catch(\Exception $e){

        //     DB::rollback();

        //     $process_result['oci_id'] = $oci_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'Overhead Cost Item <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

    public function getUnit(){

        return Unit::where('unit_id', $this->unit_id)->first();
    }
}
