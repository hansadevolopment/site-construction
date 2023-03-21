@extends('layouts.purchase')

@section('title')
    Good Receive Note
@endsection

@section('body')

<div id="tbldiv" style="width: 98%;  margin-right: 1%; margin-left: 1%; margin-top: 1%;">
    <form method="POST" action="{{route('grn_process')}}">

        @csrf

        <div class="col-sm-12">
            
            <div class="card">

                <div class="card-header">
                    Good Receive Note
                </div>

                <div class="card-body">

                    <div class="col-sm-11">
                        <?php echo $grn['attributes']['process_message'];  ?>
                    </div>

                    <div class="mb-2 row">
                        <label for="tid" class="col-sm-9 col-form-label-sm"></label>
                        <label for="tid" class="col-sm-1 col-form-label-sm">GRN ID</label>
                        <div class="col-sm-2">
                            <input type="text" name="grn_id" id="grn_id" class="form-control form-control-sm"  value="{{$grn['attributes']['grn_id']}}" readonly>
                            @if($grn['attributes']['validation_messages']->has('grn_id'))
                                <script>
                                        document.getElementById('grn_id').className = 'form-control form-control-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $grn['attributes']['validation_messages']->first("grn_id") }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <label for="tid" class="col-sm-1 col-form-label-sm">Pur. Category</label>
                        <div class="col-sm-3">
                            <select name="purchasing_category_id" id="purchasing_category_id" class="form-select form-select-sm" >
                                @foreach($grn['purchasing_category'] as $row)
                                      @if($grn['attributes']['purchasing_category_id']==$row->purchasing_category_id)
                                            <option value ="{{$row->purchasing_category_id}}" selected>{{$row->purchasing_category_name}}</option>
                                      @else
                                            <option value ="{{$row->purchasing_category_id}}">{{$row->purchasing_category_name}}</option>
                                      @endif
                                @endforeach
                                @if($grn['attributes']['purchasing_category_id']== 0)
                                      <option value ="0" selected>Select the Purchasing Category</option>
                                @endif
                            </select>
                            @if($grn['attributes']['validation_messages']->has('purchasing_category_id'))
                                <script>
                                    document.getElementById('purchasing_category_id').className = 'form-select form-select-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $grn['attributes']['validation_messages']->first("purchasing_category_id") }}</div>
                            @endif
                        </div>

                        <label for="tid" class="col-sm-1 col-form-label-sm">Pur. Location</label>
                        <div class="col-sm-3">
                            <select name="purchasing_location_id" id="purchasing_location_id" class="form-select form-select-sm" >
                                @foreach($grn['purchasing_location'] as $row)
                                      @if($grn['attributes']['purchasing_location_id']==$row->purchasing_location_id)
                                            <option value ="{{$row->purchasing_location_id}}" selected>{{$row->purchasing_location_name}}</option>
                                      @else
                                            <option value ="{{$row->purchasing_location_id}}">{{$row->purchasing_location_name}}</option>
                                      @endif
                                @endforeach
                                @if($grn['attributes']['purchasing_location_id']== 0)
                                      <option value ="0" selected>Select the Purchasing Location</option>
                                @endif
                            </select>
                            @if($grn['attributes']['validation_messages']->has('purchasing_location_id'))
                                <script>
                                    document.getElementById('purchasing_location_id').className = 'form-select form-select-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $grn['attributes']['validation_messages']->first("purchasing_location_id") }}</div>
                            @endif
                        </div>

                        <label for="tid" class="col-sm-1 col-form-label-sm"></label>
                        <label for="tid" class="col-sm-1 col-form-label-sm">GRN Date</label>
                        <div class="col-sm-2">
                            <input type="date" name="grn_date" id="grn_date" class="form-control form-control-sm"  value="{{$grn['attributes']['grn_date']}}">
                            @if($grn['attributes']['validation_messages']->has('grn_date'))
                                <script>
                                        document.getElementById('grn_date').className = 'form-control form-control-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $grn['attributes']['validation_messages']->first("grn_date") }}</div>
                            @endif
                        </div>

                    </div>

                    <div class="mb-2 row">

                        <label for="tid" class="col-sm-1 col-form-label-sm">Creditor</label>
                        <div class="col-sm-8">
                            <select name="creditor_id" id="creditor_id" class="form-select form-select-sm" >
                                @foreach($grn['creditor'] as $row)
                                      @if($grn['attributes']['creditor_id']==$row->creditor_id)
                                            <option value ="{{$row->creditor_id}}" selected>{{$row->creditor_name}}</option>
                                      @else
                                            <option value ="{{$row->creditor_id}}">{{$row->creditor_name}}</option>
                                      @endif
                                @endforeach
                                @if($grn['attributes']['creditor_id']== 0)
                                      <option value ="0" selected>Select the Creditor</option>
                                @endif
                            </select>
                            @if($grn['attributes']['validation_messages']->has('creditor_id'))
                                <script>
                                    document.getElementById('creditor_id').className = 'form-select form-select-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $grn['attributes']['validation_messages']->first("creditor_id") }}</div>
                            @endif
                        </div>

                        <label for="tid" class="col-sm-1 col-form-label-sm">PO No.</label>
                        <div class="col-sm-2">
                            <input type="text" name="purchase_order_number" id="purchase_order_number" class="form-control form-control-sm"  value="{{$grn['attributes']['purchase_order_number']}}">
                            @if($grn['attributes']['validation_messages']->has('purchase_order_number'))
                                <script>
                                        document.getElementById('purchase_order_number').className = 'form-control form-control-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $grn['attributes']['validation_messages']->first("purchase_order_number") }}</div>
                            @endif
                        </div>

                    </div>

                    <div id="div_cancel" class="mb-2 row">
                        <label id="cancel_lable" for="cancel_lable" class="col-sm-2 col-form-label-sm">Cancel Reason</label>
                        <div class="col-sm-10">
                              <input type="text" name="cancel_reason" id="cancel_reason" class="form-control form-control-sm"  value="" readonly>
                        </div>
                    </div>

                    <div class="mb-4 row">

                        <label for="tid" class="col-sm-1 col-form-label-sm">Remark</label>
                        <div class="col-sm-11">
                            <input type="text" name="remark" id="remark" class="form-control form-control-sm"  value="{{$grn['attributes']['remark']}}">
                            @if($grn['attributes']['validation_messages']->has('remark'))
                                <script>
                                        document.getElementById('remark').className = 'form-control form-control-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $grn['attributes']['validation_messages']->first("remark") }}</div>
                            @endif
                        </div>

                    </div>


                    <div  class="mb-2 row">

                        <div class="col-2">
                            <input type="submit" name="submit" id="submit" style="width: 100%;" class="btn btn-primary btn-sm" value="Save">
                        </div>

                        <div class="col-2">
                            <input type="submit" name="submit" id="submit" style="width: 100%;" class="btn btn-primary btn-sm" value="GL Post">
                        </div>

                        <div class="col-2">
                            <input type="submit" name="submit" id="submit" style="width: 100%;" class="btn btn-primary btn-sm" value="Reset">
                        </div>

                        <div class="col-2">
                            <input type="submit" name="submit" id="submit" style="width: 100%;" class="btn btn-primary btn-sm" value="Cancel" onclick="cancelProcess()">
                        </div>

                        <div class="col-2">
                            <input type="button" name="print" id="print" style="width: 100%;" class="btn btn-primary btn-sm" value="Print Document">
                        </div>

                    </div>

                    <hr>

                    <div class="mb-2 row">
                        
                        <label for="tid" class="col-sm-1 col-form-label-sm">Item</label>
                        <div class="col-sm-9">
                            <select name="item_id" id="item_id" class="form-select form-select-sm" >
                                @foreach($grn['item'] as $row)
                                    <option value ="{{$row->item_id}}">{{$row->item_name}}</option>
                                @endforeach
                                <option value ="0" selected>Select the Item</option>
                            </select>
                            @if($grn['attributes']['validation_messages']->has('item_id'))
                                <script>
                                    document.getElementById('item_id').className = 'form-select form-select-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $grn['attributes']['validation_messages']->first("item_id") }}</div>
                            @endif
                        </div>

                        <label for="tid" class="col-sm-1 col-form-label-sm">Unit</label>
                        <div class="col-sm-1">
                            <input type="text" name="unit" id="unit" class="form-control form-control-sm"  value="" readonly>
                        </div>

                    </div>

                    <div class="mb-2 row">

                        <label for="tid" class="col-sm-1 col-form-label-sm">Unit Price</label>
                        <div class="col-sm-2">
                            <input type="text" name="unit_price" id="unit_price" style="text-align:right;" class="form-control form-control-sm"  value="0.00">
                            @if($grn['attributes']['validation_messages']->has('unit_price'))
                                <script>
                                        document.getElementById('unit_price').className = 'form-control form-control-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $grn['attributes']['validation_messages']->first("unit_price") }}</div>
                            @endif
                        </div>

                        <label for="tid" class="col-sm-1 col-form-label-sm">Quantity</label>
                        <div class="col-sm-2">
                            <input type="text" name="quantity" id="quantity" style="text-align:right;" class="form-control form-control-sm"  value="0.00">
                            @if($grn['attributes']['validation_messages']->has('quantity'))
                                <script>
                                        document.getElementById('quantity').className = 'form-control form-control-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $grn['attributes']['validation_messages']->first("quantity") }}</div>
                            @endif
                        </div>

                        <label for="tid" class="col-sm-1 col-form-label-sm">Discount Amt</label>
                        <div class="col-sm-2">
                            <input type="text" name="discount_amount" id="discount_amount" style="text-align:right;" class="form-control form-control-sm"  value="0.00">
                            @if($grn['attributes']['validation_messages']->has('discount_amount'))
                                <script>
                                        document.getElementById('discount_amount').className = 'form-control form-control-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $grn['attributes']['validation_messages']->first("discount_amount") }}</div>
                            @endif
                        </div>
                        <label for="tid" class="col-sm-1 col-form-label-sm"></label>

                        <div class="col-sm-1">
                            <input type="submit" name="submit" id="submit" style="width: 100%;" class="btn btn-success btn-sm" value="Add">
                        </div>

                        <div class="col-sm-1">                      
                            <input type="submit" name="submit" id="submit" style="width: 100%;" class="btn btn-danger btn-sm" value="Delete">
                        </div>
                        
                    </div>

                    <br>
                    <div class="table-responsive">

                        <table id="tblgrn_detail" class="table table-bordered">

                            <tr style="font-family: Consolas; font-size: 11px;">
                                <th style='width: 5%;'>Item Id</th>
                                <th style='width: 35%;'>Item Name</th>
                                <th style='width: 10%;'>Unit Price</th>
                                <th style='width: 10%;'>Qty</th>
                                <th style='width: 10%;'>Gross Amount</th>
                                <th style='width: 10%;'>Tax Amount</th>
                                <th style='width: 10%;'>Discount</th>
                                <th style='width: 10%;'>Net Amount</th>
                            </tr>

                            @if( count($grn['attributes']['grn_detail']) >= 1 )

                                @foreach($grn['attributes']['grn_detail'] as $row)

                                    <tr style="font-family: Consolas; font-size: 11px;">
                                        <th>{{$row->item_id}}</th>
                                        <th>{{$row->item_name}}</th>
                                        <th style="text-align:right;"> @money($row->unit_price) </th>
                                        <th style="text-align:center;"> @money($row->quantity) </th>
                                        <th style="text-align:right;"> @money($row->gross_amount) </th>
                                        <th style="text-align:right;"> @money($row->tax_amount) </th>
                                        <th style="text-align:right;"> @money($row->discount_amount) </th>
                                        <th style="text-align:right;"> @money($row->net_amount)</th>
                                    </tr>

                                @endforeach

                            @else

                                <tr style="font-family: Consolas; font-size: 11px;">
                                    <th>-</th>
                                    <th>-</th>
                                    <th style="text-align:right;">0.00</th>
                                    <th style="text-align:center;">0</th>
                                    <th style="text-align:right;">0.00</th>
                                    <th style="text-align:right;">0.00</th>
                                    <th style="text-align:right;">0.00</th>
                                    <th style="text-align:right;">0.00</th>
                                </tr>

                            @endif



                        </table>
                    </div>

                    <div style="margin-left: 1%; margin-right: 2%;">

                        <div class="mb-2 row">

                            <label for="sup" class="col-sm-1 col-form-label-sm">Gross Amount</label>
                            <div class="col-sm-2">
                                <input type="text" style="text-align:right;" name="total_gross_amount" id="total_gross_amount" class="form-control form-control-sm"  value="{{$grn['attributes']['total_gross_amount']}}" readonly>
                            </div>

                            <label for="sup" class="col-sm-1 col-form-label-sm">Discount Amt</label>
                            <div class="col-sm-2">
                                <input type="text" style="text-align:right;" name="total_discount_amount" id="total_discount_amount" class="form-control form-control-sm"  value="{{$grn['attributes']['total_discount_amount']}}" readonly>
                            </div>

                            <label for="sup" class="col-sm-1 col-form-label-sm">Tax Amount</label>
                            <div class="col-sm-2">
                                <input type="text" style="text-align:right;" name="total_tax_amount" id="total_tax_amount" class="form-control form-control-sm"  value="{{$grn['attributes']['total_tax_amount']}}" readonly>
                            </div>

                            <label for="sup" class="col-sm-1 col-form-label-sm">Net Amount</label>
                            <div class="col-sm-2">
                                <input type="text" style="text-align:right;" name="total_net_amount" id="total_net_amount" class="form-control form-control-sm"  value="{{$grn['attributes']['total_net_amount']}}" readonly>
                            </div>

                        </div>

                    </div>
                    <br>


                    
                </div>

            </div>

        </div>

    </form>
    </div> 

    <script>

        showDiv1();

        function showDiv1() {

            //Now
            document.getElementById('div_cancel').style.display = "none";

            // var total_gross_amount = document.getElementById('total_gross_amount').value;
            // var total_discount_amount = document.getElementById('total_discount_amount').value;
            // var total_tax_amount = document.getElementById('total_tax_amount').value;
            // var total_net_amount = document.getElementById('total_net_amount').value;

            // document.getElementById('total_gross_amount').value = formatter.format(total_gross_amount);
            // document.getElementById('total_discount_amount').value =formatter.format(total_discount_amount);
            // document.getElementById('total_tax_amount').value = formatter.format(total_tax_amount);
            // document.getElementById('total_net_amount').value = formatter.format(total_net_amount);
        }

        function cancelProcess(){

            document.getElementById('cancel_reason').value = "";
            var cancel_reason = prompt("Cancel Reason", "");
            document.getElementById('cancel_reason').value = cancel_reason;
        }

        $('#remark').keypress(function (e) {
            if (e.which == 13) {
                e.preventDefault();
            }
        });

    </script>



@endsection