@extends('layouts.gl')
@section('title')
    GL Transaction Data Inquire
@endsection
@section('body')

    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{route('transaction_inquire_process')}}">
                @csrf

                <div class="card mt-3">

                    <div class="card-header">
                        GL Transaction Data Inquire
                    </div>

                    <div class="card-body">

                        <div class="row">
                            <div class="col-sm-12">
                                <?php echo $TI['attributes']['process_message'];  ?>
                            </div>
                        </div>

                        <div class="row no-gutters">
                            <div class="col-12 col-sm-12 col-md-12">

                                <div class="row mb-4">
                                    <label for="tid" class="col-sm-1 col-form-label-sm">Transaction List</label>
                                    <div class="col-sm-5">
                                        <select name="gti_id" id="gti_id" class="form-select form-select-sm" >
                                            @foreach($TI['transaction_data'] as $row)
                                                @if($TI['attributes']['gti_id'] == $row->gti_id)
                                                    <option value ="{{$row->gti_id}}" selected>{{$row->gti_name}}</option>
                                                @else
                                                    <option value ="{{$row->gti_id}}">{{$row->gti_name}}</option>
                                                @endif
                                            @endforeach
                                            @if($TI['attributes']['gti_id'] == "0")
                                                <option value ="0" selected>Select the Transaction List Name </option>
                                            @endif
                                        </select>
                                        @if($TI['attributes']['validation_messages']->has('gti_id'))
                                            <script>
                                                    document.getElementById('gti_id').className = 'form-select form-select-sm is-invalid';
                                            </script>
                                            <div class="invalid-feedback">{{ $TI['attributes']['validation_messages']->first("gti_id") }}</div>
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
                                                <th style="width: 10%;">Date</th>
                                                <th style="width: 75%;">{{$TI['attributes']['source_name']}}</th>
                                                <th style="width: 10%;"></th>
                                            </tr>
                                        </thead>
                                        @if( count($TI['attributes']['table_detail']) >= 1 )
                                            <tbody>
                                                @foreach ($TI['attributes']['table_detail'] as $rowKey => $rowValue)
                                                    <tr>
                                                        <td style="width: 5%;">{{$rowValue->je_id}}</td>
                                                        <td style="width: 10%;">{{$rowValue->je_date}}</td>
                                                        <td style="width: 75%;">{{$rowValue->remark}}</td>
                                                        <td style="width: 10%;">
                                                            <input type="button" name="btnOpen" id="btnOpen" data-source-id="{{$rowValue->source_id}}" data-gti-id="{{$TI['attributes']['gti_id']}}" class="btn btn-primary btn-sm w-100 open-transaction-inquire" value="Open">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        @else
                                            <tbody>
                                                <tr>
                                                    <td style="width: 5%;">-</td>
                                                    <td style="width: 10%;">-</td>
                                                    <td style="width: 75%;">-</td>
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
