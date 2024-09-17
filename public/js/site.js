
$(document).ready(function() {

    $("#site_id").change(function() {

        var site_id = $('#site_id').val();
        var task_id = $('#task_id').val();
        var sub_task_id = $('#sub_task_id').val();

        $.ajax({
            url : "/get_site_wise_task",
            method : 'GET',
            data : {
                "_token": "{{ csrf_token() }}",
                site_id: site_id
            },
            error: function(xhr, status, error) {
                var errorMessage = xhr.status + ': ' + xhr.statusText + ': ' + xhr.responseText
                alert('Error - ' + errorMessage);
            },
            success:function(response){

                $('#task_id').html(response);
            }
        });

        getSiteWiseTotalCost(site_id, task_id, sub_task_id);

    });

    $("#task_id").change(function() {

        var site_id = $('#site_id').val();
        var task_id = $('#task_id').val();
        var sub_task_id = $('#sub_task_id').val();

        $.ajax({
            url : "/get_site_wise_sub_task",
            method : 'GET',
            data : {
                "_token": "{{ csrf_token() }}",
                site_id : site_id,
                task_id : task_id
            },
            error: function(xhr, status, error) {
                var errorMessage = xhr.status + ': ' + xhr.statusText + ': ' + xhr.responseText
                alert('Error - ' + errorMessage);
            },
            success:function(response){

                $('#sub_task_id').html(response);
            }
        });

        getSiteWiseTotalCost(site_id, task_id, sub_task_id);

    });

    $("#sub_task_id").change(function() {

        var site_id = $('#site_id').val();
        var task_id = $('#task_id').val();
        var sub_task_id = $('#sub_task_id').val();

        getSiteWiseTotalCost(site_id, task_id, sub_task_id);

    });

    $("#item_id").change(function() {

        var item_id = $(this).val();

        $.ajax({
            url : "/get_item_for_sap_material",
            method : 'GET',
            data : {
                "_token": "{{ csrf_token() }}",
                item_id: item_id
            },
            error: function(xhr, status, error) {
                var errorMessage = xhr.status + ': ' + xhr.statusText + ': ' + xhr.responseText
                alert('Error - ' + errorMessage);
            },
            success:function(response){

                $("#unit").val(response['unit']);
                $("#price").val(response['price']);
                $("#quanity").focus();
            }
        });
    });

    $("#lc_id").change(function() {

        var lc_id = $(this).val();

        $.ajax({
            url : "/get_labour_category_for_sap_labour",
            method : 'GET',
            data : {
                "_token": "{{ csrf_token() }}",
                lc_id: lc_id
            },
            error: function(xhr, status, error) {
                var errorMessage = xhr.status + ': ' + xhr.statusText + ': ' + xhr.responseText
                alert('Error - ' + errorMessage);
            },
            success:function(response){

                $("#price").val(response['price']);
                $("#quanity").focus();
            }
        });
    });

    $('.es-site').change(function(event){

        var target_id = event.target.id;
        var explode_result =target_id.split('_');
        var site_serial = explode_result[2];

        var site_id = $('#'+event.target.id).val();

        $.ajax({
            url : "/get_site_wise_task",
            method : 'GET',
            data : {
                "_token": "{{ csrf_token() }}",
                site_id: site_id
            },
            error: function(xhr, status, error) {
                var errorMessage = xhr.status + ': ' + xhr.statusText + ': ' + xhr.responseText
                alert('Error - ' + errorMessage);
            },
            success:function(response){

                $('#task_id_'+site_serial).html(response);
            }
        });
    });

    $('.es-task').change(function(event){

        var target_id = event.target.id;
        var explode_result = target_id.split('_');
        var site_serial = explode_result[2];

        var site_id = $('#site_id_'+site_serial).val();
        var task_id = $('#'+event.target.id).val();

        $.ajax({
            url : "/get_site_wise_sub_task",
            method : 'GET',
            data : {
                "_token": "{{ csrf_token() }}",
                site_id : site_id,
                task_id : task_id
            },
            error: function(xhr, status, error) {
                var errorMessage = xhr.status + ': ' + xhr.statusText + ': ' + xhr.responseText
                alert('Error - ' + errorMessage);
            },
            success:function(response){

                $('#sub_task_id_'+site_serial).html(response);
            }
        });
    });

    $('.w-ot-hours').focusout(function(event){

        var target_id = event.target.id;
        var explode_result = target_id.split('_');
        var date_serial = explode_result[2];

        var ot_rate =  $('#ot_rate_'+date_serial).val();
        var ot_hours = parseFloat($(this).val());
        var ot_amount = ot_rate * ot_hours;
        var net_salary = $('#net_salary_'+date_serial).val();
        var net_salary = parseFloat(net_salary);
        $('#net_salary_'+date_serial).val((net_salary + ot_amount));

        $('#ot_amount_'+date_serial).val(currencyNumberFormat(ot_amount));

    });

    $('.site-inquiry-open').click(function(){

        let site_id = $(this).data("site-id");
        $("#open_site_id").val(site_id);
        $("#open_site").submit();
    });

    $('.task-inquiry-open').click(function(){

        let task_id = $(this).data("task-id");
        $("#open_task_id").val(task_id);
        $("#open_task").submit();
    });

    $('.sub-task-inquiry-open').click(function(){

        let sub_task_id = $(this).data("sub-task-id");
        let site_id = $("#site_id").val();

        $("#open_site_no").val(site_id);
        $("#open_sub_task_id").val(sub_task_id);
        $("#open_sub_task").submit();
    });

    $('.sap-material-inquiry-open').click(function(){

        let sap_material_id = $(this).data("sap-material-id");

        $("#open_sap_material_id").val(sap_material_id);
        $("#open_sap_material").submit();
    });

    $('.sap-labour-inquiry-open').click(function(){

        let sap_labour_id = $(this).data("sap-labour-id");

        $("#open_sap_labour_id").val(sap_labour_id);
        $("#open_sap_labour").submit();
    });

    $('.sap-overhead-inquiry-open').click(function(){

        let overhead_cost_item_id = $(this).data("sap-oc-id");

        $("#open_sap_overhead_cost_id").val(overhead_cost_item_id);
        $("#open_sap_overhead").submit();
    });

    $('.sap-profit-inquiry-open').click(function(){

        let profit_id = $(this).data("sap-profit-id");

        $("#open_sap_profit_id").val(profit_id);
        $("#open_sap_profit").submit();
    });

    $('.item-master-open').click(function(){

        let item_id = $(this).data("item-id");

        $("#item_id").val(item_id);
        $("#open_item").submit();
    });

    $('.employee-inquiry-open').click(function(){

        let employee_id = $(this).data("employee-id");

        $("#employee_id").val(employee_id);
        $("#open_employee").submit();
    });

    $('.lc-inquiry-open').click(function(){

        let lc_id = $(this).data("lc-id");

        $("#lc_id").val(lc_id);
        $("#open_labour_category").submit();
    });

    $('.oci-inquiry-open').click(function(){

        let oci_id = $(this).data("oci-id");

        $("#oci_id").val(oci_id);
        $("#open_overhead").submit();
    });

    $('.unit-inquiry-open').click(function(){

        let unit_id = $(this).data("unit-id");

        $("#unit_id").val(unit_id);
        $("#open_unit").submit();
    });


    $(".dpr-cs-id").change(function() {

        var cs_id = $(this).val();
        var dpr_item_id = $('.dpr-item');
        if( cs_id == 1 ){

            $("#dpr_none_quantity").show();
            $("#btn_save").hide();
            $("#dpr_quantity").hide();
            jQuery(dpr_item_id).attr("id","item_id");

            $.ajax({
                url : "/cost_section_item",
                method : 'GET',
                data : {
                    "_token": "{{ csrf_token() }}",
                    cs_id: cs_id,
                },
                error: function(xhr, status, error) {
                    var errorMessage = xhr.status + ': ' + xhr.statusText + ': ' + xhr.responseText
                    alert('Error - ' + errorMessage);
                },
                success:function(response){

                    $('#item_id').html(response);
                }
            });

        }else if( cs_id == 3 ){

            $("#dpr_none_quantity").show();
            $("#btn_save").hide();
            $("#dpr_quantity").hide();
            $('.dpr-item').attr("id","overhead_cost_item_id");

            $.ajax({
                url : "/cost_section_item",
                method : 'GET',
                data : {
                    "_token": "{{ csrf_token() }}",
                    cs_id: cs_id,
                },
                error: function(xhr, status, error) {
                    var errorMessage = xhr.status + ': ' + xhr.statusText + ': ' + xhr.responseText
                    alert('Error - ' + errorMessage);
                },
                success:function(response){

                    $('#overhead_cost_item_id').html(response);
                }
            });

        }else if( cs_id == 5 ){

            $("#dpr_none_quantity").hide();
            $("#btn_save").show();
            $("#dpr_quantity").show();

        }

    });

    $("#dpr_ref_id").change(function() {

        let dpr_ref_id = $(this).val();
        let cs_id = $('#cs_id').val();

        if( cs_id == 1){

            $.ajax({
                url : "/get_item_for_sap_material",
                method : 'GET',
                data : {
                    "_token": "{{ csrf_token() }}",
                    item_id: dpr_ref_id
                },
                error: function(xhr, status, error) {
                    var errorMessage = xhr.status + ': ' + xhr.statusText + ': ' + xhr.responseText
                    alert('Error - ' + errorMessage);
                },
                success:function(response){

                    $("#unit").val(response['unit']);
                    $("#price").val(response['price']);
                    $("#quanity").focus();
                }
            });

        }else if( cs_id == 3 ){

            $("#unit").val('');
            $("#price").val('');

        }else{

        }
    });

    $('.item-issue-note-open').click(function(){

        let iin_id = $(this).data("iin-id");

        $("#iin_id").val(iin_id);
        $("#open_item_issue_note").submit();
    });

    $('.payment-voucher-open').click(function(){

        let pv_id = $(this).data("pv-id");

        $("#open_pv_id").val(pv_id);
        $("#open_payment_voucher").submit();
    });

    $('.employee-advance-open').click(function(){

        let ea_id = $(this).data("ea-id");

        $("#open_ea_id").val(ea_id);
        $("#open_employee_advance").submit();
    });

    $('.employee-salary-open').click(function(){

        let es_id = $(this).data("es-id");

        $("#open_es_id").val(es_id);
        $("#open_employee_salary").submit();
    });

    $('.dpr-open').click(function(){

        let dpr_id = $(this).data("dpr-id");

        $("#open_dpr_id").val(dpr_id);
        $("#open_dpr").submit();
    });


    $(".sap_labour_sc_id").change(function() {

        let sc_id = $(this).val();

        if( sc_id == "1" ){

            $("#sap_labour_basic_salary").show();
            $("#sap_labour_sub_target").hide();

        }else{

            $("#sap_labour_basic_salary").hide();
            $("#sap_labour_sub_target").show();
        }

    });

    $("#btn_advance_record").click(function() {

        let site_id = $("#site_id").val();
        let task_id = $("#task_id").val();
        let sub_task_id = $("#sub_task_id").val();

        $.ajax({
            url : "/get_employee_advance",
            method : 'GET',
            data : {
                "_token": "{{ csrf_token() }}",
                site_id : site_id,
                task_id : task_id,
                sub_task_id : sub_task_id
            },
            error: function(xhr, status, error) {
                var errorMessage = xhr.status + ': ' + xhr.statusText + ': ' + xhr.responseText
                alert('Error - ' + errorMessage);
            },
            success:function(response){

                let advance_total = Number(0);
                let table_body = '';
                let icount = 1;

                $.each(response, function(i, obj) {

                    table_body += '<tr>';
                    table_body += '<td>' + icount + '</td>';
                    table_body += '<td>' + obj.ea_date + '</td>';
                    table_body += '<td class="text-end">' + formatNumber(obj.advance_balance) + '</td>';
                    table_body += '</tr>';

                    icount++;
                    advance_total = advance_total + Number(obj.advance_balance);
                });

                table_body += '<tr>';
                table_body += '<td colspan="2">Total</td>';
                table_body += '<td class="text-end">' + formatNumber(advance_total) + '</td>';
                table_body += '</tr>';


                $("#tbody_emp_salary_two").html(table_body);
                $("#advance_amount").val(formatNumber(advance_total));
            }
        });


    });


    $(".cancel-process").click(function(e) {

        if (confirm('Are you want to cancel this record ?')) {
        } else {

            e.preventDefault();
        }

    });

});

$(window).on( "load", function() {

    let sc_id = $("#sc_id").val();
    if( sc_id == "1" ){

        $("#sap_labour_basic_salary").show();
        $("#sap_labour_sub_target").hide();

    }else{

        $("#sap_labour_basic_salary").hide();
        $("#sap_labour_sub_target").show();
    }


    var cs_id = $(".dpr-cs-id").val();
    if( cs_id == 1 ){

        $("#dpr_none_quantity").show();
        $("#btn_save").hide();
        $("#dpr_quantity").hide();

    }else if( cs_id == 3 ){

        $("#dpr_none_quantity").show();
        $("#btn_save").hide();
        $("#dpr_quantity").hide();

    }else if( cs_id == 5 ){

        $("#dpr_none_quantity").hide();
        $("#btn_save").show();
        $("#dpr_quantity").show();

    }


});

function formatNumber(val){

    if(val == "" || val == null){
        return "0.00";
    }
    var result = RemoveComma(val).toFixed(2);
    return result.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function RemoveComma(val){

    var result = Number(String(val).replace(/,/g, ""));
    return result;
}


function getSiteWiseTotalCost(site_id, task_id, sub_task_id){

    $.ajax({
        url : "/sap_profit_total",
        method : 'GET',
        data : {
            "_token": "{{ csrf_token() }}",
            site_id: site_id,
            task_id: task_id,
            sub_task_id:sub_task_id
        },
        error: function(xhr, status, error) {
            var errorMessage = xhr.status + ': ' + xhr.statusText + ': ' + xhr.responseText
            alert('Error - ' + errorMessage);
        },
        success:function(response){

            $("#material_cost").val(response['material_cost']);
            $("#labour_cost").val(response['labour_cost']);
            $("#overhead_cost").val(response['overhead_cost']);
            $('#total_cost').val(response['total_cost']);
        }
    });
}

function currencyNumberFormat(number){

    let USDollar = new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: '',
    });

    return USDollar.format(number);
}

$('#employee_id').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#employee_code').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#employee_name').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#item_id').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#item_name').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#remark').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#lc_id').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#lc_name').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#price').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#oci_id').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#oci_name').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#site_id').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#site_name').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#address').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#contact_numbers').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#email').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#chief_engineer').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});


$('#unit_id').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#unit_name').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#days').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#unit').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#quantity').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#value').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#material_cost').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#labour_cost').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#overhead_cost').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#total_cost').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#profit_value').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#sub_task_id').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#sub_task_name').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});


$('#start_date').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#end_date').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#task_id').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#task_name').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#task_unit').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#pv_id').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#advance_amount').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#iin_id').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#dpr_id').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});

$('#dpr_date').keypress(function (e) {
    if (e.which == 13) {
        e.preventDefault();
    }
});
