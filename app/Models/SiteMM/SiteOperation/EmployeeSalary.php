<?php

namespace App\Models\SiteMM\SiteOperation;

use App\Models\SiteMM\Master\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

use App\Models\SiteMM\SiteOperation\EmployeeSalaryDetail;

class EmployeeSalary extends Model {

    use HasFactory;

    protected $table = 'employee_salary';

    protected $primaryKey = 'es_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $dateFormat = 'U';

    public function getEmployee(){

        return Employee::where('employee_id', $this->employee_id)->first();
    }

    public function getEmployeeSalaryDetail(){

        return $this->hasMany(EmployeeSalaryDetail::class, 'es_id');
    }

    public function saveEmployeeSalary($data){

        $employee_salary = $data['employee_salary'];
        $employee_salary_detail = $data['employee_salary_detail'];
        $employee_advance = $data['employee_advance'];
        $employee_salary_advance_settlement_detail = $data['employee_salary_advance_settlement_detail'];

        DB::beginTransaction();

        //try{

            // Main Table
            if( $employee_salary['es_id'] == '#Auto#' ){

                unset($employee_salary['es_id']);
                DB::table('employee_salary')->insert($employee_salary);
                $es_id = DB::getPdo()->lastInsertId();

            }else{

                $es_id = $employee_salary['es_id'];
                DB::table('employee_salary')->where('es_id', $employee_salary['es_id'])->update($employee_salary);
            }

            // Detail Table
            DB::table('employee_salary_detail')->where('es_id', $es_id)->delete();

            for ($x = 1; $x <= $employee_salary['site_count']; $x++){

                $employee_salary_detail[$x]['es_id'] = $es_id;
                DB::table('employee_salary_detail')->insert($employee_salary_detail[$x]);
            }

            // Deduct Employee Advance
            if(is_null($employee_advance) == FALSE ){

                foreach($employee_advance as $key => $value){

                    $ea_id = $value['ea_id'];
                    unset($value['ea_id']);
                    DB::table('employee_advance')->where('ea_id', $ea_id)->update($value);
                }
            }

            //Save Employee Advance Settlement Detail
            if( is_null($employee_salary_advance_settlement_detail) == FALSE ){

                foreach($employee_salary_advance_settlement_detail as $key => $value){

                    $value['es_id'] = $es_id;
                    if($value['settle_amount'] != 0){

                        DB::table('employee_salary_advance_settlement_detail')->insert($value);
                    }
                }
            }

            DB::commit();

            $process_result['es_id'] = $es_id;
            $process_result['process_status'] = TRUE;
            $process_result['front_end_message'] = "Saving Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        // }catch(\Exception $e){

        //     DB::rollback();

        //     $process_result['es_id'] = $data['employee_salary']['es_id'];
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'site <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }
}
