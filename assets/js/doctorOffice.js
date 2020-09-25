import './app';
require('datatables');
import './select2'

var acc = document.getElementsByClassName("accordion");
var i;
for (i = 0; i < acc.length; i++) {
    acc[i].addEventListener("click", function () {
        this.classList.toggle("active");
        var panel = this.nextElementSibling;
        if (panel.style.maxHeight) {
            panel.style.maxHeight = null;
        } else {
            panel.style.maxHeight = panel.scrollHeight + "px";
        }
    });
}

// inicial select
$(document).ready(function () {
    let datatable = $('#datatable');
    datatable.initDataTables(datatable.data('table_settings'),
        {
            searching: false,
            drawCallback: function (settings) {
                this.api().rows({page: 'current'}).column(0, {
                    search: 'applied',
                    order: 'applied'
                }).nodes().each(function (cell, i) {
                    cell.innerHTML = settings._iDisplayStart + i + 1;
                });
            }
        });

    //фильтр по выбору больницы
    $('*').filter(function () {
        return $(this).data('filter_name') !== undefined;
    }).on('change', function () {
        $("form[name=" + $(this).data('filter_name') + "]").submit();
    });

    $('.closed-popup').on('click', function () {
        $('.popup').removeClass('active');
    })
});