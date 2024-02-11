@extends('layouts.site_monitoring')
@section('title')
    Overhead Cost
@endsection
@section('body')

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{route('overhead_cost_process')}}">
                @csrf

                <div class="card mt-3">

                    <div class="card-header">
                        Overhead Cost
                    </div>

                    <div class="card-body">

                        <div class="row">
                            <div class="col-sm-12">
                                <?php echo $OC['attributes']['process_message'];  ?>
                            </div>
                        </div>

                        <div class="row no-gutters">
                            <div class="col-12 col-sm-8 col-md-8">

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">OC Item ID</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="oci_id" id="oci_id" class="form-control form-control-sm"  value="{{$OC['attributes']['oci_id']}}" readonly>
                                        @if($OC['attributes']['validation_messages']->has('oci_id'))
                                            <script>
                                                    document.getElementById('oci_id').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $OC['attributes']['validation_messages']->first("oci_id") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">OC Item Name</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="oci_name" id="oci_name" class="form-control form-control-sm"  value="{{$OC['attributes']['oci_name']}}">
                                        @if($OC['attributes']['validation_messages']->has('oci_name'))
                                            <script>
                                                    document.getElementById('oci_name').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $OC['attributes']['validation_messages']->first("oci_name") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Unit</label>
                                    <div class="col-sm-3">
                                        <select name="unit_id" id="unit_id" class="form-select form-select-sm" >
                                            @foreach($OC['unit'] as $row)
                                                @if($OC['attributes']['unit_id'] == $row->unit_id)
                                                    <option value ="{{$row->unit_id}}" selected>{{$row->unit_name}}</option>
                                                @else
                                                    <option value ="{{$row->unit_id}}">{{$row->unit_name}}</option>
                                                @endif
                                            @endforeach
                                            @if($OC['attributes']['unit_id'] == "0")
                                                <option value ="0" selected>Select the Unit </option>
                                            @endif
                                        </select>
                                        @if($OC['attributes']['validation_messages']->has('unit_id'))
                                            <script>
                                                    document.getElementById('unit_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $OC['attributes']['validation_messages']->first("unit_id") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Rental</label>
                                    <div class="col-sm-2">
                                        <select name="rental" id="rental" class="form-select form-select-sm" >
                                            @if( $OC['attributes']['rental'] )
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
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Active</label>
                                    <div class="col-sm-2">
                                        <select name="active" id="active" class="form-select form-select-sm" >
                                            @if( $OC['attributes']['active'] )
                                                <option value ="1" selected>Yes</option>
                                                <option value ="0">No</option>
                                            @else
                                                <option value ="1">Yes</option>
                                                <option value ="0" selected>No</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Remark</label>
                                    <div class="col-sm-10">
                                        <textarea  name="remark" id="remark" class="form-control" rows="2" style="resize:none">{{$OC['attributes']['remark']}}</textarea>
                                        @if($OC['attributes']['validation_messages']->has('remark'))
                                            <script>
                                                    document.getElementById('remark').className = 'form-control is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $OC['attributes']['validation_messages']->first("remark") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div  class="row mb-2">
                                    <div class="col-2">
                                        <input type="submit" name="submit" id="submit" style="width: 100%;" class="btn btn-primary btn-sm" value="Save">
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
