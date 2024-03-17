@extends('layouts.gl')

@section('title')
    General Ledger
@endsection

@section('body')

    <div id="tbldiv" style="width: 98%;  margin-right: 1%; margin-left: 1%; margin-top: 1%;">
    <form method="POST" action="{{route('generate_ledger')}}">

		@CSRF

		<div class="col-sm-12">

			<div class="card">

				<div class="card-header">
					General Ledger
				</div>

				<div class="card-body">

					<div class="col-sm-12">
						<?php echo $LR['attributes']['process_message'] ?>
					</div>

					<div class="mb-2 row">

                        <div class="mb-2 row">
                            <label for="tid" class="col-sm-2 col-form-label-sm">Controll Account</label>
                            <div class="col-sm-4">
                                <select name="ca_id" id="ca_id" class="form-select form-select-sm">
                                    @foreach($LR['main_account'] as $row)
                                        <option value ="{{$row->ca_id}}">{{$row->ma_name}}</option>
                                    @endforeach
                                    <option value =0 selected>Select the Controll Accounts</option>
                                </select>
                                @if($LR['attributes']['validation_messages']->has('ca_id'))
                                    <script>
                                            document.getElementById('ca_id').className = 'form-select form-select-sm is-invalid';
                                    </script>
                                    <div class="invalid-feedback">{{ $LR['attributes']['validation_messages']->first("ca_id") }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-4">
                            <label for="tid" class="col-sm-2 col-form-label-sm">Sub Account</label>
                            <div class="col-sm-7">
                                <select name="sa_id" id="sa_id" class="form-select form-select-sm">
                                    @foreach($LR['sub_account'] as $row)
                                        <option value ="{{$row->sa_id}}">{{$row->sa_name}}</option>
                                    @endforeach
                                    <option value =0 selected>Select the Sub Accounts</option>
                                </select>
                                @if($LR['attributes']['validation_messages']->has('sa_id'))
                                    <script>
                                            document.getElementById('sa_id').className = 'form-select form-select-sm is-invalid';
                                    </script>
                                    <div class="invalid-feedback">{{ $LR['attributes']['validation_messages']->first("sa_id") }}</div>
                                @endif
                            </div>

                            <div class="col-sm-1">
                                <input type="submit" name="submit" id="glPost" class="btn btn-primary btn-sm" style="width: 100%;" value="Generate">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <table id="tblJournalEntry" class="table table-hover table-sm table-bordered">
                                <thead>
                                    <tr style="font-family: Consolas; font-size: 14px;">
                                        <th style="width: 5%;">GL No.</th>
                                        <th style="width: 8%;">Source No.</th>
                                        <th style="width: 8%;">Date</th>
                                        <th style="width: 49%;">Description</th>
                                        <th style="width: 15%;">(Dr) Amount</th>
                                        <th style="width: 15%;">(Cr) Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="font-family: Consolas; font-size: 14px;">
                                        <th style="width: 5%;">-</th>
                                        <th style="width: 8%;">-</th>
                                        <th style="width: 8%;">-</th>
                                        <th style="width: 49%;">-</th>
                                        <th style="width: 15%;">-</th>
                                        <th style="width: 15%;">-</th>
                                    </tr>
                                    @if( count($LR['attributes']['ledger_report']) >= 1)

                                        <script> tblJournalEntry.deleteRow(1);; </script>

                                        @foreach( $LR['attributes']['ledger_report'] as $row )

                                            <tr style="font-family: Consolas; font-size: 14px;">
                                                <td style="width: 5%;">{{$row->gl_entry_id}}</td>
                                                <td style="width: 8%;">{{$row->source_id}}</td>
                                                <td style="width: 8%;">{{$row->gle_date}}</td>
                                                <td style="width: 49%;">{{$row->description}}</td>
                                                @if( $row->acc_type == 1 )
                                                    <td class="text-end" style="width: 15%;">@money($row->amount)</td>
                                                    <td class="text-end" style="width: 15%;"></td>
                                                @else
                                                    <td class="text-end" style="width: 15%;"></td>
                                                    <td class="text-end" style="width: 15%;">@money($row->amount)</td>
                                                @endif
                                            </tr>

                                        @endforeach

                                    @endif


                                </tbody>

                            </table>
                        </div>

                    </div>

                </div>
            </div>
        <div>

    </form>
    </div>

@endsection
