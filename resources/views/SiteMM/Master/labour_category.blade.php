@extends('layouts.site_monitoring')
@section('title')
    Labour Category
@endsection
@section('body')

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{route('labour_category_process')}}">
                @csrf

                <div class="card mt-3">

                    <div class="card-header">
                        Labour Category
                    </div>

                    <div class="card-body">

                        <div class="row">
                            <div class="col-sm-12">
                                <?php echo $LC['attributes']['process_message'];  ?>
                            </div>
                        </div>

                        <div class="row no-gutters">
                            <div class="col-12 col-sm-8 col-md-8">

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">LC ID</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="lc_id" id="lc_id" class="form-control form-control-sm"  value="{{$LC['attributes']['lc_id']}}" readonly>
                                        @if($LC['attributes']['validation_messages']->has('lc_id'))
                                            <script>
                                                    document.getElementById('lc_id').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $LC['attributes']['validation_messages']->first("lc_id") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">LC Name</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="lc_name" id="lc_name" class="form-control form-control-sm"  value="{{$LC['attributes']['lc_name']}}">
                                        @if($LC['attributes']['validation_messages']->has('lc_name'))
                                            <script>
                                                    document.getElementById('lc_name').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $LC['attributes']['validation_messages']->first("lc_name") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Active</label>
                                    <div class="col-sm-2">
                                        <select name="active" id="active" class="form-select form-select-sm" >
                                            @if( $LC['attributes']['active'] )
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
                                    <div class="col-sm-3">
                                        <input type="number" name="price" id="price" class="form-control form-control-sm text-end"  value="{{$LC['attributes']['price']}}">
                                        @if($LC['attributes']['validation_messages']->has('price'))
                                            <script>
                                                    document.getElementById('price').className = 'form-control form-control-sm is-invalid text-end';
                                            </script>
                                            <div class="invalid-feedback">{{ $LC['attributes']['validation_messages']->first("price") }}</div>
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
