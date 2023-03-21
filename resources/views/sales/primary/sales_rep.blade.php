@extends('layouts.sales')

@section('title')
    Sales Rep
@endsection

@section('body')

    <div id="tbldiv" style="width: 98%;  margin-right: 1%; margin-left: 1%; margin-top: 1%;">
    <form method="POST" action="{{route('sales_rep_process')}}">

        @csrf

        <div class="col-sm-12">
            
            <div class="card">

                <div class="card-header">
                    Sales Rep
                </div>

                <div class="card-body">

                    <div class="col-sm-11">
                        <?php echo $SR['attributes']['process_message'];  ?>
                    </div>

                    <div class="mb-2 row">
                        <label for="tid" class="col-sm-2 col-form-label-sm">Sales Rep ID</label>
                        <div class="col-sm-1">
                            <input type="text" name="sales_rep_id" id="sales_rep_id" class="form-control form-control-sm"  value="{{$SR['attributes']['sales_rep_id']}}" readonly>
                            @if($SR['attributes']['validation_messages']->has('sales_rep_id'))
                            <script>
                                    document.getElementById('sales_rep_id').className = 'form-control form-control-sm is-invalid';
                            </script>
                            <div class="invalid-feedback">{{ $SR['attributes']['validation_messages']->first("sales_rep_id") }}</div>
                        @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <label for="tid" class="col-sm-2 col-form-label-sm">Sales Rep Name</label>
                        <div class="col-sm-10">
                            <input type="text" name="sales_rep_name" id="sales_rep_name" class="form-control form-control-sm"  value="{{$SR['attributes']['sales_rep_name']}}">
                            @if($SR['attributes']['validation_messages']->has('sales_rep_name'))
                                <script>
                                        document.getElementById('sales_rep_name').className = 'form-control form-control-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $SR['attributes']['validation_messages']->first("sales_rep_name") }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <label for="tid" class="col-sm-2 col-form-label-sm">Contact Numbers</label>
                        <div class="col-sm-10">
                            <input type="text" name="contact_numbers" id="contact_numbers" class="form-control form-control-sm"  value="{{$SR['attributes']['contact_numbers']}}">
                            @if($SR['attributes']['validation_messages']->has('contact_numbers'))
                                <script>
                                        document.getElementById('contact_numbers').className = 'form-control form-control-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $SR['attributes']['validation_messages']->first("contact_numbers") }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <label for="tid" class="col-sm-2 col-form-label-sm">Email</label>
                        <div class="col-sm-10">
                            <input type="text" name="emails" id="emails" class="form-control form-control-sm"  value="{{$SR['attributes']['emails']}}">
                            @if($SR['attributes']['validation_messages']->has('emails'))
                                <script>
                                        document.getElementById('emails').className = 'form-control form-control-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $SR['attributes']['validation_messages']->first("emails") }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <label for="tid" class="col-sm-2 col-form-label-sm">Fax</label>
                        <div class="col-sm-10">
                            <input type="text" name="fax" id="fax" class="form-control form-control-sm"  value="{{$SR['attributes']['fax']}}">
                            @if($SR['attributes']['validation_messages']->has('fax'))
                                <script>
                                        document.getElementById('fax').className = 'form-control form-control-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $SR['attributes']['validation_messages']->first("fax") }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2 row">
                        <label for="tid" class="col-sm-2 col-form-label-sm">Address</label>
                        <div class="col-sm-10">
                            <textarea  name="address" id="address" class="form-control" rows="3" style="resize:none">{{$SR['attributes']['address']}}</textarea>
                            @if($SR['attributes']['validation_messages']->has('address'))
                                <script>
                                        document.getElementById('address').className = 'form-control form-control-sm is-invalid';
                                </script>
                                <div class="invalid-feedback">{{ $SR['attributes']['validation_messages']->first("address") }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-5 row">
                        <label for="tid" class="col-sm-2 col-form-label-sm">Active</label>
                        <div class="col-sm-2">
                            <select name="active" id="active" class="form-select form-select-sm" >
                                @if( $SR['attributes']['active'] )
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