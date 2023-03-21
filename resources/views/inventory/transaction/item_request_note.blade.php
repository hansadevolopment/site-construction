@extends('layouts.inventory')

@section('title')
    Item Request Note
@endsection

@section('body')

<div id="tbldiv" style="width: 98%;  margin-right: 1%; margin-left: 1%; margin-top: 1%;">
    <form method="POST" action="{{route('item_request_note_process')}}">

        @csrf

        <div class="col-sm-12">
            
            <div class="card">

                <div class="card-header">
                    Item Request Note - {{$Irn['attributes']['irn_referance']}}
                </div>

                <div class="card-body">

                    <div class="col-sm-11">
                        <?php echo $Irn['attributes']['process_message'];  ?>
                    </div>

                    <div class="mb-2 row">
                        <label for="tid" class="col-sm-9 col-form-label-sm"></label>
                        <label for="tid" class="col-sm-1 col-form-label-sm">Irn ID</label>
                        <div class="col-sm-2">
                            <input type="text" name="irn_id" id="irn_id" class="form-control form-control-sm"  value="{{$Irn['attributes']['irn_id']}}" readonly>
                            @if($Irn['attributes']['validation_messages']->has('irn_id'))
                                <script>
                                        document.getElementById('irn_id').className = 'form-control form-control-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $Irn['attributes']['validation_messages']->first("irn_id") }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <label for="tid" class="col-sm-1 col-form-label-sm">{{$Irn['attributes']['irn_referance']}} </label>
                        <div class="col-sm-3">
                            <select name="location_id" id="location_id" class="form-select form-select-sm" >

                                @if( $Irn['attributes']['irn_referance'] == 'Sales')

                                    @foreach($Irn['sales_location'] as $row)
                                        @if($Irn['attributes']['location_id']==$row->sales_location_id)
                                                <option value ="{{$row->sales_location_id}}" selected>{{$row->sales_location_name}}</option>
                                        @else
                                                <option value ="{{$row->sales_location_id}}">{{$row->sales_location_name}}</option>
                                        @endif
                                    @endforeach
                                    @if($Irn['attributes']['location_id']== "0")
                                        <option value ="0" selected>Select the Sales Location</option>
                                    @endif

                                @else

                                    @foreach($Irn['manufacture_location'] as $row)
                                        @if($Irn['attributes']['location_id']==$row->manufacture_location_id)
                                            <option value ="{{$row->manufacture_location_id}}" selected>{{$row->manufacture_location_name}}</option>
                                        @else
                                            <option value ="{{$row->manufacture_location_id}}">{{$row->manufacture_location_name}}</option>
                                        @endif
                                    @endforeach
                                    @if($Irn['attributes']['location_id']== "0")
                                        <option value ="0" selected>Select the Manufacture Location</option>
                                    @endif

                                @endif
                            </select>
                            @if($Irn['attributes']['validation_messages']->has($Irn['attributes']['irn_referance'] . 'Location'))
                                <script>
                                    document.getElementById('location_id').className = 'form-select form-select-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $Irn['attributes']['validation_messages']->first($Irn['attributes']['irn_referance'] . 'Location') }}</div>
                            @endif
                        </div>

                        <label for="tid" class="col-sm-1 col-form-label-sm">Irn Referance</label>
                        <div class="col-sm-2">
                            <input type="text" name="irn_referance" id="irn_referance" class="form-control form-control-sm"  value="{{$Irn['attributes']['irn_referance']}}" readonly>
                        </div>

                        <label for="tid" class="col-sm-2 col-form-label-sm"></label>
                        <label for="tid" class="col-sm-1 col-form-label-sm">Irn Date</label>
                        <div class="col-sm-2">
                            <input type="date" name="irn_date" id="irn_date" class="form-control form-control-sm"  value="{{$Irn['attributes']['irn_date']}}">
                            @if($Irn['attributes']['validation_messages']->has('irn_date'))
                                <script>
                                        document.getElementById('irn_date').className = 'form-control form-control-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $Irn['attributes']['validation_messages']->first("irn_date") }}</div>
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
                            <input type="text" name="remark" id="remark" class="form-control form-control-sm"  value="{{$Irn['attributes']['remark']}}">
                            @if($Irn['attributes']['validation_messages']->has('remark'))
                                <script>
                                        document.getElementById('remark').className = 'form-control form-control-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $Irn['attributes']['validation_messages']->first("remark") }}</div>
                            @endif
                        </div>

                    </div>


                    <div  class="mb-2 row">

                        <div class="col-2">
                            <input type="submit" name="submit" id="submit" style="width: 100%;" class="btn btn-primary btn-sm" value="Save">
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
                        <div class="col-sm-7">
                            <select name="item_id" id="item_id" class="form-select form-select-sm" >
                                @foreach($Irn['item'] as $row)
                                    @if($Irn['attributes']['item_id']==$row->item_id)
                                        <option value ="{{$row->item_id}}" selected>{{$row->item_name}}</option>
                                    @else
                                        <option value ="{{$row->item_id}}">{{$row->item_name}}</option>
                                    @endif
                                @endforeach
                                @if($Irn['attributes']['item_id']== "0")
                                    <option value ="0" selected>Select the Item</option>
                                @endif
                            </select>
                            @if($Irn['attributes']['validation_messages']->has('item_id'))
                                <script>
                                    document.getElementById('item_id').className = 'form-select form-select-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $Irn['attributes']['validation_messages']->first("item_id") }}</div>
                            @endif
                        </div>

                        <div class="col-sm-1">
                            <input type="text" name="unit" id="unit" class="form-control form-control-sm"  value="" readonly>
                        </div>

                        <label for="tid" class="col-sm-1 col-form-label-sm">Quantity</label>
                        <div class="col-sm-1">
                            <input type="text" name="quantity" id="quantity" style="text-align:right;" class="form-control form-control-sm"  value="{{$Irn['attributes']['quantity']}}">
                            @if($Irn['attributes']['validation_messages']->has('quantity'))
                                <script>
                                        document.getElementById('quantity').className = 'form-control form-control-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $Irn['attributes']['validation_messages']->first("quantity") }}</div>
                            @endif
                        </div>

                        <div class="col-sm-1">
                            <input type="submit" name="submit" id="submit" style="width: 100%;" class="btn btn-success btn-sm" value="Add">
                        </div>

                    </div>

                    <br>
                    <div class="table-responsive">

                        <table id="tblirn_detail" class="table table-bordered">

                            <tr style="font-family: Consolas; font-size: 11px;">
                                <th style='width: 5%;'>Item Id</th>
                                <th style='width: 75%;'>Item Name</th>
                                <th style='width: 10%;'>Qty</th>
                                <th style='width: 10%;'></th>
                            </tr>

                            @if( count($Irn['attributes']['irn_detail']) >= 1 )

                                @foreach($Irn['attributes']['irn_detail'] as $row)

                                    <tr style="font-family: Consolas; font-size: 11px;">
                                        <td>{{$row->item_id}}</td>
                                        <td>{{$row->item_name}}</td>
                                        <td style="text-align:right;"> $row->quantity </td>
                                        <td><input type="button" class="btn btn-danger btn-sm" style="width: 100%;" value="Remove"></td>
                                    </tr>

                                @endforeach

                            @else

                                <tr style="font-family: Consolas; font-size: 11px;">
                                    <td>-</td>
                                    <td>-</td>
                                    <td style="text-align:right;">0</td>
                                    <td><input type="button" class="btn btn-danger btn-sm" style="width: 100%;" value="Remove"></td>
                                </tr>

                            @endif



                        </table>
                    </div>
                    
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