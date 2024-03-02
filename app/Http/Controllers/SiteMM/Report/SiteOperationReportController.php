<?php

namespace App\Http\Controllers\SiteMM\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SiteMM\Master\Site;
use App\Models\SiteMM\SiteForcast\SiteMaterials;
use App\Models\SiteMM\SiteForcast\SiteLabour;
use App\Models\SiteMM\SiteForcast\SiteOverheadCost;
use App\Models\SiteMM\SiteForcast\SiteProfit;

use App\Models\SiteMM\SiteOperation\ItemIssueNote;
use App\Models\SiteMM\SiteOperation\PaymentVoucher;
use App\Models\SiteMM\SiteOperation\EmployeeSalary;

use App\Services\SiteMM\SiteOperation\MaterialService;
use App\Services\SiteMM\SiteOperation\LabourService;
use App\Services\SiteMM\SiteOperation\OverheadCostService;
use App\Helpers\Database\EloquentHelper;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

use App\Rules\ZeroValidation;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;

class SiteOperationReportController extends Controller {

    public function loadView(){

        $data['site'] = Site::where('active', 1)->get();
        $data['site_task'] = array();
        $data['site_sub_task'] = array();
        $data['attributes'] = $this->getSiteOperationReportAttributes(NULL, NULL);

        return view('SiteMM.Report.so_report')->with('SOR', $data);
    }

    private function getSiteOperationReportAttributes($process, $request){

        $attributes['site_id'] = '0';
        $attributes['task_id'] = '0';
        $attributes['sub_task_id'] = '0';

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        $inputs = $request->input();
        if(is_null($inputs) == FALSE){

            $attributes['site_id'] = $inputs['site_id'];
            $attributes['task_id'] = $inputs['task_id'];
            $attributes['sub_task_id'] = $inputs['sub_task_id'];
        }

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

    public function soReport(Request $request){

        $site_validation_result = $this->validateReport($request);
        if($site_validation_result['validation_result'] == TRUE){

            if( $request->report_type == 1 ){

                $this->prepareSummaryReport($request);
            }

            if( $request->report_type == 2 ){

                $this->prepareDetailReport($request);
            }

        }else{

            $site_validation_result['process_status'] = FALSE;

            $data['site'] = Site::where('active', 1)->get();
            $data['site_task'] = array();
            $data['site_sub_task'] = array();
            $data['attributes'] = $this->getSiteOperationReportAttributes($site_validation_result, $request);

            return view('SiteMM.Report.so_report')->with('SOR', $data);

        }

    }

    private function validateReport($request){

        //try{

            $inputs['site_id'] = $request->site_id;

            $rules['site_id'] = array( new ZeroValidation('Site', $request->site_id));


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

    private function prepareSummaryReport($request){

        $site_id = $request->site_id;
        $task_id = 0;
        $sub_task_id = 0;

        $total_meterial_cost = 0;
        $total_labour_cost = 0;
        $total_overhead_cost = 0;
        $grand_cost = 0;
        $total_profit_value = 0;
        $grand_value = 0;

        $total_operational_meterial_cost = 0;
        $total_operational_labour_cost = 0;
        $total_operational_overhead_cost = 0;
        $total_operational_cost = 0;
        $total_operational_variance = 0;

        $elqSite = Site::where('site_id', $request->site_id)->first();

        $spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

        // Header Part
		$style= array(
			'font'  => array(
					'bold'  => true,
					'size'  => 12,
					'name'  => 'Consolas'
            ),
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
		);

        $border_styleArray =array(
			'allBorders' => array(
				'borderStyle' => Border::BORDER_THIN,
				'color' => array( 'rgb' => '#FF0003')
			),
		);

		$sheet->mergeCells('A1:AB3');
		$sheet->getStyle('A1:AB3')->applyFromArray($style);
		$sheet->getStyle('A1:AB3')->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('A1:AB3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('A1', $elqSite->site_name);

        $style= array(
			'font'  => array(
					'bold'  => true,
					'size'  => 11,
					'name'  => 'Consolas'
            ),
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
		);

        $sheet->mergeCells('A4:A6');
        $sheet->getStyle('A4:A6')->applyFromArray($style);
		$sheet->getStyle('A4:A6')->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('A4:A6')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('A4', '#');

        $sheet->mergeCells('B4:J6');
        $sheet->getStyle('B4:J6')->applyFromArray($style);
		$sheet->getStyle('B4:J6')->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('B4:J6')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('B4', 'Task \ Sub Task');

        $sheet->mergeCells('K4:R4');
        $sheet->getStyle('K4:R4')->applyFromArray($style);
		$sheet->getStyle('K4:R4')->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('K4:R4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('K4', 'Budget');

        $sheet->mergeCells('S4:AB4');
        $sheet->getStyle('S4:AB4')->applyFromArray($style);
		$sheet->getStyle('S4:AB4')->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('S4:AB4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('S4', 'Operational');

        $sheet->mergeCells('K5:L6');
        $sheet->getStyle('K5:L6')->applyFromArray($style);
		$sheet->getStyle('K5:L6')->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('K5:L6')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('K5', "Material\nCost");
        $sheet->getStyle('K5')->getAlignment()->setWrapText(true);

        $sheet->mergeCells('M5:N6');
        $sheet->getStyle('M5:N6')->applyFromArray($style);
		$sheet->getStyle('M5:N6')->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('M5:N6')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('M5', "Labour\nCost");
        $sheet->getStyle('M5')->getAlignment()->setWrapText(true);

        $sheet->mergeCells('O5:P6');
        $sheet->getStyle('O5:P6')->applyFromArray($style);
		$sheet->getStyle('O5:P6')->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('O5:P6')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('O5', "Overhead\nCost");
        $sheet->getStyle('O5')->getAlignment()->setWrapText(true);

        $sheet->mergeCells('Q5:R6');
        $sheet->getStyle('Q5:R6')->applyFromArray($style);
		$sheet->getStyle('Q5:R6')->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('Q5:R6')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('Q5', "Total\nCost");
        $sheet->getStyle('Q5')->getAlignment()->setWrapText(true);

        $sheet->mergeCells('S5:T6');
        $sheet->getStyle('S5:T6')->applyFromArray($style);
		$sheet->getStyle('S5:T6')->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('S5:T6')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('S5', "Material\nValue");
        $sheet->getStyle('S5')->getAlignment()->setWrapText(true);

        $sheet->mergeCells('U5:V6');
        $sheet->getStyle('U5:V6')->applyFromArray($style);
		$sheet->getStyle('U5:V6')->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('U5:V6')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('U5', "Labour\nValue");
        $sheet->getStyle('U5')->getAlignment()->setWrapText(true);

        $sheet->mergeCells('W5:X6');
        $sheet->getStyle('W5:X6')->applyFromArray($style);
		$sheet->getStyle('W5:X6')->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('W5:X6')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('W5', "Overhead\nValue");
        $sheet->getStyle('W5')->getAlignment()->setWrapText(true);

        $sheet->mergeCells('Y5:Z6');
        $sheet->getStyle('Y5:Z6')->applyFromArray($style);
		$sheet->getStyle('Y5:Z6')->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('Y5:Z6')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('Y5', "Total\nValue");
        $sheet->getStyle('Y5')->getAlignment()->setWrapText(true);

        $sheet->mergeCells('AA5:AB6');
        $sheet->getStyle('AA5:AB6')->applyFromArray($style);
		$sheet->getStyle('AA5:AB6')->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('AA5:AB6')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('AA5', "Variance\nValue");
        $sheet->getStyle('AA5')->getAlignment()->setWrapText(true);

        // Detail Part

        $style_one = array(
			'font'  => array(
					'bold'  => false,
					'size'  => 10,
					'name'  => 'Consolas'
            ),
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
		);

        $style_two = array(
			'font'  => array(
					'bold'  => false,
					'size'  => 10,
					'name'  => 'Consolas'
            ),
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
		);

        $style_three = array(
			'font'  => array(
					'bold'  => false,
					'size'  => 10,
					'name'  => 'Consolas'
            ),
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
            ],
		);

        $style_four = array(
			'font'  => array(
					'bold'  => true,
					'size'  => 11,
					'name'  => 'Consolas'
            ),
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
            ],
		);


        $elqMeterial = ItemIssueNote::where('site_id', $site_id)->where('cancel', 0)->get();
        $elqOverheadCost = PaymentVoucher::where('site_id', $site_id)->where('cancel', 0)->get();
        $elqLabour = EmployeeSalary::select('*')
                                        ->join('employee_salary_detail', 'employee_salary_detail.es_id', '=', 'employee_salary.es_id')
                                        ->where('cancel', 0)
                                        ->where('site_id', $site_id)
                                        ->get();

        $rowInc_1 = 7;
        $rowInc_2 = 8;

        $elqTask = $elqSite->getTask;
        if($request->task_id != '0'){

            $elqTask = $elqTask->where('task_id', $request->task_id);
        }
        foreach($elqTask as $taskKey => $taskValue){

            $rowInc_3 = $rowInc_1 + 2;
            $rowInc_4 = $rowInc_2 + 2;
            $task_id = $taskValue->task_id;

            $cell_range = 'A'.$rowInc_1.':A'.$rowInc_2;
            $sheet->mergeCells($cell_range);
            $sheet->setCellValue('A'.$rowInc_1, ($taskKey+1));
            $sheet->getStyle($cell_range)->applyFromArray($style_one);
		    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

            $cell_range = 'B'.$rowInc_1.':J'.$rowInc_2;
            $sheet->mergeCells($cell_range);
            $sheet->setCellValue('B'.$rowInc_1, $taskValue->task_name);
            $sheet->getStyle($cell_range)->applyFromArray($style_two);
		    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

            $cell_range = 'K'.$rowInc_1.':AB'.$rowInc_2;
            $sheet->mergeCells($cell_range);
            $sheet->setCellValue('K'.$rowInc_1, '');
            $sheet->getStyle($cell_range)->applyFromArray($style_one);
		    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

            // Sub Task Detail

            $elqSubTask = $taskValue->subTask;
            if($request->sub_task_id != '0'){

                $elqSubTask = $elqSubTask->where('sub_task_id', $request->sub_task_id);
            }
            foreach($elqSubTask as $subtaskKey => $subtaskValue){

                $sub_task_id = $subtaskValue->sub_task_id;

                $meterial_cost = SiteMaterials::where('site_id', $site_id)->where('task_id', $task_id)->where('sub_task_id', $sub_task_id)->sum('amount');
                $labour_cost = SiteLabour::where('site_id', $site_id)->where('task_id', $task_id)->where('sub_task_id', $sub_task_id)->sum('amount');
                $overhead_cost = SiteOverheadCost::where('site_id', $site_id)->where('task_id', $task_id)->where('sub_task_id', $sub_task_id)->sum('amount');
                $profit_value = SiteProfit::where('site_id', $site_id)->where('task_id', $task_id)->where('sub_task_id', $sub_task_id)->sum('profit_value');
                $total_cost = $meterial_cost + $labour_cost + $overhead_cost;
                $total_value = $total_cost + $profit_value;

                $total_meterial_cost = $total_meterial_cost + $meterial_cost;
                $total_labour_cost = $total_labour_cost + $labour_cost;
                $total_overhead_cost = $total_overhead_cost + $overhead_cost;
                $total_profit_value = $total_profit_value + $profit_value;
                $grand_cost = $grand_cost + $total_cost;
                $grand_value = $grand_value + $total_value;

                $operational_meterial_value = $elqMeterial->where('task_id', $task_id)->where('sub_task_id', $sub_task_id)->sum('total_amount');
                $operational_labour_value = $elqLabour->where('task_id', $task_id)->where('sub_task_id', $sub_task_id)->sum('total_amount');
                $operational_overhead_cost_value = $elqOverheadCost->where('task_id', $task_id)->where('sub_task_id', $sub_task_id)->sum('total_amount');
                $operational_total_value = $operational_meterial_value + $operational_overhead_cost_value + $operational_labour_value;
                $operational_variance_value = $total_cost - $operational_total_value;

                $total_operational_meterial_cost = $total_operational_meterial_cost +  $operational_meterial_value;
                $total_operational_labour_cost = $total_operational_labour_cost + $operational_labour_value;
                $total_operational_overhead_cost = $total_operational_overhead_cost + $operational_overhead_cost_value;
                $total_operational_cost = $total_operational_cost + $operational_total_value;
                $total_operational_variance =  $total_operational_variance + $operational_variance_value;

                $meterial_cost = number_format($meterial_cost, 2);
                $labour_cost = number_format($labour_cost, 2);
                $overhead_cost = number_format($overhead_cost, 2);
                $total_cost = number_format($total_cost, 2);
                $total_value = number_format($total_value, 2);

                $operational_meterial_value = number_format($operational_meterial_value, 2);
                $operational_overhead_cost_value = number_format($operational_overhead_cost_value, 2);
                $operational_labour_value = number_format($operational_labour_value, 2);
                $operational_total_value = number_format($operational_total_value, 2);
                $operational_variance_value = number_format($operational_variance_value, 2);

                $cell_range = 'A'.$rowInc_3.':A'.$rowInc_4;
                $sheet->mergeCells($cell_range);
                $sheet->setCellValue('A'.$rowInc_3, '');
                $sheet->getStyle($cell_range)->applyFromArray($style_one);
                $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                $cell_range = 'B'.$rowInc_3.':B'.$rowInc_4;
                $sheet->mergeCells($cell_range);
                $sheet->setCellValue('B'.$rowInc_3, ($taskKey+1) . '.' . ($subtaskKey+1));
                $sheet->getStyle($cell_range)->applyFromArray($style_one);
                $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                $cell_range = 'C'.$rowInc_3.':J'.$rowInc_4;
                $sheet->mergeCells($cell_range);
                $sheet->setCellValue('C'.$rowInc_3, $subtaskValue->sub_task_name);
                $sheet->getStyle($cell_range)->applyFromArray($style_two);
                $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                $cell_range = 'K'.$rowInc_3.':L'.$rowInc_4;
                $sheet->mergeCells($cell_range);
                $sheet->setCellValue('K'.$rowInc_3, $meterial_cost);
                $sheet->getStyle($cell_range)->applyFromArray($style_three);
                $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                $cell_range = 'M'.$rowInc_3.':N'.$rowInc_4;
                $sheet->mergeCells($cell_range);
                $sheet->setCellValue('M'.$rowInc_3, $labour_cost);
                $sheet->getStyle($cell_range)->applyFromArray($style_three);
                $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                $cell_range = 'O'.$rowInc_3.':P'.$rowInc_4;
                $sheet->mergeCells($cell_range);
                $sheet->setCellValue('O'.$rowInc_3, $overhead_cost);
                $sheet->getStyle($cell_range)->applyFromArray($style_three);
                $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                $cell_range = 'Q'.$rowInc_3.':R'.$rowInc_4;
                $sheet->mergeCells($cell_range);
                $sheet->setCellValue('Q'.$rowInc_3, $total_cost);
                $sheet->getStyle($cell_range)->applyFromArray($style_three);
                $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                $cell_range = 'S'.$rowInc_3.':T'.$rowInc_4;
                $sheet->mergeCells($cell_range);
                $sheet->setCellValue('S'.$rowInc_3, $operational_meterial_value);
                $sheet->getStyle($cell_range)->applyFromArray($style_three);
                $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                $cell_range = 'U'.$rowInc_3.':V'.$rowInc_4;
                $sheet->mergeCells($cell_range);
                $sheet->setCellValue('U'.$rowInc_3, $operational_labour_value);
                $sheet->getStyle($cell_range)->applyFromArray($style_three);
                $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                $cell_range = 'W'.$rowInc_3.':X'.$rowInc_4;
                $sheet->mergeCells($cell_range);
                $sheet->setCellValue('W'.$rowInc_3, $operational_overhead_cost_value);
                $sheet->getStyle($cell_range)->applyFromArray($style_three);
                $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                $cell_range = 'Y'.$rowInc_3.':Z'.$rowInc_4;
                $sheet->mergeCells($cell_range);
                $sheet->setCellValue('Y'.$rowInc_3, $operational_total_value);
                $sheet->getStyle($cell_range)->applyFromArray($style_three);
                $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                $cell_range = 'AA'.$rowInc_3.':AB'.$rowInc_4;
                $sheet->mergeCells($cell_range);
                $sheet->setCellValue('AA'.$rowInc_3, $operational_variance_value);
                $sheet->getStyle($cell_range)->applyFromArray($style_three);
                $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);


                $rowInc_3 = $rowInc_3 + 2;
                $rowInc_4 = $rowInc_4 + 2;
            }

            $rowInc_1 = $rowInc_3;
            $rowInc_2 = $rowInc_4;
        }

        // Total
        $sheet->mergeCells('A'.$rowInc_1.':J'.$rowInc_2);
        $sheet->getStyle('A'.$rowInc_1.':J'.$rowInc_2)->applyFromArray($style);
		$sheet->getStyle('A'.$rowInc_1.':J'.$rowInc_2)->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('A'.$rowInc_1.':J'.$rowInc_2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('A'.$rowInc_1, 'Total');

        $sheet->mergeCells('K'.$rowInc_1.':L'.$rowInc_2);
        $sheet->getStyle('K'.$rowInc_1.':L'.$rowInc_2)->applyFromArray($style_four);
		$sheet->getStyle('K'.$rowInc_1.':L'.$rowInc_2)->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('K'.$rowInc_1.':L'.$rowInc_2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('K'.$rowInc_1, number_format($total_meterial_cost, 2));

        $sheet->mergeCells('M'.$rowInc_1.':N'.$rowInc_2);
        $sheet->getStyle('M'.$rowInc_1.':N'.$rowInc_2)->applyFromArray($style_four);
		$sheet->getStyle('M'.$rowInc_1.':N'.$rowInc_2)->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('M'.$rowInc_1.':N'.$rowInc_2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('M'.$rowInc_1, number_format($total_labour_cost, 2));

        $sheet->mergeCells('O'.$rowInc_1.':P'.$rowInc_2);
        $sheet->getStyle('O'.$rowInc_1.':P'.$rowInc_2)->applyFromArray($style_four);
		$sheet->getStyle('O'.$rowInc_1.':P'.$rowInc_2)->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('O'.$rowInc_1.':P'.$rowInc_2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('O'.$rowInc_1, number_format($total_overhead_cost, 2));

        $sheet->mergeCells('Q'.$rowInc_1.':R'.$rowInc_2);
        $sheet->getStyle('Q'.$rowInc_1.':R'.$rowInc_2)->applyFromArray($style_four);
		$sheet->getStyle('Q'.$rowInc_1.':R'.$rowInc_2)->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('Q'.$rowInc_1.':R'.$rowInc_2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('Q'.$rowInc_1, number_format($grand_cost, 2));

        $sheet->mergeCells('S'.$rowInc_1.':T'.$rowInc_2);
        $sheet->getStyle('S'.$rowInc_1.':T'.$rowInc_2)->applyFromArray($style_four);
		$sheet->getStyle('S'.$rowInc_1.':T'.$rowInc_2)->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('S'.$rowInc_1.':T'.$rowInc_2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('S'.$rowInc_1, number_format($total_operational_meterial_cost, 2));

        $sheet->mergeCells('U'.$rowInc_1.':V'.$rowInc_2);
        $sheet->getStyle('U'.$rowInc_1.':V'.$rowInc_2)->applyFromArray($style_four);
		$sheet->getStyle('U'.$rowInc_1.':V'.$rowInc_2)->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('U'.$rowInc_1.':V'.$rowInc_2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('U'.$rowInc_1, number_format($total_operational_labour_cost, 2));

        $sheet->mergeCells('W'.$rowInc_1.':X'.$rowInc_2);
        $sheet->getStyle('W'.$rowInc_1.':X'.$rowInc_2)->applyFromArray($style_four);
		$sheet->getStyle('W'.$rowInc_1.':X'.$rowInc_2)->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('W'.$rowInc_1.':X'.$rowInc_2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('W'.$rowInc_1, number_format($total_operational_overhead_cost, 2));

        $sheet->mergeCells('Y'.$rowInc_1.':Z'.$rowInc_2);
        $sheet->getStyle('Y'.$rowInc_1.':Z'.$rowInc_2)->applyFromArray($style_four);
		$sheet->getStyle('Y'.$rowInc_1.':Z'.$rowInc_2)->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('Y'.$rowInc_1.':Z'.$rowInc_2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('Y'.$rowInc_1, number_format($total_operational_cost, 2));

        $sheet->mergeCells('AA'.$rowInc_1.':AB'.$rowInc_2);
        $sheet->getStyle('AA'.$rowInc_1.':AB'.$rowInc_2)->applyFromArray($style_four);
		$sheet->getStyle('AA'.$rowInc_1.':AB'.$rowInc_2)->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('AA'.$rowInc_1.':AB'.$rowInc_2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('AA'.$rowInc_1, number_format($total_operational_variance, 2));

        $writer = new Xlsx($spreadsheet);
		$filename = 'Site-Operation-Summary-Report';

		header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output'); // download file

    }

    private function prepareDetailReport($request){

        $site_id = $request->site_id;
        $task_id = 0;
        $sub_task_id = 0;

        $elqSite = Site::where('site_id', $request->site_id)->first();

        $spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

        // Header Part
		$style= array(
			'font'  => array(
					'bold'  => true,
					'size'  => 12,
					'name'  => 'Consolas'
            ),
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
		);

        $border_styleArray =array(
			'allBorders' => array(
				'borderStyle' => Border::BORDER_THIN,
				'color' => array( 'rgb' => '#FF0003')
			),
		);

		$sheet->mergeCells('A1:AD3');
		$sheet->getStyle('A1:AD3')->applyFromArray($style);
		$sheet->getStyle('A1:AD3')->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('A1:AD3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('A1', $elqSite->site_name);

        $style= array(
			'font'  => array(
					'bold'  => true,
					'size'  => 11,
					'name'  => 'Consolas'
            ),
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
		);

        $sheet->mergeCells('A4:A5');
        $sheet->getStyle('A4:A5')->applyFromArray($style);
		$sheet->getStyle('A4:A5')->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('A4:A5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('A4', '#');

        $sheet->mergeCells('B4:J5');
        $sheet->getStyle('B4:J5')->applyFromArray($style);
		$sheet->getStyle('B4:J5')->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('B4:J5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('B4', 'Task \ Sub Task');

        $sheet->mergeCells('K4:L5');
        $sheet->getStyle('K4:L5')->applyFromArray($style);
		$sheet->getStyle('K4:L5')->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('K4:L5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('K4', "Date");
        $sheet->getStyle('K5')->getAlignment()->setWrapText(true);

        $sheet->mergeCells('M4:T5');
        $sheet->getStyle('M4:T5')->applyFromArray($style);
		$sheet->getStyle('M4:T5')->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('M4:T5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('M4', "Meterial Item \ Employee Name \ Overhead cost Item");
        $sheet->getStyle('M4')->getAlignment()->setWrapText(true);

        $sheet->mergeCells('U4:V5');
        $sheet->getStyle('U4:V5')->applyFromArray($style);
		$sheet->getStyle('U4:V5')->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('U4:V5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('U4', "Unit");
        $sheet->getStyle('U4')->getAlignment()->setWrapText(true);

        $sheet->mergeCells('W4:X5');
        $sheet->getStyle('W4:X5')->applyFromArray($style);
		$sheet->getStyle('W4:X5')->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('W4:X5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('W4', "Unit Price");
        $sheet->getStyle('W4')->getAlignment()->setWrapText(true);

        $sheet->mergeCells('Y4:Z5');
        $sheet->getStyle('Y4:Z5')->applyFromArray($style);
		$sheet->getStyle('Y4:Z5')->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('Y4:Z5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('Y4', "Quantity");
        $sheet->getStyle('Y4')->getAlignment()->setWrapText(true);

        $sheet->mergeCells('AA4:AB5');
        $sheet->getStyle('AA4:AB5')->applyFromArray($style);
		$sheet->getStyle('AA4:AB5')->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('AA4:AB5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('AA4', "Amount");
        $sheet->getStyle('AA4')->getAlignment()->setWrapText(true);

        $sheet->mergeCells('AC4:AD5');
        $sheet->getStyle('AC4:AD5')->applyFromArray($style);
		$sheet->getStyle('AC4:AD5')->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('AC4:AD5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('AC4', "Total Amount");
        $sheet->getStyle('AC4')->getAlignment()->setWrapText(true);

        // Detail Part

        $style_one = array(
			'font'  => array(
					'bold'  => false,
					'size'  => 10,
					'name'  => 'Consolas'
            ),
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
		);

        $style_two = array(
			'font'  => array(
					'bold'  => false,
					'size'  => 10,
					'name'  => 'Consolas'
            ),
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
		);

        $style_three = array(
			'font'  => array(
					'bold'  => false,
					'size'  => 10,
					'name'  => 'Consolas'
            ),
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
            ],
		);

        $style_four = array(
			'font'  => array(
					'bold'  => true,
					'size'  => 11,
					'name'  => 'Consolas'
            ),
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
            ],
		);


        $elqMeterial = ItemIssueNote::where('site_id', $site_id)->where('cancel', 0)->get();
        $elqOverheadCost = PaymentVoucher::where('site_id', $site_id)->where('cancel', 0)->get();
        $elqLabour = EmployeeSalary::select('*')
                                        ->join('employee_salary_detail', 'employee_salary_detail.es_id', '=', 'employee_salary.es_id')
                                        ->where('cancel', 0)
                                        ->where('site_id', $site_id)
                                        ->get();

        $grand_total = 0;
        $rowInc_1 = 6;
        $rowInc_2 = 7;

        $elqTask = $elqSite->getTask;
        if($request->task_id != '0'){

            $elqTask = $elqTask->where('task_id', $request->task_id);
        }
        foreach($elqTask as $taskKey => $taskValue){

            $rowInc_3 = $rowInc_1 + 2;
            $rowInc_4 = $rowInc_2 + 2;
            $task_id = $taskValue->task_id;

            $cell_range = 'A'.$rowInc_1.':A'.$rowInc_2;
            $sheet->mergeCells($cell_range);
            $sheet->setCellValue('A'.$rowInc_1, ($taskKey+1));
            $sheet->getStyle($cell_range)->applyFromArray($style_one);
		    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

            $cell_range = 'B'.$rowInc_1.':J'.$rowInc_2;
            $sheet->mergeCells($cell_range);
            $sheet->setCellValue('B'.$rowInc_1, $taskValue->task_name);
            $sheet->getStyle($cell_range)->applyFromArray($style_two);
		    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

            $cell_range = 'K'.$rowInc_1.':AD'.$rowInc_2;
            $sheet->mergeCells($cell_range);
            $sheet->setCellValue('K'.$rowInc_1, '');
            $sheet->getStyle($cell_range)->applyFromArray($style_one);
		    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

            // Sub Task Detail

            $elqSubTask = $taskValue->subTask;
            if($request->sub_task_id != '0'){

                $elqSubTask = $elqSubTask->where('sub_task_id', $request->sub_task_id);
            }
            foreach($elqSubTask as $subtaskKey => $subtaskValue){

                $sub_task_id = $subtaskValue->sub_task_id;

                $sub_task_total_material_amount = 0;
                $sub_task_total_labour_amount = 0;
                $sub_task_total_overhead_cost_amount = 0;
                $print_duplicate_flag = FALSE;

                // Get Meterials
                $objMaterialService = new MaterialService();
                $meterial_result = $objMaterialService->getMaterialDetail($site_id, $task_id, $sub_task_id);
                if( EloquentHelper::recordExists($meterial_result) ){

                    foreach($meterial_result as $meterial_key => $meterial_value){

                        $cell_range = 'A'.$rowInc_3.':A'.$rowInc_4;
                        $sheet->mergeCells($cell_range);
                        $sheet->setCellValue('A'.$rowInc_3, '');
                        $sheet->getStyle($cell_range)->applyFromArray($style_one);
                        $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                        $task_subtask_label = '';
                        $sub_task_name = '';
                        if( $meterial_key == 0 ){

                            $task_subtask_label = ($taskKey+1) . '.' . ($subtaskKey+1);
                            $sub_task_name = $subtaskValue->sub_task_name;
                        }

                        $cell_range = 'B'.$rowInc_3.':B'.$rowInc_4;
                        $sheet->mergeCells($cell_range);
                        $sheet->setCellValue('B'.$rowInc_3, $task_subtask_label);
                        $sheet->getStyle($cell_range)->applyFromArray($style_one);
                        $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                        $cell_range = 'C'.$rowInc_3.':J'.$rowInc_4;
                        $sheet->mergeCells($cell_range);
                        $sheet->setCellValue('C'.$rowInc_3, $sub_task_name);
                        $sheet->getStyle($cell_range)->applyFromArray($style_two);
                        $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                        $cell_range = 'K'.$rowInc_3.':L'.$rowInc_4;
                        $sheet->mergeCells($cell_range);
                        $sheet->setCellValue('K'.$rowInc_3, $meterial_value->iin_date);
                        $sheet->getStyle($cell_range)->applyFromArray($style_two);
                        $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                        $cell_range = 'M'.$rowInc_3.':T'.$rowInc_4;
                        $sheet->mergeCells($cell_range);
                        $sheet->setCellValue('M'.$rowInc_3, $meterial_value->item_name);
                        $sheet->getStyle($cell_range)->applyFromArray($style_two);
                        $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                        $cell_range = 'U'.$rowInc_3.':V'.$rowInc_4;
                        $sheet->mergeCells($cell_range);
                        $sheet->setCellValue('U'.$rowInc_3, $meterial_value->unit_name);
                        $sheet->getStyle($cell_range)->applyFromArray($style_two);
                        $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                        $cell_range = 'W'.$rowInc_3.':X'.$rowInc_4;
                        $sheet->mergeCells($cell_range);
                        $sheet->setCellValue('W'.$rowInc_3, number_format($meterial_value->price, 2));
                        $sheet->getStyle($cell_range)->applyFromArray($style_three);
                        $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                        $cell_range = 'Y'.$rowInc_3.':Z'.$rowInc_4;
                        $sheet->mergeCells($cell_range);
                        $sheet->setCellValue('Y'.$rowInc_3, $meterial_value->quantity);
                        $sheet->getStyle($cell_range)->applyFromArray($style_three);
                        $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                        $cell_range = 'AA'.$rowInc_3.':AB'.$rowInc_4;
                        $sheet->mergeCells($cell_range);
                        $sheet->setCellValue('AA'.$rowInc_3, number_format($meterial_value->amount, 2));
                        $sheet->getStyle($cell_range)->applyFromArray($style_three);
                        $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                        $cell_range = 'AC'.$rowInc_3.':AD'.$rowInc_4;
                        $sheet->mergeCells($cell_range);
                        $sheet->setCellValue('AC'.$rowInc_3, '');
                        $sheet->getStyle($cell_range)->applyFromArray($style_three);
                        $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                        $sub_task_total_material_amount += $meterial_value->amount;
                        $print_duplicate_flag = TRUE;

                        $rowInc_3 = $rowInc_3 + 2;
                        $rowInc_4 = $rowInc_4 + 2;

                    }

                    $cell_range = 'A'.$rowInc_3.':A'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('A'.$rowInc_3, '');
                    $sheet->getStyle($cell_range)->applyFromArray($style_one);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $task_subtask_label = '';
                    $sub_task_name = '';
                    if( $meterial_key == 0 ){

                        $task_subtask_label = ($taskKey+1) . '.' . ($subtaskKey+1);
                        $sub_task_name = $subtaskValue->sub_task_name;
                    }

                    $cell_range = 'B'.$rowInc_3.':B'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('B'.$rowInc_3, '');
                    $sheet->getStyle($cell_range)->applyFromArray($style_one);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $cell_range = 'C'.$rowInc_3.':J'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('C'.$rowInc_3, '');
                    $sheet->getStyle($cell_range)->applyFromArray($style_two);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $cell_range = 'K'.$rowInc_3.':L'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('K'.$rowInc_3, '');
                    $sheet->getStyle($cell_range)->applyFromArray($style_two);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $cell_range = 'M'.$rowInc_3.':T'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('M'.$rowInc_3, 'Meterial Total');
                    $sheet->getStyle($cell_range)->applyFromArray($style_two);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $cell_range = 'U'.$rowInc_3.':V'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('U'.$rowInc_3, '');
                    $sheet->getStyle($cell_range)->applyFromArray($style_two);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $cell_range = 'W'.$rowInc_3.':X'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('W'.$rowInc_3, '');
                    $sheet->getStyle($cell_range)->applyFromArray($style_three);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $cell_range = 'Y'.$rowInc_3.':Z'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('Y'.$rowInc_3, '');
                    $sheet->getStyle($cell_range)->applyFromArray($style_three);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $cell_range = 'AA'.$rowInc_3.':AB'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('AA'.$rowInc_3, '');
                    $sheet->getStyle($cell_range)->applyFromArray($style_three);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $cell_range = 'AC'.$rowInc_3.':AD'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('AC'.$rowInc_3, number_format($sub_task_total_material_amount, 2));
                    $sheet->getStyle($cell_range)->applyFromArray($style_three);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $rowInc_3 = $rowInc_3 + 2;
                    $rowInc_4 = $rowInc_4 + 2;

                }else{

                    // $cell_range = 'A'.$rowInc_3.':A'.$rowInc_4;
                    // $sheet->mergeCells($cell_range);
                    // $sheet->setCellValue('A'.$rowInc_3, '');
                    // $sheet->getStyle($cell_range)->applyFromArray($style_one);
                    // $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    // $task_subtask_label = ($taskKey+1) . '.' . ($subtaskKey+1);
                    // $sub_task_name = $subtaskValue->sub_task_name;

                    // $cell_range = 'B'.$rowInc_3.':B'.$rowInc_4;
                    // $sheet->mergeCells($cell_range);
                    // $sheet->setCellValue('B'.$rowInc_3, $task_subtask_label .  '  {+}  ');
                    // $sheet->getStyle($cell_range)->applyFromArray($style_one);
                    // $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    // $cell_range = 'C'.$rowInc_3.':J'.$rowInc_4;
                    // $sheet->mergeCells($cell_range);
                    // $sheet->setCellValue('C'.$rowInc_3, $sub_task_name);
                    // $sheet->getStyle($cell_range)->applyFromArray($style_two);
                    // $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    // $cell_range = 'K'.$rowInc_3.':L'.$rowInc_4;
                    // $sheet->mergeCells($cell_range);
                    // $sheet->setCellValue('K'.$rowInc_3, '');
                    // $sheet->getStyle($cell_range)->applyFromArray($style_two);
                    // $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    // $cell_range = 'M'.$rowInc_3.':T'.$rowInc_4;
                    // $sheet->mergeCells($cell_range);
                    // $sheet->setCellValue('M'.$rowInc_3, '');
                    // $sheet->getStyle($cell_range)->applyFromArray($style_two);
                    // $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    // $cell_range = 'U'.$rowInc_3.':V'.$rowInc_4;
                    // $sheet->mergeCells($cell_range);
                    // $sheet->setCellValue('V'.$rowInc_3, '');
                    // $sheet->getStyle($cell_range)->applyFromArray($style_two);
                    // $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    // $cell_range = 'W'.$rowInc_3.':X'.$rowInc_4;
                    // $sheet->mergeCells($cell_range);
                    // $sheet->setCellValue('W'.$rowInc_3, number_format(0));
                    // $sheet->getStyle($cell_range)->applyFromArray($style_three);
                    // $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    // $cell_range = 'Y'.$rowInc_3.':Z'.$rowInc_4;
                    // $sheet->mergeCells($cell_range);
                    // $sheet->setCellValue('Y'.$rowInc_3, '');
                    // $sheet->getStyle($cell_range)->applyFromArray($style_three);
                    // $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    // $cell_range = 'AA'.$rowInc_3.':AB'.$rowInc_4;
                    // $sheet->mergeCells($cell_range);
                    // $sheet->setCellValue('AA'.$rowInc_3, number_format(0));
                    // $sheet->getStyle($cell_range)->applyFromArray($style_three);
                    // $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    // $cell_range = 'AC'.$rowInc_3.':AD'.$rowInc_4;
                    // $sheet->mergeCells($cell_range);
                    // $sheet->setCellValue('AC'.$rowInc_3, number_format(0, 2));
                    // $sheet->getStyle($cell_range)->applyFromArray($style_three);
                    // $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    // $rowInc_3 = $rowInc_3 + 2;
                    // $rowInc_4 = $rowInc_4 + 2;
                    // $print_duplicate_flag = 1;
                }

                // Get Labours
                $objLabourService = new LabourService();
                $labour_result = $objLabourService->getLabourDetail($site_id, $task_id, $sub_task_id);
                if( EloquentHelper::recordExists($labour_result) ){

                    foreach($labour_result as $labour_key => $labour_value){

                        $cell_range = 'A'.$rowInc_3.':A'.$rowInc_4;
                        $sheet->mergeCells($cell_range);
                        $sheet->setCellValue('A'.$rowInc_3, '');
                        $sheet->getStyle($cell_range)->applyFromArray($style_one);
                        $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                        $cell_range = 'B'.$rowInc_3.':B'.$rowInc_4;
                        $sheet->mergeCells($cell_range);
                        $sheet->setCellValue('B'.$rowInc_3, $task_subtask_label);
                        $sheet->getStyle($cell_range)->applyFromArray($style_one);
                        $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                        $cell_range = 'C'.$rowInc_3.':J'.$rowInc_4;
                        $sheet->mergeCells($cell_range);
                        $sheet->setCellValue('C'.$rowInc_3, $sub_task_name);
                        $sheet->getStyle($cell_range)->applyFromArray($style_two);
                        $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                        $cell_range = 'K'.$rowInc_3.':L'.$rowInc_4;
                        $sheet->mergeCells($cell_range);
                        $sheet->setCellValue('K'.$rowInc_3, $labour_value->es_date);
                        $sheet->getStyle($cell_range)->applyFromArray($style_two);
                        $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                        $cell_range = 'M'.$rowInc_3.':T'.$rowInc_4;
                        $sheet->mergeCells($cell_range);
                        $sheet->setCellValue('M'.$rowInc_3, $labour_value->employee_name);
                        $sheet->getStyle($cell_range)->applyFromArray($style_two);
                        $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                        $cell_range = 'U'.$rowInc_3.':V'.$rowInc_4;
                        $sheet->mergeCells($cell_range);
                        $sheet->setCellValue('U'.$rowInc_3, $labour_value->unit_name);
                        $sheet->getStyle($cell_range)->applyFromArray($style_two);
                        $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                        $cell_range = 'W'.$rowInc_3.':X'.$rowInc_4;
                        $sheet->mergeCells($cell_range);
                        $sheet->setCellValue('W'.$rowInc_3, number_format($labour_value->price, 2));
                        $sheet->getStyle($cell_range)->applyFromArray($style_three);
                        $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                        $cell_range = 'Y'.$rowInc_3.':Z'.$rowInc_4;
                        $sheet->mergeCells($cell_range);
                        $sheet->setCellValue('Y'.$rowInc_3, $labour_value->total_hours);
                        $sheet->getStyle($cell_range)->applyFromArray($style_three);
                        $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                        $cell_range = 'AA'.$rowInc_3.':AB'.$rowInc_4;
                        $sheet->mergeCells($cell_range);
                        $sheet->setCellValue('AA'.$rowInc_3, number_format($labour_value->total_amount, 2));
                        $sheet->getStyle($cell_range)->applyFromArray($style_three);
                        $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                        $cell_range = 'AC'.$rowInc_3.':AD'.$rowInc_4;
                        $sheet->mergeCells($cell_range);
                        $sheet->setCellValue('AC'.$rowInc_3, '');
                        $sheet->getStyle($cell_range)->applyFromArray($style_three);
                        $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                        $sub_task_total_labour_amount += $labour_value->total_amount;
                        $print_duplicate_flag = TRUE;

                        $rowInc_3 = $rowInc_3 + 2;
                        $rowInc_4 = $rowInc_4 + 2;
                    }

                    $cell_range = 'A'.$rowInc_3.':A'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('A'.$rowInc_3, '');
                    $sheet->getStyle($cell_range)->applyFromArray($style_one);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $cell_range = 'B'.$rowInc_3.':B'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('B'.$rowInc_3, '');
                    $sheet->getStyle($cell_range)->applyFromArray($style_one);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $cell_range = 'C'.$rowInc_3.':J'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('C'.$rowInc_3,'');
                    $sheet->getStyle($cell_range)->applyFromArray($style_two);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $cell_range = 'K'.$rowInc_3.':L'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('K'.$rowInc_3, '');
                    $sheet->getStyle($cell_range)->applyFromArray($style_two);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $cell_range = 'M'.$rowInc_3.':T'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('M'.$rowInc_3, 'Labour Total');
                    $sheet->getStyle($cell_range)->applyFromArray($style_two);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $cell_range = 'U'.$rowInc_3.':V'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('U'.$rowInc_3, '');
                    $sheet->getStyle($cell_range)->applyFromArray($style_two);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $cell_range = 'W'.$rowInc_3.':X'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('W'.$rowInc_3, '');
                    $sheet->getStyle($cell_range)->applyFromArray($style_three);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $cell_range = 'Y'.$rowInc_3.':Z'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('Y'.$rowInc_3, '');
                    $sheet->getStyle($cell_range)->applyFromArray($style_three);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $cell_range = 'AA'.$rowInc_3.':AB'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('AA'.$rowInc_3, '');
                    $sheet->getStyle($cell_range)->applyFromArray($style_three);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $cell_range = 'AC'.$rowInc_3.':AD'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('AC'.$rowInc_3, number_format($sub_task_total_labour_amount, 2));
                    $sheet->getStyle($cell_range)->applyFromArray($style_three);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $rowInc_3 = $rowInc_3 + 2;
                    $rowInc_4 = $rowInc_4 + 2;

                }else{

                    // $cell_range = 'A'.$rowInc_3.':A'.$rowInc_4;
                    // $sheet->mergeCells($cell_range);
                    // $sheet->setCellValue('A'.$rowInc_3, '');
                    // $sheet->getStyle($cell_range)->applyFromArray($style_one);
                    // $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    // $task_subtask_label = ($taskKey+1) . '.' . ($subtaskKey+1);
                    // $sub_task_name = $subtaskValue->sub_task_name;

                    // $cell_range = 'B'.$rowInc_3.':B'.$rowInc_4;
                    // $sheet->mergeCells($cell_range);
                    // $sheet->setCellValue('B'.$rowInc_3, $task_subtask_label . '  {-}  ');
                    // $sheet->getStyle($cell_range)->applyFromArray($style_one);
                    // $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    // $cell_range = 'C'.$rowInc_3.':J'.$rowInc_4;
                    // $sheet->mergeCells($cell_range);
                    // $sheet->setCellValue('C'.$rowInc_3, $sub_task_name);
                    // $sheet->getStyle($cell_range)->applyFromArray($style_two);
                    // $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    // $cell_range = 'K'.$rowInc_3.':L'.$rowInc_4;
                    // $sheet->mergeCells($cell_range);
                    // $sheet->setCellValue('K'.$rowInc_3, '');
                    // $sheet->getStyle($cell_range)->applyFromArray($style_two);
                    // $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    // $cell_range = 'M'.$rowInc_3.':T'.$rowInc_4;
                    // $sheet->mergeCells($cell_range);
                    // $sheet->setCellValue('M'.$rowInc_3, '');
                    // $sheet->getStyle($cell_range)->applyFromArray($style_two);
                    // $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    // $cell_range = 'U'.$rowInc_3.':V'.$rowInc_4;
                    // $sheet->mergeCells($cell_range);
                    // $sheet->setCellValue('V'.$rowInc_3, '');
                    // $sheet->getStyle($cell_range)->applyFromArray($style_two);
                    // $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    // $cell_range = 'W'.$rowInc_3.':X'.$rowInc_4;
                    // $sheet->mergeCells($cell_range);
                    // $sheet->setCellValue('W'.$rowInc_3, number_format(0));
                    // $sheet->getStyle($cell_range)->applyFromArray($style_three);
                    // $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    // $cell_range = 'Y'.$rowInc_3.':Z'.$rowInc_4;
                    // $sheet->mergeCells($cell_range);
                    // $sheet->setCellValue('Y'.$rowInc_3, '');
                    // $sheet->getStyle($cell_range)->applyFromArray($style_three);
                    // $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    // $cell_range = 'AA'.$rowInc_3.':AB'.$rowInc_4;
                    // $sheet->mergeCells($cell_range);
                    // $sheet->setCellValue('AA'.$rowInc_3, number_format(0));
                    // $sheet->getStyle($cell_range)->applyFromArray($style_three);
                    // $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    // $rowInc_3 = $rowInc_3 + 2;
                    // $rowInc_4 = $rowInc_4 + 2;
                }

                // Get Overhead Cost
                $objOverheadCostService = new OverheadCostService();
                $overhead_cost_result = $objOverheadCostService->getOverheadCostDetail($site_id, $task_id, $sub_task_id);
                if( EloquentHelper::recordExists($overhead_cost_result) ){

                    foreach($overhead_cost_result as $overhead_cost_key => $overhead_cost_value){

                        $cell_range = 'A'.$rowInc_3.':A'.$rowInc_4;
                        $sheet->mergeCells($cell_range);
                        $sheet->setCellValue('A'.$rowInc_3, '');
                        $sheet->getStyle($cell_range)->applyFromArray($style_one);
                        $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                        $cell_range = 'B'.$rowInc_3.':B'.$rowInc_4;
                        $sheet->mergeCells($cell_range);
                        $sheet->setCellValue('B'.$rowInc_3, $task_subtask_label);
                        $sheet->getStyle($cell_range)->applyFromArray($style_one);
                        $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                        $cell_range = 'C'.$rowInc_3.':J'.$rowInc_4;
                        $sheet->mergeCells($cell_range);
                        $sheet->setCellValue('C'.$rowInc_3, $sub_task_name);
                        $sheet->getStyle($cell_range)->applyFromArray($style_two);
                        $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                        $cell_range = 'K'.$rowInc_3.':L'.$rowInc_4;
                        $sheet->mergeCells($cell_range);
                        $sheet->setCellValue('K'.$rowInc_3, $overhead_cost_value->pv_date);
                        $sheet->getStyle($cell_range)->applyFromArray($style_two);
                        $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                        $cell_range = 'M'.$rowInc_3.':T'.$rowInc_4;
                        $sheet->mergeCells($cell_range);
                        $sheet->setCellValue('M'.$rowInc_3, $overhead_cost_value->oci_name);
                        $sheet->getStyle($cell_range)->applyFromArray($style_two);
                        $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                        $cell_range = 'U'.$rowInc_3.':V'.$rowInc_4;
                        $sheet->mergeCells($cell_range);
                        $sheet->setCellValue('U'.$rowInc_3, $overhead_cost_value->unit_name);
                        $sheet->getStyle($cell_range)->applyFromArray($style_two);
                        $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                        $cell_range = 'W'.$rowInc_3.':X'.$rowInc_4;
                        $sheet->mergeCells($cell_range);
                        $sheet->setCellValue('W'.$rowInc_3, number_format($overhead_cost_value->price, 2));
                        $sheet->getStyle($cell_range)->applyFromArray($style_three);
                        $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                        $cell_range = 'Y'.$rowInc_3.':Z'.$rowInc_4;
                        $sheet->mergeCells($cell_range);
                        $sheet->setCellValue('Y'.$rowInc_3, $overhead_cost_value->quantity);
                        $sheet->getStyle($cell_range)->applyFromArray($style_three);
                        $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                        $cell_range = 'AA'.$rowInc_3.':AB'.$rowInc_4;
                        $sheet->mergeCells($cell_range);
                        $sheet->setCellValue('AA'.$rowInc_3, number_format($overhead_cost_value->amount, 2));
                        $sheet->getStyle($cell_range)->applyFromArray($style_three);
                        $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                        $cell_range = 'AC'.$rowInc_3.':AD'.$rowInc_4;
                        $sheet->mergeCells($cell_range);
                        $sheet->setCellValue('AC'.$rowInc_3, '');
                        $sheet->getStyle($cell_range)->applyFromArray($style_three);
                        $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                        $sub_task_total_overhead_cost_amount += $overhead_cost_value->amount;
                        $print_duplicate_flag = TRUE;

                        $rowInc_3 = $rowInc_3 + 2;
                        $rowInc_4 = $rowInc_4 + 2;
                    }

                    $cell_range = 'A'.$rowInc_3.':A'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('A'.$rowInc_3, '');
                    $sheet->getStyle($cell_range)->applyFromArray($style_one);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $cell_range = 'B'.$rowInc_3.':B'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('B'.$rowInc_3, '');
                    $sheet->getStyle($cell_range)->applyFromArray($style_one);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $cell_range = 'C'.$rowInc_3.':J'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('C'.$rowInc_3, '');
                    $sheet->getStyle($cell_range)->applyFromArray($style_two);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $cell_range = 'K'.$rowInc_3.':L'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('K'.$rowInc_3, '');
                    $sheet->getStyle($cell_range)->applyFromArray($style_two);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $cell_range = 'M'.$rowInc_3.':T'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('M'.$rowInc_3, 'Total Overhead Cost Amount');
                    $sheet->getStyle($cell_range)->applyFromArray($style_two);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $cell_range = 'U'.$rowInc_3.':V'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('U'.$rowInc_3, '');
                    $sheet->getStyle($cell_range)->applyFromArray($style_two);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $cell_range = 'W'.$rowInc_3.':X'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('W'.$rowInc_3, '');
                    $sheet->getStyle($cell_range)->applyFromArray($style_three);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $cell_range = 'Y'.$rowInc_3.':Z'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('Y'.$rowInc_3, '');
                    $sheet->getStyle($cell_range)->applyFromArray($style_three);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $cell_range = 'AA'.$rowInc_3.':AB'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('AA'.$rowInc_3, '');
                    $sheet->getStyle($cell_range)->applyFromArray($style_three);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $cell_range = 'AC'.$rowInc_3.':AD'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('AC'.$rowInc_3, number_format($sub_task_total_overhead_cost_amount, 2));
                    $sheet->getStyle($cell_range)->applyFromArray($style_three);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $rowInc_3 = $rowInc_3 + 2;
                    $rowInc_4 = $rowInc_4 + 2;


                }else{

                    // $cell_range = 'A'.$rowInc_3.':A'.$rowInc_4;
                    // $sheet->mergeCells($cell_range);
                    // $sheet->setCellValue('A'.$rowInc_3, '');
                    // $sheet->getStyle($cell_range)->applyFromArray($style_one);
                    // $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    // $task_subtask_label = ($taskKey+1) . '.' . ($subtaskKey+1);
                    // $sub_task_name = $subtaskValue->sub_task_name;

                    // $cell_range = 'B'.$rowInc_3.':B'.$rowInc_4;
                    // $sheet->mergeCells($cell_range);
                    // $sheet->setCellValue('B'.$rowInc_3, $task_subtask_label . '  {/}  ');
                    // $sheet->getStyle($cell_range)->applyFromArray($style_one);
                    // $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    // $cell_range = 'C'.$rowInc_3.':J'.$rowInc_4;
                    // $sheet->mergeCells($cell_range);
                    // $sheet->setCellValue('C'.$rowInc_3, $sub_task_name);
                    // $sheet->getStyle($cell_range)->applyFromArray($style_two);
                    // $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    // $cell_range = 'K'.$rowInc_3.':L'.$rowInc_4;
                    // $sheet->mergeCells($cell_range);
                    // $sheet->setCellValue('K'.$rowInc_3, '');
                    // $sheet->getStyle($cell_range)->applyFromArray($style_two);
                    // $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    // $cell_range = 'M'.$rowInc_3.':T'.$rowInc_4;
                    // $sheet->mergeCells($cell_range);
                    // $sheet->setCellValue('M'.$rowInc_3, '');
                    // $sheet->getStyle($cell_range)->applyFromArray($style_two);
                    // $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    // $cell_range = 'U'.$rowInc_3.':V'.$rowInc_4;
                    // $sheet->mergeCells($cell_range);
                    // $sheet->setCellValue('V'.$rowInc_3, '');
                    // $sheet->getStyle($cell_range)->applyFromArray($style_two);
                    // $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    // $cell_range = 'W'.$rowInc_3.':X'.$rowInc_4;
                    // $sheet->mergeCells($cell_range);
                    // $sheet->setCellValue('W'.$rowInc_3, number_format(0));
                    // $sheet->getStyle($cell_range)->applyFromArray($style_three);
                    // $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    // $cell_range = 'Y'.$rowInc_3.':Z'.$rowInc_4;
                    // $sheet->mergeCells($cell_range);
                    // $sheet->setCellValue('Y'.$rowInc_3, '');
                    // $sheet->getStyle($cell_range)->applyFromArray($style_three);
                    // $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    // $cell_range = 'AA'.$rowInc_3.':AB'.$rowInc_4;
                    // $sheet->mergeCells($cell_range);
                    // $sheet->setCellValue('AA'.$rowInc_3, number_format(0));
                    // $sheet->getStyle($cell_range)->applyFromArray($style_three);
                    // $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    // $rowInc_3 = $rowInc_3 + 2;
                    // $rowInc_4 = $rowInc_4 + 2;
                }

                if( $print_duplicate_flag == FALSE ){

                    $cell_range = 'A'.$rowInc_3.':A'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('A'.$rowInc_3, '');
                    $sheet->getStyle($cell_range)->applyFromArray($style_one);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $task_subtask_label = ($taskKey+1) . '.' . ($subtaskKey+1);
                    $sub_task_name = $subtaskValue->sub_task_name;

                    $cell_range = 'B'.$rowInc_3.':B'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('B'.$rowInc_3, $task_subtask_label);
                    $sheet->getStyle($cell_range)->applyFromArray($style_one);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $cell_range = 'C'.$rowInc_3.':J'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('C'.$rowInc_3, $sub_task_name);
                    $sheet->getStyle($cell_range)->applyFromArray($style_two);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $cell_range = 'K'.$rowInc_3.':L'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('K'.$rowInc_3, '');
                    $sheet->getStyle($cell_range)->applyFromArray($style_two);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $cell_range = 'M'.$rowInc_3.':T'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('M'.$rowInc_3, '');
                    $sheet->getStyle($cell_range)->applyFromArray($style_two);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $cell_range = 'U'.$rowInc_3.':V'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('V'.$rowInc_3, '');
                    $sheet->getStyle($cell_range)->applyFromArray($style_two);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $cell_range = 'W'.$rowInc_3.':X'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('W'.$rowInc_3, number_format(0));
                    $sheet->getStyle($cell_range)->applyFromArray($style_three);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $cell_range = 'Y'.$rowInc_3.':Z'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('Y'.$rowInc_3, '');
                    $sheet->getStyle($cell_range)->applyFromArray($style_three);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $cell_range = 'AA'.$rowInc_3.':AB'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('AA'.$rowInc_3, number_format(0));
                    $sheet->getStyle($cell_range)->applyFromArray($style_three);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $cell_range = 'AC'.$rowInc_3.':AD'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('AC'.$rowInc_3, number_format(0, 2));
                    $sheet->getStyle($cell_range)->applyFromArray($style_three);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $rowInc_3 = $rowInc_3 + 2;
                    $rowInc_4 = $rowInc_4 + 2;
                    //$print_duplicate_flag = 1;
                }

                $grand_total = $grand_total + ($sub_task_total_material_amount + $sub_task_total_labour_amount + $sub_task_total_overhead_cost_amount);

            }

            $rowInc_1 = $rowInc_3;
            $rowInc_2 = $rowInc_4;
        }

        // Total
        $sheet->mergeCells('A'.$rowInc_1.':AB'.$rowInc_2);
        $sheet->getStyle('A'.$rowInc_1.':AB'.$rowInc_2)->applyFromArray($style);
		$sheet->getStyle('A'.$rowInc_1.':AB'.$rowInc_2)->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('A'.$rowInc_1.':AB'.$rowInc_2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('A'.$rowInc_1, 'Total');

        $sheet->mergeCells('AC'.$rowInc_1.':AD'.$rowInc_2);
        $sheet->getStyle('AC'.$rowInc_1.':AD'.$rowInc_2)->applyFromArray($style_four);
		$sheet->getStyle('AC'.$rowInc_1.':AD'.$rowInc_2)->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('AC'.$rowInc_1.':AD'.$rowInc_2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('AC'.$rowInc_1, number_format($grand_total, 2));

        $writer = new Xlsx($spreadsheet);
		$filename = 'site-operation-detail-report';

		header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output'); // download file
    }

}
