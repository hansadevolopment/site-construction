<?php

namespace App\Http\Controllers\GL\ListInquiry;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;

class TransactionListInquireController extends Controller{

    public function loadView(){

        $data['transaction_data'] = DB::table('gl_transaction_item')->get();
        $data['attributes'] = $this->getTransactionListInquireAttributes(NULL, NULL);

        return view('GL.list.transaction_inquire')->with('TI', $data);
    }

    private function getTransactionListInquireAttributes($process, $request){

        $attributes['gti_id'] = 0;
        $attributes['source_name'] = '';
        $attributes['table_detail'] = array();

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

    }

    public function processTransactionListInquire(Request $request){

        if($request->gti_id == 1){

            $journal_entry_detail =  DB::table('journal_entry')->get();
            $collection = $journal_entry_detail->each(function($item, $key){

                $item->source_id = $item->je_id;
                $item->source_name = $item->remark;
            });

            $attributes['source_name'] = 'Remark';
            $attributes['table_detail'] = $collection;
        }

        $attributes['gti_id'] = $request->gti_id;
        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        $data['transaction_data'] = DB::table('gl_transaction_item')->get();
        $data['attributes'] = $attributes;
        return view('GL.list.transaction_inquire')->with('TI', $data);

    }

}
