@extends('layouts.sales')
@section('title')
    Debtor
@endsection
@section('body')

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{route('debtor_process')}}">
                @csrf

                <div class="card mt-3">

                    <div class="card-header">
                        Debtor
                    </div>

                    <div class="card-body">

                    </div>

                </div>

            </form>
        </div>
    </div>

@endsection
