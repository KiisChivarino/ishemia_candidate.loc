import './app';

require('datatables');
import './select2';
import './select2entity';
import '../css/select2.min.css'
import './hospitalByCity';
import '../css/doctorOffice.css';
import './tinymce';
import './initDatatable';

var acc = document.getElementsByClassName("accordion");
var i;
for (i = 0; i < acc.length; i++) {
    if ($(acc[i]).hasClass('active')) {
        let panel = acc[i].nextElementSibling;
        panel.style.maxHeight = panel.scrollHeight + "px";
    }
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
    $('label.radio').on('click', function () {
        $(this).children('input').each(function () {
            console.log(this);
            if (this.checked) {
                $(this).val(true);
            }
        });
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