<?php

namespace App\Models\GL\Primary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class BankAccount extends Model {

    use HasFactory;

    protected $table = 'bank_account';

    protected $primaryKey = 'ba_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $dateFormat = 'U';

    public function saveBankAccount($data){

        $bank_account = $data['bank_account'];

        DB::beginTransaction();

        //try{

            $exists_result = BankAccount::where('ba_id', $bank_account['ba_id'])->exists();
            if( $exists_result ){

               $ba_id = $bank_account['ba_id'];
               DB::table('bank_account')->where('ba_id', $ba_id)->update($bank_account);

            }else{

                unset($bank_account['ba_id']);
                DB::table('bank_account')->insert($bank_account);
                $ba_id = DB::getPdo()->lastInsertId();
            }

            DB::commit();

            $process_result['ba_id'] = $ba_id;
            $process_result['process_status'] = TRUE;
            $process_result['front_end_message'] = "Saving Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        // }catch(\Exception $e){

        //     DB::rollback();

        //     $process_result['ba_id'] = $ba_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'bank_account <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }



}
