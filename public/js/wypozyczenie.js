$(document).ready(function(){
    $('.zakoncz-wypozyczenie').click(function(){
        var id=$(this).data('id');
        location.href=url_helper('admin/wypozyczenie','zakform',{'id':id});
    });
    $('.usun-wypozyczenie').click(function(){
        var id=$(this).data('id');
        location.href=url_helper('admin/wypozyczenie','usun',{'id':id});
    });
});