@extends('layouts.site_monitoring')
@section('title')
    SO Inquire
@endsection
@section('body')

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{route('so_inquire_process')}}">
                @csrf

                <div class="card mt-3">

                    <div class="card-header">
                        SO Inquire
                    </div>

                    <div class="card-body">

                        <div class="row">
                            <div class="col-sm-12">
                                <?php echo $so_inq['attributes']['process_message'];  ?>
                            </div>
                        </div>

                        <div class="row no-gutters">
                            <div class="col-12 col-sm-12 col-md-12">

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Site</label>
                                    <div class="col-sm-5">
                                        <select name="site_id" id="site_id" class="form-select form-select-sm" >
                                            @foreach($so_inq['site'] as $row)
                                                @if($so_inq['attributes']['site_id'] == $row->site_id)
                                                    <option value ="{{$row->site_id}}" selected>{{$row->site_name}}</option>
                                                @else
                                                    <option value ="{{$row->site_id}}">{{$row->site_name}}</option>
                                                @endif
                                            @endforeach
                                            @if($so_inq['attributes']['site_id'] == "0")
                                                <option value ="0" selected>Select the Site </option>
                                            @endif
                                        </select>
                                        @if($so_inq['attributes']['validation_messages']->has('site_id'))
                                            <script>
                                                    document.getElementById('site_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $so_inq['attributes']['validation_messages']->first("site_id") }}</div>
                                        @endif
                                    </div>

                                    <label for="tid" class="col-sm-1 col-form-label-sm">Cost Section</label>
                                    <div class="col-sm-2">
                                        <select name="cs_id" id="cs_id" class="form-select form-select-sm" >
                                            @foreach($so_inq['so_inquire'] as $row)
                                                @if($so_inq['attributes']['cs_id'] == $row->soit_id)
                                                    <option value ="{{$row->soit_id}}" selected>{{$row->soit_name}}</option>
                                                @else
                                                    <option value ="{{$row->soit_id}}">{{$row->soit_name}}</option>
                                                @endif
                                            @endforeach
                                            @if( $so_inq['attributes']['cs_id'] != "0" )
                                                <option value ="0">Select the Cost Section </option>
                                            @else
                                                <option value ="0" selected>Select the Cost Section </option>
                                            @endif
                                        </select>
                                        @if($so_inq['attributes']['validation_messages']->has('cs_id'))
                                            <script>
                                                    document.getElementById('cs_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $so_inq['attributes']['validation_messages']->first("cs_id") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Task</label>
                                    <div class="col-sm-5">
                                        <select name="task_id" id="task_id" class="form-select form-select-sm" >
                                            @if( ($so_inq['attributes']['task_id'] != "0") || ($so_inq['attributes']['site_id'] != "0") )
                                                @foreach($so_inq['site_task'] as $row)
                                                    @if($so_inq['attributes']['task_id'] == $row->task_id)
                                                        <option value ="{{$row->task_id}}" selected>{{$row->task_name}}</option>
                                                    @else
                                                        <option value ="{{$row->task_id}}">{{$row->task_name}}</option>
                                                    @endif
                                                @endforeach
                                            @endif
                                            @if( $so_inq['attributes']['task_id'] != "0" )
                                                <option value ="0">Select the Task </option>
                                            @else
                                                <option value ="0" selected>Select the Task </option>
                                            @endif
                                        </select>
                                        @if($so_inq['attributes']['validation_messages']->has('task_id'))
                                            <script>
                                                    document.getElementById('task_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $so_inq['attributes']['validation_messages']->first("task_id") }}</div>
                                        @endif
                                    </div>

                                </div>

                                <div class="row mb-2">

                                    <label for="tid" class="col-sm-1 col-form-label-sm">Sub Task</label>
                                    <div class="col-sm-5">
                                        <select name="sub_task_id" id="sub_task_id" class="form-select form-select-sm" >
                                            @if( ($so_inq['attributes']['sub_task_id'] != "0") || ($so_inq['attributes']['task_id'] != "0") )
                                                @foreach($so_inq['site_sub_task'] as $row)
                                                    @if($so_inq['attributes']['sub_task_id'] == $row->sub_task_id)
                                                        <option value ="{{$row->sub_task_id}}" selected>{{$row->sub_task_name}}</option>
                                                    @else
                                                        <option value ="{{$row->sub_task_id}}">{{$row->sub_task_name}}</option>
                                                    @endif
                                                @endforeach
                                            @endif
                                            @if( $so_inq['attributes']['sub_task_id'] != "0" )
                                                <option value ="0">Select the Sub Task </option>
                                            @else
                                                <option value ="0" selected>Select the Sub Task </option>
                                            @endif
                                        </select>
                                        @if($so_inq['attributes']['validation_messages']->has('sub_task_id'))
                                            <script>
                                                    document.getElementById('sub_task_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $so_inq['attributes']['validation_messages']->first("sub_task_id") }}</div>
                                        @endif
                                    </div>

                                </div>

                                <div class="row mb-4">

                                    <label for="tid" class="col-sm-1 col-form-label-sm">From Date</label>
                                    <div class="col-sm-2">
                                        <input type="date" name="from_date" id="from_date" class="form-control form-control-sm" value="{{$so_inq['attributes']['from_date']}}">
                                        @if($so_inq['attributes']['validation_messages']->has('from_date'))
                                            <script>
                                                    document.getElementById('from_date').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $so_inq['attributes']['validation_messages']->first("from_date") }}</div>
                                        @endif
                                    </div>
                                    <label for="tid" class="col-sm-1 col-form-label-sm">To Date</label>
                                    <div class="col-sm-2">
                                        <input type="date" name="to_date" id="to_date" class="form-control form-control-sm" value="{{$so_inq['attributes']['to_date']}}">
                                        @if($so_inq['attributes']['validation_messages']->has('to_date'))
                                            <script>
                                                    document.getElementById('to_date').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $so_inq['attributes']['validation_messages']->first("to_date") }}</div>
                                        @endif
                                    </div>

                                    <div class="col-sm-1">
                                        <input type="submit" name="submit" id="submit" class="btn btn-primary btn-sm w-100" value="Display">
                                    </div>
                                </div>

                            </div>
                            <hr>

                            <div class="col-12 colmd-12">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>

                                            @if( ($so_inq['cost_section'] == 'employee_salary') )
                                                <tr>
                                                    <th style="width: 5%;">No.</th>
                                                    <th style="width: 10%;">Date</th>
                                                    <th style="width: 60%;">Employee Name</th>
                                                    <th style="width: 10%;" class="text-end">Amount</th>
                                                    <th style="width: 10%;"></th>
                                                </tr>
                                            @else
                                                <tr>
                                                    <th style="width: 5%;">No.</th>
                                                    <th style="width: 10%;">Date</th>
                                                    <th style="width: 60%;">Site</th>
                                                    <th style="width: 10%;" class="text-end">Amount</th>
                                                    <th style="width: 10%;"></th>
                                                </tr>
                                            @endif

                                        </thead>
                                        @if( count($so_inq['so_detail']) >= 1 )

                                            <tbody>

                                                @if( ($so_inq['cost_section'] == 'material') )

                                                    @foreach ($so_inq['so_detail'] as $rowKey => $rowValue)
                                                        <tr>
                                                            <td style="width: 5%;">{{$rowValue->iin_id}}</td>
                                                            <td style="width: 10%;">{{$rowValue->iin_date}}</td>
                                                            <td style="width: 60%;">{{$rowValue->getSite()->site_name}}</td>
                                                            <td style="width: 10%;" class="text-end">@money($rowValue->total_amount)</td>
                                                            <td style="width: 10%;">
                                                                <input type="button" name="btnOpen" id="btnOpen" data-iin-id="{{$rowValue->iin_id}}" class="btn btn-primary btn-sm w-100 item-issue-note-open" value="Open">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="width: 10%;"><b>Task \ Sub Task<b></td>
                                                            <td colspan="4">
                                                                {{$rowValue->getTask()->task_name}} <b>{{' \ '}}</b> {{$rowValue->getSubTask()->sub_task_name}}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="5" style="width: 5%; background-color: gray; height: 5px;"></td>
                                                        </tr>
                                                    @endforeach

                                                @elseif( ($so_inq['cost_section'] == 'overhead') )

                                                    @foreach ($so_inq['so_detail'] as $rowKey => $rowValue)
                                                        <tr>
                                                            <td style="width: 5%;">{{$rowValue->pv_id}}</td>
                                                            <td style="width: 10%;">{{$rowValue->pv_date}}</td>
                                                            <td style="width: 60%;">{{$rowValue->getSite()->site_name}}</td>
                                                            <td style="width: 10%;" class="text-end">@money($rowValue->total_amount)</td>
                                                            <td style="width: 10%;">
                                                                <input type="button" name="btnOpen" id="btnOpen" data-pv-id="{{$rowValue->pv_id}}" class="btn btn-primary btn-sm w-100 payment-voucher-open" value="Open">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="width: 10%;"><b>Task \ Sub Task<b></td>
                                                            <td colspan="4">
                                                                {{$rowValue->getTask()->task_name}} <b>{{' \ '}}</b> {{$rowValue->getSubTask()->sub_task_name}}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="5" style="width: 5%; background-color: gray; height: 5px;"></td>
                                                        </tr>
                                                    @endforeach

                                                @elseif( ($so_inq['cost_section'] == 'employee_advance') )

                                                    @foreach ($so_inq['so_detail'] as $rowKey => $rowValue)
                                                        <tr>
                                                            <td style="width: 5%;">{{$rowValue->ea_id}}</td>
                                                            <td style="width: 10%;">{{$rowValue->ea_date}}</td>
                                                            <td style="width: 60%;">{{$rowValue->getSite()->site_name}}</td>
                                                            <td style="width: 10%;" class="text-end">@money($rowValue->advance_amount)</td>
                                                            <td style="width: 10%;">
                                                                <input type="button" name="btnOpen" id="btnOpen" data-ea-id="{{$rowValue->ea_id}}" class="btn btn-primary btn-sm w-100 employee-advance-open" value="Open">
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td style="width: 10%;"><b>Task \ Sub Task<b></td>
                                                            <td colspan="4">
                                                                {{$rowValue->getTask()->task_name}} <b>{{' \ '}}</b> {{$rowValue->getSubTask()->sub_task_name}}
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td colspan="5" style="width: 5%; background-color: gray; height: 5px;"></td>
                                                        </tr>
                                                    @endforeach


                                                @elseif( ($so_inq['cost_section'] == 'employee_salary') )

                                                    @foreach ($so_inq['so_detail'] as $rowKey => $rowValue)

                                                        <tr>
                                                            <td style="width: 5%;">{{$rowValue->es_id}}</td>
                                                            <td style="width: 10%;">{{$rowValue->es_date}}</td>
                                                            <td style="width: 60%;">{{$rowValue->getEmployee()->employee_name}}</td>
                                                            <td style="width: 10%;" class="text-end">@money($rowValue->gross_amount)</td>
                                                            <td style="width: 10%;">
                                                                <input type="button" name="btnOpen" id="btnOpen" data-es-id="{{$rowValue->es_id}}" class="btn btn-primary btn-sm w-100 employee-salary-open" value="Open">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="width: 10%;"><b>In Out Time<b></td>
                                                            <td colspan="2"><b>{{$rowValue->in_date_time}}&nbsp;-&nbsp;{{$rowValue->out_date_time}}<b></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="5" style="width: 5%; background-color: gray; height: 5px;"></td>
                                                        </tr>

                                                    @endforeach

                                                @elseif( ($so_inq['cost_section'] == 'daily_progress') )

                                                    @foreach ($so_inq['so_detail'] as $rowKey => $rowValue)
                                                        <tr>
                                                            <td style="width: 5%;">{{$rowValue->dpr_id}}</td>
                                                            <td style="width: 10%;">{{$rowValue->dpr_date}}</td>
                                                            <td style="width: 60%;">{{$rowValue->getSite()->site_name}}</td>
                                                            <td style="width: 10%;" class="text-end">@money($rowValue->total_amount)</td>
                                                            <td style="width: 10%;">
                                                                <input type="button" name="btnOpen" id="btnOpen" data-dpr-id="{{$rowValue->dpr_id}}" class="btn btn-primary btn-sm w-100 dpr-open" value="Open">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="width: 10%;"><b>Task \ Sub Task<b></td>
                                                            <td colspan="4">
                                                                {{$rowValue->getTask()->task_name}} <b>{{' \ '}}</b> {{$rowValue->getSubTask()->sub_task_name}}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="5" style="width: 5%; background-color: gray; height: 5px;"></td>
                                                        </tr>
                                                    @endforeach

                                                @endif

                                            </tbody>

                                        @else
                                            <tbody>
                                                <tr>
                                                    <td style="width: 5%;">-</td>
                                                    <td style="width: 10%;">-</td>
                                                    <td style="width: 60%;">-</td>
                                                    <td style="width: 10%;" class="text-end">@money(0)</td>
                                                    <td style="width: 10%;"></td>
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

        <div style="display: none;">
            <form id="open_item_issue_note" style="display: none;" method="post" target='_blank' action="{{route('open_item_issue_note')}}">
                @csrf
                <input type="text" name="iin_id" id="iin_id" values="">
            </form>
        </div>

        <div style="display: none;">
            <form id="open_payment_voucher" style="display: none;" method="post" target='_blank' action="{{route('open_payment_voucher')}}">
                @csrf
                <input type="text" name="open_pv_id" id="open_pv_id" values="">
            </form>
        </div>

        <div style="display: none;">
            <form id="open_employee_advance" style="display: none;" method="post" target='_blank' action="{{route('open_employee_advance')}}">
                @csrf
                <input type="text" name="open_ea_id" id="open_ea_id" values="">
            </form>
        </div>

        <div style="display: none;">
            <form id="open_sap_overhead" style="display: none;" method="post" target='_blank' action="{{route('open_sap_overhead')}}">
                @csrf
                <input type="text" name="open_sap_overhead_cost_id" id="open_sap_overhead_cost_id" values="">
            </form>
        </div>

        <div style="display: none;">
            <form id="open_employee_salary" style="display: none;" method="post" target='_blank' action="{{route('open_employee_salary')}}">
                @csrf
                <input type="text" name="open_es_id" id="open_es_id" values="">
            </form>
        </div>

        <div style="display: none;">
            <form id="open_dpr" style="display: none;" method="post" target='_blank' action="{{route('open_dpr')}}">
                @csrf
                <input type="text" name="open_dpr_id" id="open_dpr_id" values="">
            </form>
        </div>

    </div>

@endsection
