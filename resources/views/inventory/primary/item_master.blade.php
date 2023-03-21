@extends('layouts.inventory')

@section('title')
    Item Master
@endsection

@section('body')

    <div id="tbldiv" style="width: 98%;  margin-right: 1%; margin-left: 1%; margin-top: 1%;">
    <form method="POST" action="{{route('item_master_process')}}">

        @csrf

        <div class="col-sm-12">
            
            <div class="card">

                <div class="card-header">
                    Item Master 
                </div>

                <div class="card-body">

                    <div class="col-sm-12">
                        <?php echo $Item['attributes']['process_message'];  ?>
                    </div>

                    <div class="row no-gutters">

                        <div class="col-12 col-sm-8 col-md-8">
                        <div style="margin-left: 2%; margin-right: 1%;">

                            <div class="mb-2 row">
                                <label for="tid" class="col-sm-2 col-form-label-sm">Item ID</label>
                                <div class="col-sm-2">
                                    <input type="text" name="item_id" id="item_id" class="form-control form-control-sm"  value="{{$Item['attributes']['item_id']}}" readonly>
                                    @if($Item['attributes']['validation_messages']->has('item_id'))
                                        <script>
                                                document.getElementById('item_id').className = 'form-control form-control-sm is-invalid';
                                        </script>
                                        <div class="invalid-feedback">{{ $Item['attributes']['validation_messages']->first("item_id") }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-2 row">
                                <label for="tid" class="col-sm-2 col-form-label-sm">Name</label>
                                <div class="col-sm-10">
                                    <input type="text" name="item_name" id="item_name" class="form-control form-control-sm"  value="{{$Item['attributes']['item_name']}}">
                                    @if($Item['attributes']['validation_messages']->has('Item Name'))
                                        <script>
                                                document.getElementById('item_name').className = 'form-control form-control-sm is-invalid';
                                        </script>
                                        <div class="invalid-feedback">{{ $Item['attributes']['validation_messages']->first("Item Name") }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-2 row">

                                <label for="tid" class="col-sm-2 col-form-label-sm">Unit</label>
                                <div class="col-sm-3">
                                    <select name="unit" id="unit" class="form-select form-select-sm" >
                                        @foreach($Item['Unit'] as $row)
                                            @if($Item['attributes']['unit']==$row->unit_id)
                                                    <option value ="{{$row->unit_id}}" selected>{{$row->unit_name}}</option>
                                            @else
                                                    <option value ="{{$row->unit_id}}">{{$row->unit_name}}</option>
                                            @endif
                                        @endforeach
                                        @if($Item['attributes']['unit']== '0')
                                            <option value ="0" selected>Select the Unit</option>
                                        @endif
                                    </select>
                                    @if($Item['attributes']['validation_messages']->has('Unit'))
                                        <script>
                                                document.getElementById('unit').className = 'form-select form-select-sm is-invalid';
                                        </script>
                                        <div class="invalid-feedback">{{ $Item['attributes']['validation_messages']->first("Unit") }}</div>
                                    @endif
                                </div>

                            </div>

                            <div class="mb-2 row">

                                <label for="tid" class="col-sm-2 col-form-label-sm">Serial</label>
                                <div class="col-sm-2">
                                    <input type="text" name="serial" id="serial" class="form-control form-control-sm"  value="{{$Item['attributes']['serial']}}" readonly>
                                    @if($Item['attributes']['validation_messages']->has('serial'))
                                        <script>
                                                document.getElementById('serial').className = 'form-control form-control-sm is-invalid';
                                        </script>
                                        <div class="invalid-feedback">{{ $Item['attributes']['validation_messages']->first("serial") }}</div>
                                    @endif
                                </div>

                                <label for="tid" class="col-sm-2 col-form-label-sm">Actual Quantity</label>
                                <div class="col-sm-2">
                                    <input type="text" name="actual_quantity" id="actual_quantity" class="form-control form-control-sm"  value="{{$Item['attributes']['actual_quantity']}}" readonly>
                                    @if($Item['attributes']['validation_messages']->has('actual_quantity'))
                                        <script>
                                                document.getElementById('actual_quantity').className = 'form-control form-control-sm is-invalid';
                                        </script>
                                        <div class="invalid-feedback">{{ $Item['attributes']['validation_messages']->first("actual_quantity") }}</div>
                                    @endif
                                </div>

                                <label for="tid" class="col-sm-2 col-form-label-sm">Re Order Quantity</label>
                                <div class="col-sm-2">
                                    <input type="text" name="reorder_quantity" id="reorder_quantity" class="form-control form-control-sm"  value="{{$Item['attributes']['reorder_quantity']}}">
                                    @if($Item['attributes']['validation_messages']->has('reorder_quantity'))
                                        <script>
                                                document.getElementById('reorder_quantity').className = 'form-control form-control-sm is-invalid';
                                        </script>
                                        <div class="invalid-feedback">{{ $Item['attributes']['validation_messages']->first("reorder_quantity") }}</div>
                                    @endif
                                </div>

                            </div>

                            <div class="mb-2 row">

                                <label for="tid" class="col-sm-2 col-form-label-sm">Item Price</label>
                                <div class="col-sm-4">
                                    <input type="text" name="item_price" id="item_price" class="form-control form-control-sm"  value="{{$Item['attributes']['item_price']}}">
                                    @if($Item['attributes']['validation_messages']->has('item_price'))
                                        <script>
                                                document.getElementById('item_price').className = 'form-control form-control-sm is-invalid';
                                        </script>
                                        <div class="invalid-feedback">{{ $Item['attributes']['validation_messages']->first("item_price") }}</div>
                                    @endif
                                </div>

                                <label for="tid" class="col-sm-2 col-form-label-sm">Item Cost</label>
                                <div class="col-sm-4">
                                    <input type="text" name="item_cost" id="item_cost" class="form-control form-control-sm"  value="{{$Item['attributes']['item_cost']}}">
                                    @if($Item['attributes']['validation_messages']->has('item_cost'))
                                        <script>
                                                document.getElementById('item_cost').className = 'form-control form-control-sm is-invalid';
                                        </script>
                                        <div class="invalid-feedback">{{ $Item['attributes']['validation_messages']->first("item_cost") }}</div>
                                    @endif
                                </div>

                            </div>

                            <div class="mb-2 row">

                                <label for="tid" class="col-sm-2 col-form-label-sm">Active</label>
                                <div class="col-sm-2">
                                    <select name="active" id="active" class="form-select form-select-sm" >
                                        @if( $Item['attributes']['active'] )
                                            <option value ="1" selected>Yes</option>
                                            <option value ="0">No</option>
                                        @else
                                            <option value ="1">Yes</option>
                                            <option value ="0" selected>No</option>
                                        @endif
                                    </select>
                                </div>

                                <label for="tid" class="col-sm-2 col-form-label-sm">Tax Apply</label>
                                <div class="col-sm-2">
                                    <select name="tax" id="tax" class="form-select form-select-sm" >
                                        @if( $Item['attributes']['tax'] )
                                            <option value ="1" selected>Yes</option>
                                            <option value ="0">No</option>
                                        @else
                                            <option value ="1">Yes</option>
                                            <option value ="0" selected>No</option>
                                        @endif
                                    </select>
                                    @if($Item['attributes']['validation_messages']->has('tax'))
                                        <script>
                                                document.getElementById('tax').className = 'form-select form-select-sm is-invalid';
                                        </script>
                                        <div class="invalid-feedback">{{ $Item['attributes']['validation_messages']->first("tax") }}</div>
                                    @endif
                                </div>

                                <label for="tid" class="col-sm-2 col-form-label-sm">Receipe</label>
                                <div class="col-sm-2">
                                    <select name="receipe" id="receipe" class="form-select form-select-sm" >
                                        @if( $Item['attributes']['receipe'] )
                                            <option value ="1" selected>Yes</option>
                                            <option value ="0">No</option>
                                        @else
                                            <option value ="1">Yes</option>
                                            <option value ="0" selected>No</option>
                                        @endif
                                    </select>
                                    @if($Item['attributes']['validation_messages']->has('receipe'))
                                        <script>
                                                document.getElementById('receipe').className = 'form-select form-select-sm is-invalid';
                                        </script>
                                        <div class="invalid-feedback">{{ $Item['attributes']['validation_messages']->first("receipe") }}</div>
                                    @endif
                                </div>

                            </div>

                            <div class="mb-5 row">

                                <label for="tid" class="col-sm-2 col-form-label-sm">Remark</label>
                                <div class="col-sm-10">
                                    <textarea  name="remark" id="remark" class="form-control" rows="2" style="resize:none">{{$Item['attributes']['remark']}}</textarea>
                                    @if($Item['attributes']['validation_messages']->has('remark'))
                                        <script>
                                                document.getElementById('remark').className = 'form-control is-invalid';
                                        </script>
                                        <div class="invalid-feedback">{{ $Item['attributes']['validation_messages']->first("remark") }}</div>
                                    @endif
                                </div>
                            </div>
                    
                            <div  class="mb-2 row">

                                <div class="col-2">
                                    <input type="submit" name="submit" id="submit" style="width: 100%;" class="btn btn-primary btn-sm" value="Save">
                                </div>
                            
                            </div>

                        </div>
                        </div>

                        <div class="col-12 col-sm-4 col-md-4">
                        <div style="margin-left: 2%; margin-right: 1%;">


                        </div>
                        </div>


                    </div>

                    
                </div>
            </div>

        </div>

    </form>
    </div>

    

    <script>
        $('#item').keypress(function (e) {
            if (e.which == 13) {
                e.preventDefault();
            }
        });
    </script>


@endsection