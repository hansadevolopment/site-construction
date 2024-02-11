@extends('layouts.site_monitoring')
@section('title')
    Site Task Sub Task Inquiry & List
@endsection
@section('body')

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{route('site_task_subtask_inquiry_process')}}">
                @csrf

                <div class="card mt-3">

                    <div class="card-header">
                        Site Task Sub Task Inquiry & List
                    </div>

                    <div class="card-body">

                        <div class="row">
                            <div class="col-sm-12">
                                <?php echo $stsil['attributes']['process_message'];  ?>
                            </div>
                        </div>

                        <div class="row no-gutters">
                            <div class="col-12 col-sm-12 col-md-12">

                                <div class="row mb-2">

                                    <label for="tid" class="col-sm-1 col-form-label-sm">Site</label>
                                    <div class="col-sm-5">
                                        <select name="site_id" id="site_id" class="form-select form-select-sm" >
                                            @foreach($stsil['site'] as $row)
                                                @if($stsil['attributes']['site_id'] == $row->site_id)
                                                    <option value ="{{$row->site_id}}" selected>{{$row->site_name}}</option>
                                                @else
                                                    <option value ="{{$row->site_id}}">{{$row->site_name}}</option>
                                                @endif
                                            @endforeach
                                            @if($stsil['attributes']['site_id'] == "0")
                                                <option value ="0" selected>Select the Site </option>
                                            @endif
                                        </select>
                                        @if($stsil['attributes']['validation_messages']->has('site_id'))
                                            <script>
                                                    document.getElementById('site_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $stsil['attributes']['validation_messages']->first("site_id") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Task</label>
                                    <div class="col-sm-5">
                                        <select name="task_id" id="task_id" class="form-select form-select-sm" >
                                            @if( ($stsil['attributes']['task_id'] != "0") || ($stsil['attributes']['site_id'] != "0") )
                                                @foreach($stsil['site_task'] as $row)
                                                    @if($stsil['attributes']['task_id'] == $row->task_id)
                                                        <option value ="{{$row->task_id}}" selected>{{$row->task_name}}</option>
                                                    @else
                                                        <option value ="{{$row->task_id}}">{{$row->task_name}}</option>
                                                    @endif
                                                @endforeach
                                            @endif
                                            @if( $stsil['attributes']['task_id'] != "0" )
                                                <option value ="0">Select the Task </option>
                                            @else
                                                <option value ="0" selected>Select the Task </option>
                                            @endif
                                        </select>
                                        @if($stsil['attributes']['validation_messages']->has('task_id'))
                                            <script>
                                                    document.getElementById('task_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $stsil['attributes']['validation_messages']->first("task_id") }}</div>
                                        @endif
                                    </div>

                                    <label for="tid" class="col-sm-1 col-form-label-sm">Active</label>
                                    <div class="col-sm-2">
                                        <select name="active" id="active" class="form-select form-select-sm" >
                                            @if( $stsil['attributes']['active'] )
                                                <option value ="1" selected>Yes</option>
                                                <option value ="0">No</option>
                                            @else
                                                <option value ="1">Yes</option>
                                                <option value ="0" selected>No</option>
                                            @endif
                                        </select>
                                    </div>


                                    <div class="col-sm-1">
                                        <input type="submit" name="submit" id="submit" class="btn btn-primary btn-sm w-100" value="Display">
                                    </div>
                                </div>


                                {{--
                                    <div class="row mb-4">
                                        <label for="tid" class="col-sm-1 col-form-label-sm">Sub Task</label>
                                        <div class="col-sm-5">
                                            <select name="sub_task_id" id="sub_task_id" class="form-select form-select-sm" >
                                                @if( ($stsil['attributes']['sub_task_id'] != "0") || ($stsil['attributes']['task_id'] != "0") )
                                                    @foreach($stsil['site_sub_task'] as $row)
                                                        @if($stsil['attributes']['sub_task_id'] == $row->sub_task_id)
                                                            <option value ="{{$row->sub_task_id}}" selected>{{$row->sub_task_name}}</option>
                                                        @else
                                                            <option value ="{{$row->sub_task_id}}">{{$row->sub_task_name}}</option>
                                                        @endif
                                                    @endforeach
                                                @endif
                                                @if( $stsil['attributes']['sub_task_id'] != "0" )
                                                    <option value ="0">Select the Sub Task </option>
                                                @else
                                                    <option value ="0" selected>Select the Sub Task </option>
                                                @endif
                                            </select>
                                            @if($stsil['attributes']['validation_messages']->has('sub_task_id'))
                                                <script>
                                                        document.getElementById('sub_task_id').className = 'form-select form-select-sm is-invalid';
                                                </script>
                                                <div class="invalid-feedback">{{ $stsil['attributes']['validation_messages']->first("sub_task_id") }}</div>
                                            @endif
                                        </div>
                                    </div>
                                --}}

                            </div>
                            <hr>

                            <div class="col-12 colmd-12">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%;">#</th>
                                                <th id="stsil" style="width: 65%;">
                                                    Site
                                                </th>
                                                <th style="width: 10%;">Active</th>
                                                <th style="width: 10%;">Sub Contract</th>
                                                <th style="width: 10%;"></th>
                                            </tr>
                                        </thead>
                                        @if( count($stsil['attributes']['stsil_detail']) >= 1 )

                                            @if( $stsil['attributes']['source'] == 'site' )

                                                <tbody>
                                                    @foreach ($stsil['attributes']['stsil_detail'] as $rowKey => $rowValue)
                                                        <tr>
                                                            <td style="width: 5%;">{{ ($rowKey + 1) }}</td>
                                                            <td style="width: 65%;">{{$rowValue->site_name}}</td>
                                                            @if( $rowValue->active == 1 )
                                                                <td style="width: 10%;">Yes</td>
                                                            @else
                                                                <td style="width: 10%;">No</td>
                                                            @endif
                                                            <td style="width: 10%;">No</td>
                                                            <td style="width: 10%;">
                                                                <input type="button" name="btnOpen" id="btnOpen" data-site-id="{{$rowValue->site_id}}" class="btn btn-primary btn-sm w-100 site-inquiry-open" value="Open">
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>

                                            @elseif( $stsil['attributes']['source'] == 'task' )

                                                <tbody>
                                                    @foreach ($stsil['attributes']['stsil_detail'] as $rowKey => $rowValue)
                                                        <tr>
                                                            <td style="width: 5%;">{{ ($rowKey + 1) }}</td>
                                                            <td style="width: 65%;">{{$rowValue->task_name}}</td>
                                                            @if( $rowValue->active == 1 )
                                                                <td style="width: 10%;">Yes</td>
                                                            @else
                                                                <td style="width: 10%;">No</td>
                                                            @endif
                                                            @if( $rowValue->sub_contract == 1 )
                                                                <td style="width: 10%;">Yes</td>
                                                            @else
                                                                <td style="width: 10%;">No</td>
                                                            @endif
                                                            <td style="width: 10%;">
                                                                <input type="button" name="btnOpen" id="btnOpen" data-task-id="{{$rowValue->task_id}}" class="btn btn-primary btn-sm w-100 task-inquiry-open" value="Open">
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>

                                            @elseif( $stsil['attributes']['source'] == 'sub-task' )

                                                <tbody>
                                                    @foreach ($stsil['attributes']['stsil_detail'] as $rowKey => $rowValue)
                                                        <tr>
                                                            <td style="width: 5%;">{{ ($rowKey + 1) }}</td>
                                                            <td style="width: 65%;">{{$rowValue->sub_task_name}}</td>
                                                            @if( $rowValue->active == 1 )
                                                                <td style="width: 10%;">Yes</td>
                                                            @else
                                                                <td style="width: 10%;">No</td>
                                                            @endif
                                                            @if( $rowValue->sub_contract == 1 )
                                                                <td style="width: 10%;">Yes</td>
                                                            @else
                                                                <td style="width: 10%;">No</td>
                                                            @endif
                                                            <td style="width: 10%;">
                                                                <input type="button" name="btnOpen" id="btnOpen" data-sub-task-id="{{$rowValue->sub_task_id}}" class="btn btn-primary btn-sm w-100 sub-task-inquiry-open" value="Open">
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>

                                            @endif

                                        @else
                                            <tbody>
                                                <tr>
                                                    <td style="width: 5%;">-</td>
                                                    <td style="width: 65%;">-</td>
                                                    <td style="width: 10%;">-</td>
                                                    <td style="width: 10%;"></td>
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
            <form id="open_site" style="display: none;" method="post" target='_blank' action="{{route('open_site')}}">
                @csrf
                <input type="text" name="open_site_id" id="open_site_id" values="">
            </form>
        </div>

        <div style="display: none;">
            <form id="open_task" style="display: none;" method="post" target='_blank' action="{{route('open_task')}}">
                @csrf
                <input type="text" name="open_task_id" id="open_task_id" values="">
            </form>
        </div>

        <div style="display: none;">
            <form id="open_sub_task" style="display: none;" method="post" target='_blank' action="{{route('open_sub_task')}}">
                @csrf
                <input type="text" name="open_site_no" id="open_site_no" values="">
                <input type="text" name="open_sub_task_id" id="open_sub_task_id" values="">
            </form>
        </div>

    </div>

@endsection
