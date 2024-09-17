
$('.remove-tmp-je').click(function(event){

    var tmp_je_id = $(this).data("id");

    $("#tmp_je_id").val(tmp_je_id);
    $("#remove_tmp_je").submit();

});

$('.open-inquire').click(function(){

    let source_id = $(this).data("source-id");
    let gpi_id = $(this).data("gpi-id");

    if( gpi_id == 1){

        $("#frm_source_open").attr('action', "/open_tax");
    }

    if( gpi_id == 2){

        $("#frm_source_open").attr('action', "/open_bank");
    }

    if( gpi_id == 3){

        $("#frm_source_open").attr('action', "/open_bank_account");
    }

    if( gpi_id == 4){

        $("#frm_source_open").attr('action', "/open_main_account");
    }

    if( gpi_id == 5){

        $("#frm_source_open").attr('action', "/open_controll_account");
    }

    if( gpi_id == 6){

        $("#frm_source_open").attr('action', "/open_sub_account");
    }

    $("#source_id").val(source_id);
    $("#frm_source_open").submit();

});

$('.open-transaction-inquire').click(function(){

    let source_id = $(this).data("source-id");
    let gti_id = $(this).data("gti-id");

    if( gti_id == 1){

        $("#frm_source_open").attr('action', "/open_journal_entry");
    }

    $("#source_id").val(source_id);
    $("#frm_source_open").submit();

});
