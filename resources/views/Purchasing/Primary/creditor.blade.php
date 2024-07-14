@extends('layouts.purchasing')

@section('title')
    Creditor
@endsection

@section('body')

    <div id="tbldiv" style="width: 98%;  margin-right: 1%; margin-left: 1%; margin-top: 1%;">
    <form method="POST" action="{{route('creditor_process')}}">

        @csrf

        <div class="col-sm-12">

            <div class="card">

                <div class="card-header">
                    Creditor
                </div>

                <div class="card-body">

                    <div class="col-sm-11">
                        <?php echo $CR['attributes']['process_message'];  ?>
                    </div>

                    <div class="mb-2 row">
                        <label for="tid" class="col-sm-2 col-form-label-sm">Creditor ID</label>
                        <div class="col-sm-2">
                            <input type="text" name="creditor_id" id="creditor_id" class="form-control form-control-sm"  value="{{$CR['attributes']['creditor_id']}}" readonly>
                            @if($CR['attributes']['validation_messages']->has('creditor_id'))
                            <script>
                                    document.getElementById('creditor_id').className = 'form-control form-control-sm is-invalid';
                            </script>
                            <div class="invalid-feedback">{{ $CR['attributes']['validation_messages']->first("creditor_id") }}</div>
                        @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <label for="tid" class="col-sm-2 col-form-label-sm">Creditor Name</label>
                        <div class="col-sm-6">
                            <input type="text" name="creditor_name" id="creditor_name" class="form-control form-control-sm"  value="{{$CR['attributes']['creditor_name']}}">
                            @if($CR['attributes']['validation_messages']->has('creditor_name'))
                                <script>
                                        document.getElementById('creditor_name').className = 'form-control form-control-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $CR['attributes']['validation_messages']->first("creditor_name") }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <label for="tid" class="col-sm-2 col-form-label-sm">Address</label>
                        <div class="col-sm-6">
                            <input type="text" name="address" id="address" class="form-control form-control-sm"  value="{{$CR['attributes']['address']}}">
                            @if($CR['attributes']['validation_messages']->has('address'))
                                <script>
                                        document.getElementById('address').className = 'form-control form-control-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $CR['attributes']['validation_messages']->first("address") }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <label for="tid" class="col-sm-2 col-form-label-sm">Contact Persons</label>
                        <div class="col-sm-6">
                            <input type="text" name="contact_persons" id="contact_persons" class="form-control form-control-sm"  value="{{$CR['attributes']['contact_persons']}}">
                            @if($CR['attributes']['validation_messages']->has('contact_persons'))
                                <script>
                                        document.getElementById('contact_persons').className = 'form-control form-control-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $CR['attributes']['validation_messages']->first("contact_persons") }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <label for="tid" class="col-sm-2 col-form-label-sm">Contact Numbers</label>
                        <div class="col-sm-6">
                            <input type="text" name="contact_numbers" id="contact_numbers" class="form-control form-control-sm"  value="{{$CR['attributes']['contact_numbers']}}">
                            @if($CR['attributes']['validation_messages']->has('contact_numbers'))
                                <script>
                                        document.getElementById('contact_numbers').className = 'form-control form-control-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $CR['attributes']['validation_messages']->first("contact_numbers") }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <label for="tid" class="col-sm-2 col-form-label-sm">Fax Numbers</label>
                        <div class="col-sm-6">
                            <input type="text" name="fax_numbers" id="fax_numbers" class="form-control form-control-sm"  value="{{$CR['attributes']['fax_numbers']}}">
                            @if($CR['attributes']['validation_messages']->has('fax_numbers'))
                                <script>
                                        document.getElementById('fax_numbers').className = 'form-control form-control-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $CR['attributes']['validation_messages']->first("fax_numbers") }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <label for="tid" class="col-sm-2 col-form-label-sm">Email</label>
                        <div class="col-sm-6">
                            <input type="text" name="email" id="email" class="form-control form-control-sm"  value="{{$CR['attributes']['email']}}">
                            @if($CR['attributes']['validation_messages']->has('email'))
                                <script>
                                        document.getElementById('email').className = 'form-control form-control-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $CR['attributes']['validation_messages']->first("email") }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <label for="tid" class="col-sm-2 col-form-label-sm">Credit Limit</label>
                        <div class="col-sm-6">
                            <input type="text" name="credit_limit" id="credit_limit" class="form-control form-control-sm"  value="{{$CR['attributes']['credit_limit']}}">
                            @if($CR['attributes']['validation_messages']->has('credit_limit'))
                                <script>
                                        document.getElementById('credit_limit').className = 'form-control form-control-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $CR['attributes']['validation_messages']->first("credit_limit") }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-5 row">
                        <label for="tid" class="col-sm-2 col-form-label-sm">Active</label>
                        <div class="col-sm-2">
                            <select name="active" id="active" class="form-select form-select-sm" >
                                @if( $CR['attributes']['active'] )
                                    <option value ="1" selected>Yes</option>
                                    <option value ="0">No</option>
                                @else
                                    <option value ="1">Yes</option>
                                    <option value ="0" selected>No</option>
                                @endif
                            </select>
                        </div>
                    </div>

                    <div  class="mb-2 row">

                        <div class="col-2">
                              <input type="submit" name="submit" id="submit" style="width: 100%;" class="btn btn-primary btn-sm" value="Save">
                        </div>

                    </div>


                </div>
            </div>

        </div>

    </form>
    </div>

@endsection
