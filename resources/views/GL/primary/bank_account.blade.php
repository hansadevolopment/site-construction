@extends('layouts.gl')
@section('title')
    Bank Account
@endsection
@section('body')

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{route('bank_account_process')}}">
                @csrf

                <div class="card mt-3">

                    <div class="card-header">
                        Bank Account
                    </div>

                    <div class="card-body">

                        <div class="row">
                            <div class="col-sm-12">
                                <?php echo $BA['attributes']['process_message'];  ?>
                            </div>
                        </div>

                        <div class="row no-gutters">

                            <div class="col-12 col-sm-8 col-md-8">

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Bank Account ID</label>
                                    <div class="col-sm-3">
                                        <input type="text" name="ba_id" id="ba_id" class="form-control form-control-sm"  value="{{$BA['attributes']['ba_id']}}" readonly>
                                        @if($BA['attributes']['validation_messages']->has('ba_id'))
                                            <script>
                                                    document.getElementById('ba_id').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $BA['attributes']['validation_messages']->first("ba_id") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Bank Account No.</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="ba_no" id="ba_no" class="form-control form-control-sm"  value="{{$BA['attributes']['ba_no']}}">
                                        @if($BA['attributes']['validation_messages']->has('ba_no'))
                                            <script>
                                                    document.getElementById('ba_no').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $BA['attributes']['validation_messages']->first("ba_no") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Branch Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="branch_name" id="branch_name" class="form-control form-control-sm"  value="{{$BA['attributes']['branch_name']}}">
                                        @if($BA['attributes']['validation_messages']->has('branch_name'))
                                            <script>
                                                    document.getElementById('branch_name').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $BA['attributes']['validation_messages']->first("branch_name") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Short Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="short_name" id="short_name" class="form-control form-control-sm"  value="{{$BA['attributes']['short_name']}}">
                                        @if($BA['attributes']['validation_messages']->has('short_name'))
                                            <script>
                                                    document.getElementById('short_name').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $BA['attributes']['validation_messages']->first("short_name") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Bank</label>
                                    <div class="col-sm-8">
                                        <select name="bank_id" id="bank_id" class="form-select form-select-sm">
                                            @foreach($BA['bank'] as $row)
                                                @if($BA['attributes']['bank_id'] == $row->bank_id)
                                                    <option value ="{{$row->bank_id}}" selected>{{$row->bank_name}}</option>
                                                @else
                                                    <option value ="{{$row->bank_id}}">{{$row->bank_name}}</option>
                                                @endif
                                            @endforeach
                                            @if($BA['attributes']['bank_id'] == "0")
                                                <option value =0 selected>Select the Bank</option>
                                            @endif
                                        </select>
                                        @if($BA['attributes']['validation_messages']->has('bank_id'))
                                            <script>
                                                    document.getElementById('bank_id').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $BA['attributes']['validation_messages']->first("bank_id") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Active</label>
                                    <div class="col-sm-4">
                                        <select name="active" id="active" class="form-select form-select-sm" >
                                            @if( $BA['attributes']['active'] )
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

                            <div class="col-12 col-sm-4 col-md-4">
                            </div>

                        </div>

                    </div>

                </div>

            </form>
        </div>
    </div>

@endsection
