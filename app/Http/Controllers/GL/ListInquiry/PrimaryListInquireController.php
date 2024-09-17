<?php

namespace App\Http\Controllers\GL\ListInquiry;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\MessageBag;

class PrimaryListInquireController extends Controller{

    public function loadView(){

        $data['primary_data'] = DB::table('gl_primary_item')->get();
        $data['attributes'] = $this->getPrimaryListInquireAttributes(NULL, NULL);

        return view('GL.list.primary_inquire')->with('PI', $data);
    }

    private function getPrimaryListInquireAttributes($process, $request){

        $attributes['gpi_id'] = 0;
        $attributes['source_name'] = '';
        $attributes['table_detail'] = array();

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

    }

    public function processPrimaryListInquire(Request $request){

        if($request->gpi_id == 1){

            $tax_detail =  DB::table('tax')->get();
            $collection = $tax_detail->each(function($item, $key){

                $item->source_id = $item->tax_id;
                $item->source_name = $item->tax_name;
            });

            $attributes['source_name'] = 'Tax Name';
            $attributes['table_detail'] = $collection;
        }

        if($request->gpi_id == 2){

            $bank_detail =  DB::table('bank')->get();
            $collection = $bank_detail->each(function($item, $key){

                $item->source_id = $item->bank_id;
                $item->source_name = $item->bank_name;
            });

            $attributes['source_name'] = 'Bank Name';
            $attributes['table_detail'] = $collection;
        }

        if($request->gpi_id == 3){

            $bank_account_detail =  DB::table('bank_account')->get();
            $collection = $bank_account_detail->each(function($item, $key){

                $item->source_id = $item->ba_id;
                $item->source_name = $item->short_name;
            });

            $attributes['source_name'] = 'Bank Account Name';
            $attributes['table_detail'] = $collection;
        }

        if($request->gpi_id == 4){

            $main_account =  DB::table('main_account')->get();
            $collection = $main_account->each(function($item, $key){

                $item->source_id = $item->ma_id;
                $item->source_name = $item->ma_name;
                $item->active = 1;
            });

            $attributes['source_name'] = 'Main Account Name';
            $attributes['table_detail'] = $collection;
        }

        if($request->gpi_id == 5){

            $controll_account =  DB::table('controll_account')->get();
            $collection = $controll_account->each(function($item, $key){

                $item->source_id = $item->ca_id;
                $item->source_name = $item->ca_name;
                $item->active = 1;
            });

            $attributes['source_name'] = 'Controll Account Name';
            $attributes['table_detail'] = $collection;
        }

        if($request->gpi_id == 6){

            $sub_account =  DB::table('sub_account')->get();
            $collection = $sub_account->each(function($item, $key){

                $item->source_id = $item->sa_id;
                $item->source_name = $item->sa_name;
                $item->active = 1;
            });

            $attributes['source_name'] = 'Sub Account Name';
            $attributes['table_detail'] = $collection;
        }


        $attributes['gpi_id'] = $request->gpi_id;
        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        $data['primary_data'] = DB::table('gl_primary_item')->get();
        $data['attributes'] = $attributes;

        return view('GL.list.primary_inquire')->with('PI', $data);

    }

}
