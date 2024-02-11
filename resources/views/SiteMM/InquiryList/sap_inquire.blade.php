@extends('layouts.site_monitoring')
@section('title')
    SAP Inquire
@endsection
@section('body')

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{route('sap_inquire_process')}}">
                @csrf

                <div class="card mt-3">

                    <div class="card-header">
                        SAP Inquire
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

                                    <label for="tid" class="col-sm-1 col-form-label-sm">Cost Section</label>
                                    <div class="col-sm-2">
                                        <select name="cs_id" id="cs_id" class="form-select form-select-sm" >
                                            @foreach($stsil['cost_section'] as $row)
                                                @if($stsil['attributes']['cs_id'] == $row->cs_id)
                                                    <option value ="{{$row->cs_id}}" selected>{{$row->cs_name}}</option>
                                                @else
                                                    <option value ="{{$row->cs_id}}">{{$row->cs_name}}</option>
                                                @endif
                                            @endforeach
                                            @if( $stsil['attributes']['cs_id'] != "0" )
                                                <option value ="0">Select the Cost Section </option>
                                            @else
                                                <option value ="0" selected>Select the Cost Section </option>
                                            @endif
                                        </select>
                                        @if($stsil['attributes']['validation_messages']->has('cs_id'))
                                            <script>
                                                    document.getElementById('cs_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $stsil['attributes']['validation_messages']->first("cs_id") }}</div>
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

                                </div>

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
                                            @if( $stsil['source_name'] != 'profit' )
                                                <tr>
                                                    <th style="width: 5%;">#</th>
                                                    <th id="stsil" style="width: 55%;">
                                                        {{$stsil['source_name']}}
                                                    </th>
                                                    <th style="width: 10%;" class="text-end">Price</th>
                                                    <th style="width: 10%;" class="text-end">Qty</th>
                                                    <th style="width: 10%;" class="text-end">Amount</th>
                                                    <th style="width: 10%;"></th>
                                                </tr>
                                            @else
                                                <tr>
                                                    <th style="width: 5%;">#</th>
                                                    <th style="width: 15%;" class="text-end">Material Cost</th>
                                                    <th style="width: 15%;" class="text-end">Labour Cost</th>
                                                    <th style="width: 15%;" class="text-end">Overhead Cost</th>
                                                    <th style="width: 15%;" class="text-end">Total Cost</th>
                                                    <th style="width: 15%;" class="text-end">Profit</th>
                                                    <th style="width: 10%;"></th>
                                                </tr>
                                            @endif
                                        </thead>
                                        @if( count($stsil['stsil_detail']) >= 1 )

                                            <tbody>
                                                @foreach ($stsil['stsil_detail'] as $rowKey => $rowValue)
                                                    @if( $stsil['source_name'] != 'profit' )
                                                        <tr>
                                                            <td style="width: 5%;">{{($rowKey + 1)}}</td>
                                                            @if( ($stsil['stsil_detail']->cost_section == 'material') )
                                                                <td style="width: 55%;">{{$rowValue->getItem()->item_name}}</td>
                                                            @elseif( ($stsil['stsil_detail']->cost_section == 'labour') )
                                                                @if( $rowValue->lc_id != 0 )
                                                                    <td style="width: 55%;">{{$rowValue->getLabourCategory()->lc_name}}</td>
                                                                @else
                                                                    <td style="width: 55%;">{{$rowValue->getUnit()->unit_name}}</td>
                                                                @endif
                                                            @elseif( ($stsil['stsil_detail']->cost_section == 'overhead') )
                                                                <td style="width: 55%;">{{$rowValue->getOverheadCost()->oci_name}}</td>
                                                            @endif
                                                            <td style="width: 10%;" class="text-end">@money($rowValue->amount)</td>
                                                            <td style="width: 10%;" class="text-end">{{1}}</td>
                                                            <td style="width: 10%;" class="text-end">@money($rowValue->amount)</td>
                                                            <td style="width: 10%;">
                                                                @if( ($stsil['stsil_detail']->cost_section == 'material') )
                                                                    <input type="button" name="btnOpen" id="btnOpen" data-sap-material-id="{{$rowValue->sap_material_id}}" class="btn btn-primary btn-sm w-100 {{$stsil['stsil_detail']->class_name}}-inquiry-open" value="Open">
                                                                @elseif( ($stsil['stsil_detail']->cost_section == 'labour') )
                                                                    <input type="button" name="btnOpen" id="btnOpen" data-sap-labour-id="{{$rowValue->sap_labour_id}}" class="btn btn-primary btn-sm w-100 {{$stsil['stsil_detail']->class_name}}-inquiry-open" value="Open">
                                                                @elseif( ($stsil['stsil_detail']->cost_section == 'overhead') )
                                                                    <input type="button" name="btnOpen" id="btnOpen" data-sap-oc-id="{{$rowValue->sap_oc_id}}" class="btn btn-primary btn-sm w-100 {{$stsil['stsil_detail']->class_name}}-inquiry-open" value="Open">
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @else
                                                        <tr>
                                                            <td style="width: 5%;">{{($rowKey + 1)}}</td>
                                                            <td style="width: 15%;" class="text-end">@money($rowValue->material_cost)</td>
                                                            <td style="width: 15%;" class="text-end">@money($rowValue->labour_cost)</td>
                                                            <td style="width: 15%;" class="text-end">@money($rowValue->overhead_cost)</td>
                                                            <td style="width: 15%;" class="text-end">@money($rowValue->total_cost)</td>
                                                            <td style="width: 15%;" class="text-end">@money($rowValue->profit_value)</td>
                                                            <td style="width: 10%;">
                                                                <input type="button" name="btnOpen" id="btnOpen" data-sap-profit-id="{{$rowValue->sap_profit_id}}"  class="btn btn-primary btn-sm w-100 {{$stsil['stsil_detail']->class_name}}-inquiry-open" value="Open">
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>

                                        @else
                                            <tbody>
                                                <tr>
                                                    <td style="width: 5%;">-</td>
                                                    <td style="width: 55%;">-</td>
                                                    <td style="width: 10%;" class="text-end">@money(0)</td>
                                                    <td style="width: 10%;" class="text-end">@money(0)</td>
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
            <form id="open_sap_material" style="display: none;" method="post" target='_blank' action="{{route('open_sap_material')}}">
                @csrf
                <input type="text" name="open_sap_material_id" id="open_sap_material_id" values="">
            </form>
        </div>

        <div style="display: none;">
            <form id="open_sap_labour" style="display: none;" method="post" target='_blank' action="{{route('open_sap_labour')}}">
                @csrf
                <input type="text" name="open_sap_labour_id" id="open_sap_labour_id" values="">
            </form>
        </div>

        <div style="display: none;">
            <form id="open_sap_overhead" style="display: none;" method="post" target='_blank' action="{{route('open_sap_overhead')}}">
                @csrf
                <input type="text" name="open_sap_overhead_cost_id" id="open_sap_overhead_cost_id" values="">
            </form>
        </div>

        <div style="display: none;">
            <form id="open_sap_profit" style="display: none;" method="post" target='_blank' action="{{route('open_sap_profit')}}">
                @csrf
                <input type="text" name="open_sap_profit_id" id="open_sap_profit_id" values="">
            </form>
        </div>

    </div>

@endsection
