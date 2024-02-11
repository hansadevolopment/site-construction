<?php

namespace App\Http\Controllers\SiteMM\InquiryList;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use \stdClass;

use App\Models\SiteMM\Master\Employee;
use App\Models\SiteMM\Master\OverheadCostItem;
use App\Models\SiteMM\Master\Item;
use App\Models\SiteMM\Master\Unit;
use App\Models\SiteMM\Master\CostSection;
use App\Models\SiteMM\Master\LabourCategory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Carbon;

class MasterInquiryController extends Controller {

    public function loadView(){

        $data['source_name'] = '';
        $data['stsil_detail'] = array();
        $data['attributes'] = $this->getMasterAttributes(NULL, NULL);

        return view('SiteMM.InquiryList.master_inquire')->with('stsil', $data);
    }

    private function getMasterAttributes($process, $request){

        $attributes['master_id'] = '0';
        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        $attributes['master_id'] = $request->master_id;
        return $attributes;
    }

    public function inquireMaster(Request $request){

        if( $request->master_id == 1 ){

            $data['source_name'] = 'Item';
            $data['stsil_detail'] = Item::get();
        }

        if( $request->master_id == 2 ){

            $data['source_name'] = 'Employee';
            $data['stsil_detail'] = Employee::get();
        }

        if( $request->master_id == 3 ){

            $data['source_name'] = 'Labour Category';
            $data['stsil_detail'] = LabourCategory::get();
        }

        if( $request->master_id == 4 ){

            $data['source_name'] = 'Overhead Cost';
            $data['stsil_detail'] = OverheadCostItem::get();
        }

        if( $request->master_id == 5 ){

            $data['source_name'] = 'Unit';
            $data['stsil_detail'] = Unit::get();
        }

        if( $request->master_id == 0 ){

            $data['source_name'] = '';
            $data['stsil_detail'] = array();
        }

        $data['attributes'] = $this->getMasterAttributes(NULL, $request);

        return view('SiteMM.InquiryList.master_inquire')->with('stsil', $data);

    }


}
