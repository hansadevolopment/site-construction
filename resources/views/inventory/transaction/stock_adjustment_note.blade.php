@extends('layouts.inventory')

@section('title')
    Stock Adjustment Note
@endsection

@section('body')

<div id="tbldiv" style="width: 98%;  margin-right: 1%; margin-left: 1%; margin-top: 1%;">
    <form method="POST" action="{{route('stock_adjustment_note_process')}}">

        @csrf

        <div class="col-sm-12">
            
            <div class="card">

                <div class="card-header">
                    Stock Adjustment Note
                </div>

                <div class="card-body">

                    <div class="col-sm-11">
                        <?php echo $SAN['attributes']['process_message'];  ?>
                    </div>

                    <div class="mb-2 row">
                        <label for="tid" class="col-sm-9 col-form-label-sm"></label>
                        <label for="tid" class="col-sm-1 col-form-label-sm">San ID</label>
                        <div class="col-sm-2">
                            <input type="text" name="san_id" id="san_id" class="form-control form-control-sm"  value="{{$SAN['attributes']['san_id']}}" readonly>
                            @if($SAN['attributes']['validation_messages']->has('san_id'))
                                <script>
                                        document.getElementById('san_id').className = 'form-control form-control-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $SAN['attributes']['validation_messages']->first("san_id") }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2 row">

                        <label for="tid" class="col-sm-1 col-form-label-sm">Remark</label>
                        <div class="col-sm-8">
                            <input type="text" name="remark" id="remark" class="form-control form-control-sm"  value="{{$SAN['attributes']['remark']}}">
                            @if($SAN['attributes']['validation_messages']->has('remark'))
                                <script>
                                        document.getElementById('remark').className = 'form-control form-control-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $SAN['attributes']['validation_messages']->first("remark") }}</div>
                            @endif
                        </div>

                        <label for="tid" class="col-sm-1 col-form-label-sm">San Date</label>
                        <div class="col-sm-2">
                            <input type="date" name="san_date" id="san_date" class="form-control form-control-sm"  value="{{$SAN['attributes']['san_date']}}">
                            @if($SAN['attributes']['validation_messages']->has('san_date'))
                                <script>
                                        document.getElementById('san_date').className = 'form-control form-control-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $SAN['attributes']['validation_messages']->first("san_date") }}</div>
                            @endif
                        </div>

                    </div>

                    <div class="mb-2 row">
                        
                        <label for="tid" class="col-sm-1 col-form-label-sm">Item</label>
                        <div class="col-sm-5">
                            <select name="item_id" id="item_id" class="form-select form-select-sm" >
                                @foreach($SAN['item'] as $row)
                                    @if($SAN['attributes']['item_id']==$row->item_id)
                                        <option value ="{{$row->item_id}}" selected>{{$row->item_name}}</option>
                                    @else
                                        <option value ="{{$row->item_id}}">{{$row->item_name}}</option>
                                    @endif
                                @endforeach
                                @if($SAN['attributes']['item_id']== "0")
                                    <option value ="0" selected>Select the Item</option>
                                @endif
                            </select>
                            @if($SAN['attributes']['validation_messages']->has('item_id'))
                                <script>
                                    document.getElementById('item_id').className = 'form-select form-select-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $SAN['attributes']['validation_messages']->first("item_id") }}</div>
                            @endif
                        </div>

                        <label for="tid" class="col-sm-1 col-form-label-sm">Serial</label>
                        <div class="col-sm-2">
                            <select name="item_serial" id="item_serial" class="form-select form-select-sm" >
                                <option value ="0" selected>Select the Item Serial</option>
                            </select>
                            @if($SAN['attributes']['validation_messages']->has('item_serial'))
                                <script>
                                    document.getElementById('item_serial').className = 'form-select form-select-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $SAN['attributes']['validation_messages']->first("item_serial") }}</div>
                            @endif
                        </div>

                        <label for="tid" class="col-sm-1 col-form-label-sm">Actual Qty</label>
                        <div class="col-sm-2">
                            <input type="text" name="actual_quantity" id="actual_quantity" style="text-align:right;" class="form-control form-control-sm"  value="" readonly>
                        </div>

                    </div>

                    <div class="mb-3 row">

                        <label for="tid" class="col-sm-1 col-form-label-sm">Quantity</label>
                        <div class="col-sm-4">
                            <input type="text" name="quantity" id="quantity" style="text-align:right;" class="form-control form-control-sm"  value="{{$SAN['attributes']['quantity']}}">
                            @if($SAN['attributes']['validation_messages']->has('quantity'))
                                <script>
                                        document.getElementById('quantity').className = 'form-control form-control-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $SAN['attributes']['validation_messages']->first("quantity") }}</div>
                            @endif
                        </div>

                    </div>

                    <div id="div_cancel" class="mb-4 row">
                        <label id="cancel_lable" for="cancel_lable" class="col-sm-2 col-form-label-sm">Cancel Reason</label>
                        <div class="col-sm-10">
                              <input type="text" name="cancel_reason" id="cancel_reason" class="form-control form-control-sm"  value="" readonly>
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
                            <input type="button" name="print" id="print" style="width: 100%;" class="btn btn-primary btn-sm" value="Stock Update">
                        </div>

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