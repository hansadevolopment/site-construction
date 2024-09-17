
$(document).ready(function() {

    $('#other_div').hide();

    let grnType = $('#grn_type').val();
    if(grnType == 1){

        $('#item_div').show();
        $('#other_div').hide();
    }else{

        $('#item_div').hide();
        $('#other_div').show();
    }

    $("#grn_type").change(function() {

        let grnType = $('#grn_type').val();
        if(grnType == 1){

            $('#item_div').show();
            $('#other_div').hide();
        }else{

            $('#item_div').hide();
            $('#other_div').show();
        }
    });

});

$('.remove-grn-dtl-id').click(function(event){

    var grnId = $('#grn_id').val();
    var grnDtlId = $(this).data("id");

    $("#grnId").val(grnId);
    $("#grnDtlId").val(grnDtlId);
    $("#removeGrnDtlId").submit();

});

$(".cancel-process").click(function(e) {

    if (confirm('Are you want to cancel this record ?')) {
    } else {

        e.preventDefault();
    }

});

$('#grn_id').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#grn_date').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#gl_post_id').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#remark').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#item_unit_price').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#item_quantity').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#item_discount_amount').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#desc_unit_price').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#desc_quantity').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#desc_discount_amount').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#gross_amount').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#discount_amount').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#tax_amount').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#net_amount').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});
