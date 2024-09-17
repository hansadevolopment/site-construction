<?php

namespace App\Models\GL\Primary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class Tax extends Model {

    use HasFactory;

    protected $table = 'tax';

    protected $primaryKey = 'tax_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $dateFormat = 'U';

    public function saveTax($data){

        $tax = $data['tax'];

        DB::beginTransaction();

        //try{

            $exists_result = Tax::where('tax_id', $tax['tax_id'])->exists();
            if( $exists_result ){

               $tax_id = $tax['tax_id'];
               DB::table('tax')->where('tax_id', $tax_id)->update($tax);

            }else{

                unset($tax['tax_id']);
                DB::table('tax')->insert($tax);
                $tax_id = DB::getPdo()->lastInsertId();
            }

            DB::commit();

            $process_result['tax_id'] = $tax_id;
            $process_result['process_status'] = TRUE;
            $process_result['front_end_message'] = "Saving Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        // }catch(\Exception $e){

        //     DB::rollback();

        //     $process_result['tax_id'] = $tax_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'Tax <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

}
