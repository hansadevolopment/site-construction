@extends('layouts.purchasing')

@section('title')
    Purchasing Location
@endsection

@section('body')

    <div id="tbldiv" style="width: 98%;  margin-right: 1%; margin-left: 1%; margin-top: 1%;">
    <form method="POST" action="{{route('purchasing_location_process')}}">

        @csrf

        <div class="col-sm-12">

            <div class="card">

                <div class="card-header">
                    Purchasing Location
                </div>

                <div class="card-body">

                    <div class="col-sm-11">
                        <?php echo $PC['attributes']['process_message'];  ?>
                    </div>

                    <div class="mb-2 row">
                        <label for="tid" class="col-sm-2 col-form-label-sm">Purchasing Location ID</label>
                        <div class="col-sm-2">
                            <input type="text" name="pl_id" id="pl_id" class="form-control form-control-sm"  value="{{$PC['attributes']['pl_id']}}" readonly>
                            @if($PC['attributes']['validation_messages']->has('pl_id'))
                            <script>
                                    document.getElementById('pl_id').className = 'form-control form-control-sm is-invalid';
                            </script>
                            <div class="invalid-feedback">{{ $PC['attributes']['validation_messages']->first("pl_id") }}</div>
                        @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <label for="tid" class="col-sm-2 col-form-label-sm">Purchasing Location Name</label>
                        <div class="col-sm-6">
                            <input type="text" name="pl_name" id="pl_name" class="form-control form-control-sm"  value="{{$PC['attributes']['pl_name']}}">
                            @if($PC['attributes']['validation_messages']->has('pl_name'))
                                <script>
                                        document.getElementById('pl_name').className = 'form-control form-control-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $PC['attributes']['validation_messages']->first("pl_name") }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-5 row">
                        <label for="tid" class="col-sm-2 col-form-label-sm">Active</label>
                        <div class="col-sm-2">
                            <select name="active" id="active" class="form-select form-select-sm" >
                                @if( $PC['attributes']['active'] )
                                    <option value ="1" selected>Yes</option>
                                    <option value ="0">No</option>
                                @else
                                    <option value ="1">Yes</option>
                                    <option value ="0" selected>No</option>
                                @endif
                            </select>
                        </div>
                    </div>

                    <div  class="mb-2 row">

                        <div class="col-2">
                              <input type="submit" name="submit" id="submit" style="width: 100%;" class="btn btn-primary btn-sm" value="Save">
                        </div>

                    </div>

                </div>
            </div>

        </div>

    </form>
    </div>

@endsection
