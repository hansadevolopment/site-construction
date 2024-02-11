<?php

namespace App\Http\Controllers\SiteMM\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SiteMM\Master\Site;
use App\Models\SiteMM\SiteForcast\SiteMaterials;
use App\Models\SiteMM\SiteForcast\SiteLabour;
use App\Models\SiteMM\SiteForcast\SiteOverheadCost;
use App\Models\SiteMM\SiteForcast\SiteProfit;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Carbon;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;

class SapReportController extends Controller {

    public function loadView(){

        $data['site'] = Site::where('active', 1)->get();
        $data['site_task'] = array();
        $data['site_sub_task'] = array();
        $data['attributes'] = $this->getSapReportAttributes(NULL, NULL);

        return view('SiteMM.Report.sap_report')->with('SAPR', $data);
    }

    private function getSapReportAttributes($process, $request){

        $attributes['site_id'] = '0';
        $attributes['task_id'] = '0';
        $attributes['sub_task_id'] = '0';

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }
    }

    public function sapReport(Request $request){

        $site_id = $request->site_id;
        $task_id = 0;
        $sub_task_id = 0;

        $total_meterial_cost = 0;
        $total_labour_cost = 0;
        $total_overhead_cost = 0;
        $grand_cost = 0;
        $total_profit_value = 0;
        $grand_value = 0;

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

		$sheet->mergeCells('A1:V3');
		$sheet->getStyle('A1:V3')->applyFromArray($style);
		$sheet->getStyle('A1:V3')->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('A1:V3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
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

        $sheet->mergeCells('K4:V4');
        $sheet->getStyle('K4:V4')->applyFromArray($style);
		$sheet->getStyle('K4:V4')->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('K4:V4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('K4', 'Budget');

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
        $sheet->setCellValue('S5', "Profit\nValue");
        $sheet->getStyle('S5')->getAlignment()->setWrapText(true);

        $sheet->mergeCells('U5:V6');
        $sheet->getStyle('U5:V6')->applyFromArray($style);
		$sheet->getStyle('U5:V6')->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('U5:V6')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('U5', "Total\nValue");
        $sheet->getStyle('U5')->getAlignment()->setWrapText(true);

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

            $cell_range = 'K'.$rowInc_1.':V'.$rowInc_2;
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

                $meterial_cost = number_format($meterial_cost, 2);
                $labour_cost = number_format($labour_cost, 2);
                $overhead_cost = number_format($overhead_cost, 2);
                $total_cost = number_format($total_cost, 2);
                $total_value = number_format($total_value, 2);

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
                $sheet->setCellValue('S'.$rowInc_3, $profit_value);
                $sheet->getStyle($cell_range)->applyFromArray($style_three);
                $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                $cell_range = 'U'.$rowInc_3.':V'.$rowInc_4;
                $sheet->mergeCells($cell_range);
                $sheet->setCellValue('U'.$rowInc_3, $total_value);
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
        $sheet->setCellValue('S'.$rowInc_1, number_format($total_profit_value, 2));

        $sheet->mergeCells('U'.$rowInc_1.':V'.$rowInc_2);
        $sheet->getStyle('U'.$rowInc_1.':V'.$rowInc_2)->applyFromArray($style_four);
		$sheet->getStyle('U'.$rowInc_1.':V'.$rowInc_2)->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('U'.$rowInc_1.':V'.$rowInc_2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('U'.$rowInc_1, number_format($grand_value, 2));

        $writer = new Xlsx($spreadsheet);
		$filename = 'Site-Action-Plan-Report';

		header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output'); // download file

    }

}
