<?php

namespace App\Http\Controllers\Purchase\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Purchase\Primary\Creditor;
use App\Models\Purchase\Primary\PurchasingCategory;
use App\Models\Purchase\Primary\PurchasingLocation;
use App\Models\Inventory\Primary\ItemMaster;
use App\Models\Purchase\Transaction\GoodReceiveNote;

use App\Rules\ZeroValidation;
use App\Rules\CurrencyValidation;
use App\Rules\Purchasing\Transaction\GRN\GrnGLPostValidation;
use App\Rules\Purchasing\Transaction\GRN\GrnCancelValidation;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\MessageBag;

class GoodReceiveNoteController extends Controller {

    public function loadView(){

        $objCreditor = new Creditor();
        $objPurchasingCategory = new PurchasingCategory();
        $objPurchasingLocation = new PurchasingLocation();
        $objItemMaster = new ItemMaster();

        $data['creditor'] = $objCreditor->getActiveCreditorList();
        $data['purchasing_category'] = $objPurchasingCategory->getActivePurchasingCategoryList();
        $data['purchasing_location'] = $objPurchasingLocation->getActivePurchasingLocationList();
        $data['item'] = $objItemMaster->getActiveItemList();

        $data['attributes'] = $this->getGrnAttributes(NULL, NULL);

        return view('purchase.transaction.good_receive_note')->with('grn', $data);
    }
    
    private function getGrnAttributes($process, $request){

        $attributes['grn_id'] = '#Auto#';
        $attributes['grn_date'] = '';
        $attributes['purchase_order_number'] = '';
        $attributes['purchasing_category_id'] = 0;
        $attributes['purchasing_location_id'] = 0;
        $attributes['creditor_id'] = 0;
        $attributes['remark'] = '';
  
        $attributes['total_gross_amount'] = 0;
        $attributes['total_discount_amount'] = 0;
        $attributes['total_tax_amount'] = 0;
        $attributes['total_net_amount'] = 0;

        $attributes['grn_detail'] = array();

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

        if( ($process['validation_result'] == TRUE) && ($process['process_result'] == TRUE)){

            $grn_id = $process['grn_id'];

            $objGrn = new GoodReceiveNote();

            $grn_table = $objGrn->getGoodReceiveNote($grn_id);
            $grn_detail_table = $objGrn->getGoodReceiveNoteDetail($grn_id);

            foreach ($grn_table as $row) {
                  
                $attributes['grn_id'] = $row->grn_id;
                $attributes['grn_date'] = $row->date;
                $attributes['purchase_order_number'] = $row->purchase_order_number;
                $attributes['creditor_id'] = $row->creditor_id;
                $attributes['purchasing_category_id'] = $row->purchasing_category_id;
                $attributes['purchasing_location_id'] = $row->purchasing_location_id;
                $attributes['remark'] = $row->remark;
    
                $attributes['total_gross_amount'] = $row->total_gross_amount;
                $attributes['total_discount_amount'] = $row->total_discount_amount;
                $attributes['total_tax_amount'] = $row->total_tax_amount;
                $attributes['total_net_amount'] = $row->total_net_amount;
            }

            $attributes['grn_detail'] = $grn_detail_table;

            $attributes['process_message'] = '<div class="alert alert-success" role="alert"> '. $process['front_end_message'] .'. </div> ';
            $attributes['validation_messages'] = new MessageBag();

        }else{

            $inputs = $request->input();
            if(is_null($inputs) == FALSE){
    
                $attributes['grn_id'] = $inputs['grn_id'];
                $attributes['grn_date'] = $inputs['grn_date'];
                $attributes['purchase_order_number'] = $inputs['purchase_order_number'];
                $attributes['creditor_id'] = $inputs['creditor_id'];
                $attributes['purchasing_category_id'] = $inputs['purchasing_category_id'];
                $attributes['purchasing_location_id'] = $inputs['purchasing_location_id'];
                $attributes['remark'] = $inputs['remark'];
    
                $attributes['total_gross_amount'] = $inputs['total_gross_amount'];
                $attributes['total_discount_amount'] = $inputs['total_discount_amount'];
                $attributes['total_tax_amount'] = $inputs['total_tax_amount'];
                $attributes['total_net_amount'] = $inputs['total_net_amount'];
            }

            $message = $process['front_end_message'] .' <br> ' . $process['back_end_message'];
            $attributes['process_message'] = '<div class="alert alert-danger" role="alert"> '. $message .'. </div> ';

            $attributes['validation_messages'] = $process['validation_messages'];
        }
      
        return $attributes;
    }

    public function grnProcess(Request $request){

        if( ($request->submit == 'Save') || ($request->submit == 'GL Post') || ($request->submit == 'Add') ){

            // Saving Validation Process
            $grn_validation_result = $this->savingValidationProcess($request);
            if( $grn_validation_result['validation_result'] == TRUE){

                //Saving Process
                $grn_saving_result = $this->saveGrn($request);
                if($grn_saving_result['process_result'] == TRUE){

                    // GL Post Process Begining
                    if($request->submit == 'GL Post'){ 

                        // GL Post Validation Process
                        $grn_gl_post_validation_result = $this->gl_post_validation_process($request);
                        if($grn_gl_post_validation_result['validation_result'] == TRUE){

                            // GL Post Process
                            $grn_gl_post_result = $this->glPostProcess($request);

                            $grn_gl_post_result['validation_result'] = $grn_gl_post_validation_result['validation_result'];
                            $grn_gl_post_result['validation_messages'] = $grn_gl_post_validation_result['validation_messages'];
    
                            $data['attributes'] = $this->getGrnAttributes($grn_gl_post_result, $request);
                        
                        }else{

                            $grn_gl_post_validation_result['validation_result'] = $grn_gl_post_validation_result['validation_result'];
                            $grn_gl_post_validation_result['validation_messages'] = $grn_gl_post_validation_result['validation_messages'];
    
                            $data['attributes'] = $this->getGrnAttributes($grn_gl_post_validation_result, $request);
                        }

                    }else{

                        $grn_saving_result['validation_result'] = $grn_validation_result['validation_result'];
                        $grn_saving_result['validation_messages'] = $grn_validation_result['validation_messages'];

                        $data['attributes'] = $this->getGrnAttributes($grn_saving_result, $request);
                    }

                }else{

                    $grn_saving_result['validation_result'] = $grn_validation_result['validation_result'];
                    $grn_saving_result['validation_messages'] = $grn_validation_result['validation_messages'];

                    $data['attributes'] = $this->getGrnAttributes($grn_saving_result, $request);
                }
                
            }else{

                $data['attributes'] = $this->getGrnAttributes($grn_validation_result, $request);
            }
        }

        // Grn Cancell
        if($request->submit == 'Cancel'){

            $grn_cancel_validation_result = $this->cancelValidationProcess($request);
            if( $grn_cancel_validation_result['validation_result'] == TRUE){

                $cancel_process_result = $this->cancelGrn($request);
                
                $cancel_process_result['validation_result'] = $grn_cancel_validation_result['validation_result'];
                $cancel_process_result['validation_messages'] = $grn_cancel_validation_result['validation_messages'];

                $data['attributes'] = $this->getGrnAttributes($cancel_process_result, $request);

            }else{

                $data['attributes'] = $this->getGrnAttributes($grn_cancel_validation_result, $request);
            }
        }

         // Grn Reset
        if($request->submit == 'Reset'){

            $data['attributes'] = $this->getGrnAttributes(NULL, NULL);
        }

        // echo '<pre>';
        // print_r($data['attributes']);
        // echo '</pre>';

        $objCreditor = new Creditor();
        $objPurchasingCategory = new PurchasingCategory();
        $objPurchasingLocation = new PurchasingLocation();
        $objItemMaster = new ItemMaster();

        $data['creditor'] = $objCreditor->getActiveCreditorList();
        $data['purchasing_category'] = $objPurchasingCategory->getActivePurchasingCategoryList();
        $data['purchasing_location'] = $objPurchasingLocation->getActivePurchasingLocationList();
        $data['item'] = $objItemMaster->getActiveItemList();

        return view('purchase.transaction.good_receive_note')->with('grn', $data);

    }

    private function savingValidationProcess($request){

        try{

            $front_end_message = '';

            $inputs['grn_id'] = $request->grn_id;
            $inputs['grn_date'] = $request->grn_date;
            $inputs['purchase_order_number'] = $request->purchase_order_number;
            $inputs['creditor_id'] = $request->creditor_id;
            $inputs['purchasing_category_id'] = $request->purchasing_category_id;
            $inputs['purchasing_location_id'] = $request->purchasing_location_id;
            $inputs['remark'] = $request->remark;

            $rules['grn_id'] = array('required', new GrnCancelValidation(), new GrnGLPostValidation());
            $rules['grn_date'] = array('required', 'date');
            $rules['purchase_order_number'] = array('max:10');
            $rules['creditor_id'] = array( new ZeroValidation('Creditor', $request->creditor_id));
            $rules['purchasing_category_id'] = array( new ZeroValidation('Purchasing Category', $request->purchasing_category_id));
            $rules['purchasing_location_id'] =  array( new ZeroValidation('Purchasing Location', $request->purchasing_location_id));
            $rules['remark'] = array('max:100');

            if($request->submit == 'Add'){

                $inputs['item_id'] = $request->item_id;
                $inputs['unit_price'] = $request->unit_price;
                $inputs['quantity'] = $request->quantity;
                $inputs['discount_amount'] = $request->discount_amount;

                $rules['item_id'] =  array( new ZeroValidation('Item', $request->item_id));
                $rules['unit_price'] = array('required', 'numeric', new CurrencyValidation(0));
                $rules['quantity'] = array('required', 'numeric', new CurrencyValidation(0));
                $rules['discount_amount'] = array('required', 'numeric', new CurrencyValidation(1));
            }

            $validator = Validator::make($inputs, $rules);

            $validation_result = $validator->passes();
            if($validation_result == FALSE){

                $front_end_message = 'Please Check Your Inputs';
            }

            $process['grn_id'] = $request->grn_id;
            $process['validation_result'] = $validation_result;
            $process['front_end_message'] = $front_end_message;
            $process['back_end_message'] = '';
            $process['validation_messages'] =  $validator->errors();

            return $process;

        }catch(\Exception $e){ 

            $process['grn_id'] = $request->grn_id;
            $process['validation_result'] = FALSE;
            $process['front_end_message'] =  $e->getMessage();
            $process['back_end_message'] =  'GRN Controller : GRN Validation Process ' . $e->getLine();;
            $process['validation_messages'] = new MessageBag();

            return $process;
        }
    }

    private function saveGrn($request){

        //try{

            $objGrn = new GoodReceiveNote();

            $data['grn'] = $this->getGoodReceiveNoteTable($request);
            $data['grn_detail'] = NULL;
            $data['grn_tax_detail'] = NULL;

            if($request->submit == 'Add'){

                $data['grn_detail'] = $this->getGoodReceiveNoteDetailTable($request);
                $data['grn_tax_detail'] = $this->getGoodReceiveNoteTaxDetailTable($request);
            }

            // Call To Model
            $grn_saving_process_result = $objGrn->grnSavingProcess($data);

            return $grn_saving_process_result;

        // }catch(\Exception $e){

        //     $process['grn_id'] = $request->grn_id;
        //     $process['process_result'] = FALSE;
        //     $process['front_end_message'] = $e->getMessage();
        //     $process['back_end_message'] = " GRN Controller : GRN Saving Process.  " . $e->getLine();

        //     return $process;
        // }

    }

    private function getGoodReceiveNoteTable($request){

        $arr['grn_id'] = $request->grn_id;
        $arr['date'] = $request->grn_date;
        $arr['creditor_id'] = $request->creditor_id;
        $arr['purchasing_category_id'] = $request->purchasing_category_id;
        $arr['purchasing_location_id'] = $request->purchasing_location_id;
        $arr['purchase_order_number'] = $request->purchase_order_number;
        $arr['remark'] = $request->remark;
        $arr['cancel_reason'] = '';
        $arr['saved_by'] = Auth::id();
        $arr['saved_on'] = Now();
        $arr['saved_ip'] = '-';

        return $arr;
    }

    public function getGoodReceiveNoteDetailTable($request){

        $objItemMaster = new ItemMaster();

        $gross_amount = $request->unit_price * $request->quantity;
        $net_amount = $gross_amount - $request->discount_amount;

        $arr['grn_id'] = $request->grn_id;
        $arr['item_id'] = $request->item_id;
        $arr['item_name'] = $objItemMaster->getItemName($request->item_id);
        $arr['unit'] = 0;
        $arr['unit_price'] = str_replace(",","", $request->unit_price);
        $arr['quantity'] =  $request->quantity;
        $arr['gross_amount'] = $gross_amount;
        $arr['tax_amount'] = 0;
        $arr['discount_amount'] = str_replace(",","", $request->discount_amount);
        $arr['net_amount'] = $net_amount;

        return $arr;
    }

    public function getGoodReceiveNoteTaxDetailTable($request){

        $icount = 1;
        $objItemMaster = new ItemMaster();
        $result = $objItemMaster->getItemTaxDetail($request->item_id);

        if( count($result) >= 1){

            foreach($result as $row){

                $gross_amount = $request->unit_price * $request->quantity;
                $tax_amount = $gross_amount * ($row->tax_rate / 100);
    
                $arr[$icount]['grn_id'] = $request->grn_id;
                $arr[$icount]['item_id'] = $request->item_id;
                $arr[$icount]['tax_id'] = $row->tax_id;
                $arr[$icount]['tax_name'] = $row->tax_short_name;
                $arr[$icount]['tax_rate'] = $row->tax_rate;
                $arr[$icount]['tax_amount'] = $tax_amount;
    
                $icount++;
            }
    
            return $arr;

        }else{

            $arr[1]['grn_id'] = $request->grn_id;
            $arr[1]['item_id'] = $request->item_id;
            $arr[1]['tax_id'] = 0;
            $arr[1]['tax_name'] = '-';
            $arr[1]['tax_rate'] = 0;
            $arr[1]['tax_amount'] = 0;

            return $arr;
        }        
    }

    private function gl_post_validation_process($request){

        try{

            $front_end_message = '';

            $inputs['grn_id'] = $request->grn_id;
            
            $rules['grn_id'] = array('required', "not_in:#Auto#", new GrnCancelValidation(), new GrnGLPostValidation());

            $validator = Validator::make($inputs, $rules);

            $validation_result = $validator->passes();
            if($validation_result == FALSE){

                $front_end_message = 'Please Check Your GRN ID ';
            }

            $process['grn_id'] = $request->grn_id;
            $process['validation_result'] = $validation_result;
            $process['front_end_message'] = $front_end_message;
            $process['back_end_message'] = '';
            $process['validation_messages'] =  $validator->errors();

            return $process;

        }catch(\Exception $e){

            $process['grn_id'] = $request->grn_id;
            $process['validation_result'] = FALSE;
            $process['front_end_message'] =  $e->getMessage();
            $process['back_end_message'] =  'Grn Controller : GL Post Validation Process ' . $e->getLine();;
            $process['validation_messages'] = new MessageBag();

            return $process;
        }
    }

    private function glPostProcess($request){

        try{
            
            $data['main_store'] = $this->getMainStoreDetail($request);
            $data['gl_post_raw'] = $this->getGlPostRaw($request);
           
            $objGrn = new GoodReceiveNote();
            $grn_gl_post = $objGrn->glPostProcess($data);

            return $grn_gl_post;

        }catch(\Exception $e){

            $process['grn_id'] = $request->grn_id;
            $process['process_result'] = FALSE;
            $process['front_end_message'] = $e->getMessage();;
            $process['back_end_message'] = " GRN Controller : GRN GL Post Process.  " . $e->getLine();

            return $process;
        }
    }

    private function getMainStoreDetail($request){

        $item = array();

        $objGrn = new GoodReceiveNote();
        $grn_detail = $objGrn->getGoodReceiveNoteDetail($request->grn_id);
        
        $icount = 1;
        foreach ($grn_detail as $row) {

            $item[$icount]['item_id'] = $row->item_id;
            $item[$icount]['item_name'] = $row->item_name;
            $item[$icount]['unit'] = $row->unit;
            $item[$icount]['serial'] = 0;
            $item[$icount]['unit_price'] = $row->unit_price;
            $item[$icount]['qty'] = $row->quantity;
            $item[$icount]['qty_balance'] =  $row->quantity;
            $item[$icount]['in_ref'] = 'grn';
            $item[$icount]['in_ref_no'] = $request->grn_id;
            $item[$icount]['saved_by'] = 1;
            $item[$icount]['saved_on'] = Now();
            $item[$icount]['saved_ip'] = '-';

            $icount++;
        }

        return $item;
    }

    private function getGlPostRaw($request){

        $gl_post_raw['grn_id'] = $request->grn_id;
        $gl_post_raw['gl_post'] = 1;
        $gl_post_raw['gl_posted_by'] = Auth::id();
        $gl_post_raw['gl_posted_on'] = Now();
        $gl_post_raw['gl_posted_ip'] = '-';

        return $gl_post_raw;
    }

    private function cancelValidationProcess($request){

        try{

            $front_end_message = '';

            $inputs['grn_id'] = $request->grn_id;
            $inputs['cancel_reason'] = $request->cancel_reason;
            
            $rules['grn_id'] = array('required', "not_in:#Auto#", new GrnCancelValidation(), new GrnGLPostValidation());
            $rules['cancel_reason'] =  array('max:50');

            $validator = Validator::make($inputs, $rules);

            $validation_result = $validator->passes();
            if($validation_result == FALSE){

                $front_end_message = 'Please Check Your Inputs';
            }

            $process['grn_id'] = $request->grn_id;
            $process['validation_result'] = $validation_result;
            $process['front_end_message'] = $front_end_message;
            $process['back_end_message'] = '';
            $process['validation_messages'] =  $validator->errors();

            return $process;


        }catch(\Exception $e){

            $process['grn_id'] = $request->grn_id;
            $process['validation_result'] = FALSE;
            $process['front_end_message'] =  $e->getMessage();
            $process['back_end_message'] =  'GRN Controller : GRN Cancel Validation Process ' . $e->getLine();;
            $process['validation_messages'] = new MessageBag();

            return $process;
        }
    }

    private function cancelGrn($request){

        $objGrn = new GoodReceiveNote();

        //try{

            $data['cancel_raw'] = $this->getGrnCancelRaw($request);

            $cancel_process_result = $objGrn->cancelGrn($data);
           
            return $cancel_process_result;

        // }catch(\Exception $e){

        //     $process['grn_id'] = $request->grn_id;
        //     $process['process_result'] = FALSE;
        //     $process['front_end_message'] = $e->getMessage();
        //     $process['back_end_message'] = " GRN Controller : GRN Cancel Process. <br> Line No. " . $e->getLine();

        //     return $process;
        // }
    }

    private function getGrnCancelRaw($request){

        $arr['grn_id'] = $request->grn_id;
        $arr['cancel'] = 1;
        $arr['cancel_reason'] = $request->cancel_reason;;
        $arr['cancel_by'] = Auth::id();
        $arr['cancel_on'] = Now();
        $arr['cancel_ip'] = '-';

        return $arr;
    }

    public function grn_print_document($grn_number){

        $html_code = $this->prepareGrnPrintDocument($grn_number);

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($html_code);
        $pdf->setPaper('A5', 'landscape');
        return $pdf->stream();
    }

    private function prepareGrnPrintDocument($grn_number){

        $objGrn = new GoodReceiveNote();

        $grn_id = '';
        $grn_date = '';
        $supplier_name = '';
        $supplier_address = '';
        $po_no = '';
        $grn_detail = '';
        $grn_total = 0;

        $grn_resultset = $objGrn->grn_information_for_print_document($grn_number);
        foreach($grn_resultset as $row){

            $grn_id = $row->grn_id;
            $grn_date = $row->date;
            $supplier_name = $row->supplier_name;
            $supplier_address = $row->address;
            $po_no = $row->po_no;
        }

        $grn_detail_resultset = $objGrn->good_receive_note_detail_table($grn_number);
        foreach($grn_detail_resultset as $row){

            $grn_detail .= "<tr style='font-family: Consolas; font-size: 12px;'>";
            $grn_detail .= "<td style='width: 60%;'> ". $row->item_name ." </td> ";
            $grn_detail .= "<td style='width: 10%;'> ". $row->quantity ." </td> ";
            $grn_detail .= "<td style='width: 15%; text-align:right;'> ". number_format($row->unit_price, 2) ." </td> ";
            $grn_detail .= "<td style='width: 15%; text-align:right;'> ". number_format($row->gross_amount, 2) ." </td> ";
            $grn_detail .= "</tr>";

            $grn_total = $grn_total + $row->gross_amount;
        }

        $grn_detail .= "<tr style='font-family: Consolas; font-size: 12px;'>";
        $grn_detail .= "<td style='width: 60%;'>  </td> ";
        $grn_detail .= "<td  colspan='2' style='width: 15%; text-align:left;'> Total Amount </td> ";
        $grn_detail .= "<td style='width: 15%; text-align:right;''> ". number_format($grn_total, 2) ." </td> ";

        $html_code = "  <!DOCTYPE html>
                        <html>
                        <head>
                            <title> GRN Document </title>
                            <style>
                                h2, h4, td, p {
                                    font-family: Consolas; font-size: 12px;'
                                }
                            </style>
                            <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css' integrity='sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO' crossorigin='anonymous'>
                            <script src='https://code.jquery.com/jquery-3.3.1.slim.min.js' integrity='sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo' crossorigin='anonymous'></script>
                            <script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js' integrity='sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49' crossorigin='anonymous'></script>
                            <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js' integrity='sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy' crossorigin='anonymous'></script>
                        </head>
                        <body>
                        
                            <table style='width:100%; font-family: 'Courier New';'>
                                <tr style='text-align:center;'>
                                    <td> <h6> Stationery Store </h6> </td>
                                </tr>
                                <tr style='text-align:center;'>
                                    <td> No. 61, Old Road, Nawinna, Maharagama </td>
                                </tr>
                                <tr style='text-align:center;'>
                                    <td> Tel - 0114674641, 0710481129 </td>
                                </tr>
                                <tr style='text-align:center;'>
                                    <td> <h5> Good Receive Note  </h5> </td>
                                </tr>
                            </table>

                            <div class='table-responsive'>
                                <table style='width:100%;'>
                                    <tr style='text-align:left; font-size: 12px;'>
                                        <td style='width: 15%;'> Customer Name: </td>
                                        <td style='width: 60%;'> ". $supplier_name ." </td>
                                        <td style='width: 15%;'> GRN Date </td>
                                        <td style='width: 10%;'> ". $grn_date ." </td>
                                    </tr>
                                    <tr style='text-align:left; font-size: 12px;'>
                                        <td style='width: 15%;'> Address </td>
                                        <td style='width: 60%;'> ". $supplier_address ." </td>
                                        <td style='width: 15%;'> GRN No. </td>
                                        <td style='width: 10%;'> ". $grn_id ." </td>
                                    </tr>
                                    <tr style='text-align:left; font-size: 12px;'>
                                        <td style='width: 15%;'>  </td>
                                        <td style='width: 60%;'>  </td>
                                        <td style='width: 15%;'> PO No. </td>
                                        <td style='width: 10%;'> ". $po_no ." </td>
                                    </tr>
                                </table>
                            </div>
                            <br>

                            <div class='table-responsive'>
                                <table id='grn_detail' class='table table-sm table-bordered'>
                                    <tr>
                                        <td style='width: 70%;'> Description </td>
                                        <td style='width: 10%;'> Qty </td>
                                        <td style='width: 10%;'> Unit Price </td>
                                        <td style='width: 10%;'> Amount </td>
                                    </tr>
                                    ". $grn_detail ."
                                </table>
                            </div>
                            
                        
                        </body>
                        </html>  ";

        return $html_code;
        
    }

}
