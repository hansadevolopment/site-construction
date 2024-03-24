@extends('layouts.gl')
@section('title')
    Bank
@endsection
@section('body')

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{route('bank_process')}}">
                @csrf

                <div class="card mt-3">

                    <div class="card-header">
                        Bank
                    </div>

                    <div class="card-body">

                        <div class="row">
                            <div class="col-sm-12">
                                <?php echo $B['attributes']['process_message'];  ?>
                            </div>
                        </div>

                        <div class="row no-gutters">

                            <div class="col-12 col-sm-6 col-md-6">

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Bank ID</label>
                                    <div class="col-sm-3">
                                        <input type="text" name="bank_id" id="bank_id" class="form-control form-control-sm"  value="{{$B['attributes']['bank_id']}}" readonly>
                                        @if($B['attributes']['validation_messages']->has('bank_id'))
                                            <script>
                                                    document.getElementById('bank_id').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $B['attributes']['validation_messages']->first("bank_id") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Bank Name</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="bank_name" id="bank_name" class="form-control form-control-sm"  value="{{$B['attributes']['bank_name']}}">
                                        @if($B['attributes']['validation_messages']->has('bank_name'))
                                            <script>
                                                    document.getElementById('bank_name').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $B['attributes']['validation_messages']->first("bank_name") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Active</label>
                                    <div class="col-sm-4">
                                        <select name="active" id="active" class="form-select form-select-sm" >
                                            @if( $B['attributes']['active'] )
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
