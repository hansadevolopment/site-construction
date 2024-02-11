<?php

namespace App\Models\SiteMM\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

use App\Models\SiteMM\Master\Unit;

class Item extends Model {

    use HasFactory;

    protected $table = 'item';

    protected $primaryKey = 'item_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $dateFormat = 'U';

    public function getUnit(){

        return Unit::where('unit_id', $this->unit_id)->first();
    }

    public function saveItem($data){

        $item = $data['item'];

        DB::beginTransaction();

        //try{

            if($item['item_id'] == '#Auto#'){

                unset($item['item_id']);

                DB::table('item')->insert($item);
                $item_id = DB::getPdo()->lastInsertId();

            }else{

               $item_id = $item['item_id'];
               DB::table('item')->where('item_id', $item_id)->update($item);
            }

            DB::commit();

            $process_result['item_id'] = $item_id;
            $process_result['process_status'] = TRUE;
            $process_result['front_end_message'] = "Saving Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        // }catch(\Exception $e){

        //     DB::rollback();

        //     $process_result['item_id'] = $item_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'item <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

}



