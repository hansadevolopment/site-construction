
$('.remove-tmp-je').click(function(event){

    var tmp_je_id = $(this).data("id");

    $("#tmp_je_id").val(tmp_je_id);
    $("#remove_tmp_je").submit();

});
