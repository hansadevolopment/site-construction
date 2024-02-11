<?php

namespace App\Http\Controllers\SiteMM\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App;

use App\Models\SiteMM\Master\Site;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Carbon;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SiteController extends Controller {

    public function loadView(){

        $data['attributes'] = $this->getSiteAttributes(NULL, NULL);

        return view('SiteMM.Master.site')->with('Site', $data);
    }

    private function getSiteAttributes($process, $request){

        $attributes['site_id'] = '#Auto#';
        $attributes['site_name'] = '';
        $attributes['address'] = '';
        $attributes['contact_numbers'] = '';
        $attributes['email'] = '';
        $attributes['chief_engineer'] = '';
        $attributes['active'] = 1;
        $attributes['remark'] = '';

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        if( ($process['validation_result'] == TRUE) && ($process['process_status'] == TRUE)){

            $elqSite = Site::where('site_id', $process['site_id'])->first();
            if($elqSite->count() >= 1) {

                $attributes['site_id'] = $elqSite->site_id;
                $attributes['site_name'] = $elqSite->site_name;
                $attributes['address'] = $elqSite->address;
                $attributes['contact_numbers'] = $elqSite->contact_numbers;
                $attributes['email'] = $elqSite->email;
                $attributes['chief_engineer'] = $elqSite->chief_engineer;
                $attributes['active'] = $elqSite->active;
                $attributes['remark'] = $elqSite->remark;
            }

            $attributes['validation_messages'] = $process['validation_messages'];

            if( $process['front_end_message'] == "Open" ){

                $message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
                $attributes['process_message'] = '';

            }else{

                $message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
                $attributes['process_message'] = '<div class="alert alert-success" role="alert"> '. $message .' </div> ';
            }

        }else{

            $inputs = $request->input();
            if(is_null($inputs) == FALSE){

                $attributes['site_id'] = $inputs['site_id'];
                $attributes['site_name'] = $inputs['site_name'];
                $attributes['address'] = $inputs['address'];
                $attributes['contact_numbers'] = $inputs['contact_numbers'];
                $attributes['email'] = $inputs['email'];
                $attributes['chief_engineer'] = $inputs['chief_engineer'];
                $attributes['active'] = $inputs['active'];
                $attributes['remark'] = $inputs['remark'];
            }

            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';
        }

        return $attributes;
    }

    public function processSite(Request $request){

        if( $request->submit == 'Reset' ){

            $data['attributes'] = $this->getSiteAttributes(NULL, NULL);
        }

        if( $request->submit == 'Save' ){

            $site_validation_result = $this->validateSite($request);
            if($site_validation_result['validation_result'] == TRUE){

                $saving_process_result = $this->saveSite($request);

                $saving_process_result['validation_result'] = $site_validation_result['validation_result'];
                $saving_process_result['validation_messages'] = $site_validation_result['validation_messages'];

                $data['attributes'] = $this->getSiteAttributes($saving_process_result, $request);

            }else{

                $site_validation_result['item_id'] = $request->item_id;
                $site_validation_result['process_status'] = FALSE;

                $data['attributes'] = $this->getSiteAttributes($site_validation_result, $request);
            }
        }

        return view('SiteMM.Master.site')->with('Site', $data);
    }

    private function validateSite($request){

        //try{

            $inputs['site_id'] = $request->site_id;
            $inputs['site_name'] = $request->site_name;
            $inputs['address'] = $request->address;
            $inputs['contact_numbers'] = $request->contact_numbers;
            $inputs['email'] = $request->email;
            $inputs['chief_engineer'] = $request->chief_engineer;
            $inputs['remark'] = $request->remark;

            $rules['site_id'] = array('required');
            $rules['site_name'] = array('required', 'max:50');
            $rules['address'] = array('required', 'max:50');
            $rules['contact_numbers'] = array('required', 'max:50');
            $rules['email'] = array('required', 'email');
            $rules['chief_engineer'] = array('required', 'max:75');
            $rules['remark'] = array( 'max:100');

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

    private function saveSite($request){

        //try{

            $objSite = new Site();

            $site['site'] = $this->getSiteArray($request);
            $saving_process_result = $objSite->saveSite($site);

            return $saving_process_result;

        // }catch(\Exception $e){

        //     $process_result['site_id'] = $request->site_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'Site Controller -> Site Saving Process <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

    private function getSiteArray($request){

        $site['site_id'] = $request->site_id;
        $site['site_name'] = $request->site_name;
        $site['address'] = $request->address;
        $site['contact_numbers'] = $request->contact_numbers;
        $site['email'] = $request->email;
        $site['chief_engineer'] = $request->chief_engineer;
        $site['active'] = $request->active;
        $site['remark'] = $request->remark;

        if( Site::where('site_id', $request->site_id)->exists() ){

            $site['updated_by'] = Auth::id();
            $site['updated_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }else{

            $site['saved_by'] = Auth::id();
            $site['saved_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        return $site;
    }

    public function getSiteWiseTask(Request $request){

        $return_text = '';
        $result = Site::where('site_id', $request->site_id)->first();

        foreach($result->task as $task_key => $task_value){

            $return_text .= " <option value = '". $task_value->task_id ."'>". $task_value->task_name ."</option> ";
        }
        $return_text .= " <option value = '0' selected> Select the Task </option> ";

        return  $return_text ;
    }

    public function openSite(Request $request){

        $process_result['site_id'] = $request->open_site_id;
        $process_result['validation_result'] = TRUE;
        $process_result['process_status'] = TRUE;
        $process_result['validation_messages'] =  new MessageBag();
        $process_result['front_end_message'] = 'Open';
        $process_result['back_end_message'] =  '';

        $data['attributes'] = $this->getSiteAttributes($process_result, $request);

        return view('SiteMM.Master.site')->with('Site', $data);
    }

    public function getExcel(){

        // $spreadsheet = new Spreadsheet();
        // $activeWorksheet = $spreadsheet->getActiveSheet();
        // $activeWorksheet->setCellValue('A1', 'Hello World !');

        // //$writer = new Xlsx($spreadsheet);
        // //$writer->save('hello world.xlsx');


		// $writer = new Xlsx($spreadsheet);
		// $filename = 'Breakdown_Report';

		// header('Content-Type: application/vnd.ms-excel');
        // header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"');
        // header('Cache-Control: max-age=0');

        // $writer->save('php://output'); // download file

        $html_code ="
                        <!DOCTYPE html>
                        <html>
                            <head>
                                <title>Page Title</title>
                            </head>
                        <body>

                            <h1>This is a Heading</h1>
                            <p>This is a paragraph.</p>

                        </body>
                        </html>   ";

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($html_code);
        $pdf->setPaper('A4', 'portrait');

		return $pdf->download('test.pdf');



    }

}
