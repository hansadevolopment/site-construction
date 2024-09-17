@extends('layouts.gl')

@section('title')
    Journal Entry
@endsection

@section('body')

    <div id="tbldiv" style="width: 98%;  margin-right: 1%; margin-left: 1%; margin-top: 1%;">
    <form method="POST" action="{{route('journal_entry_process')}}">

		@CSRF

		<div class="col-sm-12">

			<div class="card">

				<div class="card-header">
					Journal Entry
				</div>

				<div class="card-body">

					<div class="col-sm-12">
						<?php echo $JE['attributes']['process_message'] ?>
					</div>

					<div class="mb-2 row">

                        <div class="mb-2 row">
                            <label for="tid" class="col-sm-9 col-form-label-sm"></label>
                            <label for="tid" class="col-sm-1 col-form-label-sm">JE ID</label>
                            <div class="col-sm-2">
                                <input type="text" name="je_id" id="je_id" class="form-control form-control-sm" value="{{$JE['attributes']['je_id']}}" readonly>
                                @if($JE['attributes']['validation_messages']->has('JE Id'))
                                    <script>
                                            document.getElementById('je_id').className = 'form-control form-control-sm is-invalid';
                                    </script>
                                    <div class="invalid-feedback">{{ $JE['attributes']['validation_messages']->first("JE Id") }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="mb-2 row">
                            <label for="tid" class="col-sm-1 col-form-label-sm">GL Post No.</label>
                            <div class="col-sm-2">
                                <input type="text" name="gl_post_id" id="gl_post_id" class="form-control form-control-sm" value="{{$JE['attributes']['gl_post_id']}}" readonly>
                                @if($JE['attributes']['validation_messages']->has('gl_post_id'))
                                    <script>
                                            document.getElementById('gl_post_id').className = 'form-control form-control-sm is-invalid';
                                    </script>
                                    <div class="invalid-feedback">{{ $JE['attributes']['validation_messages']->first("gl_post_id") }}</div>
                                @endif
                            </div>
                            <label for="tid" class="col-sm-6 col-form-label-sm"></label>
                            <label for="tid" class="col-sm-1 col-form-label-sm">JE Date</label>
                            <div class="col-sm-2">
                                <input type="date" name="je_date" id="je_date" class="form-control form-control-sm" value="{{$JE['attributes']['je_date']}}">
                                @if($JE['attributes']['validation_messages']->has('JE Date'))
                                    <script>
                                            document.getElementById('je_date').className = 'form-control form-control-sm is-invalid';
                                    </script>
                                    <div class="invalid-feedback">{{ $JE['attributes']['validation_messages']->first("JE Date") }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="mb-2 row">
                            <label for="tid" class="col-sm-1 col-form-label-sm">Remark</label>
                            <div class="col-sm-11">
                                <input type="text" name="remark" id="remark" class="form-control form-control-sm" value="{{$JE['attributes']['remark']}}">
                                @if($JE['attributes']['validation_messages']->has('Remark'))
                                    <script>
                                            document.getElementById('remark').className = 'form-control form-control-sm is-invalid';
                                    </script>
                                    <div class="invalid-feedback">{{ $JE['attributes']['validation_messages']->first("Remark") }}</div>
                                @endif
                            </div>
                        </div>

                        <hr>

                        <div class="mb-2 row">

                            <label for="tid" class="col-sm-1 col-form-label-sm">Account</label>
                            <div class="col-sm-7">
                                <select name="account" id="account" class="form-select form-select-sm">
                                    @foreach($JE['sub_account'] as $row)
                                        <option value ="{{$row->sa_id}}">{{$row->sa_name}}</option>
                                    @endforeach
                                    <option value =0 selected>Select the Accounts</option>
                                </select>
                                @if($JE['attributes']['validation_messages']->has('Account'))
                                    <script>
                                            document.getElementById('account').className = 'form-select form-select-sm is-invalid';
                                    </script>
                                    <div class="invalid-feedback">{{ $JE['attributes']['validation_messages']->first("Account") }}</div>
                                @endif
                            </div>

                            <label for="tid" class="col-sm-1 col-form-label-sm">Accunt Type</label>
                            <div class="col-sm-2">
                                <select name="acc_type" id="acc_type" class="form-select form-select-sm">
                                    @foreach($JE['account_type'] as $row)
                                        <option value ="{{$row->acc_id}}">{{$row->name}}</option>
                                    @endforeach
                                    <option value =0 selected>Select the Type</option>
                                </select>
                                @if($JE['attributes']['validation_messages']->has('Account Type'))
                                    <script>
                                            document.getElementById('acc_type').className = 'form-select form-select-sm is-invalid';
                                    </script>
                                    <div class="invalid-feedback">{{ $JE['attributes']['validation_messages']->first("Account Type") }}</div>
                                @endif
                            </div>



                        </div>

                        <div class="mb-4 row">

                            <label for="tid" class="col-sm-1 col-form-label-sm">Description</label>
                            <div class="col-sm-7">
                                <input type="text" name="description" id="description" class="form-control form-control-sm" value="">
                                @if($JE['attributes']['validation_messages']->has('description'))
                                    <script>
                                            document.getElementById('description').className = 'form-control form-control-sm is-invalid';
                                    </script>
                                    <div class="invalid-feedback">{{ $JE['attributes']['validation_messages']->first("description") }}</div>
                                @endif
                            </div>

                            <label for="tid" class="col-sm-1 col-form-label-sm">Amount</label>
                            <div class="col-sm-2">
                                <input type="text" name="amount" id="amount" class="form-control form-control-sm" value="">
                                @if($JE['attributes']['validation_messages']->has('Amount'))
                                    <script>
                                            document.getElementById('amount').className = 'form-control form-control-sm is-invalid';
                                    </script>
                                    <div class="invalid-feedback">{{ $JE['attributes']['validation_messages']->first("Amount") }}</div>
                                @endif
                            </div>

                            <div class="col-sm-1">
                                <input type="submit" name="submit" id="submit" class="btn btn-primary btn-sm" style="width: 100%;" value="Add">
                            </div>

                        </div>

                        <div class="row mb-4">
                            <table id="tblJournalEntry" class="table table-hover table-sm table-bordered">
                                <thead>
                                    <tr style="font-family: Consolas; font-size: 14px;">
                                        <th style="width: 15%;">Account No.</th>
                                        <th style="width: 60%;">Account Name \ Description</th>
                                        <th style="width: 10%;">Type</th>
                                        <th style="width: 15%;">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="font-family: Consolas; font-size: 14px;">
                                        <td style="width: 10%;">-</td>
                                        <td style="width: 60%;">-</td>
                                        <td style="width: 10%;">-</td>
                                        <td style="width: 20%;">0.00</td>
                                        <td><input type="button" name="remove" id="remove" class="btn btn-danger btn-sm" style="width: 100%;" value="Remove"></td>
                                    </tr>
                                    @if( count($JE['attributes']['je_detail']) >= 1)

                                        <script> tblJournalEntry.deleteRow(1);; </script>

                                        @foreach( $JE['attributes']['je_detail'] as $row )

                                            <tr style="font-family: Consolas; font-size: 14px;">
                                                <td style="width: 10%;">{{$row->sa_id}}</td>
                                                <td style="width: 60%;">{{$row->sa_name}} <br> {{$row->description}}</td>
                                                @if($row->acc_type_id == 1)
                                                    <td style="width: 10%;">Debit</td>
                                                @else
                                                    <td style="width: 10%;">Credit</td>
                                                @endif
                                                <td style="width: 20%; text-align: right;"> @money($row->amount)</td>
                                                @if($JE['attributes']['je_id'] == '#Auto#')
                                                    <td><input type="button" name="remove" id="remove" data-id="{{$row->tmp_je_id}}" class="btn btn-danger btn-sm remove-tmp-je" style="width: 100%;" value="Remove"></td>
                                                @endif
                                            </tr>

                                        @endforeach

                                    @endif


                                </tbody>

                            </table>
                        </div>

                        <div class="row mb-2">
                            <label for="tid" class="col-sm-8 col-form-label-sm"></label>
                            <label for="tid" class="col-sm-2 col-form-label-sm">Total Debit Amount</label>
                            <div class="col-sm-2">
                                <input type="text" name="total_debit_amount" id="total_debit_amount" class="form-control form-control-sm text-end" value="@money($JE['attributes']['total_debit_amount'])" readonly>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="tid" class="col-sm-8 col-form-label-sm"></label>
                            <label for="tid" class="col-sm-2 col-form-label-sm">Total Credit Amount</label>
                            <div class="col-sm-2">
                                <input type="text" name="total_credit_amount" id="total_credit_amount" class="form-control form-control-sm text-end" value="@money($JE['attributes']['total_credit_amount'])" readonly>
                            </div>
                        </div>

                        <hr>

                        <div class="row mb-4">

                            <label for="tid" class="col-sm-8 col-form-label-sm"></label>
                            @if($JE['attributes']['je_id'] == '#Auto#')
                                <div class="col-sm-2">
                                    <input type="submit" name="submit" id="glPost" class="btn btn-primary btn-sm" style="width: 100%;" value="GL Post">
                                </div>
                            @else
                                <label for="tid" class="col-sm-2 col-form-label-sm"></label>
                            @endif
                            <div class="col-sm-2">
                                <input type="submit" name="submit" id="reset" class="btn btn-primary btn-sm" style="width: 100%;" value="Reset">
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        <div>

    </form>

    <div style="display: none;">
        <form id="remove_tmp_je" style="display: none;" method="post" action="{{route('remove_journal_entry')}}">
            @csrf
            <input type="text" name="tmp_je_id" id="tmp_je_id" values="">
        </form>
    </div>


    </div>

@endsection
