@extends('layouts.site_monitoring')
@section('title')
    SAP Profit
@endsection
@section('body')

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{route('sap_profit_add_process')}}">
                @csrf

                <div class="card mt-3">

                    <div class="card-header">
                        SAP Profit
                    </div>

                    <div class="card-body">

                        <div class="row">
                            <div class="col-sm-12">
                                <?php echo $sap_profit['attributes']['process_message'];  ?>
                            </div>
                        </div>

                        <div class="row no-gutters">
                            <div class="col-12 col-sm-12 col-md-12">

                                <div class="row mb-2">

                                    <label for="tid" class="col-sm-1 col-form-label-sm">Site</label>
                                    <div class="col-sm-5">
                                        <select name="site_id" id="site_id" class="form-select form-select-sm" >
                                            @foreach($sap_profit['site'] as $row)
                                                @if($sap_profit['attributes']['site_id'] == $row->site_id)
                                                    <option value ="{{$row->site_id}}" selected>{{$row->site_name}}</option>
                                                @else
                                                    <option value ="{{$row->site_id}}">{{$row->site_name}}</option>
                                                @endif
                                            @endforeach
                                            @if($sap_profit['attributes']['site_id'] == "0")
                                                <option value ="0" selected>Select the Site </option>
                                            @endif
                                        </select>
                                        @if($sap_profit['attributes']['validation_messages']->has('site_id'))
                                            <script>
                                                    document.getElementById('site_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $sap_profit['attributes']['validation_messages']->first("site_id") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Task</label>
                                    <div class="col-sm-5">
                                        <select name="task_id" id="task_id" class="form-select form-select-sm" >
                                            @if( ($sap_profit['attributes']['task_id'] != "0") || ($sap_profit['attributes']['site_id'] != "0") )
                                                @foreach($sap_profit['site_task'] as $row)
                                                    @if($sap_profit['attributes']['task_id'] == $row->task_id)
                                                        <option value ="{{$row->task_id}}" selected>{{$row->task_name}}</option>
                                                    @else
                                                        <option value ="{{$row->task_id}}">{{$row->task_name}}</option>
                                                    @endif
                                                @endforeach
                                            @endif
                                            @if( $sap_profit['attributes']['task_id'] != "0" )
                                                <option value ="0">Select the Task </option>
                                            @else
                                                <option value ="0" selected>Select the Task </option>
                                            @endif
                                        </select>
                                        @if($sap_profit['attributes']['validation_messages']->has('task_id'))
                                            <script>
                                                    document.getElementById('task_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $sap_profit['attributes']['validation_messages']->first("task_id") }}</div>
                                        @endif
                                    </div>
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Task Unit</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="task_unit" id="task_unit" class="form-control form-control-sm" value="" readonly>
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Sub Task</label>
                                    <div class="col-sm-5">
                                        <select name="sub_task_id" id="sub_task_id" class="form-select form-select-sm" >
                                            @if( ($sap_profit['attributes']['sub_task_id'] != "0") || ($sap_profit['attributes']['task_id'] != "0") )
                                                @foreach($sap_profit['site_sub_task'] as $row)
                                                    @if($sap_profit['attributes']['sub_task_id'] == $row->sub_task_id)
                                                        <option value ="{{$row->sub_task_id}}" selected>{{$row->sub_task_name}}</option>
                                                    @else
                                                        <option value ="{{$row->sub_task_id}}">{{$row->sub_task_name}}</option>
                                                    @endif
                                                @endforeach
                                            @endif
                                            @if( $sap_profit['attributes']['sub_task_id'] != "0" )
                                                <option value ="0">Select the Sub Task </option>
                                            @else
                                                <option value ="0" selected>Select the Sub Task </option>
                                            @endif
                                        </select>
                                        @if($sap_profit['attributes']['validation_messages']->has('sub_task_id'))
                                            <script>
                                                    document.getElementById('sub_task_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $sap_profit['attributes']['validation_messages']->first("sub_task_id") }}</div>
                                        @endif
                                    </div>

                                    <div class="col-sm-1">
                                        <input type="submit" name="submit" id="submit" class="btn btn-primary btn-sm w-100" value="Display">
                                    </div>
                                </div>

                                <hr>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Material Cost</label>
                                    <div class="col-sm-3">
                                        <input type="text" name="material_cost" id="material_cost" class="form-control form-control-sm text-end" value="{{$sap_profit['attributes']['material_cost']}}" readonly>
                                    </div>
                                    <div class="col-sm-2">
                                        <input type="button" name="submit" id="submit" class="btn btn-primary btn-sm w-100" value="Material Cost Display">
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Labour Cost</label>
                                    <div class="col-sm-3">
                                        <input type="text" name="labour_cost" id="labour_cost" class="form-control form-control-sm text-end" value="{{$sap_profit['attributes']['labour_cost']}}" readonly>
                                    </div>
                                    <div class="col-sm-2">
                                        <input type="button" name="submit" id="submit" class="btn btn-primary btn-sm w-100" value="Labour Cost Display">
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Overhead Cost</label>
                                    <div class="col-sm-3">
                                        <input type="text" name="overhead_cost" id="overhead_cost" class="form-control form-control-sm text-end" value="{{$sap_profit['attributes']['overhead_cost']}}" readonly>
                                    </div>
                                    <div class="col-sm-2">
                                        <input type="button" name="submit" id="submit" class="btn btn-primary btn-sm w-100" value="Overhead Cost Display">
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Total Cost</label>
                                    <div class="col-sm-3">
                                        <input type="text" name="total_cost" id="total_cost" class="form-control form-control-sm text-end" value="{{$sap_profit['attributes']['total_cost']}}" readonly>
                                    </div>
                                    <div class="col-sm-2">
                                        <input type="button" name="submit" id="submit" class="btn btn-primary btn-sm w-100" value="Total Cost Display">
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Profit Value</label>
                                    <div class="col-sm-3">
                                        <input type="text" name="profit_value" id="profit_value" class="form-control form-control-sm text-end" value="{{$sap_profit['attributes']['profit_value']}}">
                                    </div>
                                </div>
                                <br><br>

                                <div class="row mb-2 text-end">
                                    <div class="col-sm-1">
                                        <input type="submit" name="submit" id="submit" class="btn btn-primary btn-sm w-100" value="Submit">
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
