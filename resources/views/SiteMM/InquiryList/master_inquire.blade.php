@extends('layouts.site_monitoring')
@section('title')
    Master Inquire
@endsection
@section('body')

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{route('master_inquire_process')}}">
                @csrf

                <div class="card mt-3">

                    <div class="card-header">
                        Master Inquire
                    </div>

                    <div class="card-body">

                        <div class="row">
                            <div class="col-sm-12">
                                <?php echo $stsil['attributes']['process_message'];  ?>
                            </div>
                        </div>

                        <div class="row no-gutters">
                            <div class="col-12 col-sm-12 col-md-12">

                                <div class="row mb-4">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Master Report</label>
                                    <div class="col-sm-6">
                                        <select name="master_id" id="master_id" class="form-select form-select-sm" >
                                            <option value ="1">Item</option>
                                            <option value ="2">Employee</option>
                                            <option value ="3">Labour Category</option>
                                            <option value ="4">Overhead Cost</option>
                                            <option value ="5">Unit</option>
                                            <option value ="0" selected>Select the Report</option>
                                        </select>
                                    </div>

                                    <div class="col-sm-2">
                                        <input type="submit" name="submit" id="submit" class="btn btn-primary btn-sm w-100" value="Display">
                                    </div>

                                </div>

                            </div>
                            <hr>

                            <div class="col-12 colmd-12">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%;">#</th>
                                                @if( $stsil['source_name'] == 'Employee' )
                                                    <th style="width: 10%;">Emp Code.</th>
                                                    <th id="stsil" style="width: 45%;">
                                                        {{$stsil['source_name']}}
                                                    </th>
                                                @else
                                                    <th id="stsil" style="width: 55%;">
                                                        {{$stsil['source_name']}}
                                                    </th>
                                                @endif
                                                <th style="width: 10%;">Active</th>
                                                @if( ($stsil['source_name'] != 'Labour Category') || ($stsil['source_name'] == 'Unit') )
                                                    <th style="width: 10%;">Category</th>
                                                @endif
                                                @if( ($stsil['source_name'] != 'Overhead Cost') || ($stsil['source_name'] == 'Unit') )
                                                    <th style="width: 10%;" class="text-end">Price</th>
                                                @endif
                                                <th style="width: 10%;"></th>
                                            </tr>
                                        </thead>
                                        @if( count($stsil['stsil_detail']) >= 1 )

                                            <tbody>
                                                @foreach ($stsil['stsil_detail'] as $rowKey => $rowValue)
                                                    @if( $stsil['source_name'] != 'profit' )
                                                        <tr>
                                                            <td style="width: 5%;">{{($rowKey + 1)}}</td>

                                                            @if( $stsil['source_name'] == 'Employee' )
                                                                <td style="width: 10%;">{{$rowValue->employee_code}}</td>
                                                                <td id="stsil" style="width: 45%;">
                                                                    {{$rowValue->employee_name}}
                                                                </td>
                                                            @elseif( ($stsil['source_name'] == 'Labour Category') )
                                                                <td id="stsil" style="width: 55%;">
                                                                    {{$rowValue->lc_name}}
                                                                </td>
                                                            @elseif( ($stsil['source_name'] == 'Item') )
                                                                <td id="stsil" style="width: 55%;">
                                                                    {{$rowValue->item_name}}
                                                                </td>
                                                            @elseif( ($stsil['source_name'] == 'Overhead Cost') )
                                                                <td id="stsil" style="width: 55%;">
                                                                    {{$rowValue->oci_name}}
                                                                </td>
                                                            @else
                                                                <td id="stsil" style="width: 75%;">
                                                                    {{$rowValue->unit_name}}
                                                                </td>
                                                            @endif

                                                            @if( $rowValue->active == 1 )
                                                                <td style="width: 10%;">Yes</td>
                                                            @else
                                                                <td style="width: 10%;">No</td>
                                                            @endif

                                                            @if( $stsil['source_name'] == 'Employee' )
                                                                <td style="width: 10%;">{{$rowValue->getLaborCategory()->lc_name}}</td>
                                                                <td style="width: 10%;" class="text-end">@money($rowValue->getLaborCategory()->price)</td>
                                                            @elseif( ($stsil['source_name'] == 'Item') )
                                                                <td style="width: 10%;">{{$rowValue->getUnit()->unit_name}}</td>
                                                                <td style="width: 10%;" class="text-end">@money($rowValue->price)</td>
                                                            @elseif( ($stsil['source_name'] == 'Overhead Cost') )
                                                                <td style="width: 10%;">{{$rowValue->getUnit()->unit_name}}</td>
                                                            @elseif( ($stsil['source_name'] == 'Unit') )

                                                            @else
                                                                <td style="width: 10%;" class="text-end">@money($rowValue->price)</td>
                                                            @endif

                                                            <td style="width: 10%;">
                                                                @if( ($stsil['source_name'] == 'Item') )
                                                                    <input type="button" name="btnOpen" id="btnOpen" data-item-id="{{$rowValue->item_id}}" class="btn btn-primary btn-sm w-100 item-master-open" value="Open">
                                                                @elseif( ($stsil['source_name'] == 'Employee') )
                                                                    <input type="button" name="btnOpen" id="btnOpen" data-employee-id="{{$rowValue->employee_id}}" class="btn btn-primary btn-sm w-100 employee-inquiry-open" value="Open">
                                                                @elseif( ($stsil['source_name'] == 'Labour Category') )
                                                                    <input type="button" name="btnOpen" id="btnOpen" data-lc-id="{{$rowValue->lc_id}}" class="btn btn-primary btn-sm w-100 lc-inquiry-open" value="Open">
                                                                @elseif( ($stsil['source_name'] == 'Overhead Cost') )
                                                                    <input type="button" name="btnOpen" id="btnOpen" data-oci-id="{{$rowValue->oci_id}}" class="btn btn-primary btn-sm w-100 oci-inquiry-open" value="Open">
                                                                @elseif( ($stsil['source_name'] == 'Unit') )
                                                                    <input type="button" name="btnOpen" id="btnOpen" data-unit-id="{{$rowValue->unit_id}}" class="btn btn-primary btn-sm w-100 unit-inquiry-open" value="Open">
                                                                @endif
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
                                                    <td style="width: 10%;">-</td>
                                                    <td style="width: 10%;">-</td>
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

            @if( ($stsil['source_name'] == 'Item') )
                <form id="open_item" style="display: none;" method="post" target='_blank' action="{{route('open_item')}}">
                    <input type="text" name="item_id" id="item_id" values="">
            @elseif( ($stsil['source_name'] == 'Employee') )
                <form id="open_employee" style="display: none;" method="post" target='_blank' action="{{route('open_employee')}}">
                    <input type="text" name="employee_id" id="employee_id" values="">
            @elseif( ($stsil['source_name'] == 'Labour Category') )
                <form id="open_labour_category" style="display: none;" method="post" target='_blank' action="{{route('open_labour_category')}}">
                    <input type="text" name="lc_id" id="lc_id" values="">
            @elseif( ($stsil['source_name'] == 'Overhead Cost') )
                <form id="open_overhead" style="display: none;" method="post" target='_blank' action="{{route('open_overhead')}}">
                    <input type="text" name="oci_id" id="oci_id" values="">
            @elseif( ($stsil['source_name'] == 'Unit') )
                <form id="open_unit" style="display: none;" method="post" target='_blank' action="{{route('open_unit')}}">
                    <input type="text" name="unit_id" id="unit_id" values="">
            @endif

                @csrf

            </form>
        </div>

    </div>

@endsection
