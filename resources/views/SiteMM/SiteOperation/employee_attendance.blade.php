@extends('layouts.site_monitoring')
@section('title')
    Employee Attendance & Overtime
@endsection
@section('body')

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{route('employee_attendance_process')}}">
                @csrf

                <div class="card mt-3">

                    <div class="card-header">
                        Employee Attendance & Overtime
                    </div>

                    <div class="card-body">

                        <div class="row">
                            <div class="col-sm-12">
                                <?php echo $ES['attributes']['process_message'];  ?>
                            </div>
                        </div>

                        <div class="row no-gutters">
                            <div class="col-12 col-sm-12 col-md-12">

                                <div class="row mb-4">
                                    <div class="col-sm-2">
                                        <input type="submit" name="submit" id="submit" class="btn btn-primary btn-sm w-100" value="Display">
                                    </div>
                                    <div class="col-sm-2">
                                        <input type="submit" name="submit" id="submit" class="btn btn-primary btn-sm w-100" value="Save">
                                    </div>
                                    <div class="col-sm-5">
                                    </div>
                                    <label for="tid" class="col-sm-1 col-form-label-sm">PV Date</label>
                                    <div class="col-sm-2">
                                        <input type="date" name="attendance_date" id="attendance_date" class="form-control form-control-sm" value="{{$ES['attributes']['attendance_date']}}">
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 colmd-12">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%;">#</th>
                                                <th style="width: 10%;">Emp No. </th>
                                                <th style="width: 45%;">Emp Name</th>
                                                <th style="width: 5%;">Attendance</th>
                                                <th style="width: 10%;">OT Hours</th>
                                                <th style="width: 25%;">Remark</th>
                                            </tr>
                                        </thead>
                                        @if( count($ES['attributes']['attendance_overtime_detail']) >= 1 )

                                            <tbody>
                                                @foreach ($ES['attributes']['attendance_overtime_detail'] as $rowValue)
                                                    <tr>
                                                        <td>{{$rowValue['ono']}}</td>
                                                        <td style="width: 10%;"><b>{{$rowValue['emp_code']}} </b> </td>
                                                        <td style="width: 10%;">{{$rowValue['emp_name']}}</td>
                                                        <td style="width: 5%; text-align:center;">
                                                            @if( $rowValue['attendance'] )
                                                                <input type="checkbox" name="attendance_{{$rowValue['emp_id']}}" id="attendance_{{$rowValue['emp_id']}}" class="form-check-input" value="{{$rowValue['emp_id']}}" checked>
                                                            @else
                                                                <input type="checkbox" name="attendance_{{$rowValue['emp_id']}}" id="attendance_{{$rowValue['emp_id']}}" class="form-check-input" value="{{$rowValue['emp_id']}}">
                                                            @endif
                                                        </td>
                                                        <td style="width: 10%;">
                                                            <input type="number" name="ot_hours_{{$rowValue['emp_id']}}" id="ot_hours_{{$rowValue['emp_id']}}" class="form-control form-control-sm text-end" value="{{$rowValue['ot_hours']}}">
                                                        </td>
                                                        <td style="width: 25%;">
                                                            <input type="text" name="remark_{{$rowValue['emp_id']}}" id="remark_{{$rowValue['emp_id']}}" class="form-control form-control-sm" value="{{$rowValue['remark']}}">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        @else
                                            <tbody>
                                                <tr>
                                                    <td style="width: 5%;">-</td>
                                                    <td style="width: 10%;"> - </td>
                                                    <td style="width: 55%;"> - </td>
                                                    <td style="width: 10%;"> - </td>
                                                    <td style="width: 10%;"> - </td>
                                                    <td style="width: 10%;"> - </td>
                                                </tr>
                                            </tbody>
                                        @endif
                                    </table>
                                  </div>

                            </div>

                        </div>

                    </div>
                </div>

            </form>
        </div>
    </div>

@endsection
