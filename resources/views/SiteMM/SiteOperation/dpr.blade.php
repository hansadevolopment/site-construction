@extends('layouts.site_monitoring')
@section('title')
    Daily Progress Report
@endsection
@section('body')

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{route('dpr_process')}}">
                @csrf

                <div class="card mt-3">

                    <div class="card-header">
                        Daily Progress Report
                    </div>

                    <div class="card-body">

                        <div class="row">
                            <div class="col-sm-12">
                                <?php echo $DPR['attributes']['process_message'];  ?>
                            </div>
                        </div>

                        <div class="row no-gutters">
                            <div class="col-12 col-sm-12 col-md-12">

                                <div class="row mb-2">

                                    <label for="tid" class="col-sm-1 col-form-label-sm">Cost Section</label>
                                    <div class="col-sm-2">
                                        <select name="cs_id" id="cs_id" class="form-select form-select-sm dpr-cs-id" >
                                            @foreach($DPR['cost_section'] as $row)
                                                @if($DPR['attributes']['cs_id'] == $row->cs_id)
                                                    <option value ="{{$row->cs_id}}" selected>{{$row->cs_name}}</option>
                                                @else
                                                    <option value ="{{$row->cs_id}}">{{$row->cs_name}}</option>
                                                @endif
                                            @endforeach
                                            @if($DPR['attributes']['cs_id'] == "0")
                                                <option value ="0" selected>Select the Cost Section </option>
                                            @endif
                                        </select>
                                        @if($DPR['attributes']['validation_messages']->has('cs_id'))
                                            <script>
                                                    document.getElementById('cs_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $DPR['attributes']['validation_messages']->first("cs_id") }}</div>
                                        @endif
                                    </div>

                                    <div class="col-sm-6">
                                    </div>

                                    <label for="tid" class="col-sm-1 col-form-label-sm">DPR No</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="dpr_id" id="dpr_id" class="form-control form-control-sm" value="{{$DPR['attributes']['dpr_id']}}">
                                        @if($DPR['attributes']['validation_messages']->has('dpr_id'))
                                            <script>
                                                    document.getElementById('dpr_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $DPR['attributes']['validation_messages']->first("dpr_id") }}</div>
                                        @endif
                                    </div>

                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Site</label>
                                    <div class="col-sm-5">
                                        <select name="site_id" id="site_id" class="form-select form-select-sm" >
                                            @foreach($DPR['site'] as $row)
                                                @if($DPR['attributes']['site_id'] == $row->site_id)
                                                    <option value ="{{$row->site_id}}" selected>{{$row->site_name}}</option>
                                                @else
                                                    <option value ="{{$row->site_id}}">{{$row->site_name}}</option>
                                                @endif
                                            @endforeach
                                            @if($DPR['attributes']['site_id'] == "0")
                                                <option value ="0" selected>Select the Site </option>
                                            @endif
                                        </select>
                                        @if($DPR['attributes']['validation_messages']->has('site_id'))
                                            <script>
                                                    document.getElementById('site_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $DPR['attributes']['validation_messages']->first("site_id") }}</div>
                                        @endif
                                    </div>

                                    <div class="col-sm-3">
                                    </div>

                                    <label for="tid" class="col-sm-1 col-form-label-sm">DPR Date</label>
                                    <div class="col-sm-2">
                                        <input type="date" name="dpr_date" id="dpr_date" class="form-control form-control-sm" value="{{$DPR['attributes']['dpr_date']}}">
                                        @if($DPR['attributes']['validation_messages']->has('dpr_date'))
                                            <script>
                                                    document.getElementById('dpr_date').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $DPR['attributes']['validation_messages']->first("dpr_date") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Task</label>
                                    <div class="col-sm-5">
                                        <select name="task_id" id="task_id" class="form-select form-select-sm" >
                                            @if( ($DPR['attributes']['task_id'] != "0") || ($DPR['attributes']['site_id'] != "0") )
                                                @foreach($DPR['site_task'] as $row)
                                                    @if($DPR['attributes']['task_id'] == $row->task_id)
                                                        <option value ="{{$row->task_id}}" selected>{{$row->task_name}}</option>
                                                    @else
                                                        <option value ="{{$row->task_id}}">{{$row->task_name}}</option>
                                                    @endif
                                                @endforeach
                                            @endif
                                            @if( $DPR['attributes']['task_id'] != "0" )
                                                <option value ="0">Select the Task </option>
                                            @else
                                                <option value ="0" selected>Select the Task </option>
                                            @endif
                                        </select>
                                        @if($DPR['attributes']['validation_messages']->has('task_id'))
                                            <script>
                                                    document.getElementById('task_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $DPR['attributes']['validation_messages']->first("task_id") }}</div>
                                        @endif
                                    </div>

                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Sub Task</label>
                                    <div class="col-sm-5">
                                        <select name="sub_task_id" id="sub_task_id" class="form-select form-select-sm" >
                                            @if( ($DPR['attributes']['sub_task_id'] != "0") || ($DPR['attributes']['task_id'] != "0") )
                                                @foreach($DPR['site_sub_task'] as $row)
                                                    @if($DPR['attributes']['sub_task_id'] == $row->sub_task_id)
                                                        <option value ="{{$row->sub_task_id}}" selected>{{$row->sub_task_name}}</option>
                                                    @else
                                                        <option value ="{{$row->sub_task_id}}">{{$row->sub_task_name}}</option>
                                                    @endif
                                                @endforeach
                                            @endif
                                            @if( $DPR['attributes']['sub_task_id'] != "0" )
                                                <option value ="0">Select the Sub Task </option>
                                            @else
                                                <option value ="0" selected>Select the Sub Task </option>
                                            @endif
                                        </select>
                                        @if($DPR['attributes']['validation_messages']->has('sub_task_id'))
                                            <script>
                                                    document.getElementById('sub_task_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $DPR['attributes']['validation_messages']->first("sub_task_id") }}</div>
                                        @endif
                                    </div>

                                    <label for="tid" class="col-sm-1 col-form-label-sm">Task Unit</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="task_unit" id="task_unit" class="form-control form-control-sm" value="" readonly>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Remark</label>
                                    <div class="col-sm-11">
                                        <textarea  name="remark" id="remark" class="form-control" rows="2" style="resize:none">{{$DPR['attributes']['remark']}}</textarea>
                                        @if($DPR['attributes']['validation_messages']->has('remark'))
                                            <script>
                                                    document.getElementById('remark').className = 'form-control is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $DPR['attributes']['validation_messages']->first("remark") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-sm-1">
                                        <input type="submit" name="submit" id="submit" class="btn btn-primary btn-sm w-100" value="Reset">
                                    </div>
                                    <div class="col-sm-1">
                                        <input type="submit" name="submit" id="submit" class="btn btn-danger btn-sm w-100 cancel-process" value="Cancel">
                                    </div>
                                </div>
                                <hr>

                                <div class="row mb-3">

                                    <label for="tid" class="col-sm-1 col-form-label-sm">Item</label>
                                    <div class="col-sm-5">
                                        <select name="item_id" id="item_id" class="form-select form-select-sm dpr-item">
                                            @if($DPR['attributes']['cs_id'] == 1)
                                                @foreach($DPR['item'] as $row)
                                                    @if($DPR['attributes']['item_id'] == $row->item_id)
                                                        <option value ="{{$row->item_id}}" selected>{{$row->item_name}}</option>
                                                    @else
                                                        <option value ="{{$row->item_id}}">{{$row->item_name}}</option>
                                                    @endif
                                                @endforeach
                                            @elseif( $DPR['attributes']['cs_id'] == 3 )
                                                @foreach($DPR['item'] as $row)
                                                    @if($DPR['attributes']['item_id'] == $row->item_id)
                                                        <option value ="{{$row->oci_id}}" selected>{{$row->oci_name}}</option>
                                                    @else
                                                        <option value ="{{$row->oci_id}}">{{$row->oci_name}}</option>
                                                    @endif
                                                @endforeach
                                            @else
                                            @endif
                                            @if($DPR['attributes']['item_id'] == 0)
                                                <option value ="0" selected>Select the Item</option>
                                            @endif
                                        </select>
                                        @if($DPR['attributes']['validation_messages']->has('item_id'))
                                            <script>
                                                    document.getElementById('item_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $DPR['attributes']['validation_messages']->first("item_id") }}</div>
                                        @endif
                                    </div>

                                    <div class="col-sm-1">
                                        <input type="text" name="unit" id="unit" class="form-control form-control-sm" placeholder="Unit" value="{{$DPR['attributes']['unit']}}" readonly>
                                    </div>

                                    <div class="col-sm-2">
                                        <input type="text" name="price" id="price" class="form-control form-control-sm text-end" placeholder="Price" value="{{$DPR['attributes']['price']}}">
                                        @if($DPR['attributes']['validation_messages']->has('price'))
                                            <script>
                                                    document.getElementById('price').className = 'form-control form-control-sm is-invalid text-end';
                                            </script>
                                            <div class="invalid-feedback">{{ $DPR['attributes']['validation_messages']->first("price") }}</div>
                                        @endif
                                    </div>

                                    <div class="col-sm-2">
                                        <input type="text" name="quantity" id="quantity" class="form-control form-control-sm text-end" placeholder="Quantity" value="{{$DPR['attributes']['quantity']}}">
                                        @if($DPR['attributes']['validation_messages']->has('quantity'))
                                            <script>
                                                    document.getElementById('quantity').className = 'form-control form-control-sm is-invalid text-end';
                                            </script>
                                            <div class="invalid-feedback">{{ $DPR['attributes']['validation_messages']->first("quantity") }}</div>
                                        @endif
                                    </div>

                                    <div class="col-sm-1">
                                        <input type="submit" name="submit" id="submit" class="btn btn-success btn-sm w-100" value="Add">
                                    </div>
                                </div>


                                <hr>

                            </div>

                            <div class="col-12 colmd-12">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%;">#</th>
                                                <th style="width: 55%;">Item</th>
                                                <th style="width: 10%;">Unit</th>
                                                <th style="width: 10%;" class="text-end">Price</th>
                                                <th style="width: 10%;" class="text-end">Qty</th>
                                                <th style="width: 10%;" class="text-end">(Rs.) Amount</th>
                                            </tr>
                                        </thead>
                                        @if( count($DPR['attributes']['dpr_detail']) >= 1 )

                                            <tbody>
                                                @foreach ($DPR['attributes']['dpr_detail'] as $rowKey => $rowValue)
                                                    <tr>
                                                        <td style="width: 5%;">{{$rowValue->ono}}</td>
                                                        <td style="width: 45%;">{{$rowValue->item_name}}</td>
                                                        <td style="width: 10%;">{{$rowValue->unit_name}}</td>
                                                        <td style="width: 10%;" class="text-end">@money($rowValue->price)</td>
                                                        <td style="width: 10%;" class="text-end">{{$rowValue->quantity}}</td>
                                                        <td style="width: 10%;" class="text-end">@money($rowValue->amount)</td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <td colspan="5">Total</td>
                                                    <td class="text-end"><strong>@money($DPR['attributes']['dpr_total'])</strong></td>
                                                    <td></td>
                                                </tr>
                                            </tbody>

                                        @else
                                            <tbody>
                                                <tr>
                                                    <td style="width: 5%;">-</td>
                                                    <td style="width: 45%;">-</td>
                                                    <td style="width: 10%;">-</td>
                                                    <td style="width: 10%;" class="text-end">0.00</td>
                                                    <td style="width: 10%;" class="text-end">0</td>
                                                    <td style="width: 10%;" class="text-end">0.00</td>
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
