$(document).ready(function(){
    $('input.rating').rating();
    $('.rating-symbol').click(function(){
        var obj = $(this).parents('.element-do-oceny').find('input.rating');
        var ocena = obj.val();
        var id=obj.data('id');
        location.href=url_helper('samochod','ocen',{'id':id,'ocena':ocena});
    });
});