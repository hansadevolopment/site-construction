@extends('layouts.sales')

@section('title')
    Debtor
@endsection

@section('body')

    <div id="tbldiv" style="width: 98%;  margin-right: 1%; margin-left: 1%; margin-top: 1%;">
    <form method="POST" action="{{route('debtor_process')}}">

        @csrf

        <div class="col-sm-12">
            
            <div class="card">

                <div class="card-header">
                    Debtor
                </div>

                <div class="card-body">

                    <div class="col-sm-11">
                        <?php echo $Debtor['attributes']['process_message'];  ?>
                    </div>

                    <div class="mb-2 row">
                        <label for="tid" class="col-sm-2 col-form-label-sm">Debtor ID</label>
                        <div class="col-sm-1">
                            <input type="text" name="debtor_id" id="debtor_id" class="form-control form-control-sm"  value="{{$Debtor['attributes']['debtor_id']}}" readonly>
                            @if($Debtor['attributes']['validation_messages']->has('debtor_id'))
                            <script>
                                    document.getElementById('debtor_id').className += ' is-invalid';
                            </script>
                            <div class="invalid-feedback">{{ $Debtor['attributes']['validation_messages']->first("debtor_id") }}</div>
                        @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <label for="tid" class="col-sm-2 col-form-label-sm">Debtor Name</label>
                        <div class="col-sm-10">
                            <input type="text" name="debtor_name" id="debtor_name" class="form-control form-control-sm"  value="{{$Debtor['attributes']['debtor_name']}}">
                            @if($Debtor['attributes']['validation_messages']->has('debtor_name'))
                                <script>
                                        document.getElementById('debtor_name').className = 'form-control form-control-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $Debtor['attributes']['validation_messages']->first("debtor_name") }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <label for="tid" class="col-sm-2 col-form-label-sm">Contact Number</label>
                        <div class="col-sm-10">
                            <input type="text" name="contact_number" id="contact_number" class="form-control form-control-sm"  value="{{$Debtor['attributes']['contact_number']}}">
                            @if($Debtor['attributes']['validation_messages']->has('contact_number'))
                                <script>
                                        document.getElementById('contact_number').className = 'form-control form-control-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $Debtor['attributes']['validation_messages']->first("contact_number") }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <label for="tid" class="col-sm-2 col-form-label-sm">Contact Persons</label>
                        <div class="col-sm-10">
                            <input type="text" name="contact_persons" id="contact_persons" class="form-control form-control-sm"  value="{{$Debtor['attributes']['contact_persons']}}">
                            @if($Debtor['attributes']['validation_messages']->has('contact_persons'))
                                <script>
                                        document.getElementById('contact_persons').className = 'form-control form-control-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $Debtor['attributes']['validation_messages']->first("contact_persons") }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <label for="tid" class="col-sm-2 col-form-label-sm">Email</label>
                        <div class="col-sm-10">
                            <input type="text" name="emails" id="emails" class="form-control form-control-sm"  value="{{$Debtor['attributes']['emails']}}">
                            @if($Debtor['attributes']['validation_messages']->has('emails'))
                                <script>
                                        document.getElementById('emails').className = 'form-control form-control-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $Debtor['attributes']['validation_messages']->first("emails") }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <label for="tid" class="col-sm-2 col-form-label-sm">Fax</label>
                        <div class="col-sm-10">
                            <input type="text" name="fax" id="fax" class="form-control form-control-sm"  value="{{$Debtor['attributes']['fax']}}">
                            @if($Debtor['attributes']['validation_messages']->has('fax'))
                                <script>
                                        document.getElementById('fax').className = 'form-control form-control-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $Debtor['attributes']['validation_messages']->first("fax") }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <label for="tid" class="col-sm-2 col-form-label-sm">Address</label>
                        <div class="col-sm-10">
                            <textarea  name="address" id="address" class="form-control" rows="3" style="resize:none">{{$Debtor['attributes']['address']}}</textarea>
                            @if($Debtor['attributes']['validation_messages']->has('address'))
                                <script>
                                        document.getElementById('address').className = 'form-control is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $Debtor['attributes']['validation_messages']->first("address") }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-5 row">
                        <label for="tid" class="col-sm-2 col-form-label-sm">Active</label>
                        <div class="col-sm-2">
                            <select name="active" id="active" class="form-select form-select-sm" >
                                @if( $Debtor['attributes']['active'] )
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

    <script>

        $('#client').keypress(function (e) {
            if (e.which == 13) {
                e.preventDefault();
            }
        });

    </script>


@endsection