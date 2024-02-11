<?php

namespace App\Http\Controllers\SiteMM\SiteForcast;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SiteMM\Master\Site;
use App\Models\SiteMM\Master\Item;
use App\Models\SiteMM\Master\Unit;
use App\Models\SiteMM\SiteForcast\SiteTask;
use App\Models\SiteMM\SiteForcast\SiteSubTask;
use App\Models\SiteMM\SiteForcast\SiteMaterials;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Carbon;

use App\Rules\ZeroValidation;
use App\Rules\QuantityValidation;

class SiteMaterialsController extends Controller {

    public function loadView(){

        $data['site'] = Site::where('active', 1)->get();
        $data['item'] = Item::where('active', 1)->get();
        $data['site_task'] = array();       // SiteTask::where('active', 1)->get();
        $data['site_sub_task'] = array();   //SiteSubTask::where('active', 1)->get();
        $data['attributes'] = $this->getSiteActionPlanMaterialAttributes(NULL, NULL);

        return view('SiteMM.SiteForcast.sap_material')->with('sap_material', $data);
    }

    private function getSiteActionPlanMaterialAttributes($process, $request){

        $attributes['site_id'] = '0';
        $attributes['task_id'] = '0';
        $attributes['sub_task_id'] = '0';
        $attributes['remark'] = '';
        $attributes['material_detail'] = array();
        $attributes['material_total'] = 0;

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        $material_total = 0;
        $sap_material_detail = SiteMaterials::where('site_id', $request->site_id)
                                            ->where('task_id', $request->task_id)
                                            ->where('sub_task_id', $request->sub_task_id)
                                            ->orderBy('updated_at', 'desc')
                                            ->get();
        foreach($sap_material_detail as $key => $value){

            $unit_id = Item::where('item_id', $value->item_id)->value('unit_id');

            $value->ono = ($key+1);
            $value->unit_name = Unit::where('unit_id', $unit_id)->value('unit_name');
            $value->item_name = Item::where('item_id', $value->item_id)->value('item_name');
            $material_total = $material_total + $value->amount;
        }
        $attributes['material_detail'] = $sap_material_detail;
        $attributes['material_total'] = $material_total;


        $inputs = $request->input();
        if(is_null($inputs) == FALSE){

            $attributes['site_id'] = $inputs['site_id'];
            $attributes['task_id'] = $inputs['task_id'];
            $attributes['sub_task_id'] = $inputs['sub_task_id'];
            $attributes['item_id'] = $inputs['item_id'];
            $attributes['unit'] = $inputs['unit'];
            $attributes['price'] = $inputs['price'];
            $attributes['remark'] = $inputs['remark'];
        }

        if( ($process['validation_result'] == TRUE) && ($process['process_status'] == TRUE)){

            $attributes['validation_messages'] = $process['validation_messages'];

            if($request->submit == 'Display'){

                $attributes['validation_messages'] = new MessageBag();
                $attributes['process_message'] = "";
            }else{

                $message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
                $attributes['process_message'] = '<div class="alert alert-success" role="alert"> '. $message .' </div> ';
            }


        }else{

            $attributes['validation_messages'] = $process['validation_messages'];

			$message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .' </div> ';
        }

        return $attributes;
    }

    public function addSapMaterial(Request $request){

        if($request->submit == 'Display'){

            $process_result = [
                                    "process_status" => true,
                                    "front_end_message" => "Saving Process is Completed successfully.",
                                    "back_end_message" => "",
                                    "validation_result" => true,
                                    "validation_messages" => array()
                              ];

            $data['attributes'] = $this->getSiteActionPlanMaterialAttributes($process_result, $request);
        }

        if($request->submit == 'Add'){

            $site_validation_result = $this->validateItemsForSapMaterial($request);

            if($site_validation_result['validation_result'] == TRUE){

                $saving_process_result = $this->saveSapMaterial($request);

                $saving_process_result['validation_result'] = $site_validation_result['validation_result'];
                $saving_process_result['validation_messages'] = $site_validation_result['validation_messages'];

                $data['attributes'] = $this->getSiteActionPlanMaterialAttributes($saving_process_result, $request);

            }else{

                $site_validation_result['process_status'] = FALSE;

                $data['attributes'] = $this->getSiteActionPlanMaterialAttributes($site_validation_result, $request);
            }
        }

        $data['site'] = Site::where('active', 1)->get();
        $data['item'] = Item::where('active', 1)->get();
        $data['site_task'] = SiteTask::where('active', 1)->where('site_id', $request->site_id)->get();
        $data['site_sub_task'] = SiteSubTask::where('active', 1)->where('task_id', $request->task_id)->get();

        return view('SiteMM.SiteForcast.sap_material')->with('sap_material', $data);

    }

    private function validateItemsForSapMaterial($request){

        //try{

            $inputs['task_id'] = $request->task_id;
            $inputs['site_id'] = $request->site_id;
            $inputs['sub_task_id'] = $request->sub_task_id;
            $inputs['item_id'] = $request->item_id;
            $inputs['quantity'] = $request->quantity;
            $inputs['status_id'] = $request->status_id;
            $inputs['remark'] = $request->remark;

            $rules['site_id'] = array( new ZeroValidation('Site', $request->site_id));
            $rules['task_id'] = array( new ZeroValidation('Task', $request->task_id));
            $rules['sub_task_id'] = array( new ZeroValidation('Sub Task', $request->sub_task_id));
            $rules['item_id'] = array( new ZeroValidation('Item', $request->item_id));
            $rules['quantity'] = array('required', 'numeric', new QuantityValidation());
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

    private function saveSapMaterial($request){

        //try{

            $objSiteMaterials = new SiteMaterials();

            $sap_material['sap_material'] = $this->getSapMaterialArray($request);
            $saving_process_result = $objSiteMaterials->saveSiteMaterials($sap_material);

            return $saving_process_result;

        // }catch(\Exception $e){

        //     $process_result['site_id'] = $request->site_id;
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'Site Controller -> Site Saving Process <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }

    private function getSapMaterialArray($request){

        $sap_material['site_id'] = $request->site_id;
        $sap_material['task_id'] = $request->task_id;
        $sap_material['sub_task_id'] = $request->sub_task_id;
        $sap_material['item_id'] = $request->item_id;
        $sap_material['price'] = str_replace(",","",$request->price);
        $sap_material['quantity'] = $request->quantity;
        $sap_material['amount'] = floatval(str_replace(",","",$request->price)) * $request->quantity;
        $sap_material['remark'] = $request->remark;

        $upsert_flag = SiteMaterials::where('site_id', $request->site_id)->where('task_id', $request->task_id)
                                    ->where('sub_task_id', $request->sub_task_id)->where('item_id', $request->item_id)
                                    ->exists();
        if( $upsert_flag ){

            $sap_material['updated_by'] = Auth::id();
            $sap_material['updated_on'] = Carbon::now()->format('Y-m-d H:i:s');

        }else{

            $sap_material['saved_by'] = Auth::id();
            $sap_material['saved_on'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        //dd($request->all());

        return $sap_material;
    }

    public function openSapMaterial(Request $request){

        $material_total = 0;
        $site_id = 0;
        $task_id = 0;

        $sap_material_detail = SiteMaterials::where('sap_material_id', $request->open_sap_material_id)->get();
        foreach($sap_material_detail as $key => $value){

            $unit_id = Item::where('item_id', $value->item_id)->value('unit_id');

            $value->ono = ($key+1);
            $value->unit_name = Unit::where('unit_id', $unit_id)->value('unit_name');
            $value->item_name = Item::where('item_id', $value->item_id)->value('item_name');

            $material_total = $material_total + $value->amount;

            $site_id = $value->site_id;;
            $task_id = $value->task_id;

            $attributes['site_id'] = $value->site_id;
            $attributes['task_id'] = $value->task_id;
            $attributes['sub_task_id'] = $value->sub_task_id;
            $attributes['item_id'] = $value->item_id;
            $attributes['unit'] = $value->getItem()->getUnit()->unit_name;
            $attributes['price'] = $value->price;
            $attributes['remark'] = $value->remark;
            $attributes['material_total'] = $material_total;
        }

        $attributes['material_detail'] = $sap_material_detail;
        $attributes['validation_messages'] = new MessageBag();
        $attributes['process_message'] = "";

        $data['attributes'] =  $attributes;
        $data['site'] = Site::where('active', 1)->get();
        $data['item'] = Item::where('active', 1)->get();
        $data['site_task'] = SiteTask::where('active', 1)->where('site_id', $site_id)->get();
        $data['site_sub_task'] = SiteSubTask::where('active', 1)->where('task_id', $task_id)->get();

        return view('SiteMM.SiteForcast.sap_material')->with('sap_material', $data);
    }

}
