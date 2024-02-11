@extends('layouts.site_monitoring')
@section('title')
    Employee Advance
@endsection
@section('body')

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{route('employee_advance_process')}}">
                @csrf

                <div class="card mt-3">

                    <div class="card-header">
                        Employee Advance
                    </div>

                    <div class="card-body">

                        <div class="row">
                            <div class="col-sm-12">
                                <?php echo $EA['attributes']['process_message'];  ?>
                            </div>
                        </div>

                        <div class="row no-gutters">
                            <div class="col-12 col-sm-12 col-md-12">

                                <div class="row mb-2">

                                    <div class="col-sm-9">
                                    </div>

                                    <label for="tid" class="col-sm-1 col-form-label-sm">EA No</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="ea_id" id="ea_id" class="form-control form-control-sm" value="{{$EA['attributes']['ea_id']}}" readonly>
                                        @if($EA['attributes']['validation_messages']->has('ea_id'))
                                            <script>
                                                    document.getElementById('ea_id').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $EA['attributes']['validation_messages']->first("ea_id") }}</div>
                                        @endif
                                    </div>

                                </div>

                                <div class="row mb-2">

                                    <label for="tid" class="col-sm-1 col-form-label-sm">Salary Category</label>
                                    <div class="col-sm-3">
                                        <select name="sc_id" id="sc_id" class="form-select form-select-sm" >
                                            @foreach($EA['salary_category'] as $row)
                                                @if($EA['attributes']['sc_id'] == $row->id)
                                                    <option value ="{{$row->id}}" selected>{{$row->category}}</option>
                                                @else
                                                    <option value ="{{$row->id}}">{{$row->category}}</option>
                                                @endif
                                            @endforeach
                                            @if($EA['attributes']['sc_id'] == "0")
                                                <option value ="0" selected>Select the Salary Category </option>
                                            @endif
                                        </select>
                                        @if($EA['attributes']['validation_messages']->has('sc_id'))
                                            <script>
                                                    document.getElementById('sc_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $EA['attributes']['validation_messages']->first("sc_id") }}</div>
                                        @endif
                                    </div>

                                    <div class="col-sm-5">
                                    </div>

                                    <label for="tid" class="col-sm-1 col-form-label-sm">EA Date</label>
                                    <div class="col-sm-2">
                                        <input type="date" name="ea_date" id="ea_date" class="form-control form-control-sm" value="{{$EA['attributes']['ea_date']}}">
                                        @if($EA['attributes']['validation_messages']->has('ea_date'))
                                            <script>
                                                    document.getElementById('ea_date').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $EA['attributes']['validation_messages']->first("ea_date") }}</div>
                                        @endif
                                    </div>

                                </div>


                                <div class="row mb-2">

                                    <label for="tid" class="col-sm-1 col-form-label-sm">Employee</label>
                                    <div class="col-sm-5">
                                        <select name="employee_id" id="employee_id" class="form-select form-select-sm" >
                                            @foreach($EA['employee'] as $row)
                                                @if($EA['attributes']['employee_id'] == $row->employee_id)
                                                    <option value ="{{$row->employee_id}}" selected>{{$row->employee_name . ' -->  ' . $row->day_salary}}</option>
                                                @else
                                                    <option value ="{{$row->employee_id}}">{{$row->employee_name . ' -->  ' . $row->day_salary}}</option>
                                                @endif
                                            @endforeach
                                            @if($EA['attributes']['employee_id'] == "0")
                                                <option value ="0" selected>Select the Employee </option>
                                            @endif
                                        </select>
                                        @if($EA['attributes']['validation_messages']->has('employee_id'))
                                            <script>
                                                    document.getElementById('employee_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $EA['attributes']['validation_messages']->first("employee_id") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Site</label>
                                    <div class="col-sm-5">
                                        <select name="site_id" id="site_id" class="form-select form-select-sm" >
                                            @foreach($EA['site'] as $row)
                                                @if($EA['attributes']['site_id'] == $row->site_id)
                                                    <option value ="{{$row->site_id}}" selected>{{$row->site_name}}</option>
                                                @else
                                                    <option value ="{{$row->site_id}}">{{$row->site_name}}</option>
                                                @endif
                                            @endforeach
                                            @if($EA['attributes']['site_id'] == "0")
                                                <option value ="0" selected>Select the Site </option>
                                            @endif
                                        </select>
                                        @if($EA['attributes']['validation_messages']->has('site_id'))
                                            <script>
                                                    document.getElementById('site_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $EA['attributes']['validation_messages']->first("site_id") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Task</label>
                                    <div class="col-sm-5">
                                        <select name="task_id" id="task_id" class="form-select form-select-sm" >
                                            @if( ($EA['attributes']['task_id'] != "0") || ($EA['attributes']['site_id'] != "0") )
                                                @foreach($EA['site_task'] as $row)
                                                    @if($EA['attributes']['task_id'] == $row->task_id)
                                                        <option value ="{{$row->task_id}}" selected>{{$row->task_name}}</option>
                                                    @else
                                                        <option value ="{{$row->task_id}}">{{$row->task_name}}</option>
                                                    @endif
                                                @endforeach
                                            @endif
                                            @if( $EA['attributes']['task_id'] != "0" )
                                                <option value ="0">Select the Task </option>
                                            @else
                                                <option value ="0" selected>Select the Task </option>
                                            @endif
                                        </select>
                                        @if($EA['attributes']['validation_messages']->has('task_id'))
                                            <script>
                                                    document.getElementById('task_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $EA['attributes']['validation_messages']->first("task_id") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Sub Task</label>
                                    <div class="col-sm-5">
                                        <select name="sub_task_id" id="sub_task_id" class="form-select form-select-sm" >
                                            @if( ($EA['attributes']['sub_task_id'] != "0") || ($EA['attributes']['task_id'] != "0") )
                                                @foreach($EA['site_sub_task'] as $row)
                                                    @if($EA['attributes']['sub_task_id'] == $row->sub_task_id)
                                                        <option value ="{{$row->sub_task_id}}" selected>{{$row->sub_task_name}}</option>
                                                    @else
                                                        <option value ="{{$row->sub_task_id}}">{{$row->sub_task_name}}</option>
                                                    @endif
                                                @endforeach
                                            @endif
                                            @if( $EA['attributes']['sub_task_id'] != "0" )
                                                <option value ="0">Select the Sub Task </option>
                                            @else
                                                <option value ="0" selected>Select the Sub Task </option>
                                            @endif
                                        </select>
                                        @if($EA['attributes']['validation_messages']->has('sub_task_id'))
                                            <script>
                                                    document.getElementById('sub_task_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $EA['attributes']['validation_messages']->first("sub_task_id") }}</div>
                                        @endif
                                    </div>
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Task Unit</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="task_unit" id="task_unit" class="form-control form-control-sm" value="" readonly>
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Advance Amt</label>
                                    <div class="col-sm-3">
                                        <input type="text" name="advance_amount" id="advance_amount" class="form-control form-control-sm text-end" value="{{$EA['attributes']['advance_amount']}}">
                                        @if($EA['attributes']['validation_messages']->has('advance_amount'))
                                            <script>
                                                    document.getElementById('advance_amount').className = 'form-select form-select-sm is-invalid text-end';
                                            </script>
                                            <div class="invalid-feedback">{{ $EA['attributes']['validation_messages']->first("advance_amount") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Remark</label>
                                    <div class="col-sm-11">
                                        <input type="text" name="remark" id="remark" class="form-control form-control-sm" value="{{$EA['attributes']['remark']}}">
                                        @if($EA['attributes']['validation_messages']->has('remark'))
                                            <script>
                                                    document.getElementById('remark').className = 'form-control is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $EA['attributes']['validation_messages']->first("remark") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-sm-1">
                                        <input type="submit" name="submit" id="submit" class="btn btn-primary btn-sm w-100" value="Save">
                                    </div>
                                    <div class="col-sm-1">
                                        <input type="submit" name="submit" id="submit" class="btn btn-primary btn-sm w-100" value="Reset">
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>
                </div>

            </form>
        </div>
    </div>

@endsection
