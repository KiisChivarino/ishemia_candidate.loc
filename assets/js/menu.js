$(document).ready(function ()
{
    //раскрытие текущего пункта меню
    $(".sublist .active").closest('ul').show();

    //выпадающий список в меню
    $(".sublist").on("click", (function () {
        $(this).children("ul").slideToggle()
    }));

});