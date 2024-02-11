@extends('layouts.site_monitoring')
@section('title')
    Item
@endsection
@section('body')

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{route('item_process')}}">
                @csrf

                <div class="card mt-3">

                    <div class="card-header">
                        Item
                    </div>

                    <div class="card-body">

                        <div class="row">
                            <div class="col-sm-12">
                                <?php echo $Item['attributes']['process_message'];  ?>
                            </div>
                        </div>

                        <div class="row no-gutters">
                            <div class="col-12 col-sm-8 col-md-8">

                                <div class="row mb-2">
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

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Item Name</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="item_name" id="item_name" class="form-control form-control-sm"  value="{{$Item['attributes']['item_name']}}">
                                        @if($Item['attributes']['validation_messages']->has('item_name'))
                                            <script>
                                                    document.getElementById('item_name').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $Item['attributes']['validation_messages']->first("item_name") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Unit</label>
                                    <div class="col-sm-4">
                                        <select name="unit_id" id="unit_id" class="form-select form-select-sm" >
                                            @foreach($Item['unit'] as $row)
                                                @if($Item['attributes']['unit_id'] == $row->unit_id)
                                                    <option value ="{{$row->unit_id}}" selected>{{$row->unit_name}}</option>
                                                @else
                                                    <option value ="{{$row->unit_id}}">{{$row->unit_name}}</option>
                                                @endif
                                            @endforeach
                                            @if($Item['attributes']['unit_id'] == "0")
                                                <option value ="0" selected>Select the Unit </option>
                                            @endif
                                        </select>
                                        @if($Item['attributes']['validation_messages']->has('unit_id'))
                                            <script>
                                                    document.getElementById('unit_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $Item['attributes']['validation_messages']->first("unit_id") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Active</label>
                                    <div class="col-sm-4">
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
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Price</label>
                                    <div class="col-sm-4">
                                        <input type="text" name="price" id="price" class="form-control form-control-sm text-end"  value="{{$Item['attributes']['price']}}">
                                        @if($Item['attributes']['validation_messages']->has('price'))
                                            <script>
                                                    document.getElementById('price').className = 'form-control form-control-sm is-invalid text-end';
                                            </script>
                                            <div class="invalid-feedback">{{ $Item['attributes']['validation_messages']->first("price") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-4">
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

                                <div  class="row mb-2">
                                    <div class="col-2">
                                        <input type="submit" name="submit" id="submit" style="width: 100%;" class="btn btn-primary btn-sm" value="Save">
                                    </div>
                                    <div class="col-2">
                                        <input type="submit" name="submit" id="submit" style="width: 100%;" class="btn btn-primary btn-sm" value="Reset">
                                    </div>
                                </div>

                            </div>

                            <div class="col-12 col-sm-4 col-md-4">
                            </div>

                        </div>

                    </div>
                </div>

            </form>
        </div>
    </div>

@endsection
