@extends('layouts.site_monitoring')
@section('title')
    Employee
@endsection
@section('body')

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{route('employee_process')}}">
                @csrf

                <div class="card mt-3">

                    <div class="card-header">
                        Employee
                    </div>

                    <div class="card-body">

                        <div class="row">
                            <div class="col-sm-12">
                                <?php echo $Emp['attributes']['process_message'];  ?>
                            </div>
                        </div>

                        <div class="row no-gutters">
                            <div class="col-12 col-sm-8 col-md-8">

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Employee ID</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="employee_id" id="employee_id" class="form-control form-control-sm"  value="{{$Emp['attributes']['employee_id']}}" readonly>
                                        @if($Emp['attributes']['validation_messages']->has('employee_id'))
                                            <script>
                                                    document.getElementById('employee_id').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $Emp['attributes']['validation_messages']->first("employee_id") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Employee Code</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="employee_code" id="employee_code" class="form-control form-control-sm"  value="{{$Emp['attributes']['employee_code']}}">
                                        @if($Emp['attributes']['validation_messages']->has('employee_code'))
                                            <script>
                                                    document.getElementById('employee_code').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $Emp['attributes']['validation_messages']->first("employee_code") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Employee Name</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="employee_name" id="employee_name" class="form-control form-control-sm"  value="{{$Emp['attributes']['employee_name']}}">
                                        @if($Emp['attributes']['validation_messages']->has('employee_name'))
                                            <script>
                                                    document.getElementById('employee_name').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $Emp['attributes']['validation_messages']->first("employee_name") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Labour Category</label>
                                    <div class="col-sm-4">
                                        <select name="lc_id" id="lc_id" class="form-select form-select-sm" >
                                            @foreach($Emp['labour_category'] as $row)
                                                @if($Emp['attributes']['lc_id'] == $row->lc_id)
                                                    <option value ="{{$row->lc_id}}" selected>{{$row->lc_name . '  --> ' . number_format($row->price, 2)}}</option>
                                                @else
                                                    <option value ="{{$row->lc_id}}">{{$row->lc_name . '  --> ' . number_format($row->price, 2)}}</option>
                                                @endif
                                            @endforeach
                                            @if($Emp['attributes']['lc_id'] == "0")
                                                <option value ="0" selected>Select the Labour Category </option>
                                            @endif
                                        </select>
                                        @if($Emp['attributes']['validation_messages']->has('lc_id'))
                                            <script>
                                                    document.getElementById('lc_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $Emp['attributes']['validation_messages']->first("lc_id") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Active</label>
                                    <div class="col-sm-2">
                                        <select name="active" id="active" class="form-select form-select-sm" >
                                            @if( $Emp['attributes']['active'] )
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
