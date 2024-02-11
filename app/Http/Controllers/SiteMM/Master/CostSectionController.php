<?php

namespace App\Http\Controllers\SiteMM\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SiteMM\Master\Employee;
use App\Models\SiteMM\Master\Item;
use App\Models\SiteMM\Master\OverheadCostItem;

class CostSectionController extends Controller {

    public function getCostSectionItem(Request $request){

        $return_text = '';

        if( $request->cs_id == 1 ){

            $elqItem = Item::where('active', 1)->get();

            foreach($elqItem as $item_key => $item_value){

                $return_text .= " <option value = '". $item_value->item_id ."'>". $item_value->item_name ."</option> ";
            }
            $return_text .= " <option value = '0' selected> Select the Item </option> ";

            return  $return_text ;
        }

        if( $request->cs_id == 3 ){

            $elqOverheadCostItem = OverheadCostItem::where('active', 1)->get();

            foreach($elqOverheadCostItem as $oci_key => $oci_value){

                $return_text .= " <option value = '". $oci_value->oci_id ."'>". $oci_value->oci_name ."</option> ";
            }
            $return_text .= " <option value = '0' selected> Select the Item </option> ";

            return  $return_text ;
        }

    }


}
