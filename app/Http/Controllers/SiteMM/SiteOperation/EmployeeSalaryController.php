<?php

namespace App\Http\Controllers\SiteMM\SiteOperation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SiteMM\Master\Employee;
use App\Models\SiteMM\Master\Site;

use App\Models\SiteMM\SiteForcast\SiteTask;
use App\Models\SiteMM\SiteForcast\SiteSubTask;
use App\Models\SiteMM\SiteOperation\EmployeeSalary;
use App\Models\SiteMM\SiteOperation\EmployeeSalaryDetail;

use App\Http\Controllers\SiteMM\SiteOperation\EmployeeAdvanceController;
use App\Rules\CurrencyValidation;
use App\Traits\Validations\InputValidateTrait;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Carbon;
use Carbon\CarbonPeriod;

use stdClass;

use App\Rules\ZeroValidation;
use App\Rules\QuantityValidation;
use App\Rules\SiteMM\SiteOperation\SiteWorkingHoursValidation;
use App\Rules\SiteMM\SiteOperation\EmployeeSalaryCancalValidation;
use App\Rules\SiteMM\SiteOperation\EmployeeSalaryIsUpdateValidation;

class EmployeeSalaryController extends Controller {

    use InputValidateTrait;

    public function loadView(){

        $data['salary_category'] = $this->getSalaryCategory();
        $data['site_count'] = $this->getSiteCount();
        $data['employee'] = Employee::where('active', 1)->get();
        $data['employee_advance'] = array();
        $data['attributes'] = $this->getEmployeeSalaryAttributes(NULL, NULL);

        return view('SiteMM.SiteOperation.employee_salary')->with('ES', $data);
    }

    private function getEmployeeSalaryAttributes($process, $request){

        $attributes['es_id'] = '#Auto#';
        $attributes['es_date'] = '';
        $attributes['sc_id'] = 0;
        $attributes['site_count'] = 0;
        $attributes['in_date_time'] = Carbon::today()->toDateString();
        $attributes['out_date_time'] = Carbon::today()->toDateString();
        $attributes['employee_id'] = '0';
        $attributes['working_hours'] = 0;
        $attributes['working_rate'] = 0;
        $attributes['working_amount'] = 0;
        $attributes['overtime_hours'] = 0;
        $attributes['overtime_rate'] = 0;
        $attributes['overtime_amount'] = 0;
        $attributes['total_hours'] = 0;
        $attributes['gross_amount'] = 0;
        $attributes['advance_amount'] = 0;
        $attributes['net_amount'] = 0;
        $attributes['remark'] = '';

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        $inputs = $request->input();
        if(is_null($inputs) == FALSE){

            $attributes['es_id'] = $inputs['es_id'];
            $attributes['es_date'] = $inputs['es_date'];
            $attributes['sc_id'] = $inputs['sc_id'];
            $attributes['site_count'] = $inputs['site_count'];
            $attributes['in_date_time'] = $inputs['in_date_time'];
            $attributes['out_date_time'] = $inputs['out_date_time'];
            $attributes['employee_id'] = $inputs['employee_id'];

            $attributes['working_hours'] = $inputs['working_hours'];
            $attributes['working_rate'] = $inputs['working_rate'];
            $attributes['working_amount'] = $inputs['working_amount'];
            $attributes['overtime_hours'] = $inputs['overtime_hours'];
            $attributes['overtime_rate'] = $inputs['overtime_rate'];
            $attributes['overtime_amount'] = $inputs['overtime_amount'];
            $attributes['total_hours'] = $inputs['total_hours'];
            $attributes['gross_amount'] = $inputs['gross_amount'];
            $attributes['advance_amount'] = $inputs['advance_amount'];
            $attributes['net_amount'] = $inputs['net_amount'];
            $attributes['remark'] = $inputs['remark'];

            if( $request->submit == 'Save' ){

                for ($x = 1; $x <= $attributes['site_count']; $x++){

                    $attributes['site_id_'.$x] = $inputs['site_id_'.$x];
                    $attributes['task_id_'.$x] = $inputs['task_id_'.$x];
                    $attributes['sub_task_id_'.$x] = $inputs['sub_task_id_'.$x];

                    $attributes['site_working_hours_'.$x] = $inputs['site_working_hours_'.$x];
                    $attributes['site_working_amount_'.$x] = $inputs['site_working_amount_'.$x];
                    $attributes['site_ot_hours_'.$x] = $inputs['site_ot_hours_'.$x];
                    $attributes['site_ot_amount_'.$x] = $inputs['site_ot_amount_'.$x];
                    $attributes['site_total_hours_'.$x] = $inputs['site_total_hours_'.$x];
                    $attributes['site_total_amount_'.$x] = $inputs['site_total_amount_'.$x];
                }

            }else{

                for ($x = 1; $x <= $attributes['site_count']; $x++){

                    $attributes['site_id_'.$x] = 0;
                    $attributes['task_id_'.$x] = 0;
                    $attributes['sub_task_id_'.$x] = 0;

                    $attributes['site_working_hours_'.$x] = 0;
                    $attributes['site_working_amount_'.$x] = 0;
                    $attributes['site_ot_hours_'.$x] = 0;
                    $attributes['site_ot_amount_'.$x] = 0;
                    $attributes['site_total_hours_'.$x] = 0;
                    $attributes['site_total_amount_'.$x] = 0;
                }
            }
        }

        $salary_information = $this->getSalaryCalculation($request);
        if(is_null($inputs) == FALSE){

            $attributes['working_hours'] = $salary_information['working_hours'];
            $attributes['working_rate'] = $salary_information['working_rate'];
            $attributes['working_amount'] = $salary_information['working_amount'];
            $attributes['overtime_hours'] = $salary_information['overtime_hours'];
            $attributes['overtime_rate'] = $salary_information['overtime_rate'];
            $attributes['overtime_amount'] = $salary_information['overtime_amount'];
            $attributes['total_hours'] = $salary_information['total_hours'];
            $attributes['gross_amount'] = $salary_information['gross_amount'];
            $attributes['advance_amount'] = $salary_information['advance_amount'];
            $attributes['net_amount'] = $salary_information['net_amount'];
        }

        if( ($process['validation_result'] == TRUE) && ($process['process_status'] == TRUE)){

            if($request->submit == 'Calculate'){

                $attributes['validation_messages'] = new MessageBag();
                $attributes['process_message'] = "";

            }else{


                $elqES = EmployeeSalary::where('es_id', $process['es_id'])->first();
                if ( is_null($elqES) == FALSE ){

                    $attributes['es_id'] = $elqES->es_id;
                    $attributes['es_date'] = $elqES->es_date;

                    for ($x = 1; $x <= $elqES->site_count; $x++){

                        $elqESD = EmployeeSalaryDetail::where('es_id', $process['es_id'])->where('ono', $x)->first();
                        if ( is_null($elqESD) == FALSE ){

                            $attributes['site_working_hours_'.$x] = $elqESD->working_hours;
                            $attributes['site_working_amount_'.$x] = $elqESD->working_amount;
                            $attributes['site_ot_hours_'.$x] = $elqESD->ot_hours;
                            $attributes['site_ot_amount_'.$x] = $elqESD->ot_amount;
                            $attributes['site_total_hours_'.$x] = $elqESD->total_hours;
                            $attributes['site_total_amount_'.$x] = $elqESD->total_amount;
                        }
                    }
                }

                if( $request->in_date_time ==  $request->out_date_time){

                    $attributes['validation_messages'] = $process['validation_messages'];
                    $process['front_end_message'] = 'Cancel Process is Completed successfully.';

                    $message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
                    $attributes['process_message'] = '<div class="alert alert-success" role="alert"> '. $message .' </div> ';

                }else{

                    $attributes['validation_messages'] = $process['validation_messages'];

                    $message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
                    $attributes['process_message'] = '<div class="alert alert-success" role="alert"> '. $message .' </div> ';
                }
            }

        }else{

            $attributes['validation_messages'] = $process['validation_messages'];

            $message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';
        }

        return $attributes;
    }

    public function processEmployeeSalary(Request $request){

        if($request->submit == 'Reset'){

            $data['attributes'] = $this->getEmployeeSalaryAttributes(NULL, NULL);
        }

        if($request->submit == 'Calculate'){

            $sp_validation_result = $this->validateSalaryCalculationProcess($request);
            if($sp_validation_result['validation_result'] == TRUE){

                $sp_validation_result['process_status'] = TRUE;
                $sp_validation_result['validation_result'] = $sp_validation_result['validation_result'];
                $sp_validation_result['validation_messages'] = $sp_validation_result['validation_messages'];

                $data['attributes'] = $this->getEmployeeSalaryAttributes($sp_validation_result, $request);

            }else{

                $sp_validation_result['process_status'] = FALSE;
                $data['attributes'] = $this->getEmployeeSalaryAttributes($sp_validation_result, $request);
            }
        }

        if($request->submit == 'Save'){

            $sp_validation_result = $this->validateSalaryCalculationProcess($request);
            if($sp_validation_result['validation_result'] == TRUE){

                $saving_process_result = $this->saveEmployeeSalary($request);
                $saving_process_result['validation_result'] = $sp_validation_result['validation_result'];
                $saving_process_result['validation_messages'] = $sp_validation_result['validation_messages'];

                $data['attributes'] = $this->getEmployeeSalaryAttributes($saving_process_result, $request);

            }else{

                $sp_validation_result['process_status'] = FALSE;
                $data['attributes'] = $this->getEmployeeSalaryAttributes($sp_validation_result, $request);
            }
        }

        $objEmployeeAdvanceController = new EmployeeAdvanceController();
        $advance_result = $objEmployeeAdvanceController->getEmployeeSalaryAdvanceRecordsForBasicSalary($request->employee_id);

        $data['site_count'] = $this->getSiteCount();
        $data['employee'] = Employee::where('active', 1)->get();
        $data['employee_advance'] = $advance_result;
        $data['salary_category'] = $this->getSalaryCategory();

        $inputs_item = $request->input();
        for ($x = 1; $x <= $request->site_count; $x++){

            if($request->submit == 'Calculate'){

                $data['site' . $x] = Site::where('active', 1)->get();
                $data['site_task' . $x] = array();
                $data['site_sub_task' .$x] = array();
            }

            if($request->submit == 'Save'){

                $data['site' . $x] = Site::where('active', 1)->get();
                $data['site_task' . $x] = SiteTask::where('active', 1)->where('site_id', $inputs_item['site_id_'. $x])->get();
                $data['site_sub_task' .$x] = SiteSubTask::where('active', 1)->where('task_id', $inputs_item['task_id_'. $x])->get();
            }
        }

        return view('SiteMM.SiteOperation.employee_salary')->with('ES', $data);
    }

    private function validateSalaryCalculationProcess($request){

        //try{

            $inputs['es_id'] = $request->es_id;
            $inputs['es_date'] = $request->es_date;
            $inputs['sc_id'] = $request->sc_id;
            $inputs['employee_id'] = $request->employee_id;
            $inputs['site_count'] = $request->site_count;
            $inputs['in_date_time'] = $request->in_date_time;
            $inputs['out_date_time'] = $request->out_date_time;

            $total_site_working_hours = 0;
            $total_site_ot_hours = 0;
            if( $request->submit == 'Save' ){

                $inputs_item = $request->input();
                for ($x = 1; $x <= $request->site_count; $x++){

                    $inputs['site_id_'.$x] = $inputs_item['site_id_'. $x];
                    $inputs['task_id_'.$x] = $inputs_item['task_id_'. $x];
                    $inputs['sub_task_id_'.$x] = $inputs_item['sub_task_id_'. $x];
                    $inputs['site_working_hours_'.$x] = $inputs_item['site_working_hours_'. $x];
                    $inputs['site_ot_hours_'.$x] = $inputs_item['site_ot_hours_'. $x];

                    $total_site_working_hours += $inputs_item['site_working_hours_'. $x];
                    $total_site_ot_hours += $inputs_item['site_ot_hours_'. $x];
                }
            }

            $rules['es_id'] = array('required', new EmployeeSalaryCancalValidation(), new EmployeeSalaryIsUpdateValidation());
            $rules['es_date'] = array('required', 'date');
            $rules['sc_id'] = array( new ZeroValidation('Salary Category', $request->sc_id));
            $rules['employee_id'] = array( new ZeroValidation('Employee', $request->employee_id));
            $rules['site_count'] = array( new ZeroValidation('Site Count', $request->site_count));
            $rules['in_date_time'] = array('required', 'date');
            $rules['out_date_time'] = array('required', 'date');

            if( $request->submit == 'Save' ){

                $inputs_item = $request->input();
                for ($x = 1; $x <= $request->site_count; $x++){

                    $rules['site_id_'.$x] = array( new ZeroValidation('Site', $inputs_item['site_id_'. $x]));
                    $rules['task_id_'.$x] = array( new ZeroValidation('Task', $inputs_item['task_id_'. $x]));
                    $rules['sub_task_id_'.$x] = array( new ZeroValidation('Sub Task', $inputs_item['sub_task_id_'. $x]));
                    $rules['site_working_hours_'.$x] = array( new QuantityValidation('Working Hours', $inputs_item['site_working_hours_'. $x]), new SiteWorkingHoursValidation($request->working_hours,  $total_site_working_hours));
                    $rules['site_ot_hours_'.$x] = array( new CurrencyValidation(1), new SiteWorkingHoursValidation($request->overtime_hours, $total_site_ot_hours));
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

            //dd( $process_result );

            return $process_result;

        // }catch(\Exception $e){

        //     $process_result['validation_result'] = FALSE;
        //     $process_result['validation_messages'] = new MessageBag();
        //     $process_result['front_end_message'] =  $e->getMessage();
        //     $process_result['back_end_message'] =  'Employee Salary Controller - Validation Function Fault';

		// 	return $process_result;
        // }
    }

    private function getSalaryCalculation($request){

        $elqSalaryInformation = Employee::where('employee_id', $request->employee_id)->first();
        if( is_null($elqSalaryInformation) == FALSE ){

            $objEmployeeAdvanceController = new EmployeeAdvanceController();
            $advance_result = $objEmployeeAdvanceController->getEmployeeSalaryAdvanceRecordsForBasicSalary($request->employee_id);
            $advance_amount = $advance_result->sum('advance_amount');

            $salary = $elqSalaryInformation->getLaborCategory()->price;
            $working_rate = $elqSalaryInformation->working_rate;
            $ot_rate = $elqSalaryInformation->ot_rate;
            $day_salary = $elqSalaryInformation->day_salary;

            $in_tdate_time = Carbon::parse($request->in_date_time);
            $out_date_time = Carbon::parse($request->out_date_time);
            $total_hours = $out_date_time->diffInHours($in_tdate_time);

            $salary_information['total_hours'] = $total_hours;

            if( $total_hours > 8 ){

                $working_amount = 8 * $working_rate;
                $overtime_amount = ($total_hours - 8) * $ot_rate;
                $gross_amount = $working_amount + $overtime_amount;
                $net_amount = $gross_amount - $advance_amount;

                $salary_information['overtime_hours'] = $total_hours - 8;
                $salary_information['working_hours'] = 8;
                $salary_information['working_amount'] = number_format($working_amount, 2);
                $salary_information['overtime_amount'] = number_format($overtime_amount, 2);
                $salary_information['gross_amount'] = number_format($gross_amount, 2);
                $salary_information['net_amount'] = number_format($net_amount, 2);

            }elseif( $total_hours == 8){

                $gross_amount = $day_salary;
                $net_amount = $gross_amount - $advance_amount;

                $salary_information['overtime_hours'] = 0;
                $salary_information['working_hours'] = 8;
                $salary_information['working_amount'] = number_format($day_salary, 2);
                $salary_information['overtime_amount'] = number_format(0, 2);
                $salary_information['gross_amount'] = number_format($gross_amount, 2);
                $salary_information['net_amount'] = number_format($net_amount, 2);

            }elseif ( $total_hours < 8 ){

                $gross_amount = $total_hours * $working_rate;
                $net_amount = $gross_amount - $advance_amount;

                $salary_information['overtime_hours'] = 0;
                $salary_information['working_hours'] = $total_hours;
                $salary_information['working_amount'] =  number_format(($total_hours * $working_rate), 2);
                $salary_information['overtime_amount'] = number_format(0, 2);
                $salary_information['gross_amount'] = number_format($gross_amount, 2);
                $salary_information['net_amount'] = number_format($net_amount, 2);
            }

            $salary_information['working_rate'] = number_format($working_rate, 2);
            $salary_information['overtime_rate'] = number_format($ot_rate, 2);
            $salary_information['advance_amount'] = number_format($advance_amount, 2);

            return $salary_information;

        }else{

            $salary_information['total_hours'] = 0;
            $salary_information['overtime_hours'] = 0;
            $salary_information['working_hours'] = 0;
            $salary_information['working_amount'] = number_format(0, 2);
            $salary_information['overtime_amount'] = number_format(0, 2);
            $salary_information['gross_amount'] = number_format(0, 2);
            $salary_information['net_amount'] = number_format(0, 2);
            $salary_information['working_rate'] = number_format(0, 2);
            $salary_information['overtime_rate'] = number_format(0, 2);
            $salary_information['advance_amount'] = number_format(0, 2);

            return $salary_information;
        }

    }

    private function saveEmployeeSalary($request){

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
        $employee_salary['in_date_time'] = $request->in_date_time;
        $employee_salary['out_date_time'] = $request->out_date_time;
        $employee_salary['site_count'] = $request->site_count;
        $employee_salary['working_hours'] = $request->working_hours;
        $employee_salary['working_rate'] = floatval(str_replace(',', '',$request->working_rate));
        $employee_salary['working_amount'] = floatval(str_replace(',', '',$request->working_amount));
        $employee_salary['ot_hours'] = $request->overtime_hours;
        $employee_salary['ot_rate'] = floatval(str_replace(',', '',$request->overtime_rate));
        $employee_salary['ot_amount'] = floatval(str_replace(',', '',$request->overtime_amount));
        $employee_salary['gross_amount'] = floatval(str_replace(',', '',$request->gross_amount));
        $employee_salary['advance_amount'] = floatval(str_replace(',', '',$request->advance_amount));
        $employee_salary['net_salary'] = floatval(str_replace(',', '',$request->net_amount));
        $employee_salary['remark'] = $request->remark;
        $employee_salary['cancel'] = 0;

        if( $request->in_date_time ==  $request->out_date_time){

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

        $inputs_item = $request->input();
        for ($x = 1; $x <= $request->site_count; $x++){

            $employee_salary_detail[$x]['es_id'] = $request->es_id;
            $employee_salary_detail[$x]['ono'] = $x;
            $employee_salary_detail[$x]['site_id'] = $inputs_item['site_id_'. $x];
            $employee_salary_detail[$x]['task_id'] = $inputs_item['task_id_'. $x];
            $employee_salary_detail[$x]['sub_task_id'] = $inputs_item['sub_task_id_'. $x];

            $employee_salary_detail[$x]['working_hours'] = $inputs_item['site_working_hours_'. $x];
            $employee_salary_detail[$x]['working_rate'] = $request->working_rate;
            $employee_salary_detail[$x]['working_amount'] = $inputs_item['site_working_hours_'. $x] * $request->working_rate;

            $employee_salary_detail[$x]['ot_hours'] = $inputs_item['site_ot_hours_'. $x];
            $employee_salary_detail[$x]['ot_rate'] = $request->overtime_rate;
            $employee_salary_detail[$x]['ot_amount'] = $inputs_item['site_ot_hours_'. $x] * $request->overtime_rate;

            $employee_salary_detail[$x]['total_hours'] = $inputs_item['site_working_hours_'. $x] + $inputs_item['site_ot_hours_'. $x];
            $employee_salary_detail[$x]['total_amount'] = ($inputs_item['site_working_hours_'. $x] * $request->working_rate) + ($inputs_item['site_ot_hours_'. $x] * $request->overtime_rate);
        }

        return $employee_salary_detail;
    }

    private function getEmployeeAdvanceSettlementRecords($request){

        $employee_advance = null;

        $objEmployeeAdvanceController = new EmployeeAdvanceController();
        $advance_result = $objEmployeeAdvanceController->getEmployeeSalaryAdvanceRecordsForBasicSalary($request->employee_id);
        $total_advance_amount = $advance_result->sum('advance_amount');
        $gross_amount = floatval(str_replace(',', '',$request->gross_amount));
        $reduce_amount = $gross_amount;

        foreach($advance_result as $key => $value ){

            $employee_advance[($key+1)]['ea_id'] = $value->ea_id;

            if( ($total_advance_amount < $gross_amount) || ($total_advance_amount == $gross_amount) ){

                $employee_advance[($key+1)]['advance_balance'] = 0;
                $employee_advance[($key+1)]['settle'] = 1;
                $employee_advance[($key+1)]['settle_by'] = Auth::id();
                $employee_advance[($key+1)]['settle_on'] = Carbon::now()->format('Y-m-d H:i:s');
            }

            if( $total_advance_amount > $gross_amount ){

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
        $advance_result = $objEmployeeAdvanceController->getEmployeeSalaryAdvanceRecordsForBasicSalary($request->employee_id);
        $total_advance_amount = $advance_result->sum('advance_amount');
        $gross_amount = floatval(str_replace(',', '',$request->gross_amount));
        $reduce_amount = $gross_amount;

        foreach($advance_result as $key => $value ){

            if( ($total_advance_amount < $gross_amount) || ($total_advance_amount == $gross_amount) ){

                $employee_salary_advance_detail[($key+1)]['oid'] = ($key+1);
                $employee_salary_advance_detail[($key+1)]['ea_id'] = $value->ea_id;
                $employee_salary_advance_detail[($key+1)]['settle_amount'] = $value->advance_amount;
            }

            if( $total_advance_amount > $gross_amount ){

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

    public function openEmployeeSalary(Request $request){

        $elqEmployeeSalary = EmployeeSalary::where('es_id', $request->open_es_id)->first();
        if( $elqEmployeeSalary->count() >= 1){

            $in_date = Carbon::parse($elqEmployeeSalary->in_date_time)->format('Y-m-d');
            $in_time = Carbon::parse($elqEmployeeSalary->in_date_time)->format('H:i');
            $inDateTime = $in_date . 'T' . $in_time;

            $out_date = Carbon::parse($elqEmployeeSalary->out_date_time)->format('Y-m-d');
            $out_time = Carbon::parse($elqEmployeeSalary->out_date_time)->format('H:i');
            $outDateTime = $out_date . 'T' . $out_time;

            if( $elqEmployeeSalary->sc_id == 1 ){

                $attributes['es_id'] = $elqEmployeeSalary->es_id;
                $attributes['es_date'] = $elqEmployeeSalary->es_date;
                $attributes['sc_id'] = $elqEmployeeSalary->sc_id;
                $attributes['site_count'] = $elqEmployeeSalary->site_count;
                $attributes['in_date_time'] = $inDateTime;
                $attributes['out_date_time'] = $outDateTime;
                $attributes['employee_id'] = $elqEmployeeSalary->employee_id;
                $attributes['working_hours'] = $elqEmployeeSalary->working_hours;
                $attributes['working_rate'] = number_format($elqEmployeeSalary->working_rate, 2);
                $attributes['working_amount'] = number_format($elqEmployeeSalary->working_amount, 2);
                $attributes['overtime_hours'] = $elqEmployeeSalary->ot_hours;
                $attributes['overtime_rate'] = number_format($elqEmployeeSalary->ot_rate, 2);
                $attributes['overtime_amount'] = number_format($elqEmployeeSalary->ot_amount, 2);
                $attributes['total_hours'] = $elqEmployeeSalary->working_hours + $elqEmployeeSalary->ot_hours;
                $attributes['gross_amount'] = number_format($elqEmployeeSalary->gross_amount, 2);
                $attributes['advance_amount'] = number_format($elqEmployeeSalary->advance_amount, 2);
                $attributes['net_amount'] = number_format($elqEmployeeSalary->net_salary, 2);
                $attributes['remark'] = $elqEmployeeSalary->remark;
                $attributes['validation_messages'] = new MessageBag();
                $attributes['process_message'] = "";

                $objEmployeeAdvanceController = new EmployeeAdvanceController();
                $advance_result = $objEmployeeAdvanceController->getEmployeeSalaryAdvanceRecordsForBasicSalary($request->employee_id);

                $elqEmployeeSalaryDetail = EmployeeSalaryDetail::where('es_id', $request->open_es_id)->get();
                foreach( $elqEmployeeSalaryDetail as $key => $value ){

                    $attributes['site_id_'. ($key+1)] = $value->site_id. ($key+1);
                    $attributes['task_id_'. ($key+1)] = $value->task_id . ($key+1);
                    $attributes['sub_task_id_'. ($key+1)] = $value->sub_task_id . ($key+1);

                    $attributes['site_working_hours_'. ($key+1)] = $value->working_hours;
                    $attributes['site_working_amount_'. ($key+1)] = number_format($value->working_amount, 2);
                    $attributes['site_ot_hours_'. ($key+1)] = $value->ot_hours;
                    $attributes['site_ot_amount_'. ($key+1)] = number_format($value->ot_amount, 2);
                    $attributes['site_total_hours_'. ($key+1)] = number_format($value->total_hours, 2);
                    $attributes['site_total_amount_'. ($key+1)] = number_format($value->total_amount, 2);

                    $data['site' . ($key+1)] = Site::where('active', 1)->get();
                    $data['site_task' . ($key+1)] = SiteTask::where('active', 1)->where('site_id', $value->site_id)->get();
                    $data['site_sub_task' .($key+1)] = SiteSubTask::where('active', 1)->where('task_id', $value->task_id)->get();
                }

                $data['attributes'] = $attributes;
                $data['site_count'] = $this->getSiteCount();
                $data['employee'] = Employee::where('active', 1)->get();
                $data['employee_advance'] = $advance_result;
                $data['salary_category'] = $this->getSalaryCategory();

                return view('SiteMM.SiteOperation.employee_salary')->with('ES', $data);

            }else{

                $attributes['es_id'] = $elqEmployeeSalary->es_id;
                $attributes['es_date'] = $elqEmployeeSalary->es_date;
                $attributes['sc_id'] = $elqEmployeeSalary->sc_id;
                $attributes['employee_id'] = $elqEmployeeSalary->employee_id;

                $site_id = 0;
                $task_id = 0;
                $sub_task_id = 0;

                $elqEmployeeSalaryDetail = EmployeeSalaryDetail::where('es_id', $request->open_es_id)->get();
                foreach($elqEmployeeSalaryDetail as $row => $value){

                    $site_id = $value->site_id;
                    $task_id = $value->task_id;
                    $sub_task_id = $value->sub_task_id;

                    $attributes['site_id'] = $value->site_id;
                    $attributes['task_id'] = $value->task_id;
                    $attributes['sub_task_id'] = $value->sub_task_id;
                    $attributes['pay_amount'] = $value->total_amount;
                    $attributes['advance_amount'] = $value->advance_amount;
                    $attributes['remark'] = $value->remark;
                }

                $attributes['validation_messages'] = new MessageBag();
                $attributes['process_message'] = "";
                $objEmployeeSalaryController =  new EmployeeSalaryController();

                $data['attributes'] = $attributes;
                $data['employee_advance'] = array();
                $data['site'] = Site::where('active', 1)->get();
                $data['site_task'] = SiteTask::where('active', 1)->where('site_id', $site_id)->get();
                $data['site_sub_task'] = SiteSubTask::where('active', 1)->where('task_id', $task_id)->get();
                $data['salary_category'] = $objEmployeeSalaryController->getSalaryCategory();
                $data['employee'] = Employee::where('active', 1)->get();

                return view('SiteMM.SiteOperation.employee_salary_two')->with('ES', $data);
            }
        }

    }

    public function getSalaryCategory(){

        $objOne = new stdClass();
        $objOne->id = 1;
        $objOne->category = 'Basic Salary';

        $objTwo = new stdClass();
        $objTwo->id = 2;
        $objTwo->category = 'Target';

        $objThree = new stdClass();
        $objThree->id = 3;
        $objThree->category = 'Sub Contract';

        $salary_category[1] = $objOne;
        $salary_category[2] = $objTwo;
        $salary_category[3] = $objThree;

        return $salary_category;
    }

    public function getSiteCount(){

        $objOne = new stdClass();
        $objOne->id = 1;
        $objOne->iteration = '1';

        $objTwo = new stdClass();
        $objTwo->id = 2;
        $objTwo->iteration = '2';

        $objThree = new stdClass();
        $objThree->id = 3;
        $objThree->iteration = '3';

        $objFour = new stdClass();
        $objFour->id = 4;
        $objFour->iteration = '4';

        $objFive = new stdClass();
        $objFive->id = 5;
        $objFive->iteration = '5';

        $objSix = new stdClass();
        $objSix->id = 6;
        $objSix->iteration = '6';

        $objSeven = new stdClass();
        $objSeven->id = 7;
        $objSeven->iteration = '7';

        $objEight = new stdClass();
        $objEight->id = 8;
        $objEight->iteration = '8';

        $objNine = new stdClass();
        $objNine->id = 9;
        $objNine->iteration = '9';

        $objTen = new stdClass();
        $objTen->id = 10;
        $objTen->iteration = '10';

        $salary_category[1] = $objOne;
        $salary_category[2] = $objTwo;
        $salary_category[3] = $objThree;
        $salary_category[4] = $objFour;
        $salary_category[5] = $objFive;
        $salary_category[6] = $objSix;
        $salary_category[7] = $objSeven;
        $salary_category[8] = $objEight;
        $salary_category[9] = $objNine;
        $salary_category[10] = $objTen;

        return $salary_category;
    }



}
