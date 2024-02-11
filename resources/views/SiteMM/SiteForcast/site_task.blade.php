@extends('layouts.site_monitoring')
@section('title')
    Site Task
@endsection
@section('body')

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{route('site_task_process')}}">
                @csrf

                <div class="card mt-3">

                    <div class="card-header">
                        Site Task
                    </div>

                    <div class="card-body">

                        <div class="row">
                            <div class="col-sm-12">
                                <?php echo $Site['attributes']['process_message'];  ?>
                            </div>
                        </div>

                        <div class="row no-gutters">
                            <div class="col-12 col-sm-6 col-md-6">

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Task ID</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="task_id" id="task_id" class="form-control form-control-sm"  value="{{$Site['attributes']['task_id']}}" readonly>
                                        @if($Site['attributes']['validation_messages']->has('task_id'))
                                            <script>
                                                    document.getElementById('task_id').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $Site['attributes']['validation_messages']->first("task_id") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Name</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="task_name" id="task_name" class="form-control form-control-sm"  value="{{$Site['attributes']['task_name']}}">
                                        @if($Site['attributes']['validation_messages']->has('task_name'))
                                            <script>
                                                    document.getElementById('task_name').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $Site['attributes']['validation_messages']->first("task_name") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Site</label>
                                    <div class="col-sm-10">
                                        <select name="site_id" id="site_id" class="form-select form-select-sm" >
                                            @foreach($Site['site'] as $row)
                                                @if($Site['attributes']['site_id'] == $row->site_id)
                                                    <option value ="{{$row->site_id}}" selected>{{$row->site_name}}</option>
                                                @else
                                                    <option value ="{{$row->site_id}}">{{$row->site_name}}</option>
                                                @endif
                                            @endforeach
                                            @if($Site['attributes']['site_id'] == "0")
                                                <option value ="0" selected>Select the Site </option>
                                            @endif
                                        </select>
                                        @if($Site['attributes']['validation_messages']->has('site_id'))
                                            <script>
                                                    document.getElementById('site_id').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $Site['attributes']['validation_messages']->first("site_id") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Start Date</label>
                                    <div class="col-sm-4">
                                        <input type="date" name="start_date" id="start_date" class="form-control form-control-sm"  value="{{$Site['attributes']['start_date']}}">
                                        @if($Site['attributes']['validation_messages']->has('start_date'))
                                            <script>
                                                    document.getElementById('start_date').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $Site['attributes']['validation_messages']->first("start_date") }}</div>
                                        @endif
                                    </div>
                                    <label for="tid" class="col-sm-2 col-form-label-sm">End Date</label>
                                    <div class="col-sm-4">
                                        <input type="date" name="end_date" id="end_date" class="form-control form-control-sm"  value="{{$Site['attributes']['end_date']}}">
                                        @if($Site['attributes']['validation_messages']->has('end_date'))
                                            <script>
                                                    document.getElementById('end_date').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $Site['attributes']['validation_messages']->first("end_date") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Active</label>
                                    <div class="col-sm-4">
                                        <select name="active" id="active" class="form-select form-select-sm" >
                                            @if( $Site['attributes']['active'] )
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
                                            @if( $Site['attributes']['sub_contract'] )
                                                <option value ="1" selected>Yes</option>
                                                <option value ="0">No</option>
                                            @else
                                                <option value ="1">Yes</option>
                                                <option value ="0" selected>No</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Remark</label>
                                    <div class="col-sm-10">
                                        <textarea  name="remark" id="remark" class="form-control" rows="2" style="resize:none">{{$Site['attributes']['remark']}}</textarea>
                                        @if($Site['attributes']['validation_messages']->has('remark'))
                                            <script>
                                                    document.getElementById('remark').className = 'form-control is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $Site['attributes']['validation_messages']->first("remark") }}</div>
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
