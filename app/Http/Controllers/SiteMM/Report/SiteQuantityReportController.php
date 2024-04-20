<?php

namespace App\Http\Controllers\SiteMM\Report;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

use App\Models\SiteMM\Master\Site;
use App\Models\SiteMM\Master\Item;
use App\Models\SiteMM\SiteForcast\SiteMaterials;
use App\Services\SiteMM\SiteOperation\MaterialService;
use App\Services\SiteMM\SiteOperation\DprService;
use App\Services\SiteMM\SiteForcast\SapMaterialService;

use App\Helpers\Database\EloquentHelper;

use App\Rules\ZeroValidation;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;

class SiteQuantityReportController extends Controller {

    public function loadView(){

        $data['site'] = Site::where('active', 1)->get();
        $data['site_task'] = array();
        $data['site_sub_task'] = array();
        $data['attributes'] = $this->getSapReportAttributes(NULL, NULL);

        return view('SiteMM.Report.sq_report')->with('SQR', $data);
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

    public function sqReport(Request $request){

        $site_validation_result = $this->validateReport($request);
        if($site_validation_result['validation_result'] == TRUE){

            $this->prepareQuantityReport($request);

        }else{

            $data['site'] = Site::where('active', 1)->get();
            $data['site_task'] = array();
            $data['site_sub_task'] = array();
            $data['attributes'] = $this->getSapReportAttributes($site_validation_result, $request);

            return view('SiteMM.Report.sq_report')->with('SQR', $data);

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


    private function prepareQuantityReport($request){

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

		$sheet->mergeCells('A1:Z3');
		$sheet->getStyle('A1:Z3')->applyFromArray($style);
		$sheet->getStyle('A1:Z3')->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('A1:Z3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
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

        $sheet->mergeCells('K4:R5');
        $sheet->getStyle('K4:R5')->applyFromArray($style);
		$sheet->getStyle('K4:R5')->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('K4:R5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('K4', "Item");
        $sheet->getStyle('K4')->getAlignment()->setWrapText(true);

        $sheet->mergeCells('S4:T5');
        $sheet->getStyle('S4:T5')->applyFromArray($style);
		$sheet->getStyle('S4:T5')->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('S4:T5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('S4', "Budget\nQty");
        $sheet->getStyle('S4')->getAlignment()->setWrapText(true);

        $sheet->mergeCells('U4:V5');
        $sheet->getStyle('U4:V5')->applyFromArray($style);
		$sheet->getStyle('U4:V5')->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('U4:V5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('U4', "Issue\nQty");
        $sheet->getStyle('U4')->getAlignment()->setWrapText(true);

        $sheet->mergeCells('W4:X5');
        $sheet->getStyle('W4:X5')->applyFromArray($style);
		$sheet->getStyle('W4:X5')->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('W4:X5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('W4', "Used\nQty");
        $sheet->getStyle('W4')->getAlignment()->setWrapText(true);

        $sheet->mergeCells('Y4:Z5');
        $sheet->getStyle('Y4:Z5')->applyFromArray($style);
		$sheet->getStyle('Y4:Z5')->getBorders()->applyFromArray($border_styleArray);
		$sheet->getStyle('Y4:Z5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF99');
        $sheet->setCellValue('Y4', "Balance\nQty");
        $sheet->getStyle('Y4')->getAlignment()->setWrapText(true);

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


        $elqSapMeterial = SiteMaterials::where('site_id', $site_id)->orderBy('task_id', 'asc')->orderBy('sub_task_id', 'asc')->get();

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

            $cell_range = 'K'.$rowInc_1.':Z'.$rowInc_2;
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
                $objSapMaterialService = new SapMaterialService();
                $sap_meterial_result = $objSapMaterialService->getSapMaterialDetail($site_id, $task_id, $sub_task_id);

                if( EloquentHelper::recordsExists($sap_meterial_result) ){

                    // Meterials
                    foreach($sap_meterial_result as $meterial_key => $meterial_value){

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

                        $item_name = Item::where('item_id', $meterial_value->item_id)->value('item_name');

                        $cell_range = 'K'.$rowInc_3.':R'.$rowInc_4;
                        $sheet->mergeCells($cell_range);
                        $sheet->setCellValue('K'.$rowInc_3, $item_name);
                        $sheet->getStyle($cell_range)->applyFromArray($style_two);
                        $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                        $cell_range = 'S'.$rowInc_3.':T'.$rowInc_4;
                        $sheet->mergeCells($cell_range);
                        $sheet->setCellValue('S'.$rowInc_3, $meterial_value->quantity);
                        $sheet->getStyle($cell_range)->applyFromArray($style_three);
                        $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                        $issued_item = MaterialService::getMaterialDetailItemWise($site_id, $task_id, $sub_task_id, $meterial_value->item_id);

                        $cell_range = 'U'.$rowInc_3.':V'.$rowInc_4;
                        $sheet->mergeCells($cell_range);
                        $sheet->setCellValue('U'.$rowInc_3, $issued_item);
                        $sheet->getStyle($cell_range)->applyFromArray($style_three);
                        $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                        $used_quantity = DprService::getDprQuantityItemWise($site_id, $task_id, $sub_task_id, $meterial_value->item_id);

                        $cell_range = 'W'.$rowInc_3.':X'.$rowInc_4;
                        $sheet->mergeCells($cell_range);
                        $sheet->setCellValue('W'.$rowInc_3, $used_quantity);
                        $sheet->getStyle($cell_range)->applyFromArray($style_three);
                        $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                        $balance_quantity = ($issued_item - $used_quantity);

                        $cell_range = 'Y'.$rowInc_3.':Z'.$rowInc_4;
                        $sheet->mergeCells($cell_range);
                        $sheet->setCellValue('Y'.$rowInc_3, $balance_quantity);
                        $sheet->getStyle($cell_range)->applyFromArray($style_three);
                        $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                        $sub_task_total_material_amount += $meterial_value->amount;
                        $print_duplicate_flag = TRUE;

                        $rowInc_3 = $rowInc_3 + 2;
                        $rowInc_4 = $rowInc_4 + 2;
                    }

                }else{

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

                    $cell_range = 'K'.$rowInc_3.':R'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('K'.$rowInc_3, '');
                    $sheet->getStyle($cell_range)->applyFromArray($style_two);
                    $sheet->getStyle($cell_range)->getBorders()->applyFromArray($border_styleArray);

                    $cell_range = 'S'.$rowInc_3.':T'.$rowInc_4;
                    $sheet->mergeCells($cell_range);
                    $sheet->setCellValue('T'.$rowInc_3, '');
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

                    $rowInc_3 = $rowInc_3 + 2;
                    $rowInc_4 = $rowInc_4 + 2;
                    //$print_duplicate_flag = 1;
                }

                $grand_total = $grand_total + ($sub_task_total_material_amount + $sub_task_total_labour_amount + $sub_task_total_overhead_cost_amount);

            }

            $rowInc_1 = $rowInc_3;
            $rowInc_2 = $rowInc_4;
        }

        $writer = new Xlsx($spreadsheet);
		$filename = 'Site-Operation-Quantity-Report';

		header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output'); // download file

    }

}
