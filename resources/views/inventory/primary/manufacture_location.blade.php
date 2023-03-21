@extends('layouts.inventory')

@section('title')
    Manufacture Location
@endsection

@section('body')

    <div id="tbldiv" style="width: 98%;  margin-right: 1%; margin-left: 1%; margin-top: 1%;">
    <form method="POST" action="{{route('manufacture_location_process')}}">

        @csrf

        <div class="col-sm-12">
            
            <div class="card">

                <div class="card-header">
                    Manufacture Location
                </div>

                <div class="card-body">

                    <div class="col-sm-11">
                        <?php echo $ML['attributes']['process_message'];  ?>
                    </div>

                    <div class="mb-2 row">
                        <label for="tid" class="col-sm-2 col-form-label-sm">Manufacture Location ID</label>
                        <div class="col-sm-1">
                            <input type="text" name="manufacture_location_id" id="manufacture_location_id" class="form-control form-control-sm"  value="{{$ML['attributes']['manufacture_location_id']}}" readonly>
                            @if($ML['attributes']['validation_messages']->has('manufacture_location_id'))
                            <script>
                                    document.getElementById('manufacture_location_id').className = 'form-control form-control-sm is-invalid';
                            </script>
                            <div class="invalid-feedback">{{ $ML['attributes']['validation_messages']->first("manufacture_location_id") }}</div>
                        @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <label for="tid" class="col-sm-2 col-form-label-sm">Manufacture Location Name</label>
                        <div class="col-sm-10">
                            <input type="text" name="manufacture_location_name" id="manufacture_location_name" class="form-control form-control-sm"  value="{{$ML['attributes']['manufacture_location_name']}}">
                            @if($ML['attributes']['validation_messages']->has('manufacture_location_name'))
                                <script>
                                        document.getElementById('manufacture_location_name').className = 'form-control form-control-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $ML['attributes']['validation_messages']->first("manufacture_location_name") }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-5 row">
                        <label for="tid" class="col-sm-2 col-form-label-sm">Active</label>
                        <div class="col-sm-2">
                            <select name="active" id="active" class="form-select form-select-sm" >
                                @if( $ML['attributes']['active'] )
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

    <script>

        $('#supplier').keypress(function (e) {
            if (e.which == 13) {
                e.preventDefault();
            }
        });

    </script>



@endsection