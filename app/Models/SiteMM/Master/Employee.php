<?php

namespace App\Models\SiteMM\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

use App\Models\SiteMM\Master\LabourCategory;

class Employee extends Model {

    use HasFactory;

    protected $table = 'employee';

    protected $primaryKey = 'employee_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $dateFormat = 'U';

    public function getLaborCategory(){

        return LabourCategory::where('lc_id', $this->lc_id)->first();
    }

    public function saveEmployee($data){

        $employee = $data['employee'];

        DB::beginTransaction();

        //try{

            if($employee['employee_id'] == '#Auto#'){

                unset($employee['employee_id']);

                DB::table('employee')->insert($employee);
                $employee_id = DB::getPdo()->lastInsertId();

            }else{

               $employee_id = $employee['employee_id'];
               DB::table('employee')->where('employee_id', $employee_id)->update($employee);
            }

            DB::commit();

            $process_result['employee_id'] = $employee_id;
            $process_result['process_status'] = TRUE;
            $process_result['front_end_message'] = "Saving Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        // }catch(\Exception $e){

        //     DB::rollback();

        //     $process_result['employee_id'] = $employee_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'item <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }




}
