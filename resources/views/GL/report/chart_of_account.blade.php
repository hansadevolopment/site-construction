@extends('layouts.gl')

@section('title')
    Chart of Account
@endsection

@section('body')

    <div id="tbldiv" style="width: 98%;  margin-right: 1%; margin-left: 1%; margin-top: 1%;">
    <form method="POST" action="{{route('generate_chart_of_account')}}">

		@CSRF

		<div class="col-sm-12">

			<div class="card">

				<div class="card-header">
					Chart of Account
				</div>

				<div class="card-body">

                    <div class="pl-3">
                        <ol>
                            @foreach($CA['main_account'] as $mainAccountKey => $mainAccountValue)
                                <li>
                                    <b>{{$mainAccountValue->ma_id}} - {{$mainAccountValue->ma_name}} </b>
                                </li>
                                <ol>
                                    @foreach($CA['controll_account']->where('ma_id', $mainAccountValue->ma_id ) as $controllAccount)
                                        <li>
                                            <i>{{$controllAccount->ca_id}} - {{$controllAccount->ca_name}} </i>
                                        </li>
                                        <ol>
                                            @foreach($CA['sub_account']->where('ca_id', $controllAccount->ca_id ) as $subAccount)
                                                <li>
                                                    {{$subAccount->sa_id}} - {{$subAccount->sa_name}}
                                                </li>
                                            @endforeach
                                        </ol>
                                    @endforeach
                                </ol>
                            @endforeach
                        </ol>
                    </div>

                </div>
            </div>
        <div>

    </form>
    </div>

@endsection
