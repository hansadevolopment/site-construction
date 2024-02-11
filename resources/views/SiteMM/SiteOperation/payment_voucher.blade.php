@extends('layouts.site_monitoring')
@section('title')
    Payment Voucher
@endsection
@section('body')

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{route('payment_voucher_process')}}">
                @csrf

                <div class="card mt-3">

                    <div class="card-header">
                        Payment Voucher
                    </div>

                    <div class="card-body">

                        <div class="row">
                            <div class="col-sm-12">
                                <?php echo $PV['attributes']['process_message'];  ?>
                            </div>
                        </div>

                        <div class="row no-gutters">
                            <div class="col-12 col-sm-12 col-md-12">

                                <div class="row mb-2">

                                    <div class="col-sm-9">
                                    </div>

                                    <label for="tid" class="col-sm-1 col-form-label-sm">PV No</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="pv_id" id="pv_id" class="form-control form-control-sm" value="{{$PV['attributes']['pv_id']}}" readonly>
                                        @if($PV['attributes']['validation_messages']->has('pv_id'))
                                            <script>
                                                    document.getElementById('pv_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $PV['attributes']['validation_messages']->first("pv_id") }}</div>
                                        @endif
                                    </div>

                                </div>

                                <div class="row mb-2">

                                    <label for="tid" class="col-sm-1 col-form-label-sm">Site</label>
                                    <div class="col-sm-5">
                                        <select name="site_id" id="site_id" class="form-select form-select-sm" >
                                            @foreach($PV['site'] as $row)
                                                @if($PV['attributes']['site_id'] == $row->site_id)
                                                    <option value ="{{$row->site_id}}" selected>{{$row->site_name}}</option>
                                                @else
                                                    <option value ="{{$row->site_id}}">{{$row->site_name}}</option>
                                                @endif
                                            @endforeach
                                            @if($PV['attributes']['site_id'] == "0")
                                                <option value ="0" selected>Select the Site </option>
                                            @endif
                                        </select>
                                        @if($PV['attributes']['validation_messages']->has('site_id'))
                                            <script>
                                                    document.getElementById('site_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $PV['attributes']['validation_messages']->first("site_id") }}</div>
                                        @endif
                                    </div>

                                    <label for="tid" class="col-sm-3 col-form-label-sm"></label>

                                    <label for="tid" class="col-sm-1 col-form-label-sm">PV Date</label>
                                    <div class="col-sm-2">
                                        <input type="date" name="pv_date" id="pv_date" class="form-control form-control-sm" value="{{$PV['attributes']['pv_date']}}">
                                        @if($PV['attributes']['validation_messages']->has('pv_date'))
                                            <script>
                                                    document.getElementById('pv_date').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $PV['attributes']['validation_messages']->first("pv_date") }}</div>
                                        @endif
                                    </div>

                                </div>

                                <div class="row mb-2">

                                    <label for="tid" class="col-sm-1 col-form-label-sm">Task</label>
                                    <div class="col-sm-5">
                                        <select name="task_id" id="task_id" class="form-select form-select-sm" >
                                            @if( ($PV['attributes']['task_id'] != "0") || ($PV['attributes']['site_id'] != "0") )
                                                @foreach($PV['site_task'] as $row)
                                                    @if($PV['attributes']['task_id'] == $row->task_id)
                                                        <option value ="{{$row->task_id}}" selected>{{$row->task_name}}</option>
                                                    @else
                                                        <option value ="{{$row->task_id}}">{{$row->task_name}}</option>
                                                    @endif
                                                @endforeach
                                            @endif
                                            @if( $PV['attributes']['task_id'] != "0" )
                                                <option value ="0">Select the Task </option>
                                            @else
                                                <option value ="0" selected>Select the Task </option>
                                            @endif
                                        </select>
                                        @if($PV['attributes']['validation_messages']->has('task_id'))
                                            <script>
                                                    document.getElementById('task_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $PV['attributes']['validation_messages']->first("task_id") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Sub Task</label>
                                    <div class="col-sm-5">
                                        <select name="sub_task_id" id="sub_task_id" class="form-select form-select-sm" >
                                            @if( ($PV['attributes']['sub_task_id'] != "0") || ($PV['attributes']['task_id'] != "0") )
                                                @foreach($PV['site_sub_task'] as $row)
                                                    @if($PV['attributes']['sub_task_id'] == $row->sub_task_id)
                                                        <option value ="{{$row->sub_task_id}}" selected>{{$row->sub_task_name}}</option>
                                                    @else
                                                        <option value ="{{$row->sub_task_id}}">{{$row->sub_task_name}}</option>
                                                    @endif
                                                @endforeach
                                            @endif
                                            @if( $PV['attributes']['sub_task_id'] != "0" )
                                                <option value ="0">Select the Sub Task </option>
                                            @else
                                                <option value ="0" selected>Select the Sub Task </option>
                                            @endif
                                        </select>
                                        @if($PV['attributes']['validation_messages']->has('sub_task_id'))
                                            <script>
                                                    document.getElementById('sub_task_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $PV['attributes']['validation_messages']->first("sub_task_id") }}</div>
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
                                        <textarea  name="remark" id="remark" class="form-control" rows="2" style="resize:none">{{$PV['attributes']['remark']}}</textarea>
                                        @if($PV['attributes']['validation_messages']->has('remark'))
                                            <script>
                                                    document.getElementById('remark').className = 'form-control is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $PV['attributes']['validation_messages']->first("remark") }}</div>
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

                                    <label for="tid" class="col-sm-1 col-form-label-sm">Overhead Cost Item</label>
                                    <div class="col-sm-5">
                                        <select name="oci_id" id="oci_id" class="form-select form-select-sm">
                                            @foreach($PV['overhead_cost_item'] as $row)
                                                <option value ="{{$row->oci_id}}">{{$row->oci_name}}</option>
                                            @endforeach
                                            <option value ="0" selected>Select the Item</option>
                                        </select>
                                        @if($PV['attributes']['validation_messages']->has('oci_id'))
                                            <script>
                                                    document.getElementById('oci_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $PV['attributes']['validation_messages']->first("oci_id") }}</div>
                                        @endif
                                    </div>

                                    <div class="col-sm-1">
                                        <input type="text" name="unit" id="unit" class="form-control form-control-sm" value="" placeholder="Unit" readonly>
                                    </div>

                                    <div class="col-sm-2">
                                        <input type="text" name="quantity" id="quantity" class="form-control form-control-sm text-end" value="" placeholder="Quantity">
                                        @if($PV['attributes']['validation_messages']->has('quantity'))
                                            <script>
                                                    document.getElementById('quantity').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $PV['attributes']['validation_messages']->first("quantity") }}</div>
                                        @endif
                                    </div>

                                    <div class="col-sm-2">
                                        <input type="text" name="price" id="price" class="form-control form-control-sm text-end" value="" placeholder="Price">
                                        @if($PV['attributes']['validation_messages']->has('price'))
                                            <script>
                                                    document.getElementById('price').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $PV['attributes']['validation_messages']->first("price") }}</div>
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
                                                <th style="width: 55%;">Overhead Cost</th>
                                                <th style="width: 10%;">Unit</th>
                                                <th style="width: 10%;" class="text-end">Quantity</th>
                                                <th style="width: 10%;" class="text-end">Price</th>
                                                <th style="width: 10%;" class="text-end">(Rs.) Amount</th>
                                            </tr>
                                        </thead>
                                        @if( count($PV['attributes']['pv_detail']) >= 1 )

                                            <tbody>
                                                @foreach ($PV['attributes']['pv_detail'] as $rowKey => $rowValue)
                                                    <tr>
                                                        <td style="width: 5%;">{{$rowValue->ono}}</td>
                                                        <td style="width: 55%;">{{$rowValue->item_name}}</td>
                                                        <td style="width: 10%;">{{$rowValue->unit_name}}</td>
                                                        <td style="width: 10%;" class="text-end">@money($rowValue->quantity)</td>
                                                        <td style="width: 10%;" class="text-end">@money($rowValue->price)</td>
                                                        <td style="width: 10%;" class="text-end">@money($rowValue->amount)</td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <td colspan="5">Total</td>
                                                    <td class="text-end"><strong>@money($PV['attributes']['pv_total'])</strong></td>
                                                    <td></td>
                                                </tr>
                                            </tbody>

                                        @else
                                            <tbody>
                                                <tr>
                                                    <td style="width: 5%;">-</td>
                                                    <td style="width: 55%;">-</td>
                                                    <td style="width: 10%;">-</td>
                                                    <td style="width: 10%;" class="text-end">0.00</td>
                                                    <td style="width: 10%;" class="text-end">0.00</td>
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
