@extends('layouts.inventory')

@section('title')
    Production Note
@endsection

@section('body')

<div id="tbldiv" style="width: 98%;  margin-right: 1%; margin-left: 1%; margin-top: 1%;">
    <form method="POST" action="{{route('production_note_process')}}">

        @csrf

        <div class="col-sm-12">
            
            <div class="card">

                <div class="card-header">
                    Production Note 
                </div>

                <div class="card-body">

                    <div class="col-sm-11">
                        <?php echo $PN['attributes']['process_message'];  ?>
                    </div>

                    <div class="mb-2 row">
                        <label for="tid" class="col-sm-9 col-form-label-sm"></label>
                        <label for="tid" class="col-sm-1 col-form-label-sm">PN ID</label>
                        <div class="col-sm-2">
                            <input type="text" name="pn_id" id="pn_id" class="form-control form-control-sm"  value="{{$PN['attributes']['pn_id']}}" readonly>
                            @if($PN['attributes']['validation_messages']->has('pn_id'))
                                <script>
                                        document.getElementById('pn_id').className = 'form-control form-control-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $PN['attributes']['validation_messages']->first("pn_id") }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <label for="tid" class="col-sm-1 col-form-label-sm">Manufacture </label>
                        <div class="col-sm-3">
                            <select name="manufacture_location_id" id="manufacture_location_id" class="form-select form-select-sm" >
                                @foreach($PN['manufacture_location'] as $row)
                                    @if($PN['attributes']['manufacture_location_id']==$row->manufacture_location_id)
                                        <option value ="{{$row->manufacture_location_id}}" selected>{{$row->manufacture_location_name}}</option>
                                    @else
                                        <option value ="{{$row->manufacture_location_id}}">{{$row->manufacture_location_name}}</option>
                                    @endif
                                @endforeach
                                @if($PN['attributes']['manufacture_location_id']== "0")
                                    <option value ="0" selected>Select the Manufacture Location</option>
                                @endif
                            </select>
                            @if($PN['attributes']['validation_messages']->has('Manufacture Location'))
                                <script>
                                    document.getElementById('manufacture_location_id').className = 'form-select form-select-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $PN['attributes']['validation_messages']->first('Manufacture Location') }}</div>
                            @endif
                        </div>

                        <label for="tid" class="col-sm-5 col-form-label-sm"></label>
                        <label for="tid" class="col-sm-1 col-form-label-sm">PN Date</label>
                        <div class="col-sm-2">
                            <input type="date" name="pn_date" id="pn_date" class="form-control form-control-sm"  value="{{$PN['attributes']['pn_date']}}">
                            @if($PN['attributes']['validation_messages']->has('pn_date'))
                                <script>
                                        document.getElementById('pn_date').className = 'form-control form-control-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $PN['attributes']['validation_messages']->first("pn_date") }}</div>
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
                            <input type="text" name="remark" id="remark" class="form-control form-control-sm"  value="{{$PN['attributes']['remark']}}">
                            @if($PN['attributes']['validation_messages']->has('remark'))
                                <script>
                                        document.getElementById('remark').className = 'form-control form-control-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $PN['attributes']['validation_messages']->first("remark") }}</div>
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

                        <div class="col-2">
                            <input type="button" name="print" id="print" style="width: 100%;" class="btn btn-primary btn-sm" value="Stock Update">
                        </div>

                    </div>

                    <hr>

                    <div class="mb-2 row">
                        
                        <label for="tid" class="col-sm-1 col-form-label-sm">Item</label>
                        <div class="col-sm-8">
                            <select name="item_id" id="item_id" class="form-select form-select-sm" >
                                @foreach($PN['item'] as $row)
                                    @if($PN['attributes']['item_id']==$row->item_id)
                                        <option value ="{{$row->item_id}}" selected>{{$row->item_name}}</option>
                                    @else
                                        <option value ="{{$row->item_id}}">{{$row->item_name}}</option>
                                    @endif
                                @endforeach
                                @if($PN['attributes']['item_id']== "0")
                                    <option value ="0" selected>Select the Item</option>
                                @endif
                            </select>
                            @if($PN['attributes']['validation_messages']->has('item_id'))
                                <script>
                                    document.getElementById('item_id').className = 'form-select form-select-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $PN['attributes']['validation_messages']->first("item_id") }}</div>
                            @endif
                        </div>

                        <label for="tid" class="col-sm-1 col-form-label-sm">Quantity</label>
                        <div class="col-sm-1">
                            <input type="text" name="quantity" id="quantity" style="text-align:right;" class="form-control form-control-sm"  value="{{$PN['attributes']['quantity']}}">
                            @if($PN['attributes']['validation_messages']->has('quantity'))
                                <script>
                                        document.getElementById('quantity').className = 'form-control form-control-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $PN['attributes']['validation_messages']->first("quantity") }}</div>
                            @endif
                        </div>

                        <div class="col-sm-1">
                            <input type="submit" name="submit" id="submit" style="width: 100%;" class="btn btn-success btn-sm" value="Add">
                        </div>

                    </div>

                    <br>
                    <div class="table-responsive">

                        <table id="tblpn_detail" class="table table-bordered">

                            <tr style="font-family: Consolas; font-size: 11px;">
                                <th style='width: 5%;'>Item Id</th>
                                <th style='width: 75%;'>Item Name</th>
                                <th style='width: 10%;'>Qty</th>
                                <th style='width: 10%;'></th>
                            </tr>

                            @if( count($PN['attributes']['pn_detail']) >= 1 )

                                @foreach($PN['attributes']['pn_detail'] as $row)

                                    <tr style="font-family: Consolas; font-size: 11px;">
                                        <td>{{$row->item_id}}</td>
                                        <td>{{$row->item_name}}</td>
                                        <td style="text-align:right;"> {{$row->quantity}} </td>
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