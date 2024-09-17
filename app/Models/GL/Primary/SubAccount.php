<?php

namespace App\Models\GL\Primary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class SubAccount extends Model {

    use HasFactory;

    protected $table = 'sub_account';

    protected $primaryKey = 'sa_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $dateFormat = 'U';

    public function saveSubAccount($data){

        $sub_account = $data['sub_account'];

        DB::beginTransaction();

        //try{

            $exists_result = SubAccount::where('sa_id', $sub_account['sa_id'])->exists();
            if( $exists_result ){

               $sa_id = $sub_account['sa_id'];
               DB::table('sub_account')->where('sa_id', $sa_id)->update($sub_account);

            }else{

                DB::table('sub_account')->insert($sub_account);
                $sa_id = $sub_account['sa_id'];
            }

            DB::commit();

            $process_result['sa_id'] = $sa_id;
            $process_result['process_status'] = TRUE;
            $process_result['front_end_message'] = "Saving Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        // }catch(\Exception $e){

        //     DB::rollback();

        //     $process_result['sa_id'] = $sa_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'Sub Account <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }
}
