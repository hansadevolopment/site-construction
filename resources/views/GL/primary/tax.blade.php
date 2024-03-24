@extends('layouts.gl')
@section('title')
    Tax
@endsection
@section('body')

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{route('tax_process')}}">
                @csrf

                <div class="card mt-3">

                    <div class="card-header">
                        Tax
                    </div>

                    <div class="card-body">

                        <div class="row">
                            <div class="col-sm-12">
                                <?php echo $Tax['attributes']['process_message'];  ?>
                            </div>
                        </div>

                        <div class="row no-gutters">

                            <div class="col-12 col-sm-6 col-md-6">

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Tax ID</label>
                                    <div class="col-sm-3">
                                        <input type="text" name="tax_id" id="tax_id" class="form-control form-control-sm"  value="{{$Tax['attributes']['tax_id']}}" readonly>
                                        @if($Tax['attributes']['validation_messages']->has('tax_id'))
                                            <script>
                                                    document.getElementById('tax_id').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $Tax['attributes']['validation_messages']->first("tax_id") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Tax Name</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="tax_name" id="tax_name" class="form-control form-control-sm"  value="{{$Tax['attributes']['tax_name']}}">
                                        @if($Tax['attributes']['validation_messages']->has('tax_name'))
                                            <script>
                                                    document.getElementById('tax_name').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $Tax['attributes']['validation_messages']->first("tax_name") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Short Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="short_name" id="short_name" class="form-control form-control-sm"  value="{{$Tax['attributes']['short_name']}}">
                                        @if($Tax['attributes']['validation_messages']->has('short_name'))
                                            <script>
                                                    document.getElementById('short_name').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $Tax['attributes']['validation_messages']->first("short_name") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Tax Rate</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="tax_rate" id="tax_rate" class="form-control form-control-sm"  value="{{$Tax['attributes']['tax_rate']}}">
                                        @if($Tax['attributes']['validation_messages']->has('tax_rate'))
                                            <script>
                                                    document.getElementById('tax_rate').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $Tax['attributes']['validation_messages']->first("tax_rate") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Active</label>
                                    <div class="col-sm-4">
                                        <select name="active" id="active" class="form-select form-select-sm" >
                                            @if( $Tax['attributes']['active'] )
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
                                    <div class="col-2">
                                        <input type="submit" name="submit" id="submit" style="width: 100%;" class="btn btn-primary btn-sm" value="Reset">
                                    </div>
                                </div>

                            </div>

                            <div class="col-12 col-sm-6 col-md-6">
                            </div>

                        </div>

                    </div>

                </div>

            </form>
        </div>
    </div>

@endsection
