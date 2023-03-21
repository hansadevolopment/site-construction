<?php

namespace App\Models\Purchase\Transaction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;
use App\Models\Inventory\Primary\ItemMaster;

class GoodReceiveNote extends Model {

    use HasFactory;

    public function grnSavingProcess($data){

        $grn_id = 0;

        $grn = $data['grn'];
        $grn_detail = $data['grn_detail'];
        $grn_tax_detail = $data['grn_tax_detail'];

        DB::beginTransaction();

        //try{

            // Good Receive Note
            if($grn['grn_id'] == '#Auto#'){

                unset($grn['grn_id']);

                DB::table('good_receive_note')->insert($grn);
                $grn_id = DB::getPdo()->lastInsertId();

            }else{

               $grn_id = $grn['grn_id'];
               DB::table('good_receive_note')->where('grn_id', $grn_id)->update($grn);
            }

            // Good Receive Note Detail
            if(is_null($grn_detail) == FALSE){
                
                $item_exists = DB::table('good_receive_note_detail')->where('grn_id', '=',  $grn_id)->where('item_id', '=', $grn_detail['item_id'])->exists();

                if ($item_exists == TRUE) {

                    DB::table('good_receive_note_detail')->where('grn_id', '=',  $grn_id)->where('item_id', '=', $grn_detail['item_id'])->update($grn_detail);
                }else{

                    $grn_detail['grn_id'] = $grn_id;
                    DB::table('good_receive_note_detail')->insert($grn_detail);
                }
            }

            // Good Receive Note Tax
            if(is_null($grn_tax_detail) == FALSE){
                
                DB::table('good_receive_note_tax')->where('grn_id', $grn_id)->where('item_id', $grn_detail['item_id'])->delete();

                $tax_amount = 0;
                foreach($grn_tax_detail as $row){

                    $tax_amount = $tax_amount + $row['tax_amount'];

                    $row['grn_id'] = $grn_id;
                    DB::table('good_receive_note_tax')->insert($row);
                }

                // Tax Update at Good Receive Note Detail
                $grn_tax_item['tax_amount'] = $tax_amount;

                DB::table('good_receive_note_detail')->where('grn_id', '=',  $grn_id)->where('item_id', $grn_detail['item_id'])->update($grn_tax_item);
            }


            // Update Grn Total Amounts
            $grn_total_detail = $this->getGoodReceiveNoteTotalAmounts($grn_id);
            foreach($grn_total_detail as $row){

                $grn_total_array['total_gross_amount'] = $row->gross_amount;
                $grn_total_array['total_discount_amount'] = $row->discount_amount;
                $grn_total_array['total_tax_amount'] = $row->tax_amount;
                $grn_total_array['total_net_amount'] = $row->net_amount;

                DB::table('good_receive_note')->where('grn_id', $grn_id)->update($grn_total_array);
            }

            DB::commit();

            $process_result['grn_id'] = $grn_id;
            $process_result['process_result'] = TRUE;
            $process_result['front_end_message'] = "Saving Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        // }catch(\Exception $e){

        //     $process_result['grn_id'] = $data['grn']['grn_id'];
        //     $process_result['process_result'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'GRN Model : GRN Saving Process. <br> Line No. ' . $e->getLine();

        //     DB::rollback();

        //     return $process_result;
        // }

    }

    public function glPostProcess($data){

        $objItemMaster = new ItemMaster();

        $item = $data['main_store'];
        $gl_post_raw = $data['gl_post_raw'];

        DB::beginTransaction();

        try{

            for ($i = 1; $i < (count($item)+1); $i++){

                //Update Main Stores
                $item_id = $item[$i]['item_id'];
                $item_serial_no = $objItemMaster->getItemSerialNumber($item_id) + 1;

                $item[$i]['serial'] = $item_serial_no;

                DB::table('main_store')->insert($item[$i]);

                //Update Item Master
                $item_array['serial'] = $item_serial_no;
                DB::table('item_master')->where('item_id', $item_id)->update($item_array);
            }


            DB::table('good_receive_note')->where('grn_id', $gl_post_raw['grn_id'])->update($gl_post_raw);

            DB::commit();

            $process_result['grn_id'] = $gl_post_raw['grn_id'];
            $process_result['process_result'] = TRUE;
            $process_result['front_end_message'] = "Confirming Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        }catch(\Exception $e){

            DB::rollback();

            $process_result['grn_id'] = $gl_post_raw['grn_id'];
            $process_result['process_result'] = FALSE;
            $process_result['front_end_message'] = $e->getMessage();
            $process_result['back_end_message'] = 'GRN Model : GRN Confirm Process. <br> Line No. ' . $e->getLine();

            return $process_result;
        }
    }

    public function cancelGrn($data){

        DB::beginTransaction();

        //try{

            $cancel_raw =  $data['cancel_raw'];

            DB::table('good_receive_note')->where('grn_id', $cancel_raw['grn_id'])->update($cancel_raw);

            DB::commit();

            $process_result['grn_id'] = $cancel_raw['grn_id'];
            $process_result['process_result'] = TRUE;
            $process_result['front_end_message'] = "Cancel Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        // }catch(\Exception $e){

        //     DB::rollback();

        //     $process_result['grn_id'] = $cancel_raw['grn_id'];
        //     $process_result['process_result'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'GRN Model : GRN Cancel Process. <br> Line No. ' . $e->getLine();

        //     return $process_result;
        // }
    }

    public function getGoodReceiveNote($grn_id){

        $result = DB::table('good_receive_note')->where('grn_id', $grn_id)->get();

        return $result;
    }

    public function getGoodReceiveNoteDetail($grn_id){

        $result = DB::table('good_receive_note_detail')->where('grn_id', $grn_id)->get();

        return $result;
    }

    public function isCancelGoodReceiveNote($grn_id){

        $cancel = DB::table('good_receive_note')->where('grn_id', $grn_id)->where('cancel', 1)->exists();

        return $cancel;
    }

    public function isGLPostedGoodReceiveNote($grn_id){

        $confirm = DB::table('good_receive_note')->where('grn_id', $grn_id)->where('gl_post', 1)->exists();

        return $confirm;
    }

    public function getGoodReceiveNoteTotalAmounts($grn_id){

        $sql_query=" select		grn_id, sum(gross_amount) as 'gross_amount',  sum(tax_amount) as 'tax_amount', sum(discount_amount) as 'discount_amount', sum(net_amount) as 'net_amount'
                     from		good_receive_note_detail
                     where		grn_id = ? 
                     group by   grn_id ";

        $result = DB::select($sql_query,[$grn_id]);

        return $result;
    }

    public function grn_information_for_print_document($grn_id){

        $sql_query=" select		g.grn_id, g.date, g.purchase_order_number, c.creditor_name, c.address
                     from		good_receive_note g inner join creditor c on g.creditor_id = c.creditor_id
                     where		g.grn_id = ?  ";
        $result = DB::select($sql_query,[$grn_id]);

        return $result;
    }

}
