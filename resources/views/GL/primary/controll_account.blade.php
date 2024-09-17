@extends('layouts.gl')
@section('title')
    Controll Account
@endsection
@section('body')

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{route('controll_account_process')}}">
                @csrf

                <div class="card mt-3">

                    <div class="card-header">
                        Controll Account
                    </div>

                    <div class="card-body">

                        <div class="row">
                            <div class="col-sm-12">
                                <?php echo $CA['attributes']['process_message'];  ?>
                            </div>
                        </div>

                        <div class="row no-gutters">

                            <div class="col-12 col-sm-6 col-md-6">

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Main Account</label>
                                    <div class="col-sm-4">
                                        <select name="ma_id" id="ma_id" class="form-select form-select-sm" >
                                            @foreach($CA['main_account'] as $row)
                                                @if($CA['attributes']['ma_id'] == $row->ma_id)
                                                    <option value ="{{$row->ma_id}}" selected>{{$row->ma_name}}</option>
                                                @else
                                                    <option value ="{{$row->ma_id}}">{{$row->ma_name}}</option>
                                                @endif
                                            @endforeach
                                            @if($CA['attributes']['ma_id'] == "0")
                                                <option value ="0" selected>Select the Main Account </option>
                                            @endif
                                        </select>
                                        @if($CA['attributes']['validation_messages']->has('ma_id'))
                                            <script>
                                                    document.getElementById('ma_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $CA['attributes']['validation_messages']->first("ma_id") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Account ID</label>
                                    <div class="col-sm-3">
                                        <input type="text" name="ca_id" id="ca_id" class="form-control form-control-sm"  value="{{$CA['attributes']['ca_id']}}" readonly>
                                        @if($CA['attributes']['validation_messages']->has('ca_id'))
                                            <script>
                                                    document.getElementById('ca_id').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $CA['attributes']['validation_messages']->first("ca_id") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Account Name</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="ca_name" id="ca_name" class="form-control form-control-sm"  value="{{$CA['attributes']['ca_name']}}">
                                        @if($CA['attributes']['validation_messages']->has('ca_name'))
                                            <script>
                                                    document.getElementById('ca_name').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $CA['attributes']['validation_messages']->first("ca_name") }}</div>
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
