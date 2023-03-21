@extends('layouts.purchase')

@section('title')
    Purchasing Category
@endsection

@section('body')

    <div id="tbldiv" style="width: 98%;  margin-right: 1%; margin-left: 1%; margin-top: 1%;">
    <form method="POST" action="{{route('purchase_category_process')}}">

        @csrf

        <div class="col-sm-12">
            
            <div class="card">

                <div class="card-header">
                    Purchasing Category
                </div>

                <div class="card-body">

                    <div class="col-sm-11">
                        <?php echo $PC['attributes']['process_message'];  ?>
                    </div>

                    <div class="mb-2 row">
                        <label for="tid" class="col-sm-2 col-form-label-sm">Purchasing Category ID</label>
                        <div class="col-sm-1">
                            <input type="text" name="purchasing_category_id" id="purchasing_category_id" class="form-control form-control-sm"  value="{{$PC['attributes']['purchasing_category_id']}}" readonly>
                            @if($PC['attributes']['validation_messages']->has('purchasing_category_id'))
                            <script>
                                    document.getElementById('purchasing_category_id').className = 'form-control form-control-sm is-invalid';
                            </script>
                            <div class="invalid-feedback">{{ $PC['attributes']['validation_messages']->first("purchasing_category_id") }}</div>
                        @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <label for="tid" class="col-sm-2 col-form-label-sm">Purchasing Category Name</label>
                        <div class="col-sm-10">
                            <input type="text" name="purchasing_category_name" id="purchasing_category_name" class="form-control form-control-sm"  value="{{$PC['attributes']['purchasing_category_name']}}">
                            @if($PC['attributes']['validation_messages']->has('purchasing_category_name'))
                                <script>
                                        document.getElementById('purchasing_category_name').className = 'form-control form-control-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $PC['attributes']['validation_messages']->first("purchasing_category_name") }}</div>
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

    <script>

        $('#supplier').keypress(function (e) {
            if (e.which == 13) {
                e.preventDefault();
            }
        });

    </script>



@endsection