@extends('layouts.sales')
@section('title')
    Custom Order
@endsection
@section('body')

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{route('main_account_process')}}">
                @csrf

                <div class="card mt-3">

                    <div class="card-header">
                        Custom Order
                    </div>

                    <div class="card-body">

                    </div>

                </div>

            </form>
        </div>
    </div>

@endsection
