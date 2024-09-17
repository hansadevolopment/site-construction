@extends('layouts.site_monitoring')
@section('title')
    Site Sub Task
@endsection
@section('body')

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{route('site_sub_task_process')}}">
                @csrf

                <div class="card mt-3">

                    <div class="card-header">
                        Site Sub Task
                    </div>

                    <div class="card-body">

                        <div class="row">
                            <div class="col-sm-12">
                                <?php echo $SubSite['attributes']['process_message'];  ?>
                            </div>
                        </div>

                        <div class="row no-gutters">
                            <div class="col-12 col-sm-6 col-md-6">

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Sub Task ID</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="sub_task_id" id="sub_task_id" class="form-control form-control-sm"  value="{{$SubSite['attributes']['sub_task_id']}}" readonly>
                                        @if($SubSite['attributes']['validation_messages']->has('sub_task_id'))
                                            <script>
                                                    document.getElementById('sub_task_id').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $SubSite['attributes']['validation_messages']->first("sub_task_id") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Sub Task Name</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="sub_task_name" id="sub_task_name" class="form-control form-control-sm"  value="{{$SubSite['attributes']['sub_task_name']}}">
                                        @if($SubSite['attributes']['validation_messages']->has('sub_task_name'))
                                            <script>
                                                    document.getElementById('sub_task_name').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $SubSite['attributes']['validation_messages']->first("sub_task_name") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Site</label>
                                    <div class="col-sm-10">
                                        <select name="site_id" id="site_id" class="form-select form-select-sm" >
                                            @foreach($SubSite['site'] as $row)
                                                @if($SubSite['attributes']['site_id'] == $row->site_id)
                                                    <option value ="{{$row->site_id}}" selected>{{$row->site_name}}</option>
                                                @else
                                                    <option value ="{{$row->site_id}}">{{$row->site_name}}</option>
                                                @endif
                                            @endforeach
                                            @if($SubSite['attributes']['site_id'] == "0")
                                                <option value ="0" selected>Select the Site </option>
                                            @endif
                                        </select>
                                        @if($SubSite['attributes']['validation_messages']->has('site_id'))
                                            <script>
                                                    document.getElementById('site_id').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $SubSite['attributes']['validation_messages']->first("site_id") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Task</label>
                                    <div class="col-sm-10">
                                        <select name="task_id" id="task_id" class="form-select form-select-sm" >
                                            @if($SubSite['attributes']['task_id'] == "0")
                                                <option value ="0" selected>Select the Task </option>
                                            @else
                                                @foreach($SubSite['task'] as $row)
                                                    @if($SubSite['attributes']['task_id'] == $row->task_id)
                                                        <option value ="{{$row->task_id}}" selected>{{$row->task_name}}</option>
                                                    @else
                                                        <option value ="{{$row->task_id}}">{{$row->task_name}}</option>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </select>
                                        @if($SubSite['attributes']['validation_messages']->has('task_id'))
                                            <script>
                                                    document.getElementById('task_id').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $SubSite['attributes']['validation_messages']->first("task_id") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Unit</label>
                                    <div class="col-sm-10">
                                        <select name="unit_id" id="unit_id" class="form-select form-select-sm" >
                                            @foreach($SubSite['unit'] as $row)
                                                @if($SubSite['attributes']['unit_id'] == $row->unit_id)
                                                    <option value ="{{$row->unit_id}}" selected>{{$row->unit_name}}</option>
                                                @else
                                                    <option value ="{{$row->unit_id}}">{{$row->unit_name}}</option>
                                                @endif
                                            @endforeach
                                            @if($SubSite['attributes']['unit_id'] == "0")
                                                <option value ="0" selected>Select the Unit </option>
                                            @endif
                                        </select>
                                        @if($SubSite['attributes']['validation_messages']->has('unit_id'))
                                            <script>
                                                    document.getElementById('unit_id').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $SubSite['attributes']['validation_messages']->first("unit_id") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Quantity</label>
                                    <div class="col-sm-4">
                                        <input type="text" name="quantity" id="quantity" class="form-control form-control-sm text-end"  value="{{$SubSite['attributes']['quantity']}}">
                                        @if($SubSite['attributes']['validation_messages']->has('quantity'))
                                            <script>
                                                    document.getElementById('quantity').className = 'form-control form-control-sm is-invalid text-end';
                                            </script>
                                            <div class="invalid-feedback">{{ $SubSite['attributes']['validation_messages']->first("quantity") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Start Date</label>
                                    <div class="col-sm-4">
                                        <input type="date" name="start_date" id="start_date" class="form-control form-control-sm"  value="{{$SubSite['attributes']['start_date']}}">
                                        @if($SubSite['attributes']['validation_messages']->has('start_date'))
                                            <script>
                                                    document.getElementById('start_date').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $SubSite['attributes']['validation_messages']->first("start_date") }}</div>
                                        @endif
                                    </div>
                                    <label for="tid" class="col-sm-2 col-form-label-sm">End Date</label>
                                    <div class="col-sm-4">
                                        <input type="date" name="end_date" id="end_date" class="form-control form-control-sm"  value="{{$SubSite['attributes']['end_date']}}">
                                        @if($SubSite['attributes']['validation_messages']->has('end_date'))
                                            <script>
                                                    document.getElementById('end_date').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $SubSite['attributes']['validation_messages']->first("end_date") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Active</label>
                                    <div class="col-sm-4">
                                        <select name="active" id="active" class="form-select form-select-sm" >
                                            @if( $SubSite['attributes']['active'] )
                                                <option value ="1" selected>Yes</option>
                                                <option value ="0">No</option>
                                            @else
                                                <option value ="1">Yes</option>
                                                <option value ="0" selected>No</option>
                                            @endif
                                        </select>
                                    </div>
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Sub Contract</label>
                                    <div class="col-sm-4">
                                        <select name="sub_contract" id="sub_contract" class="form-select form-select-sm" >
                                            @if( $SubSite['attributes']['sub_contract'] )
                                                <option value ="1" selected>Yes</option>
                                                <option value ="0">No</option>
                                            @else
                                                <option value ="1">Yes</option>
                                                <option value ="0" selected>No</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Status</label>
                                    <div class="col-sm-4">
                                        <select name="status_id" id="status_id" class="form-select form-select-sm" >
                                            @foreach($SubSite['status'] as $row)
                                                @if($SubSite['attributes']['status_id'] == $row->status_id)
                                                    <option value ="{{$row->status_id}}" selected>{{$row->status_name}}</option>
                                                @else
                                                    <option value ="{{$row->status_id}}">{{$row->status_name}}</option>
                                                @endif
                                            @endforeach
                                            @if($SubSite['attributes']['status_id'] == "0")
                                                <option value ="0" selected>Select the Status </option>
                                            @endif
                                        </select>
                                        @if($SubSite['attributes']['validation_messages']->has('status_id'))
                                            <script>
                                                    document.getElementById('status_id').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $SubSite['attributes']['validation_messages']->first("status_id") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Remark</label>
                                    <div class="col-sm-10">
                                        <textarea  name="remark" id="remark" class="form-control" rows="2" style="resize:none">{{$SubSite['attributes']['remark']}}</textarea>
                                        @if($SubSite['attributes']['validation_messages']->has('remark'))
                                            <script>
                                                    document.getElementById('remark').className = 'form-control is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $SubSite['attributes']['validation_messages']->first("remark") }}</div>
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
