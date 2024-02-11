<?php

namespace App\Http\Controllers\SiteMM\SiteOperation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SiteMM\Master\Employee;
use App\Models\SiteMM\SiteOperation\EmployeeAttendanceOverTime;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Carbon;
use Carbon\CarbonPeriod;

use App\Rules\ZeroValidation;
use App\Rules\CurrencyValidation;
use Illuminate\Support\Arr;

class EmployeeAttendanceOverTimeController extends Controller {

    public function loadView(){

        $data['attributes'] = $this->getEmployeeAttendanceOvertimeAttributes(NULL, NULL);

        return view('SiteMM.SiteOperation.employee_attendance')->with('ES', $data);
    }

    private function getEmployeeAttendanceOvertimeAttributes($process, $request){

        $attributes['attendance_date'] = Carbon::today()->toDateString();
        $attributes['attendance_overtime_detail'] = $this->getEmployeeAttendanceOvertimeDetail(Carbon::today()->toDateString());

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }


        $inputs = $request->input();
        if(is_null($inputs) == FALSE){

            $attributes['attendance_date'] = $inputs['attendance_date'];
        }
        $attributes['attendance_overtime_detail'] = $this->getEmployeeAttendanceOvertimeDetail($request->attendance_date);

        if( ($process['validation_result'] == TRUE) && ($process['process_status'] == TRUE)){

            $attributes['validation_messages'] = $process['validation_messages'];
            $message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-success" role="alert"> '. $message .' </div> ';

        }else{

            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';
        }

        return $attributes;
    }

    private function getEmployeeAttendanceOvertimeDetail($attendance_date){

        $eao_detail = array();

        $elqEmployee = Employee::where('active', 1)->get();
        foreach($elqEmployee as $key => $row ){

            $eao_detail[$key+1]['ono'] = ($key +1);
            $eao_detail[$key+1]['emp_id'] = $row->employee_id;
            $eao_detail[$key+1]['emp_code'] = $row->employee_code;
            $eao_detail[$key+1]['emp_name'] = $row->employee_name;

            $elqEmployeeAttendanceOvertime = EmployeeAttendanceOverTime::where('eao_date', $attendance_date)->get();
            if( is_null($elqEmployeeAttendanceOvertime) == false ){

                $elqEAO = $elqEmployeeAttendanceOvertime->where('employee_id', $row->employee_id);
                if( $elqEAO->count() >= 1 ){

                    foreach( $elqEAO as $key => $value){

                        $eao_detail[$key+1]['attendance'] = $value->attendance;
                        $eao_detail[$key+1]['ot_hours'] = $value->ot_hours;
                        $eao_detail[$key+1]['remark'] = $value->remark;
                    }

                }else{

                    $eao_detail[$key+1]['attendance'] = '';
                    $eao_detail[$key+1]['ot_hours'] = 0;
                    $eao_detail[$key+1]['remark'] = '';
                }

            }else{

                $eao_detail[$key+1]['attendance'] = '';
                $eao_detail[$key+1]['ot_hours'] = 0;
                $eao_detail[$key+1]['remark'] = '';
            }
        }

        return $eao_detail;
    }

    public function processEmployeeAttendanceOvertime(Request $request){

        $attendance_overtime_validation_result = $this->validateEmployeeAttendanceOvertimeInput($request);
        if($attendance_overtime_validation_result['validation_result'] == TRUE){

            $saving_process_result = $this->saveEmployeeAttendanceOvertime($request);

            $saving_process_result['validation_result'] = $attendance_overtime_validation_result['validation_result'];
            $saving_process_result['validation_messages'] = $attendance_overtime_validation_result['validation_messages'];
            $data['attributes'] = $this->getEmployeeAttendanceOvertimeAttributes($saving_process_result, $request);

        }else{

            $attendance_overtime_validation_result['process_status'] = FALSE;
            $data['attributes'] = $this->getEmployeeAttendanceOvertimeAttributes($attendance_overtime_validation_result, $request);
        }


        return view('SiteMM.SiteOperation.employee_attendance')->with('ES', $data);
    }

    private function validateEmployeeAttendanceOvertimeInput($request){

        //try{

            $inputs['attendance_date'] = $request->attendance_date;
            $rules['attendance_date'] = array('required', 'date');

            $request_inputs = $request->all();
            foreach($request_inputs as $input_key => $input_value){

                if( substr($input_key, 0, 9) == 'ot_hours_' ){

                    $inputs['ot_hours_' . substr($input_key, 9, strlen($input_key))] = $input_value;
                    $rules['ot_hours_' . substr($input_key, 9, strlen($input_key))] = array('numeric');
                }

                if( substr($input_key, 0, 7) == 'remark_' ){

                    $inputs['remark_' . substr($input_key, 7, strlen($input_key))] = $input_value;
                    $rules['remark_' . substr($input_key, 7, strlen($input_key))] = array('max:100');
                }
            }

            $front_end_message = '';

            $validator = Validator::make($inputs, $rules);
            $validation_result = $validator->passes();
            if($validation_result == FALSE){

                $front_end_message = 'Please Check Your Inputs';
            }

            $process_result['validation_result'] = $validator->passes();
            $process_result['validation_messages'] =  $validator->errors();
            $process_result['front_end_message'] = $front_end_message;
            $process_result['back_end_message'] =  'Employee Salary Controller - Validation Process ';

            return $process_result;

        // }catch(\Exception $e){

        //     $process_result['validation_result'] = FALSE;
        //     $process_result['validation_messages'] = new MessageBag();
        //     $process_result['front_end_message'] =  $e->getMessage();
        //     $process_result['back_end_message'] =  'Employee Salary Controller - Validation Function Fault';

		// 	return $process_result;
        // }
    }

    private function saveEmployeeAttendanceOvertime($request){

        //try{

            $objEmployeeAttendanceOverTime = new EmployeeAttendanceOverTime();

            $eao['eao'] = $this->getEmployeeAttendanceOverTimeArray($request);
            $saving_process_result = $objEmployeeAttendanceOverTime->saveEmployeeAttendanceOverTime($eao);

            return $saving_process_result;

        // }catch(\Exception $e){

        //     $process_result['site_id'] = $request->site_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'Site Controller -> Site Saving Process <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

    private function getEmployeeAttendanceOverTimeArray($request){

        $request_inputs = $request->all();

        $elqEmployee = Employee::where('active', 1)->get();
        foreach($elqEmployee as $key => $row ){

            $employee_id = $row->employee_id;

            $eao[$key+1]['eao_date'] = $request->attendance_date;
            $eao[$key+1]['employee_id'] = $employee_id;

            if( isset($request_inputs['attendance_' . $employee_id]) ){

                $eao[$key+1]['attendance'] = 1;
            }else{

                $eao[$key+1]['attendance'] = 0;
            }

            $eao[$key+1]['ot_hours'] = $request_inputs['ot_hours_' . $employee_id];
            $eao[$key+1]['remark'] = $request_inputs['remark_' . $employee_id];

            $eao[$key+1]['saved_by'] = Auth::id();
            $eao[$key+1]['saved_on'] = Carbon::now()->format('Y-m-d H:i:s');;
            $eao[$key+1]['updated_by'] = NULL;
            $eao[$key+1]['updated_on'] = NULL;
        }

        return $eao;
    }


}
