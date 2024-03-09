@extends('layouts.gl')
@section('title')
    Main Account
@endsection
@section('body')

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{route('main_account_process')}}">
                @csrf

                <div class="card mt-3">

                    <div class="card-header">
                        Main Account
                    </div>

                    <div class="card-body">

                        <div class="row">
                            <div class="col-sm-12">
                                <?php echo $MA['attributes']['process_message'];  ?>
                            </div>
                        </div>

                        <div class="row no-gutters">

                            <div class="col-12 col-sm-6 col-md-6">

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Account Type</label>
                                    <div class="col-sm-4">
                                        <select name="at_id" id="at_id" class="form-select form-select-sm" >
                                            @foreach($MA['account_type'] as $row)
                                                @if($MA['attributes']['at_id'] == $row->at_id)
                                                    <option value ="{{$row->at_id}}" selected>{{$row->at_name}}</option>
                                                @else
                                                    <option value ="{{$row->at_id}}">{{$row->at_name}}</option>
                                                @endif
                                            @endforeach
                                            @if($MA['attributes']['at_id'] == "0")
                                                <option value ="0" selected>Select the Account Type </option>
                                            @endif
                                        </select>
                                        @if($MA['attributes']['validation_messages']->has('at_id'))
                                            <script>
                                                    document.getElementById('at_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $MA['attributes']['validation_messages']->first("at_id") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Account ID</label>
                                    <div class="col-sm-3">
                                        <input type="text" name="ma_id" id="ma_id" class="form-control form-control-sm"  value="{{$MA['attributes']['ma_id']}}" readonly>
                                        @if($MA['attributes']['validation_messages']->has('ma_id'))
                                            <script>
                                                    document.getElementById('ma_id').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $MA['attributes']['validation_messages']->first("ma_id") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Account Name</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="ma_name" id="ma_name" class="form-control form-control-sm"  value="{{$MA['attributes']['ma_name']}}">
                                        @if($MA['attributes']['validation_messages']->has('ma_name'))
                                            <script>
                                                    document.getElementById('ma_name').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $MA['attributes']['validation_messages']->first("ma_name") }}</div>
                                        @endif
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
