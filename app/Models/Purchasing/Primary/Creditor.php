<?php

namespace App\Models\Purchasing\Primary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class Creditor extends Model {

    use HasFactory;

    protected $table = 'creditor';

    protected $primaryKey = 'creditor_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $dateFormat = 'U';

    public function saveCreditor($data){

        $creditor = $data['creditor'];

        $creditor_id = 0;

        DB::beginTransaction();

        //try{

            if($creditor['creditor_id'] == '#Auto#'){

                unset($creditor['creditor_id']);

                DB::table('creditor')->insert($creditor);
                $creditor_id = DB::getPdo()->lastInsertId();

            }else{

               $creditor_id = $creditor['creditor_id'];
               DB::table('creditor')->where('creditor', $creditor_id)->update($creditor);
            }

            DB::commit();

            $process_result['creditor_id'] = $creditor_id;
            $process_result['process_status'] = TRUE;
            $process_result['front_end_message'] = "Saving Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        // }catch(\Exception $e){

        //     DB::rollback();

        //     $process_result['creditor_id'] = $creditor_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'Creditor <br> ' . $e->getLine();

        //     return $process_result;
        // }

    }

}
