<?php

namespace App\Http\Controllers\SiteMM\SiteOperation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SiteMM\Master\Employee;
use App\Models\SiteMM\Master\Site;

use App\Models\SiteMM\SiteForcast\SiteTask;
use App\Models\SiteMM\SiteForcast\SiteSubTask;

use App\Http\Controllers\SiteMM\SiteOperation\EmployeeSalaryController;

use App\Models\SiteMM\SiteOperation\EmployeeSalary;
use App\Models\SiteMM\SiteOperation\EmployeeSalaryDetail;

use App\Traits\Validations\InputValidateTrait;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Carbon;

use \stdClass;

use App\Rules\ZeroValidation;
use App\Rules\CurrencyValidation;
use App\Rules\SiteMM\SiteOperation\EmployeeSalaryIsUpdateValidation;

class EmployeeSalaryTwoController extends Controller {

    use InputValidateTrait;

    public function loadView(){

        $objEmployeeSalaryController =  new EmployeeSalaryController();
        $objEmployeeAdvanceController = new EmployeeAdvanceController();
        //$advance_result = $objEmployeeAdvanceController->getEmployeeSalaryAdvanceRecordsForBasicSalary($request->employee_id);

        $data['site'] = Site::where('active', 1)->get();
        $data['site_task'] = array();
        $data['site_sub_task'] = array();
        $data['employee_advance'] = array();
        $data['salary_category'] = $objEmployeeSalaryController->getSalaryCategory();
        $data['employee'] = Employee::where('active', 1)->get();
        $data['attributes'] = $this->getEmployeeSalaryAttributes(NULL, NULL);

        return view('SiteMM.SiteOperation.employee_salary_two')->with('ES', $data);
    }

    private function getEmployeeSalaryAttributes($process, $request){

        $attributes['es_id'] = '#Auto#';
        $attributes['es_date'] = Carbon::today()->toDateString();
        $attributes['sc_id'] = '0';
        $attributes['employee_id'] = '0';
        $attributes['site_id'] = '0';
        $attributes['task_id'] = '0';
        $attributes['sub_task_id'] = '0';
        $attributes['pay_amount'] = '0';
        $attributes['advance_amount'] = '0';
        $attributes['remark'] = '';

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        if( ($process['validation_result'] == TRUE) && ($process['process_status'] == TRUE)){

            $elqES = EmployeeSalary::where('es_id', $process['es_id'] )->first();
            if(is_null($elqES) == FALSE){

                $attributes['es_id'] = $elqES->es_id;
                $attributes['es_date'] = $elqES->es_date;
                $attributes['sc_id'] = $request->sc_id;
                $attributes['employee_id'] = $elqES->employee_id;
                $elqESDetail = $elqES->getEmployeeSalaryDetail;
                $attributes['site_id'] = $elqESDetail[0]->site_id;
                $attributes['task_id'] = $elqESDetail[0]->task_id;
                $attributes['sub_task_id'] = $elqESDetail[0]->sub_task_id;
                $attributes['pay_amount'] = number_format($elqES->net_salary, 2);
                $attributes['advance_amount'] = number_format($elqES->net_salary, 2);
                $attributes['remark'] = $elqES->remark;
            }
            $attributes['validation_messages'] = $process['validation_messages'];

            if($request->submit == 'Display'){

                $attributes['validation_messages'] = new MessageBag();
                $attributes['process_message'] = "";
            }else{

                $message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
                $attributes['process_message'] = '<div class="alert alert-success" role="alert"> '. $message .' </div> ';
            }

        }else{

            $inputs = $request->input();
            if(is_null($inputs) == FALSE){

                $attributes['es_id'] = $inputs['es_id'];
                $attributes['es_date'] = Carbon::today()->toDateString();
                $attributes['sc_id'] = $inputs['sc_id'];
                $attributes['employee_id'] = $inputs['employee_id'];
                $attributes['site_id'] = $inputs['site_id'];
                $attributes['task_id'] = $inputs['task_id'];
                $attributes['sub_task_id'] = $inputs['sub_task_id'];
                $attributes['pay_amount'] = $inputs['pay_amount'];
                $attributes['advance_amount'] = 0;
                $attributes['remark'] =$inputs['remark'];
            }

            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';
        }

        return $attributes;

    }

    public function processEmployeeSalary(Request $request){

        if( $request->submit == 'Reset' ){

            $data['attributes'] = $this->getEmployeeSalaryAttributes(NULL, NULL);
        }

        if( $request->submit == 'Save' ){

            $ea_validation_result = $this->validatePaymentVoucher($request);
            if($ea_validation_result['validation_result'] == TRUE){

                $saving_process_result = $this->saveSalaryPayment($request);
                $saving_process_result['validation_result'] = $ea_validation_result['validation_result'];
                $saving_process_result['validation_messages'] = $ea_validation_result['validation_messages'];
                $data['attributes'] = $this->getEmployeeSalaryAttributes($saving_process_result, $request);

            }else{

                $ea_validation_result['process_status'] = FALSE;
                $data['attributes'] = $this->getEmployeeSalaryAttributes($ea_validation_result, $request);
            }

        }

        $objEmployeeSalaryController =  new EmployeeSalaryController();

        $data['employee_advance'] = array();
        $data['site'] = Site::where('active', 1)->get();
        $data['site_task'] = SiteTask::where('active', 1)->where('site_id', $request->site_id)->get();
        $data['site_sub_task'] = SiteSubTask::where('active', 1)->where('task_id', $request->task_id)->get();
        $data['salary_category'] = $objEmployeeSalaryController->getSalaryCategory();
        $data['employee'] = Employee::where('active', 1)->get();

        return view('SiteMM.SiteOperation.employee_salary_two')->with('ES', $data);
    }

    private function validatePaymentVoucher($request){

        //try{

            $inputs['es_id'] = $request->es_id;
            $inputs['es_date'] = $request->es_date;
            $inputs['employee_id'] = $request->employee_id;
            $inputs['sc_id'] = $request->sc_id;
            $inputs['site_id'] = $request->site_id;
            $inputs['task_id'] = $request->task_id;
            $inputs['sub_task_id'] = $request->sub_task_id;
            $inputs['pay_amount'] = floatval(str_replace(',', '',$request->pay_amount));
            $inputs['remark'] = $request->remark;

            $rules['es_id'] = array('required', new EmployeeSalaryIsUpdateValidation());
            $rules['es_date'] = array('required', 'date');
            $rules['employee_id'] = array( new ZeroValidation('Employee', $request->employee_id));
            $rules['sc_id'] = array( new ZeroValidation('Salary Category', $request->sc_id));
            $rules['site_id'] = array( new ZeroValidation('Site', $request->site_id));
            $rules['task_id'] = array( new ZeroValidation('Task', $request->task_id));
            $rules['sub_task_id'] = array( new ZeroValidation('Sub Task', $request->sub_task_id));
            $rules['pay_amount'] = array('required', 'numeric', new CurrencyValidation(1));
            $rules['remark'] = array('max:100');

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

    private function saveSalaryPayment($request){

        //try{

            $objEmployeeSalary = new EmployeeSalary();

            $employee_salary['employee_salary'] = $this->getEmployeeSalaryArray($request);
            $employee_salary['employee_salary_detail'] = $this->getEmployeeSalaryDetailArray($request);
            $employee_salary['employee_advance'] = $this->getEmployeeAdvanceSettlementRecords($request);
            $employee_salary['employee_salary_advance_settlement_detail'] = $this->getEmployeeAdvanceSettlementDetail($request);
            $saving_process_result = $objEmployeeSalary->saveEmployeeSalary($employee_salary);

            return $saving_process_result;

        // }catch(\Exception $e){

        //     $process_result['site_id'] = $request->site_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'Site Controller -> Site Saving Process <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

    private function getEmployeeSalaryArray($request){

        $employee_salary['es_id'] = $request->es_id;
        $employee_salary['es_date'] = $request->es_date;
        $employee_salary['sc_id'] = $request->sc_id;
        $employee_salary['employee_id'] = $request->employee_id;
        $employee_salary['in_date_time'] = $request->es_date;
        $employee_salary['out_date_time'] = $request->es_date;
        $employee_salary['site_count'] = 1;
        $employee_salary['working_hours'] = 0;
        $employee_salary['working_rate'] = 0;
        $employee_salary['working_amount'] = floatval(str_replace(',', '',$request->pay_amount));
        $employee_salary['ot_hours'] = 0;
        $employee_salary['ot_rate'] = 0;
        $employee_salary['ot_amount'] = 0;
        $employee_salary['gross_amount'] = floatval(str_replace(',', '',$request->pay_amount));
        $employee_salary['advance_amount'] = 0;
        $employee_salary['net_salary'] = floatval(str_replace(',', '',$request->pay_amount));
        $employee_salary['remark'] = $request->remark;
        $employee_salary['cancel'] = 0;

        if( $request->pay_amount ==  0){

            $employee_salary['cancel'] = 1;
            $employee_salary['cancel_by'] = Auth::id();
            $employee_salary['cancel_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        if( $request->es_id == '#Auto#' ){

            $employee_salary['saved_by'] = Auth::id();
            $employee_salary['saved_on'] = Carbon::now()->format('Y-m-d H:i:s');

        }else{

            $employee_salary['updated_by'] = Auth::id();
            $employee_salary['updated_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        return $employee_salary;
    }

    private function getEmployeeSalaryDetailArray($request){

        for ($x = 1; $x <= 1; $x++){

            $employee_salary_detail[$x]['es_id'] = $request->es_id;
            $employee_salary_detail[$x]['ono'] = $x;
            $employee_salary_detail[$x]['site_id'] = $request->site_id;
            $employee_salary_detail[$x]['task_id'] = $request->task_id;
            $employee_salary_detail[$x]['sub_task_id'] = $request->sub_task_id;

            $employee_salary_detail[$x]['working_hours'] = 0;
            $employee_salary_detail[$x]['working_rate'] = 0;
            $employee_salary_detail[$x]['working_amount'] = floatval(str_replace(',', '',$request->pay_amount));

            $employee_salary_detail[$x]['ot_hours'] = 0;
            $employee_salary_detail[$x]['ot_rate'] = 0;
            $employee_salary_detail[$x]['ot_amount'] = 0;

            $employee_salary_detail[$x]['total_hours'] = 0;
            $employee_salary_detail[$x]['total_amount'] = floatval(str_replace(',', '',$request->pay_amount));
        }

        return $employee_salary_detail;
    }


    private function getEmployeeAdvanceSettlementRecords($request){

        $employee_advance = null;

        $request->not_json = TRUE;

        $objEmployeeAdvanceController = new EmployeeAdvanceController();
        $advance_result = $objEmployeeAdvanceController->getEmployeeAdvance($request);
        $total_advance_amount = $advance_result->sum('advance_amount');
        $pay_amount = floatval(str_replace(',', '',$request->pay_amount));
        $reduce_amount = $pay_amount;

        foreach($advance_result as $key => $value ){

            $employee_advance[($key+1)]['ea_id'] = $value->ea_id;

            if( ($total_advance_amount < $pay_amount) || ($total_advance_amount == $pay_amount) ){

                $employee_advance[($key+1)]['advance_balance'] = 0;
                $employee_advance[($key+1)]['settle'] = 1;
                $employee_advance[($key+1)]['settle_by'] = Auth::id();
                $employee_advance[($key+1)]['settle_on'] = Carbon::now()->format('Y-m-d H:i:s');
            }

            if( $total_advance_amount > $pay_amount ){

                if( $value->advance_balance < $reduce_amount ){

                    $employee_advance[($key+1)]['advance_balance'] = 0;
                    $reduce_amount = $reduce_amount - $value->advance_balance;
                    $employee_advance[($key+1)]['settle'] = 1;
                    $employee_advance[($key+1)]['settle_by'] = Auth::id();
                    $employee_advance[($key+1)]['settle_on'] = Carbon::now()->format('Y-m-d H:i:s');

                }elseif( $value->advance_balance == $reduce_amount ){

                    $employee_advance[($key+1)]['advance_balance'] = 0;
                    $reduce_amount = $reduce_amount - $value->advance_balance;
                    $employee_advance[($key+1)]['settle'] = 1;
                    $employee_advance[($key+1)]['settle_by'] = Auth::id();
                    $employee_advance[($key+1)]['settle_on'] = Carbon::now()->format('Y-m-d H:i:s');

                }elseif( $value->advance_balance > $reduce_amount ){

                    $employee_advance[($key+1)]['advance_balance'] = $value->advance_balance - $reduce_amount;
                    $reduce_amount = 0;
                }
            }
        }

        return $employee_advance;
    }


    private function getEmployeeAdvanceSettlementDetail($request){

        $request->not_json = TRUE;
        $employee_salary_advance_detail = array();

        $objEmployeeAdvanceController = new EmployeeAdvanceController();
        $advance_result = $objEmployeeAdvanceController->getEmployeeAdvance($request);
        $total_advance_amount = $advance_result->sum('advance_amount');
        $pay_amount = floatval(str_replace(',', '',$request->pay_amount));
        $reduce_amount = $pay_amount;

        foreach($advance_result as $key => $value ){

            if( ($total_advance_amount < $pay_amount) || ($total_advance_amount == $pay_amount) ){

                $employee_salary_advance_detail[($key+1)]['oid'] = ($key+1);
                $employee_salary_advance_detail[($key+1)]['ea_id'] = $value->ea_id;
                $employee_salary_advance_detail[($key+1)]['settle_amount'] = $value->advance_amount;
            }

            if( $total_advance_amount > $pay_amount ){

                if( $value->advance_balance < $reduce_amount ){

                    $reduce_amount = $reduce_amount - $value->advance_balance;

                    $employee_salary_advance_detail[($key+1)]['oid'] = ($key+1);
                    $employee_salary_advance_detail[($key+1)]['ea_id'] = $value->ea_id;
                    $employee_salary_advance_detail[($key+1)]['settle_amount'] = $value->advance_amount;

                }elseif( $value->advance_balance == $reduce_amount ){

                    $reduce_amount = $reduce_amount - $value->advance_balance;

                    $employee_salary_advance_detail[($key+1)]['oid'] = ($key+1);
                    $employee_salary_advance_detail[($key+1)]['ea_id'] = $value->ea_id;
                    $employee_salary_advance_detail[($key+1)]['settle_amount'] = $value->advance_amount;

                }elseif( $value->advance_balance > $reduce_amount ){

                    $employee_salary_advance_detail[($key+1)]['oid'] = ($key+1);
                    $employee_salary_advance_detail[($key+1)]['ea_id'] = $value->ea_id;
                    $employee_salary_advance_detail[($key+1)]['settle_amount'] = $reduce_amount;

                    $reduce_amount = 0;
                }
            }
        }

        return $employee_salary_advance_detail;
    }

}
