@extends('layouts.inventory')

@section('title')
    Brand
@endsection

@section('body')

    <div id="tbldiv" style="width: 98%;  margin-right: 1%; margin-left: 1%; margin-top: 1%;">
    <form method="POST" action="{{route('brand_process')}}">

        @csrf

        <div class="col-sm-12">
            
            <div class="card">

                <div class="card-header">
                    Brand
                </div>

                <div class="card-body">

                    <div class="col-sm-11">
                        <?php echo $BR['attributes']['process_message'];  ?>
                    </div>

                    <div class="mb-2 row">
                        <label for="tid" class="col-sm-2 col-form-label-sm">Brand ID</label>
                        <div class="col-sm-1">
                            <input type="text" name="brand_id" id="brand_id" class="form-control form-control-sm"  value="{{$BR['attributes']['brand_id']}}" readonly>
                            @if($BR['attributes']['validation_messages']->has('brand_id'))
                            <script>
                                    document.getElementById('brand_id').className = 'form-control form-control-sm is-invalid';
                            </script>
                            <div class="invalid-feedback">{{ $BR['attributes']['validation_messages']->first("brand_id") }}</div>
                        @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <label for="tid" class="col-sm-2 col-form-label-sm">Brand Name</label>
                        <div class="col-sm-10">
                            <input type="text" name="brand_name" id="brand_name" class="form-control form-control-sm"  value="{{$BR['attributes']['brand_name']}}">
                            @if($BR['attributes']['validation_messages']->has('brand_name'))
                                <script>
                                        document.getElementById('brand_name').className = 'form-control form-control-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $BR['attributes']['validation_messages']->first("brand_name") }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-5 row">
                        <label for="tid" class="col-sm-2 col-form-label-sm">Active</label>
                        <div class="col-sm-2">
                            <select name="active" id="active" class="form-select form-select-sm" >
                                @if( $BR['attributes']['active'] )
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