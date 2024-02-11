@extends('layouts.site_monitoring')
@section('title')
    Employee Salary
@endsection
@section('body')

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{route('employee_salary_two_process')}}">
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
                                                    document.getElementById('es_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $ES['attributes']['validation_messages']->first("es_id") }}</div>
                                        @endif
                                    </div>

                                </div>

                                <div class="row mb-2">

                                    <label for="tid" class="col-sm-1 col-form-label-sm">Salary Category</label>
                                    <div class="col-sm-3">
                                        <select name="sc_id" id="sc_id" class="form-select form-select-sm" >
                                            @foreach($ES['salary_category'] as $row)
                                                @if( $row->id != 1 )
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

                                    <div class="col-sm-5">
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
                                                    <option value ="{{$row->employee_id}}" selected>{{$row->employee_name . ' -->  ' . $row->day_salary}}</option>
                                                @else
                                                    <option value ="{{$row->employee_id}}">{{$row->employee_name . ' -->  ' . $row->day_salary}}</option>
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
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Site</label>
                                    <div class="col-sm-5">
                                        <select name="site_id" id="site_id" class="form-select form-select-sm" >
                                            @foreach($ES['site'] as $row)
                                                @if($ES['attributes']['site_id'] == $row->site_id)
                                                    <option value ="{{$row->site_id}}" selected>{{$row->site_name}}</option>
                                                @else
                                                    <option value ="{{$row->site_id}}">{{$row->site_name}}</option>
                                                @endif
                                            @endforeach
                                            @if($ES['attributes']['site_id'] == "0")
                                                <option value ="0" selected>Select the Site </option>
                                            @endif
                                        </select>
                                        @if($ES['attributes']['validation_messages']->has('site_id'))
                                            <script>
                                                    document.getElementById('site_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $ES['attributes']['validation_messages']->first("site_id") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Task</label>
                                    <div class="col-sm-5">
                                        <select name="task_id" id="task_id" class="form-select form-select-sm" >
                                            @if( ($ES['attributes']['task_id'] != "0") || ($ES['attributes']['site_id'] != "0") )
                                                @foreach($ES['site_task'] as $row)
                                                    @if($ES['attributes']['task_id'] == $row->task_id)
                                                        <option value ="{{$row->task_id}}" selected>{{$row->task_name}}</option>
                                                    @else
                                                        <option value ="{{$row->task_id}}">{{$row->task_name}}</option>
                                                    @endif
                                                @endforeach
                                            @endif
                                            @if( $ES['attributes']['task_id'] != "0" )
                                                <option value ="0">Select the Task </option>
                                            @else
                                                <option value ="0" selected>Select the Task </option>
                                            @endif
                                        </select>
                                        @if($ES['attributes']['validation_messages']->has('task_id'))
                                            <script>
                                                    document.getElementById('task_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $ES['attributes']['validation_messages']->first("task_id") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Sub Task</label>
                                    <div class="col-sm-5">
                                        <select name="sub_task_id" id="sub_task_id" class="form-select form-select-sm" >
                                            @if( ($ES['attributes']['sub_task_id'] != "0") || ($ES['attributes']['task_id'] != "0") )
                                                @foreach($ES['site_sub_task'] as $row)
                                                    @if($ES['attributes']['sub_task_id'] == $row->sub_task_id)
                                                        <option value ="{{$row->sub_task_id}}" selected>{{$row->sub_task_name}}</option>
                                                    @else
                                                        <option value ="{{$row->sub_task_id}}">{{$row->sub_task_name}}</option>
                                                    @endif
                                                @endforeach
                                            @endif
                                            @if( $ES['attributes']['sub_task_id'] != "0" )
                                                <option value ="0">Select the Sub Task </option>
                                            @else
                                                <option value ="0" selected>Select the Sub Task </option>
                                            @endif
                                        </select>
                                        @if($ES['attributes']['validation_messages']->has('sub_task_id'))
                                            <script>
                                                    document.getElementById('sub_task_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $ES['attributes']['validation_messages']->first("sub_task_id") }}</div>
                                        @endif
                                    </div>
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Task Unit</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="task_unit" id="task_unit" class="form-control form-control-sm" value="" readonly>
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Pay Amount</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="pay_amount" id="pay_amount" class="form-control form-control-sm text-end" value="{{$ES['attributes']['pay_amount']}}">
                                        @if($ES['attributes']['validation_messages']->has('pay_amount'))
                                            <script>
                                                    document.getElementById('pay_amount').className = 'form-select form-select-sm is-invalid text-end';
                                            </script>
                                            <div class="invalid-feedback">{{ $ES['attributes']['validation_messages']->first("pay_amount") }}</div>
                                        @endif
                                    </div>
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Advance Amt</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="advance_amount" id="advance_amount" class="form-control form-control-sm text-end" value="" readonly>
                                    </div>
                                    <div class="col-sm-2">
                                        <input type="button" name="btn_advance_record" id="btn_advance_record" class="btn btn-primary btn-sm w-100" value="Advance Records" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Remark</label>
                                    <div class="col-sm-11">
                                        <input type="text" name="remark" id="remark" class="form-control form-control-sm" value="{{$ES['attributes']['remark']}}">
                                        @if($ES['attributes']['validation_messages']->has('remark'))
                                            <script>
                                                    document.getElementById('remark').className = 'form-control is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $ES['attributes']['validation_messages']->first("remark") }}</div>
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
                                    <tbody id="tbody_emp_salary_two">
                                        @if( count($ES['employee_advance']) >= 1 )

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
