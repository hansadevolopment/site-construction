<?php

namespace App\Http\Controllers\SiteMM\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SiteMM\Master\Employee;
use App\Models\SiteMM\Master\LabourCategory;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Carbon;

use App\Rules\ZeroValidation;

class EmployeeController extends Controller {

    public function loadView(){

        $data['labour_category'] = LabourCategory::where('active', 1)->get();
        $data['attributes'] = $this->getEmployeeAttributes(NULL, NULL);

        return view('SiteMM.Master.employee')->with('Emp', $data);
    }

    private function getEmployeeAttributes($process, $request){

        $attributes['employee_id'] = '#Auto#';
        $attributes['employee_code'] = '';
        $attributes['employee_name'] = '';
        $attributes['lc_id'] = '0';
        $attributes['active'] = 1;

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        if( ($process['validation_result'] == TRUE) && ($process['process_status'] == TRUE)){

            $elqEmployee = Employee::where('employee_id', $process['employee_id'])->first();
            if($elqEmployee->count() >= 1) {

                $attributes['employee_id'] = $elqEmployee->employee_id;
                $attributes['employee_code'] = $elqEmployee->employee_code;
                $attributes['employee_name'] = $elqEmployee->employee_name;
                $attributes['lc_id'] = $elqEmployee->lc_id;
                $attributes['active'] = $elqEmployee->active;
            }

            $attributes['validation_messages'] = $process['validation_messages'];

            if( $process['back_end_message'] == '' ){

                $message = '';
                $attributes['process_message'] = '';

            }else{

                $message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
                $attributes['process_message'] = '<div class="alert alert-success" role="alert"> '. $message .' </div> ';
            }

        }else{

            $inputs = $request->input();
            if(is_null($inputs) == FALSE){

                $attributes['employee_id'] = $inputs['employee_id'];
                $attributes['employee_code'] = $inputs['employee_code'];
                $attributes['employee_name'] = $inputs['employee_name'];
                $attributes['lc_id'] = $inputs['lc_id'];
                $attributes['active'] = $inputs['active'];
            }

            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';
        }

        return $attributes;
    }

    public function processEmployee(Request $request){

        $employee_validation_result = $this->validateEmployee($request);

        if($employee_validation_result['validation_result'] == TRUE){

			$saving_process_result = $this->saveEmployee($request);

			$saving_process_result['validation_result'] = $employee_validation_result['validation_result'];
			$saving_process_result['validation_messages'] = $employee_validation_result['validation_messages'];

            $data['attributes'] = $this->getEmployeeAttributes($saving_process_result, $request);

		}else{

			$employee_validation_result['employee_id'] = $request->employee_id;
			$employee_validation_result['process_status'] = FALSE;

            $data['attributes'] = $this->getEmployeeAttributes($employee_validation_result, $request);
		}

        $data['labour_category'] = LabourCategory::where('active', 1)->get();

        return view('SiteMM.Master.employee')->with('Emp', $data);
    }

    private function validateEmployee($request){

        //try{

            $inputs['employee_id'] = $request->employee_id;
            $inputs['employee_code'] = $request->employee_code;
            $inputs['employee_name'] = $request->employee_name;
            $inputs['lc_id'] = $request->lc_id;

            $rules['employee_id'] = array('required');
            $rules['employee_code'] = array('required', 'max:10');
            $rules['employee_name'] = array('required', 'max:100');
            $rules['lc_id'] =  array( new ZeroValidation('Labour Category', $request->lc_id));

            $front_end_message = '';

            $validator = Validator::make($inputs, $rules);
            $validation_result = $validator->passes();
            if($validation_result == FALSE){

                $front_end_message = 'Please Check Your Inputs';
            }

            $process_result['validation_result'] = $validator->passes();
            $process_result['validation_messages'] =  $validator->errors();
            $process_result['front_end_message'] = $front_end_message;
            $process_result['back_end_message'] =  'Site Controller - Validation Process ';

            return $process_result;

        // }catch(\Exception $e){

        //     $process_result['validation_result'] = FALSE;
        //     $process_result['validation_messages'] = new MessageBag();
        //     $process_result['front_end_message'] =  $e->getMessage();
        //     $process_result['back_end_message'] =  'Site Controller - Validation Function Fault';

		// 	return $process_result;
        // }
    }

    private function saveEmployee($request){

        //try{

            $objEmployee = new Employee();

            $employee['employee'] = $this->getEmployeeArray($request);
            $saving_process_result = $objEmployee->saveEmployee($employee);

            return $saving_process_result;

        // }catch(\Exception $e){

        //     $process_result['employee_id'] = $request->employee_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'item Controller -> item Saving Process <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

    private function getEmployeeArray($request){

        $employee['employee_id'] = $request->employee_id;
        $employee['employee_code'] = $request->employee_code;
        $employee['employee_name'] = $request->employee_name;
        $employee['lc_id'] = $request->lc_id;
        $employee['active'] = $request->active;

        if( Employee::where('employee_id', $request->employee_id)->exists() ){

            $employee['updated_by'] = Auth::id();
            $employee['updated_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }else{

            $employee['saved_by'] = Auth::id();
            $employee['saved_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        return $employee;
    }

    public function openEmployee(Request $request){

        $process_result['employee_id'] = $request->employee_id;
        $process_result['process_status'] = TRUE;
        $process_result['validation_result'] = TRUE;
        $process_result['validation_messages'] =  new MessageBag();
        $process_result['front_end_message'] = '';
        $process_result['back_end_message'] = '';

        $data['attributes'] = $this->getEmployeeAttributes($process_result, $request);
        $data['labour_category'] = LabourCategory::where('active', 1)->get();

        return view('SiteMM.Master.employee')->with('Emp', $data);
    }


}
