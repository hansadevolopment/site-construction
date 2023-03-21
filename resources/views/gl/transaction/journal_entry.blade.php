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
                            </div>              
                        </div>

                        <div class="mb-2 row">
                            <label for="tid" class="col-sm-1 col-form-label-sm">Remark</label>   
                            <div class="col-sm-8">
                                <input type="text" name="remark" id="remark" class="form-control form-control-sm" value="{{$JE['attributes']['remark']}}">
                                @if($JE['attributes']['validation_messages']->has('Remark'))
                                    <script>
                                            document.getElementById('remark').className = 'form-control form-control-sm is-invalid';
                                    </script>
                                    <div class="invalid-feedback">{{ $JE['attributes']['validation_messages']->first("Remark") }}</div>
                                @endif
                            </div> 
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

                        <hr>

                        <div class="mb-4 row">

                            <label for="tid" class="col-sm-1 col-form-label-sm">Account</label>   
                            <div class="col-sm-4">
                                <select name="sub_account" id="sub_account" class="form-select form-control-sm">
                                    @foreach($JE['sub_accounts'] as $row)
                                        <option value ="{{$row->sa_id}}">{{$row->sa_name}}</option>
                                    @endforeach
                                    <option value =0 selected>Select the Accounts</option>
                                </select>
                            </div>
                            
                            <label for="tid" class="col-sm-1 col-form-label-sm">Acc. Type</label>   
                            <div class="col-sm-2">
                                <select name="account_type" id="account_type" class="form-select form-control-sm">
                                    @foreach($JE['account_type'] as $row)
                                        <option value ="{{$row->acc_id}}">{{$row->name}}</option>
                                    @endforeach
                                    <option value =0 selected>Select the Type</option>
                                </select>
                            </div>

                            <label for="tid" class="col-sm-1 col-form-label-sm">Amount</label>   
                            <div class="col-sm-2">
                                <input type="text" name="amount" id="amount" class="form-control form-control-sm" value="">
                            </div>

                            <div class="col-sm-1">
                                <input type="submit" name="submit" id="submit" class="btn btn-primary btn-sm" style="width: 100%;" value="Add">
                            </div>

                        </div>

                        <div class="mb-4 row">

                            <table id="tblJournalEntry" class="table table-hover table-sm table-bordered">
                                <thead>
                                    <tr style="font-family: Consolas; font-size: 14px;">
                                        <th style="width: 15%;">Acc. No.</th>
                                        <th style="width: 60%;">Account Name</th>
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
                                    @if( count($JE['data_table']) >= 1)

                                        <script> tblJournalEntry.deleteRow(1);; </script>

                                        @foreach( $JE['data_table'] as $row )

                                            <tr style="font-family: Consolas; font-size: 14px;">
                                                <td style="width: 10%;">{{$row->sa_id}}</td>
                                                <td style="width: 60%;">{{$row->sa_name}}</td>
                                                <td style="width: 10%;">{{$row->short_name}}</td>
                                                <td style="width: 20%; text-align: right;"> @money($row->amount)</td>
                                                <td><input type="button" name="remove" id="remove" class="btn btn-danger btn-sm" style="width: 100%;" value="Remove"></td>
                                            </tr>

                                        @endforeach

                                    @endif


                                </tbody>

                            </table>

                        </div>

                        <hr>

                        <div class="mb-4 row">

                            <label for="tid" class="col-sm-8 col-form-label-sm"></label>  
                            <div class="col-sm-2"> 
                                <input type="submit" name="submit" id="glPost" class="btn btn-primary btn-sm" style="width: 100%;" value="GL Post">
                            </div>
                            <div class="col-sm-2"> 
                                <input type="submit" name="submit" id="print" class="btn btn-primary btn-sm" style="width: 100%;" value="Print">
                            </div>
                        </div>


                    </div>



                </div>

            </div>

        <div>


    </form>
    </div>

@endsection