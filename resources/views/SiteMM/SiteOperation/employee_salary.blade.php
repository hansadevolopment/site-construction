@extends('layouts.site_monitoring')
@section('title')
    Employee Salary
@endsection
@section('body')

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{route('employee_salary_process')}}">
                @csrf

                <div class="card mt-3">

                    <div class="card-header">
                        Employee Salary
                    </div>

                    <div class="card-body">

                        <div class="row">
                            <div class="col-sm-12">
                                <?php echo $ES['attributes']['process_message'];  ?>
                            </div>
                        </div>

                        <div class="row no-gutters">
                            <div class="col-12 col-sm-12 col-md-12">

                                <div class="row mb-2">

                                    <div class="col-sm-9">
                                    </div>

                                    <label for="tid" class="col-sm-1 col-form-label-sm">ES No</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="es_id" id="es_id" class="form-control form-control-sm" value="{{$ES['attributes']['es_id']}}" readonly>
                                        @if($ES['attributes']['validation_messages']->has('es_id'))
                                            <script>
                                                    document.getElementById('es_id').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $ES['attributes']['validation_messages']->first("es_id") }}</div>
                                        @endif
                                    </div>

                                </div>


                                <div class="row mb-2">

                                    <label for="tid" class="col-sm-1 col-form-label-sm">Salary Category</label>
                                    <div class="col-sm-2">
                                        <select name="sc_id" id="sc_id" class="form-select form-select-sm" >
                                            @foreach($ES['salary_category'] as $row)
                                                @if( $row->id == 1 )
                                                    @if($ES['attributes']['sc_id'] == $row->id)
                                                        <option value ="{{$row->id}}" selected>{{$row->category}}</option>
                                                    @else
                                                        <option value ="{{$row->id}}">{{$row->category}}</option>
                                                    @endif
                                                @endif
                                            @endforeach
                                            @if($ES['attributes']['sc_id'] == "0")
                                                <option value ="0" selected>Select the Salary Category </option>
                                            @endif
                                        </select>
                                        @if($ES['attributes']['validation_messages']->has('sc_id'))
                                            <script>
                                                    document.getElementById('sc_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $ES['attributes']['validation_messages']->first("sc_id") }}</div>
                                        @endif
                                    </div>

                                    <div class="col-sm-6">
                                    </div>

                                    <label for="tid" class="col-sm-1 col-form-label-sm">ES Date</label>
                                    <div class="col-sm-2">
                                        <input type="date" name="es_date" id="es_date" class="form-control form-control-sm" value="{{$ES['attributes']['es_date']}}">
                                        @if($ES['attributes']['validation_messages']->has('es_date'))
                                            <script>
                                                    document.getElementById('es_date').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $ES['attributes']['validation_messages']->first("es_date") }}</div>
                                        @endif
                                    </div>

                                </div>

                                <div class="row mb-2">

                                    <label for="tid" class="col-sm-1 col-form-label-sm">Employee</label>
                                    <div class="col-sm-5">
                                        <select name="employee_id" id="employee_id" class="form-select form-select-sm" >
                                            @foreach($ES['employee'] as $row)
                                                @if($ES['attributes']['employee_id'] == $row->employee_id)
                                                    <option value ="{{$row->employee_id}}" selected>{{$row->employee_name . ' :- ' . number_format($row->day_salary, 2) }}</option>
                                                @else
                                                    <option value ="{{$row->employee_id}}">{{$row->employee_name . ' :- ' . number_format($row->day_salary, 2)}}</option>
                                                @endif
                                            @endforeach
                                            @if($ES['attributes']['employee_id'] == "0")
                                                <option value ="0" selected>Select the Employee </option>
                                            @endif
                                        </select>
                                        @if($ES['attributes']['validation_messages']->has('employee_id'))
                                            <script>
                                                    document.getElementById('employee_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $ES['attributes']['validation_messages']->first("employee_id") }}</div>
                                        @endif
                                    </div>

                                    <label for="tid" class="col-sm-1 col-form-label-sm">Site Count</label>
                                    <div class="col-sm-2">
                                        <select name="site_count" id="site_count" class="form-select form-select-sm" >
                                            @foreach($ES['site_count'] as $row)
                                                @if($ES['attributes']['site_count'] == $row->id)
                                                    <option value ="{{$row->id}}" selected>{{$row->iteration}}</option>
                                                @else
                                                    <option value ="{{$row->id}}">{{$row->iteration}}</option>
                                                @endif
                                            @endforeach
                                            @if($ES['attributes']['site_count'] == "0")
                                                <option value ="0" selected>Select the Site Count </option>
                                            @endif
                                        </select>
                                        @if($ES['attributes']['validation_messages']->has('site_count'))
                                            <script>
                                                    document.getElementById('site_count').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $ES['attributes']['validation_messages']->first("site_count") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-1 col-form-label-sm">In Date Time</label>
                                    <div class="col-sm-2">
                                        <input type="datetime-local" name="in_date_time" id="in_date_time" class="form-control form-control-sm" value="{{$ES['attributes']['in_date_time']}}">
                                        @if($ES['attributes']['validation_messages']->has('in_date_time'))
                                            <script>
                                                    document.getElementById('in_date_time').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $ES['attributes']['validation_messages']->first("in_date_time") }}</div>
                                        @endif
                                    </div>
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Out Date Time</label>
                                    <div class="col-sm-2">
                                        <input type="datetime-local" name="out_date_time" id="out_date_time" class="form-control form-control-sm" value="{{$ES['attributes']['out_date_time']}}">
                                        @if($ES['attributes']['validation_messages']->has('out_date_time'))
                                            <script>
                                                    document.getElementById('out_date_time').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $ES['attributes']['validation_messages']->first("out_date_time") }}</div>
                                        @endif
                                    </div>
                                    <div class="col-sm-1">
                                        <input type="submit" name="submit" id="submit" class="btn btn-primary btn-sm w-100" value="Calculate">
                                    </div>
                                    <div class="col-sm-2">
                                        <input type="button" name="btn_advance_record" id="btn_advance_record" class="btn btn-primary btn-sm w-100" value="Advance Records" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                    </div>
                                    <div class="col-sm-1">
                                        <input type="submit" name="submit" id="submit" class="btn btn-primary btn-sm w-100" value="Reset">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Remark</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="remark" id="remark" class="form-control form-control-sm" value="{{$ES['attributes']['remark']}}">
                                        @if($ES['attributes']['validation_messages']->has('remark'))
                                            <script>
                                                    document.getElementById('remark').className = 'form-control is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $ES['attributes']['validation_messages']->first("remark") }}</div>
                                        @endif
                                    </div>
                                    <div class="col-sm-1">
                                        <input type="submit" name="submit" id="submit" class="btn btn-primary btn-sm w-100" value="Save">
                                    </div>
                                </div>

                                <hr>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Working Hours</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="working_hours" id="working_hours" class="form-control form-control-sm text-end" value="{{$ES['attributes']['working_hours']}}" readonly>
                                    </div>
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Working Rate</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="working_rate" id="working_rate" class="form-control form-control-sm text-end" value="{{$ES['attributes']['working_rate']}}" readonly>
                                    </div>
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Amount</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="working_amount" id="working_amount" class="form-control form-control-sm text-end" value="{{$ES['attributes']['working_amount']}}" readonly>
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Overtime Hours</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="overtime_hours" id="overtime_hours" class="form-control form-control-sm text-end" value="{{$ES['attributes']['overtime_hours']}}" readonly>
                                    </div>
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Overtime Rate</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="overtime_rate" id="overtime_rate" class="form-control form-control-sm text-end" value="{{$ES['attributes']['overtime_rate']}}" readonly>
                                    </div>
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Amount</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="overtime_amount" id="overtime_amount" class="form-control form-control-sm text-end" value="{{$ES['attributes']['overtime_amount']}}" readonly>
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Total Hours</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="total_hours" id="total_hours" class="form-control form-control-sm text-end" value="{{$ES['attributes']['total_hours']}}" readonly>
                                    </div>
                                    <label for="tid" class="col-sm-3 col-form-label-sm"></label>
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Gross Amount</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="gross_amount" id="gross_amount" class="form-control form-control-sm text-end" value="{{$ES['attributes']['gross_amount']}}" readonly>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Advance Amount</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="advance_amount" id="advance_amount" class="form-control form-control-sm text-end" value="{{$ES['attributes']['advance_amount']}}" readonly>
                                    </div>
                                    <label for="tid" class="col-sm-3 col-form-label-sm"></label>
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Net Amount</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="net_amount" id="net_amount" class="form-control form-control-sm text-end" value="{{$ES['attributes']['net_amount']}}" readonly>
                                    </div>
                                </div>

                                <hr>

                            </div>

                            <div>

                                @for ($x = 1; $x <= $ES['attributes']['site_count']; $x++)

                                    <div class="card">

                                        <div class="card-header">
                                            Site # {{$x}}
                                        </div>

                                        <div class="card-body">

                                            <div class="row mb-2">

                                                <label for="tid" class="col-sm-1 col-form-label-sm">Site</label>
                                                <div class="col-sm-3">
                                                    <select name="site_id_{{$x}}" id="site_id_{{$x}}" class="form-select form-select-sm es-site" >
                                                        @foreach($ES['site' . $x] as $row)
                                                            @if($ES['attributes']['site_id_'.$x] == $row->site_id)
                                                                <option value ="{{$row->site_id}}" selected>{{$row->site_name}}</option>
                                                            @else
                                                                <option value ="{{$row->site_id}}">{{$row->site_name}}</option>
                                                            @endif
                                                        @endforeach
                                                        @if($ES['attributes']['site_id_'.$x] == "0")
                                                            <option value ="0" selected>Select the Site </option>
                                                        @endif
                                                    </select>
                                                    @if($ES['attributes']['validation_messages']->has('site_id_'.$x))
                                                        <script>
                                                                document.getElementById('site_id_{{$x}}').className = 'form-select form-select-sm is-invalid es-site';
                                                        </script>
                                                        <div class="invalid-feedback">{{ $ES['attributes']['validation_messages']->first('site_id_'.$x) }}</div>
                                                    @endif
                                                </div>

                                                <label for="tid" class="col-sm-1 col-form-label-sm">Task</label>
                                                <div class="col-sm-3">
                                                    <select name="task_id_{{$x}}" id="task_id_{{$x}}" class="form-select form-select-sm es-task" >
                                                        @if( ($ES['attributes']['task_id_'.$x] != "0") || ($ES['attributes']['site_id_'.$x] != "0") )
                                                            @foreach($ES['site_task' . $x] as $row)
                                                                @if($ES['attributes']['task_id_'.$x] == $row->task_id)
                                                                    <option value ="{{$row->task_id}}" selected>{{$row->task_name}}</option>
                                                                @else
                                                                    <option value ="{{$row->task_id}}">{{$row->task_name}}</option>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                        @if( $ES['attributes']['task_id_'.$x] != "0" )
                                                            <option value ="0">Select the Task </option>
                                                        @else
                                                            <option value ="0" selected>Select the Task </option>
                                                        @endif
                                                    </select>
                                                    @if($ES['attributes']['validation_messages']->has('task_id_'.$x))
                                                        <script>
                                                                document.getElementById('task_id_{{$x}}').className = 'form-select form-select-sm is-invalid es-task';
                                                        </script>
                                                        <div class="invalid-feedback">{{ $ES['attributes']['validation_messages']->first('task_id_'.$x) }}</div>
                                                    @endif
                                                </div>

                                                <label for="tid" class="col-sm-1 col-form-label-sm">Sub Task</label>
                                                <div class="col-sm-3">
                                                    <select name="sub_task_id_{{$x}}" id="sub_task_id_{{$x}}" class="form-select form-select-sm" >
                                                        @if( ($ES['attributes']['sub_task_id_'.$x] != "0") || ($ES['attributes']['task_id_'.$x] != "0") )
                                                            @foreach($ES['site_sub_task' . $x] as $row)
                                                                @if($ES['attributes']['sub_task_id_'.$x] == $row->sub_task_id)
                                                                    <option value ="{{$row->sub_task_id}}" selected>{{$row->sub_task_name}}</option>
                                                                @else
                                                                    <option value ="{{$row->sub_task_id}}">{{$row->sub_task_name}}</option>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                        @if( $ES['attributes']['sub_task_id_'.$x] != "0" )
                                                            <option value ="0">Select the Sub Task </option>
                                                        @else
                                                            <option value ="0" selected>Select the Sub Task </option>
                                                        @endif
                                                    </select>
                                                    @if($ES['attributes']['validation_messages']->has('sub_task_id_'.$x))
                                                        <script>
                                                                document.getElementById('sub_task_id_{{$x}}').className = 'form-select form-select-sm is-invalid';
                                                        </script>
                                                        <div class="invalid-feedback">{{ $ES['attributes']['validation_messages']->first('sub_task_id_'.$x) }}</div>
                                                    @endif
                                                </div>

                                            </div>

                                            <hr>

                                            <div class="row mb-2">

                                                <label for="tid" class="col-sm-1 col-form-label-sm">Working Hours</label>
                                                <div class="col-sm-2">
                                                    <input type="text" name="site_working_hours_{{$x}}" id="site_working_hours_{{$x}}" class="form-control form-control-sm text-end" value="{{$ES['attributes']['site_working_hours_'.$x]}}">
                                                    @if($ES['attributes']['validation_messages']->has('site_working_hours_'.$x))
                                                        <script>
                                                                document.getElementById('site_working_hours_{{$x}}').className = 'form-control form-control-sm is-invalid';
                                                        </script>
                                                        <div class="invalid-feedback">{{ $ES['attributes']['validation_messages']->first('site_working_hours_'.$x) }}</div>
                                                    @endif
                                                </div>

                                                <label for="tid" class="col-sm-1 col-form-label-sm">Working Rate</label>
                                                <div class="col-sm-2">
                                                    <input type="text" name="working_rate" id="working_rate" class="form-control form-control-sm text-end" value="{{$ES['attributes']['working_rate']}}" readonly>
                                                </div>

                                                <label for="tid" class="col-sm-2 col-form-label-sm">Working Amount</label>
                                                <div class="col-sm-2">
                                                    <input type="text" name="site_working_amount_{{$x}}" id="site_working_amount_{{$x}}" class="form-control form-control-sm text-end" value="{{$ES['attributes']['site_working_amount_'.$x]}}" readonly>
                                                </div>

                                            </div>

                                            <div class="row mb-2">

                                                <label for="tid" class="col-sm-1 col-form-label-sm">OT Hours</label>
                                                <div class="col-sm-2">
                                                    <input type="text" name="site_ot_hours_{{$x}}" id="site_ot_hours_{{$x}}" class="form-control form-control-sm text-end" value="{{$ES['attributes']['site_ot_hours_'.$x]}}">
                                                    @if($ES['attributes']['validation_messages']->has('site_ot_hours_'.$x))
                                                        <script>
                                                                document.getElementById('site_ot_hours_{{$x}}').className = 'form-control form-control-sm is-invalid';
                                                        </script>
                                                        <div class="invalid-feedback">{{ $ES['attributes']['validation_messages']->first('site_ot_hours_'.$x) }}</div>
                                                    @endif
                                                </div>

                                                <label for="tid" class="col-sm-1 col-form-label-sm">OT Rate</label>
                                                <div class="col-sm-2">
                                                    <input type="text" name="overtime_rate" id="overtime_rate" class="form-control form-control-sm text-end" value="{{$ES['attributes']['overtime_rate']}}" readonly>
                                                </div>

                                                <label for="tid" class="col-sm-2 col-form-label-sm">OT Amount</label>
                                                <div class="col-sm-2">
                                                    <input type="text" name="site_ot_amount_{{$x}}" id="site_ot_amount_{{$x}}" class="form-control form-control-sm text-end" value="{{$ES['attributes']['site_ot_amount_'.$x]}}" readonly>
                                                </div>

                                            </div>

                                            <div class="row mb-2">
                                                <label for="tid" class="col-sm-1 col-form-label-sm">Total Hours</label>
                                                <div class="col-sm-2">
                                                    <input type="text" name="site_total_hours_{{$x}}" id="site_total_hours_{{$x}}" class="form-control form-control-sm text-end" value="{{$ES['attributes']['site_total_hours_'.$x]}}" readonly>
                                                </div>
                                                <label for="tid" class="col-sm-1 col-form-label-sm">Total Amount</label>
                                                <div class="col-sm-2">
                                                    <input type="text" name="site_total_amount_{{$x}}" id="site_total_amount_{{$x}}" class="form-control form-control-sm text-end" value="{{$ES['attributes']['site_total_amount_'.$x]}}" readonly>
                                                </div>
                                            </div>

                                        </div>

                                    </div>

                                    <br>

                                @endfor

                            </div>

                        </div>

                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Advance Records</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="width: 10%;">#</th>
                                            <th style="width: 60%;">Date </th>
                                            <th style="width: 30%;" class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if( count($ES['employee_advance']) >= 1 )
                                            @foreach ($ES['employee_advance'] as $key => $value )
                                                <tr>
                                                    <td style="width: 10%;">{{$key+1}}</td>
                                                    <td style="width: 60%;">{{$value->ea_date}}</td>
                                                    <td style="width: 30%;" class="text-end">@money($value->advance_balance)</td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td colspan="2" style="width: 70%;">Total</td>
                                                <td style="width: 30%;" class="text-end"><strong>@money($ES['employee_advance']->sum('advance_balance'))</strong></td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td style="width: 10%;">1</td>
                                                <td style="width: 60%;"></td>
                                                <td style="width: 30%;" class="text-end">@money(0)</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>

@endsection
