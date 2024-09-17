<?php

namespace App\Models\GL\Primary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class Bank extends Model {

    use HasFactory;

    protected $table = 'bank';

    protected $primaryKey = 'bank_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $dateFormat = 'U';

    public function saveBank($data){

        $bank = $data['bank'];

        DB::beginTransaction();

        //try{

            $exists_result = Bank::where('bank_id', $bank['bank_id'])->exists();
            if( $exists_result ){

               $bank_id = $bank['bank_id'];
               DB::table('bank')->where('bank_id', $bank_id)->update($bank);

            }else{

                unset($bank['bank_id']);
                DB::table('bank')->insert($bank);
                $bank_id = DB::getPdo()->lastInsertId();
            }

            DB::commit();

            $process_result['bank_id'] = $bank_id;
            $process_result['process_status'] = TRUE;
            $process_result['front_end_message'] = "Saving Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        // }catch(\Exception $e){

        //     DB::rollback();

        //     $process_result['bank_id'] = $bank_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'Bank <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }


}
