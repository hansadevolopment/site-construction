@extends('layouts.site_monitoring')
@section('title')
    SAP Labour
@endsection
@section('body')

    <div class="row">
        <div class="col-12">

            <form id="frm_sap_labour" method="POST" action="{{route('sap_labour_add_process')}}">

                @csrf

                <div class="card mt-3">

                    <div class="card-header">
                        SAP Labour
                    </div>

                    <div class="card-body">

                        <div class="row">
                            <div class="col-sm-12">
                                <?php echo $sap_labour['attributes']['process_message'];  ?>
                            </div>
                        </div>

                        <div class="row no-gutters">
                            <div class="col-12 col-sm-12 col-md-12">

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Salary Category</label>
                                    <div class="col-sm-2">
                                        <select name="sc_id" id="sc_id" class="form-select form-select-sm sap_labour_sc_id" >
                                            @foreach($sap_labour['salary_category'] as $row)
                                                @if($sap_labour['attributes']['sc_id'] == $row->id)
                                                    <option value ="{{$row->id}}" selected>{{$row->category}}</option>
                                                @else
                                                    <option value ="{{$row->id}}">{{$row->category}}</option>
                                                @endif
                                            @endforeach
                                            @if($sap_labour['attributes']['sc_id'] == "0")
                                                <option value ="0" selected>Select the Salary Category </option>
                                            @endif
                                        </select>
                                        @if($sap_labour['attributes']['validation_messages']->has('sc_id'))
                                            <script>
                                                    document.getElementById('sc_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $sap_labour['attributes']['validation_messages']->first("sc_id") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">

                                    <label for="tid" class="col-sm-1 col-form-label-sm">Site</label>
                                    <div class="col-sm-5">
                                        <select name="site_id" id="site_id" class="form-select form-select-sm" >
                                            @foreach($sap_labour['site'] as $row)
                                                @if($sap_labour['attributes']['site_id'] == $row->site_id)
                                                    <option value ="{{$row->site_id}}" selected>{{$row->site_name}}</option>
                                                @else
                                                    <option value ="{{$row->site_id}}">{{$row->site_name}}</option>
                                                @endif
                                            @endforeach
                                            @if($sap_labour['attributes']['site_id'] == "0")
                                                <option value ="0" selected>Select the Site </option>
                                            @endif
                                        </select>
                                        @if($sap_labour['attributes']['validation_messages']->has('site_id'))
                                            <script>
                                                    document.getElementById('site_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $sap_labour['attributes']['validation_messages']->first("site_id") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Task</label>
                                    <div class="col-sm-5">
                                        <select name="task_id" id="task_id" class="form-select form-select-sm" >
                                            @if( ($sap_labour['attributes']['task_id'] != "0") || ($sap_labour['attributes']['site_id'] != "0") )
                                                @foreach($sap_labour['site_task'] as $row)
                                                    @if($sap_labour['attributes']['task_id'] == $row->task_id)
                                                        <option value ="{{$row->task_id}}" selected>{{$row->task_name}}</option>
                                                    @else
                                                        <option value ="{{$row->task_id}}">{{$row->task_name}}</option>
                                                    @endif
                                                @endforeach
                                            @endif
                                            @if( $sap_labour['attributes']['task_id'] != "0" )
                                                <option value ="0">Select the Task </option>
                                            @else
                                                <option value ="0" selected>Select the Task </option>
                                            @endif
                                        </select>
                                        @if($sap_labour['attributes']['validation_messages']->has('task_id'))
                                            <script>
                                                    document.getElementById('task_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $sap_labour['attributes']['validation_messages']->first("task_id") }}</div>
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
                                            @if( ($sap_labour['attributes']['sub_task_id'] != "0") || ($sap_labour['attributes']['task_id'] != "0") )
                                                @foreach($sap_labour['site_sub_task'] as $row)
                                                    @if($sap_labour['attributes']['sub_task_id'] == $row->sub_task_id)
                                                        <option value ="{{$row->sub_task_id}}" selected>{{$row->sub_task_name}}</option>
                                                    @else
                                                        <option value ="{{$row->sub_task_id}}">{{$row->sub_task_name}}</option>
                                                    @endif
                                                @endforeach
                                            @endif
                                            @if( $sap_labour['attributes']['sub_task_id'] != "0" )
                                                <option value ="0">Select the Sub Task </option>
                                            @else
                                                <option value ="0" selected>Select the Sub Task </option>
                                            @endif
                                        </select>
                                        @if($sap_labour['attributes']['validation_messages']->has('sub_task_id'))
                                            <script>
                                                    document.getElementById('sub_task_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $sap_labour['attributes']['validation_messages']->first("sub_task_id") }}</div>
                                        @endif
                                    </div>

                                    <div class="col-sm-1">
                                        <input type="submit" name="submit" id="submit" class="btn btn-primary btn-sm w-100" value="Display">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Remark</label>
                                    <div class="col-sm-11">
                                        <textarea  name="remark" id="remark" class="form-control" rows="2" style="resize:none">{{$sap_labour['attributes']['remark']}}</textarea>
                                        @if($sap_labour['attributes']['validation_messages']->has('remark'))
                                            <script>
                                                    document.getElementById('remark').className = 'form-control is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $sap_labour['attributes']['validation_messages']->first("remark") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <hr>



                            </div>

                            <div id="sap_labour_basic_salary" class="col-12 col-md-12">

                                <div class="row mb-2">

                                    <label for="tid" class="col-sm-1 col-form-label-sm">Labour</label>
                                    <div class="col-sm-6">
                                        <select name="lc_id" id="lc_id" class="form-select form-select-sm">
                                            @foreach($sap_labour['labour'] as $row)
                                                <option value ="{{$row->lc_id}}">{{ucwords($row->lc_name . '  ---> ' . $row->price)}}</option>
                                            @endforeach
                                            <option value ="0" selected>Select the Labour</option>
                                        </select>
                                        @if($sap_labour['attributes']['validation_messages']->has('lc_id'))
                                            <script>
                                                    document.getElementById('lc_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $sap_labour['attributes']['validation_messages']->first("lc_id") }}</div>
                                        @endif
                                    </div>

                                    <div class="col-sm-2">
                                        <input type="text" name="price" id="price" class="form-control form-control-sm text-end" value="" placeholder="Price" readonly>
                                    </div>

                                    <div class="col-sm-2">
                                        <input type="text" name="days" id="days" class="form-control form-control-sm text-end" value="" placeholder="Days">
                                        @if($sap_labour['attributes']['validation_messages']->has('days'))
                                            <script>
                                                    document.getElementById('days').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $sap_labour['attributes']['validation_messages']->first("days") }}</div>
                                        @endif
                                    </div>

                                    <div class="col-sm-1">
                                        <input type="submit" name="submit" id="submit" class="btn btn-success btn-sm w-100" value="Add">
                                    </div>
                                </div>

                                <hr>

                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%;">#</th>
                                                <th style="width: 55%;">Labour</th>
                                                <th style="width: 10%;" class="text-end">Price</th>
                                                <th style="width: 10%;" class="text-end">Days</th>
                                                <th style="width: 10%;" class="text-end">(Rs.) Amount</th>
                                            </tr>
                                        </thead>
                                        @if( count($sap_labour['attributes']['labour_detail']) >= 1 )

                                            <tbody>
                                                @foreach ($sap_labour['attributes']['labour_detail'] as $rowKey => $rowValue)
                                                    <tr>
                                                        <td style="width: 5%;">{{$rowValue->ono}}</td>
                                                        <td style="width: 65%;">{{ucwords($rowValue->lc_name)}}</td>
                                                        <td style="width: 10%;" class="text-end">@money($rowValue->price)</td>
                                                        <td style="width: 10%;" class="text-end">{{$rowValue->days}}</td>
                                                        <td style="width: 10%;" class="text-end">@money($rowValue->amount)</td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <td colspan="4">Total</td>
                                                    <td class="text-end"><strong>@money($sap_labour['attributes']['labour_total'])</strong></td>
                                                    <td></td>
                                                </tr>
                                            </tbody>

                                        @else
                                            <tbody>
                                                <tr>
                                                    <td style="width: 5%;">-</td>
                                                    <td style="width: 65%;">-</td>
                                                    <td style="width: 10%;" class="text-end">0.00</td>
                                                    <td style="width: 10%;" class="text-end">0</td>
                                                    <td style="width: 10%;" class="text-end">0.00</td>
                                                </tr>
                                            </tbody>
                                        @endif
                                    </table>
                                  </div>

                            </div>

                            <div id="sap_labour_sub_target" class="col-12 col-md-12" style="display:none;">

                                <div class="row mb-2">

                                    <label for="tid" class="col-sm-1 col-form-label-sm">Unit</label>
                                    <div class="col-sm-6">
                                        <select name="unit_id" id="unit_id" class="form-select form-select-sm">
                                            @foreach($sap_labour['unit'] as $row)
                                                <option value ="{{$row->unit_id}}">{{ucwords($row->unit_name)}}</option>
                                            @endforeach
                                            <option value ="0" selected>Select the Unit</option>
                                        </select>
                                        @if($sap_labour['attributes']['validation_messages']->has('unit_id'))
                                            <script>
                                                    document.getElementById('unit_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $sap_labour['attributes']['validation_messages']->first("unit_id") }}</div>
                                        @endif
                                    </div>

                                    <div class="col-sm-2">
                                        <input type="text" name="unit_rate" id="unit_rate" class="form-control form-control-sm text-end" value="" placeholder="Unit Rate">
                                        @if($sap_labour['attributes']['validation_messages']->has('unit_rate'))
                                            <script>
                                                    document.getElementById('unit_rate').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $sap_labour['attributes']['validation_messages']->first("unit_rate") }}</div>
                                        @endif
                                    </div>

                                    <div class="col-sm-2">
                                        <input type="text" name="quantity" id="quantity" class="form-control form-control-sm text-end" value="" placeholder="Quantity">
                                        @if($sap_labour['attributes']['validation_messages']->has('quantity'))
                                            <script>
                                                    document.getElementById('quantity').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $sap_labour['attributes']['validation_messages']->first("quantity") }}</div>
                                        @endif
                                    </div>

                                    <div class="col-sm-1">
                                        <input type="submit" name="submit" id="submit" class="btn btn-success btn-sm w-100" value="Add">
                                    </div>
                                </div>

                                <hr>

                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%;">#</th>
                                                <th style="width: 50%;">Unit</th>
                                                <th style="width: 15%;" class="text-end">Unit Rate</th>
                                                <th style="width: 15%;" class="text-end">Quantity</th>
                                                <th style="width: 15%;" class="text-end">(Rs.) Amount</th>
                                            </tr>
                                        </thead>
                                        @if( count($sap_labour['attributes']['labour_detail']) >= 1 )

                                            <tbody>
                                                @foreach ($sap_labour['attributes']['labour_detail'] as $rowKey => $rowValue)
                                                    <tr>
                                                        <td style="width: 5%;">{{$rowValue->ono}}</td>
                                                        <td style="width: 50%;">{{ucwords($rowValue->lc_name)}}</td>
                                                        <td style="width: 15%;" class="text-end">@money($rowValue->price)</td>
                                                        <td style="width: 15%;" class="text-end">{{$rowValue->days}}</td>
                                                        <td style="width: 15%;" class="text-end">@money($rowValue->amount)</td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <td colspan="4">Total</td>
                                                    <td class="text-end"><strong>@money($sap_labour['attributes']['labour_total'])</strong></td>
                                                    <td></td>
                                                </tr>
                                            </tbody>

                                        @else
                                            <tbody>
                                                <tr>
                                                    <td style="width: 5%;">-</td>
                                                    <td style="width: 50%;">-</td>
                                                    <td style="width: 15%;" class="text-end">0.00</td>
                                                    <td style="width: 15%;" class="text-end">0</td>
                                                    <td style="width: 15%;" class="text-end">0.00</td>
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
