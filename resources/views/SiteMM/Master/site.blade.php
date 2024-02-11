@extends('layouts.site_monitoring')
@section('title')
    Site
@endsection
@section('body')

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{route('site_process')}}">
                @csrf

                <div class="card mt-3">

                    <div class="card-header">
                        Site
                    </div>

                    <div class="card-body">

                        <div class="row">
                            <div class="col-sm-12">
                                <?php echo $Site['attributes']['process_message'];  ?>
                            </div>
                        </div>

                        <div class="row no-gutters">
                            <div class="col-12 col-sm-8 col-md-8">

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Site ID</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="site_id" id="site_id" class="form-control form-control-sm"  value="{{$Site['attributes']['site_id']}}" readonly>
                                        @if($Site['attributes']['validation_messages']->has('site_id'))
                                            <script>
                                                    document.getElementById('site_id').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $Site['attributes']['validation_messages']->first("site_id") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Name</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="site_name" id="site_name" class="form-control form-control-sm"  value="{{$Site['attributes']['site_name']}}">
                                        @if($Site['attributes']['validation_messages']->has('site_name'))
                                            <script>
                                                    document.getElementById('site_name').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $Site['attributes']['validation_messages']->first("site_name") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Address</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="address" id="address" class="form-control form-control-sm"  value="{{$Site['attributes']['address']}}">
                                        @if($Site['attributes']['validation_messages']->has('address'))
                                            <script>
                                                    document.getElementById('address').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $Site['attributes']['validation_messages']->first("address") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Contact Numbers</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="contact_numbers" id="contact_numbers" class="form-control form-control-sm"  value="{{$Site['attributes']['contact_numbers']}}">
                                        @if($Site['attributes']['validation_messages']->has('contact_numbers'))
                                            <script>
                                                    document.getElementById('contact_numbers').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $Site['attributes']['validation_messages']->first("contact_numbers") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Email</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="email" id="email" class="form-control form-control-sm"  value="{{$Site['attributes']['email']}}">
                                        @if($Site['attributes']['validation_messages']->has('email'))
                                            <script>
                                                    document.getElementById('email').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $Site['attributes']['validation_messages']->first("email") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Chief Engineer</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="chief_engineer" id="chief_engineer" class="form-control form-control-sm"  value="{{$Site['attributes']['chief_engineer']}}">
                                        @if($Site['attributes']['validation_messages']->has('chief_engineer'))
                                            <script>
                                                    document.getElementById('chief_engineer').className = 'form-control form-control-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $Site['attributes']['validation_messages']->first("chief_engineer") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Active</label>
                                    <div class="col-sm-2">
                                        <select name="active" id="active" class="form-select form-select-sm" >
                                            @if( $Site['attributes']['active'] )
                                                <option value ="1" selected>Yes</option>
                                                <option value ="0">No</option>
                                            @else
                                                <option value ="1">Yes</option>
                                                <option value ="0" selected>No</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-5">
                                    <label for="tid" class="col-sm-2 col-form-label-sm">Remark</label>
                                    <div class="col-sm-10">
                                        <textarea  name="remark" id="remark" class="form-control" rows="2" style="resize:none">{{$Site['attributes']['remark']}}</textarea>
                                        @if($Site['attributes']['validation_messages']->has('remark'))
                                            <script>
                                                    document.getElementById('remark').className = 'form-control is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $Site['attributes']['validation_messages']->first("remark") }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div  class="row mb-2">
                                    <div class="col-2">
                                        <input type="submit" name="submit" id="submit" style="width: 100%;" class="btn btn-primary btn-sm" value="Save">
                                    </div>
                                    <div class="col-2">
                                        <input type="submit" name="submit" id="submit" style="width: 100%;" class="btn btn-primary btn-sm" value="Reset">
                                    </div>
                                </div>

                            </div>

                            <div class="col-12 col-sm-4 col-md-4">
                            </div>

                        </div>

                    </div>
                </div>

            </form>
        </div>
    </div>

@endsection
