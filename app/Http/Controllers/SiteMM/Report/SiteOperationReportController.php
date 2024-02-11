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
    }

    public function soReport(Request $request){


    }


}
