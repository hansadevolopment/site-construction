@extends('layouts.gl')
@section('title')
    GL Primary Data Inquire
@endsection
@section('body')

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{route('primary_inquire_process')}}">
                @csrf

                <div class="card mt-3">

                    <div class="card-header">
                        GL Primary Data Inquire
                    </div>

                    <div class="card-body">

                        <div class="row">
                            <div class="col-sm-12">
                                <?php echo $PI['attributes']['process_message'];  ?>
                            </div>
                        </div>

                        <div class="row no-gutters">
                            <div class="col-12 col-sm-12 col-md-12">

                                <div class="row mb-4">
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Primary List</label>
                                    <div class="col-sm-5">
                                        <select name="gpi_id" id="gpi_id" class="form-select form-select-sm" >
                                            @foreach($PI['primary_data'] as $row)
                                                @if($PI['attributes']['gpi_id'] == $row->gpi_id)
                                                    <option value ="{{$row->gpi_id}}" selected>{{$row->gpi_name}}</option>
                                                @else
                                                    <option value ="{{$row->gpi_id}}">{{$row->gpi_name}}</option>
                                                @endif
                                            @endforeach
                                            @if($PI['attributes']['gpi_id'] == "0")
                                                <option value ="0" selected>Select the Primary List Name </option>
                                            @endif
                                        </select>
                                        @if($PI['attributes']['validation_messages']->has('gpi_id'))
                                            <script>
                                                    document.getElementById('gpi_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $PI['attributes']['validation_messages']->first("gpi_id") }}</div>
                                        @endif
                                    </div>

                                    <div class="col-sm-1">
                                        <input type="submit" name="submit" id="submit" class="btn btn-primary btn-sm w-100" value="Display">
                                    </div>

                                </div>

                            </div>
                            <hr>

                            <div class="col-12 col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%;">#</th>
                                                <th style="width: 75%;">{{$PI['attributes']['source_name']}}</th>
                                                <th style="width: 10%;">Active</th>
                                                <th style="width: 10%;"></th>
                                            </tr>
                                        </thead>
                                        @if( count($PI['attributes']['table_detail']) >= 1 )
                                            <tbody>
                                                @foreach ($PI['attributes']['table_detail'] as $rowKey => $rowValue)
                                                    <tr>
                                                        <td style="width: 5%;">{{$rowKey+1}}</td>
                                                        <td style="width: 75%;">{{$rowValue->source_name}}</td>
                                                        @if($rowValue->active == 1)
                                                            <td style="width: 10%;">Yes</td>
                                                        @else
                                                            <td style="width: 10%;">No</td>
                                                        @endif
                                                        <td style="width: 10%;">
                                                            <input type="button" name="btnOpen" id="btnOpen" data-source-id="{{$rowValue->source_id}}" data-gpi-id="{{$PI['attributes']['gpi_id']}}" class="btn btn-primary btn-sm w-100 open-inquire" value="Open">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        @else
                                            <tbody>
                                                <tr>
                                                    <td style="width: 5%;">-</td>
                                                    <td style="width: 75%;">-</td>
                                                    <td style="width: 10%;">-</td>
                                                    <td style="width: 10%;"></td>
                                                </tr>
                                            </tbody>
                                        @endif
                                    </table>
                                  </div>

                            </div>

                        </div>

                    </div>
                </div>

            </form>
        </div>

        <div style="display: none;">
            <form id="frm_source_open" style="display: none;" method="post" target='_blank' action="">
                @csrf
                <input type="text" name="source_id" id="source_id" values="">
            </form>
        </div>

    </div>

@endsection
