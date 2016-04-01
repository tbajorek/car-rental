/* Funkcje pomocnicze */

window.BASE_URL = '/';

function add_params(params, url)
{
    if(url==undefined)
        url=location.href;
    var before = url.substr(0, url.indexOf('?')+1);
    url=url.substr(url.indexOf('?')+1);
    var tmp=explode('&', url);
    for(var key in params)
        tmp[tmp.length]=key+'='+params[key];
    url = implode('&',tmp);
    return before+url;
}

function replace_params(params, url)
{
    if(url==undefined)
        url=location.href;
    var before = url.substr(0, url.indexOf('?')+1);
    url=url.substr(url.indexOf('?')+1);
    var tmp_params=explode('&', url);
    for(var key in tmp_params)
    {
        var tmp=explode('=',tmp_params[key]);
        if(isset(params[tmp[0]]))
        {
            tmp[1]=params[tmp[0]];
            delete params[tmp[0]];
        }
        tmp_params[key]=implode('=',tmp);
    }
    if(count(params)>0)
        url = add_params(params, before+implode('&',tmp_params));
    else
        url = before+implode('&',tmp_params);
    return url;
}

function remove_params(params, url)
{
    if(url==undefined)
        url=location.href;
    var before = url.substr(0, url.indexOf('?')+1);
    url=url.substr(url.indexOf('?')+1);
    var tmp_params=explode('&', url);
    for(var key in tmp_params)
    {
        var tmp = explode('=', tmp_params[key]);
        var index = array_search(tmp[0], params);
        if (index !== false) {
            params.splice(index,1);
            tmp_params.splice(key,1);
        }
        else
            tmp_params[key] = implode('=', tmp);
    }
    console.log(tmp_params);
    url = implode('&',tmp_params);
    return before+url;
}

//--------------------------------------

function url_helper(controller, action, params)
{
    var url='';
    if(params==undefined)
        params = {};
    if(controller!=undefined)
        params['controller'] = lcfirst(controller);
    if(action!='index'&&action!=undefined)
        params['action'] = lcfirst(action);

    if(count(params)>0)
    {
        url = '/?';
        var tmp = [];
        for(var key in params)
            tmp[tmp.length] = key + '=' + params[key];
        url += implode('&', tmp);
    }
    else
        url = '/';
    return url;
}

function getDateDiff(startDate, endDate)
{
    return Math.abs((endDate.getTime() - startDate.getTime()) / (1000 * 60 * 60 * 24));
}

$(document).ready(function(){
    /* Polubienia */
    $('.btn-wishlist').click(function(){
        var id = $(this).data('id');
        if($(this).data('type')=='like')
        {
            location.href = url_helper('ulubione', 'polub', {'id':id});
        }
        else
        {
            location.href = url_helper('ulubione', 'odlub', {'id':id});
        }
    });

    /* Filtry */
    $('.filter-set').click(function(){
        var params = {};
        if($('#filter_nazwa').val()!='')
            params['nazwa']=$('#filter_nazwa').val();
        if($('#filter_marka').val()!=0)
            params['marka']=$('#filter_marka').val();
        if($('#filter_dostepne').prop('checked')!=false)
            params['dostepne']=1;
        location.href = add_params(params, remove_params(['nazwa','marka','dostepne']));
    });

    /* Ulubione */
    $('.ulubione-set').click(function(){
        location.href = add_params({'ulubione':true});
    });
    $('.ulubione-unset').click(function(){
        location.href = remove_params(['ulubione']);
    });

    /* Przełączenie do rezerwacji */
    $('.btn-cart').click(function(){
        var id = $(this).data('id');
        location.href = url_helper('rezerwacja', 'form', {'id':id});
    });

    /* Edycja */
    $('.btn-edit').click(function(){
        var id = $(this).data('id');
        location.href = url_helper('admin/samochod', 'edytujform', {'id':id});
    });

    /* Usunięcie */
    $('.btn-remove').click(function(){
        var id = $(this).data('id');
        location.href = url_helper('admin/samochod', 'usun', {'id':id});
    });

    $('table').DataTable({
        "language": {
            "sProcessing":   "Przetwarzanie...",
            "sLengthMenu":   "Pokaż _MENU_ pozycji",
            "sZeroRecords":  "Nie znaleziono pasujących pozycji",
            "sInfoThousands":  " ",
            "sInfo":         "Pozycje od _START_ do _END_ z _TOTAL_ łącznie",
            "sInfoEmpty":    "Pozycji 0 z 0 dostępnych",
            "sInfoFiltered": "(filtrowanie spośród _MAX_ dostęępnych pozycji)",
            "sInfoPostFix":  "",
            "sSearch":       "Szukaj:",
            "sUrl":          "",
            "oPaginate": {
                "sFirst":    "Pierwsza",
                "sPrevious": "Poprzednia",
                "sNext":     "Nastęępna",
                "sLast":     "Ostatnia"
            },
            "sEmptyTable":     "Brak danych",
            "sLoadingRecords": "Wczytywanie...",
            "oAria": {
                "sSortAscending":  ": aktywuj, by posortować kolumnę rosnąco",
                "sSortDescending": ": aktywuj, by posortować kolumnę malejąco"
            }
        }
    });
});