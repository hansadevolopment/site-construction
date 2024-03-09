@extends('layouts.gl')
@section('title')
    Sub Account
@endsection
@section('body')

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{route('sub_account_process')}}">
                @csrf

                <div class="card mt-3">

                    <div class="card-header">
                        Sub Account
                    </div>

                    <div class="card-body">

                        <div class="row">
                            <div class="col-sm-12">
                                <?php echo $SA['attributes']['process_message'];  ?>
                            </div>
                        </div>

                        <div class="row no-gutters">

                            <div class="col-12 col-sm-6 col-md-6">

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-3 col-form-label-sm">Controll Account</label>
                                    <div class="col-sm-6">
                                        <select name="ca_id" id="ca_id" class="form-select form-select-sm" >
                                            @foreach($SA['controll_account'] as $row)
                                                @if($SA['attributes']['ca_id'] == $row->ca_id)
                                                    <option value ="{{$row->ca_id}}" selected>{{$row->ca_name}}</option>
                                                @else
                                                    <option value ="{{$row->ca_id}}">{{$row->ca_name}}</option>
                                                @endif
                                            @endforeach
                                            @if($SA['attributes']['ca_id'] == "0")
                                                <option value ="0" selected>Select the Controll Account </option>
                                            @endif
                                        </select>
                                        @if($SA['attributes']['validation_messages']->has('ca_id'))
                                            <script>
                                                    document.getElementById('ca_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $SA['attributes']['validation_messages']->first("ca_id") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-3 col-form-label-sm">Account ID</label>
                                    <div class="col-sm-3">
                                        <input type="text" name="sa_id" id="sa_id" class="form-control form-control-sm"  value="{{$SA['attributes']['sa_id']}}" readonly>
                                        @if($SA['attributes']['validation_messages']->has('sa_id'))
                                            <script>
                                                    document.getElementById('sa_id').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $SA['attributes']['validation_messages']->first("sa_id") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <label for="tid" class="col-sm-3 col-form-label-sm">Account Name</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="sa_name" id="sa_name" class="form-control form-control-sm"  value="{{$SA['attributes']['sa_name']}}">
                                        @if($SA['attributes']['validation_messages']->has('sa_name'))
                                            <script>
                                                    document.getElementById('sa_name').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $SA['attributes']['validation_messages']->first("sa_name") }}</div>
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
