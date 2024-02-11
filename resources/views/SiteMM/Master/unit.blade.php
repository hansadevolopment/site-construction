@extends('layouts.site_monitoring')
@section('title')
    Unit
@endsection
@section('body')

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{route('unit_process')}}">
                @csrf

                <div class="card mt-3">

                    <div class="card-header">
                        Unit
                    </div>

                    <div class="card-body">

                        <div class="row">
                            <div class="col-sm-12">
                                <?php echo $U['attributes']['process_message'];  ?>
                            </div>
                        </div>

                        <div class="row no-gutters">
                            <div class="col-12 col-sm-8 col-md-8">

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Unit ID</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="unit_id" id="unit_id" class="form-control form-control-sm"  value="{{$U['attributes']['unit_id']}}" readonly>
                                        @if($U['attributes']['validation_messages']->has('unit_id'))
                                            <script>
                                                    document.getElementById('unit_id').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $U['attributes']['validation_messages']->first("unit_id") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Unit Name</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="unit_name" id="unit_name" class="form-control form-control-sm"  value="{{$U['attributes']['unit_name']}}">
                                        @if($U['attributes']['validation_messages']->has('unit_name'))
                                            <script>
                                                    document.getElementById('unit_name').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $U['attributes']['validation_messages']->first("unit_name") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Active</label>
                                    <div class="col-sm-2">
                                        <select name="active" id="active" class="form-select form-select-sm" >
                                            @if( $U['attributes']['active'] )
                                                <option value ="1" selected>Yes</option>
                                                <option value ="0">No</option>
                                            @else
                                                <option value ="1">Yes</option>
                                                <option value ="0" selected>No</option>
                                            @endif
                                        </select>
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
