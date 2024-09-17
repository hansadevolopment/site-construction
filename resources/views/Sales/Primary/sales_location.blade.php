@extends('layouts.sales')
@section('title')
    Sales Location
@endsection
@section('body')

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{route('sales_location_process')}}">
                @csrf

                <div class="card mt-3">

                    <div class="card-header">
                        Sales Location
                    </div>

                    <div class="card-body">

                    </div>

                </div>

            </form>
        </div>
    </div>

@endsection
