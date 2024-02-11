<?php

namespace App\Http\Controllers\SiteMM\SiteOperation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\SiteMM\Master\Employee;
use App\Models\SiteMM\Master\Site;

use App\Models\SiteMM\SiteForcast\SiteTask;
use App\Models\SiteMM\SiteForcast\SiteSubTask;

use App\Http\Controllers\SiteMM\SiteOperation\EmployeeSalaryController;
use App\Models\SiteMM\SiteOperation\EmployeeAdvance;
use App\Models\SiteMM\SiteOperation\PaymentVoucher;
use App\Models\SiteMM\SiteOperation\PaymentVoucherDetail;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Carbon;

use App\Rules\ZeroValidation;
use App\Rules\CurrencyValidation;
use App\Rules\SiteMM\SiteOperation\EmployeeAdvanceIsCancalValidation;

class EmployeeAdvanceController extends Controller {

    public function loadView(){

        $objEmployeeSalaryController =  new EmployeeSalaryController();

        $data['site'] = Site::where('active', 1)->get();
        $data['site_task'] = array();
        $data['site_sub_task'] = array();
        $data['salary_category'] = $objEmployeeSalaryController->getSalaryCategory();
        $data['employee'] = Employee::where('active', 1)->get();
        $data['attributes'] = $this->getEmployeeAdvanceAttributes(NULL, NULL);

        return view('SiteMM.SiteOperation.employee_advance')->with('EA', $data);
    }

    private function getEmployeeAdvanceAttributes($process, $request){

        $attributes['ea_id'] = '#Auto#';
        $attributes['ea_date'] = Carbon::today()->toDateString();
        $attributes['sc_id'] = '0';
        $attributes['employee_id'] = '0';
        $attributes['site_id'] = '0';
        $attributes['task_id'] = '0';
        $attributes['sub_task_id'] = '0';
        $attributes['advance_amount'] = '0';
        $attributes['remark'] = '';
        $attributes['ea_detail'] = array();
        $attributes['ea_total'] = 0;

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        if( ($process['validation_result'] == TRUE) && ($process['process_status'] == TRUE)){

            $elqEa = PaymentVoucher::where('ea_id', $process['ea_id'] )->first();
            if(is_null($elqEa) == FALSE){

                $attributes['ea_id'] = $elqEa->ea_id;
                $attributes['ea_date'] = $elqEa->pv_date;
                $attributes['sc_id'] = $request->sc_id;
                $attributes['employee_id'] = $elqEa->employee_id;
                $attributes['site_id'] = $elqEa->site_id;
                $attributes['task_id'] = $elqEa->task_id;
                $attributes['sub_task_id'] = $elqEa->sub_task_id;
                $attributes['advance_amount'] = number_format($elqEa->total_amount, 2);
                $attributes['remark'] = $elqEa->remark;
            }

            $attributes['validation_messages'] = $process['validation_messages'];

            if($request->submit == 'Display'){

                $attributes['validation_messages'] = new MessageBag();
                $attributes['process_message'] = "";
            }else{

                if( $attributes['advance_amount'] == 0 ){

                    $process['front_end_message'] = 'Cancel Process is Completed successfully.';
                    $message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
                    $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';

                }else{

                    $message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
                    $attributes['process_message'] = '<div class="alert alert-success" role="alert"> '. $message .' </div> ';
                }
            }

        }else{

            $inputs = $request->input();
            if(is_null($inputs) == FALSE){

                $attributes['ea_id'] = $inputs['ea_id'];
                $attributes['ea_date'] = $inputs['ea_date'];
                $attributes['sc_id'] = $inputs['sc_id'];
                $attributes['employee_id'] = $inputs['employee_id'];
                $attributes['site_id'] = $inputs['site_id'];
                $attributes['task_id'] = $inputs['task_id'];
                $attributes['sub_task_id'] = $inputs['sub_task_id'];
                $attributes['advance_amount'] = $inputs['advance_amount'];
                $attributes['remark'] = $inputs['remark'];
            }

            $ea_total = 0;
            $elqEaDetail = PaymentVoucherDetail::where('pv_id', $request->ea_id)->get();
            foreach($elqEaDetail as $key => $value){

                $value->ono = ($key+1);
                $value->employee_name = Employee::where('employee_id', $value->employee_id)->value('employee_name');
                $ea_total = $ea_total + $value->amount;
            }

            $attributes['ea_detail'] = $elqEaDetail;
            $attributes['ea_total'] = $elqEaDetail->sum('amount');
            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';
        }

        return $attributes;
    }

    public function processEmployeeAdvance(Request $request){

        if( $request->submit == 'Reset' ){

            $data['site_task'] = array();
            $data['site_sub_task'] = array();
            $data['attributes'] = $this->getEmployeeAdvanceAttributes(NULL, NULL);
        }

        if( $request->submit == 'Save' ){

            $ea_validation_result = $this->validatePaymentVoucher($request);
            if($ea_validation_result['validation_result'] == TRUE){

                $saving_process_result = $this->addPaymentVoucher($request);
                $saving_process_result['validation_result'] = $ea_validation_result['validation_result'];
                $saving_process_result['validation_messages'] = $ea_validation_result['validation_messages'];
                $data['attributes'] = $this->getEmployeeAdvanceAttributes($saving_process_result, $request);

            }else{

                $ea_validation_result['process_status'] = FALSE;
                $data['attributes'] = $this->getEmployeeAdvanceAttributes($ea_validation_result, $request);
            }

            $data['site_task'] = SiteTask::where('active', 1)->where('site_id', $request->site_id)->get();
            $data['site_sub_task'] = SiteSubTask::where('active', 1)->where('task_id', $request->task_id)->get();
        }


        $objEmployeeSalaryController =  new EmployeeSalaryController();

        $data['site'] = Site::where('active', 1)->get();
        $data['salary_category'] = $objEmployeeSalaryController->getSalaryCategory();
        $data['employee'] = Employee::where('active', 1)->get();

        return view('SiteMM.SiteOperation.employee_advance')->with('EA', $data);
    }

    private function validatePaymentVoucher($request){

        //try{

            $inputs['ea_id'] = $request->ea_id;
            $inputs['ea_date'] = $request->ea_date;
            $inputs['employee_id'] = $request->employee_id;
            $inputs['sc_id'] = $request->sc_id;

            if($request->sc_id != 1 ){

                $inputs['site_id'] = $request->site_id;
                $inputs['task_id'] = $request->task_id;
                $inputs['sub_task_id'] = $request->sub_task_id;
            }
            $inputs['remark'] = $request->remark;
            $inputs['advance_amount'] = $request->advance_amount;

            $rules['ea_id'] = array('required', new EmployeeAdvanceIsCancalValidation());
            $rules['ea_date'] = array('required', 'date');
            $rules['employee_id'] = array( new ZeroValidation('Employee', $request->employee_id));
            $rules['sc_id'] = array( new ZeroValidation('Salary Category', $request->sc_id));

            if($request->sc_id != 1 ){

                $rules['site_id'] = array( new ZeroValidation('Site', $request->site_id));
                $rules['task_id'] = array( new ZeroValidation('Task', $request->task_id));
                $rules['sub_task_id'] = array( new ZeroValidation('Sub Task', $request->sub_task_id));
            }
            $rules['remark'] = array('max:100');

            if($request->ea_id == '#Auto#'){

                $rules['advance_amount'] = array('required', 'numeric', new CurrencyValidation(0));
            }else{

                $rules['advance_amount'] = array('required', 'numeric', new CurrencyValidation(1));
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

    private function addPaymentVoucher($request){

        //try{

            $objPaymentVoucher = new PaymentVoucher();

            $employee_advance['ea'] = $this->getEmployeeAdvanceArray($request);
            $employee_advance['pv'] = $this->getPaymentVoucherArray($request);
            $employee_advance['pv_detail'] = $this->getPaymentVoucherDetailArray($request);

            $saving_process_result = $objPaymentVoucher->savePaymentVoucher($employee_advance);

            return $saving_process_result;

        // }catch(\Exception $e){

        //     $process_result['site_id'] = $request->site_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'Site Controller -> Site Saving Process <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

    private function getEmployeeAdvanceArray($request){

        $advance_amount = str_replace(",","",$request->advance_amount);

        $employee_advance['ea_id'] = $request->ea_id;
        $employee_advance['ea_date'] = $request->ea_date;
        $employee_advance['sc_id'] = $request->sc_id;
        $employee_advance['employee_id'] = $request->employee_id;

        if($request->sc_id != 1 ){

            $employee_advance['site_id'] = $request->site_id;
            $employee_advance['task_id'] = $request->task_id;
            $employee_advance['sub_task_id'] = $request->sub_task_id;
        }else{

            $employee_advance['site_id'] = 0;
            $employee_advance['task_id'] = 0;
            $employee_advance['sub_task_id'] = 0;
        }

        $employee_advance['advance_amount'] = $advance_amount;
        $employee_advance['advance_balance'] = $advance_amount;
        $employee_advance['remark'] = $request->remark;
        $employee_advance['cancel'] = 0;
        $employee_advance['settle'] = 0;

        if($advance_amount == 0){

            $employee_advance['cancel'] = 1;
            $employee_advance['cancel_by'] = Auth::id();
            $employee_advance['cancel_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        if( $request->ea_id == '#Auto#' ){

            $employee_advance['saved_by'] = Auth::id();
            $employee_advance['saved_on'] = Carbon::now()->format('Y-m-d H:i:s');

        }else{

            $employee_advance['updated_by'] = Auth::id();
            $employee_advance['updated_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        return $employee_advance;
    }

    private function getPaymentVoucherArray($request){

        $advance_amount = str_replace(",","",$request->advance_amount);

        $payment_voucher['pv_id'] = '#Auto#';
        $payment_voucher['pv_date'] = $request->ea_date;

        if($request->sc_id != 1 ){

            $payment_voucher['site_id'] = $request->site_id;
            $payment_voucher['task_id'] = $request->task_id;
            $payment_voucher['sub_task_id'] = $request->sub_task_id;

        }else{

            $payment_voucher['site_id'] = 0;
            $payment_voucher['task_id'] = 0;
            $payment_voucher['sub_task_id'] = 0;
        }

        $payment_voucher['cs_id'] = 2;
        $payment_voucher['advance'] = 1;
        $payment_voucher['ea_id'] = 0;
        $payment_voucher['total_amount'] = $advance_amount;
        $payment_voucher['remark'] = $request->remark;

        $payment_voucher['cancel'] = 0;

        if($advance_amount == 0){

            $payment_voucher['cancel'] = 1;
            $payment_voucher['cancel_by'] = Auth::id();
            $payment_voucher['cancel_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        if( $request->ea_id == '#Auto#' ){

            $payment_voucher['pv_id'] = 0;
            $payment_voucher['saved_by'] = Auth::id();
            $payment_voucher['saved_on'] = Carbon::now()->format('Y-m-d H:i:s');

        }else{

            $payment_voucher['updated_by'] = Auth::id();
            $payment_voucher['updated_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        return $payment_voucher;
    }

    private function getPaymentVoucherDetailArray($request){

        $pv_detail['pv_id'] = '#Auto#';
        $pv_detail['oci_id'] = 0;
        $pv_detail['employee_id'] = $request->employee_id;
        $pv_detail['price'] = str_replace(",","",$request->advance_amount);
        $pv_detail['quantity'] = 1;
        $pv_detail['amount'] = floatval(str_replace(",","",$request->advance_amount)) * 1;

        if($request->ea_id == '#Auto#'){

            $pv_detail['saved_by'] = Auth::id();
            $pv_detail['saved_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }else{

            $pv_detail['saved_by'] = Auth::id();
            $pv_detail['saved_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        return $pv_detail;
    }

    public function getEmployeeSalaryAdvanceRecordsForBasicSalary($employee_id){

        $elqEmployeeAdvance = DB::table('employee_advance')
                                ->where('cancel', 0)
                                ->where('settle', 0)
                                ->where('employee_id', $employee_id)
                                ->orderBy('ea_date', 'asc')
                                ->get();

        return $elqEmployeeAdvance;
    }

    public function getEmployeeAdvance(Request $request){

        $elqEmployeeAdvance = DB::table('employee_advance')->where('cancel', 0)->where('settle', 0)
                                ->where('site_id', $request->site_id)->where('task_id', $request->task_id)->where('sub_task_id', $request->sub_task_id)
                                ->orderBy('ea_date', 'asc')
                                ->get();

        if( isset($request->not_json)){

            return $elqEmployeeAdvance;
        }

        return  response()->json($elqEmployeeAdvance);
    }

    public function openEmployeeAdvance(Request $request){

        $elqEmployeeAdvance = EmployeeAdvance::where('ea_id', $request->open_ea_id )->first();
        if( is_null($elqEmployeeAdvance) == FALSE ){

            $attributes['ea_id'] = $elqEmployeeAdvance->ea_id;
            $attributes['ea_date'] = $elqEmployeeAdvance->ea_date;
            $attributes['sc_id'] = $elqEmployeeAdvance->sc_id;
            $attributes['employee_id'] = $elqEmployeeAdvance->employee_id;
            $attributes['site_id'] = $elqEmployeeAdvance->site_id;
            $attributes['task_id'] = $elqEmployeeAdvance->task_id;
            $attributes['sub_task_id'] = $elqEmployeeAdvance->sub_task_id;
            $attributes['advance_amount'] = $elqEmployeeAdvance->advance_amount;
            $attributes['remark'] = $elqEmployeeAdvance->remark;

            $data['site_task'] = SiteTask::where('active', 1)->where('site_id', $elqEmployeeAdvance->site_id)->get();
            $data['site_sub_task'] = SiteSubTask::where('active', 1)->where('task_id', $elqEmployeeAdvance->task_id)->get();
        }

        $objEmployeeSalaryController =  new EmployeeSalaryController();

        $attributes['validation_messages'] = new MessageBag();
        $attributes['process_message'] = "";

        $data['attributes'] = $attributes;
        $data['site'] = Site::where('active', 1)->get();
        $data['salary_category'] = $objEmployeeSalaryController->getSalaryCategory();
        $data['employee'] = Employee::where('active', 1)->get();

        return view('SiteMM.SiteOperation.employee_advance')->with('EA', $data);
    }

}
