@extends('layouts.purchasing')

@section('title')
    Good Receive Note
@endsection

@section('body')

    <div id="tbldiv" style="width: 98%;  margin-right: 1%; margin-left: 1%; margin-top: 1%;">
    <form method="POST" action="{{route('grn_process')}}">

		@CSRF

		<div class="col-sm-12">

			<div class="card">

				<div class="card-header">
					Good Receive Note
				</div>

				<div class="card-body">

					<div class="col-sm-12">
						<?php echo $GRN['attributes']['processMessage'] ?>
					</div>

					<div class="mb-2 row">

                        <div class="mb-2 row">
                            <label for="tid" class="col-sm-9 col-form-label-sm"></label>
                            <label for="tid" class="col-sm-1 col-form-label-sm">GRN ID</label>
                            <div class="col-sm-2">
                                <input type="text" name="grn_id" id="grn_id" class="form-control form-control-sm" value="{{$GRN['attributes']['grn_id']}}" readonly>
                                @if($GRN['attributes']['validationMessages']->has('grn_id'))
                                    <script>
                                            document.getElementById('grn_id').className = 'form-control form-control-sm is-invalid';
                                    </script>
                                    <div class="invalid-feedback">{{ $GRN['attributes']['validationMessages']->first("grn_id") }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="mb-2 row">
                            <label for="tid" class="col-sm-1 col-form-label-sm">Creditor</label>
                            <div class="col-sm-5">
                                <select name="creditor_id" id="creditor_id" class="form-select form-select-sm">
                                    @foreach($GRN['creditor'] as $row)
                                        @if( $GRN['attributes']['creditor_id'] == $row->creditor_id)
                                            <option value ="{{$row->creditor_id}}" selected>{{$row->creditor_name}}</option>
                                        @else
                                            <option value ="{{$row->creditor_id}}">{{$row->creditor_name}}</option>
                                        @endif
                                    @endforeach
                                    @if( $GRN['attributes']['creditor_id'] == 0)
                                        <option value =0 selected>Select the Creditor</option>
                                    @else
                                        <option value =0>Select the Creditor</option>
                                    @endif
                                </select>
                                @if($GRN['attributes']['validationMessages']->has('creditor'))
                                    <script>
                                            document.getElementById('creditor_id').className = 'form-select form-select-sm is-invalid';
                                    </script>
                                    <div class="invalid-feedback">{{ $GRN['attributes']['validationMessages']->first("creditor") }}</div>
                                @endif
                            </div>
                            <label for="tid" class="col-sm-3 col-form-label-sm"></label>
                            <label for="tid" class="col-sm-1 col-form-label-sm">GRN Date</label>
                            <div class="col-sm-2">
                                <input type="date" name="grn_date" id="grn_date" class="form-control form-control-sm" value="{{$GRN['attributes']['grn_date']}}">
                                @if($GRN['attributes']['validationMessages']->has('JE Date'))
                                    <script>
                                            document.getElementById('grn_date').className = 'form-control form-control-sm is-invalid';
                                    </script>
                                    <div class="invalid-feedback">{{ $GRN['attributes']['validationMessages']->first("JE Date") }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="mb-2 row">
                            <label for="tid" class="col-sm-1 col-form-label-sm">Pur. Category</label>
                            <div class="col-sm-5">
                                <select name="pc_id" id="pc_id" class="form-select form-select-sm">
                                    @foreach($GRN['purchasing_category'] as $row)
                                        @if($GRN['attributes']['pc_id'] == $row->pc_id)
                                            <option value ="{{$row->pc_id}}" selected>{{$row->pc_name}}</option>
                                        @else
                                            <option value ="{{$row->pc_id}}">{{$row->pc_name}}</option>
                                        @endif
                                    @endforeach
                                    @if($GRN['attributes']['pc_id'] == 0)
                                        <option value =0 selected>Select the Purchasing Category</option>
                                    @else
                                        <option value =0>Select the Purchasing Category</option>
                                    @endif
                                </select>
                                @if($GRN['attributes']['validationMessages']->has('purchase_category'))
                                    <script>
                                            document.getElementById('pc_id').className = 'form-select form-select-sm is-invalid';
                                    </script>
                                    <div class="invalid-feedback">{{ $GRN['attributes']['validationMessages']->first("purchase_category") }}</div>
                                @endif
                            </div>
                            <label for="tid" class="col-sm-3 col-form-label-sm"></label>
                            <label for="tid" class="col-sm-1 col-form-label-sm">GL Post No.</label>
                            <div class="col-sm-2">
                                <input type="text" name="gl_post_id" id="gl_post_id" class="form-control form-control-sm" value="{{$GRN['attributes']['gl_post_id']}}" readonly>
                                @if($GRN['attributes']['validationMessages']->has('gl_post_id'))
                                    <script>
                                            document.getElementById('gl_post_id').className = 'form-control form-control-sm is-invalid';
                                    </script>
                                    <div class="invalid-feedback">{{ $GRN['attributes']['validationMessages']->first("gl_post_id") }}</div>
                                @endif
                            </div>

                        </div>

                        <div class="mb-2 row">
                            <label for="tid" class="col-sm-1 col-form-label-sm">Pur. Location</label>
                            <div class="col-sm-5">
                                <select name="pl_id" id="pl_id" class="form-select form-select-sm">
                                    @foreach($GRN['purchasing_location'] as $row)
                                        @if( $GRN['attributes']['pl_id'] == 0  )
                                            <option value ="{{$row->pl_id}}" selected>{{$row->pl_name}}</option>
                                        @else
                                            <option value ="{{$row->pl_id}}">{{$row->pl_name}}</option>
                                        @endif
                                    @endforeach
                                    @if( $GRN['attributes']['pl_id'] == 0  )
                                        <option value =0 selected>Select the Purchasing Location</option>
                                    @else
                                        <option value =0>Select the Purchasing Location</option>
                                    @endif
                                </select>
                                @if($GRN['attributes']['validationMessages']->has('purchase_location'))
                                    <script>
                                            document.getElementById('pl_id').className = 'form-select form-select-sm is-invalid';
                                    </script>
                                    <div class="invalid-feedback">{{ $GRN['attributes']['validationMessages']->first("purchase_location") }}</div>
                                @endif
                            </div>
                            <label for="tid" class="col-sm-3 col-form-label-sm"></label>
                            <label for="tid" class="col-sm-1 col-form-label-sm">Type</label>
                            <div class="col-sm-2">
                                <select name="grn_type" id="grn_type" class="form-select form-select-sm" >
                                    @if( $GRN['attributes']['grn_type'] == 1 )
                                        <option value ="1" selected>Item</option>
                                        <option value ="2">Other</option>
                                    @else
                                        <option value ="1">Item</option>
                                        <option value ="2" selected>Other</option>
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="mb-4 row">
                            <label for="tid" class="col-sm-1 col-form-label-sm">Remark</label>
                            <div class="col-sm-11">
                                <input type="text" name="remark" id="remark" class="form-control form-control-sm" value="{{$GRN['attributes']['remark']}}">
                                @if($GRN['attributes']['validationMessages']->has('Remark'))
                                    <script>
                                            document.getElementById('remark').className = 'form-control form-control-sm is-invalid';
                                    </script>
                                    <div class="invalid-feedback">{{ $GRN['attributes']['validationMessages']->first("Remark") }}</div>
                                @endif
                            </div>
                        </div>

                        <hr>

                        <div id="item_div" class="mb-4">
                            <div class="mb-2 row">

                                <div class="col-sm-5">
                                    <select name="item_id" id="item_id" class="form-select form-select-sm">
                                        @foreach($GRN['item'] as $row)
                                            @if( $GRN['attributes']['item_id'] == $row->item_id  )
                                                <option value ="{{$row->item_id}}" selected>{{$row->item_name}}</option>
                                            @else
                                                <option value ="{{$row->item_id}}">{{$row->item_name}}</option>
                                            @endif
                                        @endforeach
                                        @if( $GRN['attributes']['item_id'] == 0  )
                                            <option value =0 selected>Select the Item</option>
                                        @else
                                            <option value =0>Select the Item</option>
                                        @endif
                                    </select>
                                    @if($GRN['attributes']['validationMessages']->has('item_id'))
                                        <script>
                                                document.getElementById('item_id').className = 'form-select form-select-sm is-invalid';
                                        </script>
                                        <div class="invalid-feedback">{{ $GRN['attributes']['validationMessages']->first("item_id") }}</div>
                                    @endif
                                </div>

                                <div class="col-sm-2">
                                    <input type="text" name="item_unit_price" id="item_unit_price" class="form-control form-control-sm text-end" value="{{$GRN['attributes']['unit_price']}}" placeholder="Unit Price">
                                    @if($GRN['attributes']['validationMessages']->has('item_unit_price'))
                                        <script>
                                                document.getElementById('item_unit_price').className = 'form-control form-control-sm is-invalid text-end';
                                        </script>
                                        <div class="invalid-feedback">{{ $GRN['attributes']['validationMessages']->first("item_unit_price") }}</div>
                                    @endif
                                </div>

                                <div class="col-sm-2">
                                    <input type="text" name="item_quantity" id="item_quantity" class="form-control form-control-sm text-end" value="{{$GRN['attributes']['quantity']}}" placeholder="Quantity">
                                    @if($GRN['attributes']['validationMessages']->has('item_quantity'))
                                        <script>
                                                document.getElementById('item_quantity').className = 'form-control form-control-sm is-invalid text-end';
                                        </script>
                                        <div class="invalid-feedback">{{ $GRN['attributes']['validationMessages']->first("item_quantity") }}</div>
                                    @endif
                                </div>

                                <div class="col-sm-2">
                                    <input type="text" name="item_discount_amount" id="item_discount_amount" class="form-control form-control-sm text-end" value="{{$GRN['attributes']['discount_amount']}}" placeholder="Discount Amount">
                                    @if($GRN['attributes']['validationMessages']->has('item_discount_amount'))
                                        <script>
                                                document.getElementById('item_discount_amount').className = 'form-control form-control-sm is-invalid text-end';
                                        </script>
                                        <div class="invalid-feedback">{{ $GRN['attributes']['validationMessages']->first("item_discount_amount") }}</div>
                                    @endif
                                </div>

                                <div class="col-sm-1">
                                    <input type="submit" name="submit" id="submit" class="btn btn-success btn-sm" style="width: 100%;" value="Add">
                                </div>

                            </div>
                        </div>

                        <div id="other_div" class="mb-4">
                            <div class="mb-2 row">
                                <div class="col-sm-7">
                                    <textarea  name="item_description" id="item_description" class="form-control" rows="3" style="resize:none">{{$GRN['attributes']['item_description']}}</textarea>
                                    @if($GRN['attributes']['validationMessages']->has('item_description'))
                                        <script>
                                                document.getElementById('item_description').className = 'form-control form-control-sm is-invalid';
                                        </script>
                                        <div class="invalid-feedback">{{ $GRN['attributes']['validationMessages']->first("item_description") }}</div>
                                    @endif
                                </div>

                                <div class="col-sm-2">
                                    <input type="text" name="desc_unit_price" id="desc_unit_price" class="form-control form-control-sm text-end" style="margin-bottom: 10px !important;" value="{{$GRN['attributes']['unit_price']}}" placeholder="Unit Price">
                                    @if($GRN['attributes']['validationMessages']->has('desc_unit_price'))
                                        <script>
                                                document.getElementById('desc_unit_price').className = 'form-control form-control-sm is-invalid text-end';
                                        </script>
                                        <div class="invalid-feedback">{{ $GRN['attributes']['validationMessages']->first("desc_unit_price") }}</div>
                                    @endif
                                    <input type="text" name="desc_discount_amount" id="desc_discount_amount" class="form-control form-control-sm text-end" value="{{$GRN['attributes']['discount_amount']}}" placeholder="Discount Amount">
                                    @if($GRN['attributes']['validationMessages']->has('desc_discount_amount'))
                                        <script>
                                                document.getElementById('desc_discount_amount').className = 'form-control form-control-sm is-invalid text-end';
                                        </script>
                                        <div class="invalid-feedback">{{ $GRN['attributes']['validationMessages']->first("desc_discount_amount") }}</div>
                                    @endif
                                </div>

                                <div class="col-sm-2">
                                    <input type="text" name="desc_quantity" id="desc_quantity" class="form-control form-control-sm text-end" style="margin-bottom: 10px !important;"  value="{{$GRN['attributes']['quantity']}}" placeholder="Quantity">
                                    @if($GRN['attributes']['validationMessages']->has('desc_quantity'))
                                        <script>
                                                document.getElementById('desc_quantity').className = 'form-control form-control-sm is-invalid text-end';
                                        </script>
                                        <div class="invalid-feedback">{{ $GRN['attributes']['validationMessages']->first("desc_quantity") }}</div>
                                    @endif
                                    <input type="button" name="btnTax" id="btnTax" class="btn btn-secondary btn-sm" style="width: 100%;" value="Tax" data-bs-toggle="modal" data-bs-target="#TaxModal">
                                </div>

                                <div class="col-sm-1">
                                    <input type="submit" name="submit" id="submit" class="btn btn-success btn-sm" style="width: 100%;" value="Add">
                                </div>

                            </div>

                        </div>

                        <div class="row mb-3">
                            <table id="tblItem" class="table table-hover table-sm table-bordered">
                                <thead>
                                    <tr style="font-family: Consolas; font-size: 14px;">
                                        <th style="width: 50%;">Item</th>
                                        <th style="width: 10%;">Unit Price</th>
                                        <th style="width: 10%;">Quantity</th>
                                        <th style="width: 10%;">Discount Amt</th>
                                        <th style="width: 10%;">Sub Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="font-family: Consolas; font-size: 14px;">
                                        <td style="width: 50%;">-</td>
                                        <td style="width: 10%;" class="text-end">0.00</td>
                                        <td style="width: 10%;" class="text-end">0.00</td>
                                        <td style="width: 10%;" class="text-end">0.00</td>
                                        <td style="width: 10%;" class="text-end">0.00</td>
                                        <td><input type="button" name="remove" id="remove" class="btn btn-danger btn-sm" style="width: 100%;" value="Remove"></td>
                                    </tr>
                                    @if( count($GRN['attributes']['grn_detail']) >= 1)

                                        <script> tblItem.deleteRow(1);; </script>

                                        @foreach( $GRN['attributes']['grn_detail'] as $rowKey => $rowValue )

                                            <tr style="font-family: Consolas; font-size: 14px;">
                                                @if($GRN['attributes']['grn_type'] == 1)
                                                    <td rowspan="2" style="width: 50%;">{{$rowValue->item_name}}</td>
                                                @else
                                                    <td rowspan="2" style="width: 50%;">{{$rowValue->item_description}}</td>
                                                @endif
                                                <td style="width: 10%; text-align: right;"> @money($rowValue->unit_price) </td>
                                                <td style="width: 10%; text-align: right;"> @quantity($rowValue->quantity) </td>
                                                <td style="width: 10%; text-align: right;"> @money($rowValue->discount_amount) </td>
                                                <td style="width: 10%; text-align: right;"> @money($rowValue->gross_amount) </td>
                                                <td style="width: 10%; text-align: right;"> </td>
                                            </tr>

                                            <tr style="font-family: Consolas; font-size: 14px;">
                                                <td style="width: 10%; text-align: right;">Tax </td>
                                                <td style="width: 10%; text-align: right;">@money($rowValue->tax_amount) </td>
                                                <td style="width: 10%; text-align: right;"><strong> Net Amount </strong></td>
                                                <td style="width: 10%; text-align: right;"><strong> @money($rowValue->net_amount) </strong></td>
                                                <td style="width: 10%;">
                                                    <input type="button" name="remove" id="remove" data-id="{{$rowValue->grn_dtl_id}}" class="btn btn-danger btn-sm remove-grn-dtl-id" style="width: 100%;" value="Remove">
                                                </td>
                                            </tr>

                                        @endforeach

                                    @endif

                                </tbody>

                            </table>
                        </div>

                        <hr>

                        <div class="row mb-2">
                            <label for="tid" class="col-sm-8 col-form-label-sm"></label>
                            <label for="tid" class="col-sm-2 col-form-label-sm">Total Gross Amount</label>
                            <div class="col-sm-2">
                                <input type="text" name="gross_amount" id="gross_amount" class="form-control form-control-sm text-end" style="width: 100%;" value="{{$GRN['attributes']['total_gross_amount']}}" readonly>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <label for="tid" class="col-sm-8 col-form-label-sm"></label>
                            <label for="tid" class="col-sm-2 col-form-label-sm">Total Discount Amount</label>
                            <div class="col-sm-2">
                                <input type="text" name="discount_amount" id="discount_amount" class="form-control form-control-sm text-end" style="width: 100%;" value="{{$GRN['attributes']['total_discount_amount']}}" readonly>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <label for="tid" class="col-sm-8 col-form-label-sm"></label>
                            <label for="tid" class="col-sm-2 col-form-label-sm">Total Tax Amount</label>
                            <div class="col-sm-2">
                                <input type="text" name="tax_amount" id="tax_amount" class="form-control form-control-sm text-end" style="width: 100%;" value="{{$GRN['attributes']['total_tax_amount']}}" readonly>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <label for="tid" class="col-sm-8 col-form-label-sm"></label>
                            <label for="tid" class="col-sm-2 col-form-label-sm">Total Net Amount</label>
                            <div class="col-sm-2">
                                <input type="text" name="net_amount" id="net_amount" class="form-control form-control-sm text-end" style="width: 100%;" value="{{$GRN['attributes']['total_net_amount']}}" readonly>
                            </div>
                        </div>

                        <div class="row mb-4">

                            <label for="tid" class="col-sm-4 col-form-label-sm"></label>
                            <div class="col-sm-2">
                                <input type="submit" name="submit" id="save" class="btn btn-primary btn-sm" style="width: 100%;" value="Save">
                            </div>
                            <div class="col-sm-2">
                                <input type="submit" name="submit" id="reset" class="btn btn-primary btn-sm" style="width: 100%;" value="Reset">
                            </div>
                            <div class="col-sm-2">
                                <input type="submit" name="submit" id="cancel" class="btn btn-danger btn-sm cancel-process" style="width: 100%;" value="Cancel" {{$GRN['attributes']['btn_disable']}}>
                            </div>
                            <div class="col-sm-2">
                                <input type="submit" name="submit" id="glPost" class="btn btn-primary btn-sm" style="width: 100%;" value="GL Post" {{$GRN['attributes']['btn_disable']}}>
                                {{-- @if($GRN['attributes']['grn_id'] == '#Auto#')
                                    <input type="submit" name="submit" id="glPost" class="btn btn-primary btn-sm" style="width: 100%;" value="GL Post" disabled>
                                @else
                                    <input type="submit" name="submit" id="glPost" class="btn btn-primary btn-sm" style="width: 100%;" value="GL Post">
                                @endif --}}
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        <div>

    </form>

    <!-- Modal -->
    <div class="modal fade" id="TaxModal" tabindex="-1" aria-labelledby="TaxModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="TaxModalLabel">Tax Modal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @foreach ($GRN['tax'] as $row)
                        <div class="form-check">
                            <input type="checkbox" name="tax[]" id="tax_{{$row->tax_id}}" class="form-check-input"  value="{{$row->tax_id}}" >
                            <label class="form-check-label" for="flexCheckDefault">
                                {{$row->tax_name}} {{' --> '}} <strong>{{$row->tax_rate}}</strong>
                            </label>
                        </div>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div style="display: none;">
        <form id="removeGrnDtlId" style="display: none;" method="post" action="{{route('remove_grn_item')}}">
            @csrf
            <input type="text" name="grnId" id="grnId" values="">
            <input type="text" name="grnDtlId" id="grnDtlId" values="">
        </form>
    </div>


    </div>

@endsection
