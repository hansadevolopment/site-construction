<?php

namespace App\Models\Inventory\Primary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class ItemMaster extends Model {

    use HasFactory;

    public function itemSavingProcess($data){

        $item = $data['item'];

        DB::beginTransaction();

        //try{

            if($item['item_id'] == '#Auto#'){

                unset($item['item_id']);

                DB::table('item_master')->insert($item);
                $item_id = DB::getPdo()->lastInsertId();

            }else{

               $item_id = $item['item_id'];
               DB::table('item_master')->where('item_id', $item_id)->update($item);
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
        //     $process_result['back_end_message'] = 'Item <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }


    public function getItem($item_id){

        $result = DB::table('item_master')->where('item_id', $item_id)->get();

        return $result;
    }

    public function getItemName($item_id){

        $item_name = DB::table('item_master')->where('item_id', $item_id)->value('item_name');

        return $item_name;
    }

    public function getActiveItemList(){

        $result = DB::table('item_master')->where('active', 1)->get();

        return $result;
    }

    public function getItemTaxDetail($item_id){

        $sql_query = " select		it.item_id, i.item_name, t.tax_id, t.tax_name, t.tax_short_name, t.tax_rate 
                       from		    item_tax it 
                                        inner join item_master i on i.item_id = it.item_id
                                        left outer join tax t on it.tax_id = t.tax_id
                       where		it.item_id = ? ";
                       
        $result = DB::select($sql_query, [$item_id]);

        return $result;
    }

    public function getItemSerialNumber($item_id){

        $item_serial = DB::table('item_master')->where('item_id', $item_id)->value('serial');

        return $item_serial; 
    }


}
