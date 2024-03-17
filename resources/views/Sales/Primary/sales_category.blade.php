@extends('layouts.sales')
@section('title')
    Sales Category
@endsection
@section('body')

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{route('sales_category_process')}}">
                @csrf

                <div class="card mt-3">

                    <div class="card-header">
                        Sales Category
                    </div>

                    <div class="card-body">

                    </div>

                </div>

            </form>
        </div>
    </div>

@endsection
