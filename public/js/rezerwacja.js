function calculate()
{
    if($('#data_od').val()!=''&&$('#data_do').val()!='')
    {
        var dodatki_dziennie=0.00;
        var dodatki_jednorazowo=0.00;
        $('.akcesorium').each(function() {
            if ($(this).prop('checked') == true)
            {
                if ($(this).data('dziennie') == '1')
                    dodatki_dziennie += parseFloat($(this).data('cena'));
                else if ($(this).data('dziennie') == '0')
                    dodatki_jednorazowo += parseFloat($(this).data('cena'));
            }
        });
        var data_od = explode('-', $('#data_od').val());
        var data_do = explode('-', $('#data_do').val());
        var dni = getDateDiff(new Date(parseInt(data_od[2]),parseInt(data_od[1])-1,parseInt(data_od[0])), new Date(parseInt(data_do[2]),parseInt(data_do[1])-1,parseInt(data_do[0])));
        var cena_dziennie = dodatki_dziennie+parseFloat($('#cena_pojazdu_dziennie').val());
        var cena_razem = cena_dziennie*dni;
        $('#cena_dziennie').html(cena_dziennie);
        $('#cena_razem').html(cena_razem+dodatki_jednorazowo);
        if(dodatki_jednorazowo>0)
        {
            $('#cena_dodatkowo').html(dodatki_jednorazowo);
            $('#pole_cena_dodatkowo').show();
        }
        else
            $('#pole_cena_dodatkowo').hide();
    }
}

$(document).ready(function(){
    $('input[type=date]').change(function(){
        calculate();
    });
    $('.akcesorium').change(function(){
        calculate();
    });
    $('#pojazd').change(function(){
        var id=$(this).val();
        location.href=replace_params({'id':id});
    });
    $('.usun-rezerwacje').click(function(){
        var id=$(this).data('id');
        location.href=url_helper('rezerwacja','usun',{'id':id});
    });
    $('.akceptuj-rezerwacje').click(function(){
        var id=$(this).data('id');
        location.href=url_helper('admin/rezerwacja','akceptuj',{'id':id});
    });
    $('.admin-usun-rezerwacje').click(function(){
        var id=$(this).data('id');
        location.href=url_helper('admin/rezerwacja','usun',{'id':id});
    });
});