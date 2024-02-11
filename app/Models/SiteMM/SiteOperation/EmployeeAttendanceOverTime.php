<?php

namespace App\Models\SiteMM\SiteOperation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class EmployeeAttendanceOverTime extends Model {

    use HasFactory;

    protected $table = 'employee_attendance_overtime';

    protected $primaryKey = 'eao_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $dateFormat = 'U';

    public function saveEmployeeAttendanceOverTime($data){

        $eao = $data['eao'];

        DB::beginTransaction();

        //try{

            foreach($eao as $key => $value){

                $upsert_flag = EmployeeAttendanceOverTime::where('employee_id', $value['employee_id'])->where('eao_date', $value['eao_date'])->exists();
                if( $upsert_flag ){

                    DB::table('employee_attendance_overtime')->where('employee_id', $value['employee_id'])->where('eao_date', $value['eao_date'])->update($value);
                }else{

                    DB::table('employee_attendance_overtime')->insert($value);
                }
            }

            DB::commit();

            $process_result['process_status'] = TRUE;
            $process_result['front_end_message'] = "Saving Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        // }catch(\Exception $e){

        //     DB::rollback();

        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'site <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

}
