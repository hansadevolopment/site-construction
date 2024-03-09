<?php

namespace App\Models\GL\Primary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class MainAccount extends Model{

    use HasFactory;

    protected $table = 'main_account';

    protected $primaryKey = 'ma_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $dateFormat = 'U';

    public function saveMainAccount($data){

        $main_account = $data['main_account'];

        DB::beginTransaction();

        //try{

            $exists_result = MainAccount::where('ma_id', $main_account['ma_id'])->exists();
            if( $exists_result ){

               $ma_id = $main_account['ma_id'];
               DB::table('main_account')->where('ma_id', $ma_id)->update($main_account);

            }else{

                DB::table('main_account')->insert($main_account);
                $ma_id = $main_account['ma_id'];
            }

            DB::commit();

            $process_result['ma_id'] = $ma_id;
            $process_result['process_status'] = TRUE;
            $process_result['front_end_message'] = "Saving Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        // }catch(\Exception $e){

        //     DB::rollback();

        //     $process_result['ma_id'] = $ma_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'Main Account <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }
}
