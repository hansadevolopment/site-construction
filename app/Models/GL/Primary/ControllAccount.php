<?php

namespace App\Models\GL\Primary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class ControllAccount extends Model {

    use HasFactory;

    protected $table = 'controll_account';

    protected $primaryKey = 'ca_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $dateFormat = 'U';

    public function saveControllAccount($data){

        $controll_account = $data['controll_account'];

        DB::beginTransaction();

        //try{

            $exists_result = ControllAccount::where('ca_id', $controll_account['ca_id'])->exists();
            if( $exists_result ){

               $ca_id = $controll_account['ca_id'];
               DB::table('controll_account')->where('ca_id', $ca_id)->update($controll_account);

            }else{

                DB::table('controll_account')->insert($controll_account);
                $ca_id = $controll_account['ca_id'];
            }

            DB::commit();

            $process_result['ca_id'] = $ca_id;
            $process_result['process_status'] = TRUE;
            $process_result['front_end_message'] = "Saving Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        // }catch(\Exception $e){

        //     DB::rollback();

        //     $process_result['ca_id'] = $ca_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'Controll Account <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

}
