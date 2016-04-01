$(document).ready(function(){
    /* Dodanie rabatu */
    $('#dodaj-nowy-rabat').click(function(){
        location.href = url_helper('admin/rabat', 'dodajform');
    });

    /* Edycja rabatu */
    $('.edytuj-rabat').click(function(){
        var id = $(this).data('id');
        location.href = url_helper('admin/rabat', 'edytujform', {'id':id});
    });

    /* Usunięcie rabatu */
    $('.usun-rabat').click(function(){
        var id = $(this).data('id');
        location.href = url_helper('admin/rabat', 'usun', {'id':id});
    });


    /* Dodanie akcesorium */
    $('#dodaj-nowe-akcesorium').click(function(){
        location.href = url_helper('admin/akcesorium', 'dodajform');
    });

    /* Edycja akcesorium */
    $('.edytuj-akcesorium').click(function(){
        var id = $(this).data('id');
        location.href = url_helper('admin/akcesorium', 'edytujform', {'id':id});
    });

    /* Usunięcie akcesorium */
    $('.usun-akcesorium').click(function(){
        var id = $(this).data('id');
        location.href = url_helper('admin/akcesorium', 'usun', {'id':id});
    });


    /* Dodanie marki */
    $('#dodaj-nowa-marke').click(function(){
        location.href = url_helper('admin/marka', 'dodajform');
    });

    /* Edycja marki */
    $('.edytuj-marke').click(function(){
        var id = $(this).data('id');
        location.href = url_helper('admin/marka', 'edytujform', {'id':id});
    });

    /* Usunięcie marki */
    $('.usun-marke').click(function(){
        var id = $(this).data('id');
        location.href = url_helper('admin/marka', 'usun', {'id':id});
    });


    /* Dodanie kategorii */
    $('#dodaj-nowa-kategorie').click(function(){
        location.href = url_helper('admin/kategoria', 'dodajform');
    });

    /* Edycja kategorii */
    $('.edytuj-kategorie').click(function(){
        var id = $(this).data('id');
        location.href = url_helper('admin/kategoria', 'edytujform', {'id':id});
    });

    /* Usunięcie kategorii */
    $('.usun-kategorie').click(function(){
        var id = $(this).data('id');
        location.href = url_helper('admin/kategoria', 'usun', {'id':id});
    });
});