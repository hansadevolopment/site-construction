@extends('layouts.site_monitoring')
@section('title')
    SAP Report
@endsection
@section('body')

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{route('sap_report_process')}}">
                @csrf

                <div class="card mt-3">

                    <div class="card-header">
                        SAP Report
                    </div>

                    <div class="card-body">

                        <div class="row">
                            <div class="col-sm-12">
                                <?php echo $SAPR['attributes']['process_message'];  ?>
                            </div>
                        </div>

                        <div class="row no-gutters">
                            <div class="col-12 col-sm-12 col-md-12">

                                <div class="row mb-2">

                                    <label for="tid" class="col-sm-1 col-form-label-sm">Site</label>
                                    <div class="col-sm-5">
                                        <select name="site_id" id="site_id" class="form-select form-select-sm" >
                                            @foreach($SAPR['site'] as $row)
                                                @if($SAPR['attributes']['site_id'] == $row->site_id)
                                                    <option value ="{{$row->site_id}}" selected>{{$row->site_name}}</option>
                                                @else
                                                    <option value ="{{$row->site_id}}">{{$row->site_name}}</option>
                                                @endif
                                            @endforeach
                                            @if($SAPR['attributes']['site_id'] == "0")
                                                <option value ="0" selected>Select the Site </option>
                                            @endif
                                        </select>
                                        @if($SAPR['attributes']['validation_messages']->has('site_id'))
                                            <script>
                                                    document.getElementById('site_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $SAPR['attributes']['validation_messages']->first("site_id") }}</div>
                                        @endif
                                    </div>

                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Task</label>
                                    <div class="col-sm-5">
                                        <select name="task_id" id="task_id" class="form-select form-select-sm" >
                                            @if( ($SAPR['attributes']['task_id'] != "0") || ($SAPR['attributes']['site_id'] != "0") )
                                                @foreach($SAPR['site_task'] as $row)
                                                    @if($SAPR['attributes']['task_id'] == $row->task_id)
                                                        <option value ="{{$row->task_id}}" selected>{{$row->task_name}}</option>
                                                    @else
                                                        <option value ="{{$row->task_id}}">{{$row->task_name}}</option>
                                                    @endif
                                                @endforeach
                                            @endif
                                            @if( $SAPR['attributes']['task_id'] != "0" )
                                                <option value ="0">Select the Task </option>
                                            @else
                                                <option value ="0" selected>Select the Task </option>
                                            @endif
                                        </select>
                                        @if($SAPR['attributes']['validation_messages']->has('task_id'))
                                            <script>
                                                    document.getElementById('task_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $SAPR['attributes']['validation_messages']->first("task_id") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Sub Task</label>
                                    <div class="col-sm-5">
                                        <select name="sub_task_id" id="sub_task_id" class="form-select form-select-sm" >
                                            @if( ($SAPR['attributes']['sub_task_id'] != "0") || ($SAPR['attributes']['task_id'] != "0") )
                                                @foreach($SAPR['site_sub_task'] as $row)
                                                    @if($SAPR['attributes']['sub_task_id'] == $row->sub_task_id)
                                                        <option value ="{{$row->sub_task_id}}" selected>{{$row->sub_task_name}}</option>
                                                    @else
                                                        <option value ="{{$row->sub_task_id}}">{{$row->sub_task_name}}</option>
                                                    @endif
                                                @endforeach
                                            @endif
                                            @if( $SAPR['attributes']['sub_task_id'] != "0" )
                                                <option value ="0">Select the Sub Task </option>
                                            @else
                                                <option value ="0" selected>Select the Sub Task </option>
                                            @endif
                                        </select>
                                        @if($SAPR['attributes']['validation_messages']->has('sub_task_id'))
                                            <script>
                                                    document.getElementById('sub_task_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $SAPR['attributes']['validation_messages']->first("sub_task_id") }}</div>
                                        @endif
                                    </div>

                                    <div class="col-sm-2">
                                        <input type="submit" name="submit" id="submit" class="btn btn-primary btn-sm w-100" value="Genarate">
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
