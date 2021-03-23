import './app';

require('jquery-mask-plugin');
require('datatables');

import './select2';
import './tabs';
import '../css/admin.scss'
import './mask';
import './hospitalByCity';
import './fileUpload';
import './menu';



require('../images/operation-icon-1.svg');
require('../images/operation-icon-2.svg');
require('../images/operation-icon-3.svg');
require('../images/favicons/adm-fav.ico');
require('fancybox')($);


$(document).ready(function () {
    $('.fancybox').fancybox({
        buttons: [
            'close'
        ],
        helpers: {
            title: null
        },
        tpl: {
            closeBtn: '<a title="Close" class="fancybox-item fancybox-close"></a>'
        },
        afterShow: function () {
            let click = 1;
            $('.fancybox-wrap').click(function () {
                let n = 90 * ++click;
                $('.fancybox-skin')
                    .css('webkitTransform', 'rotate(-' + n + 'deg)')
                    .css('mozTransform', 'rotate(-' + n + 'deg)');
            });
        }
    });

    //фильтр по выбору select
    $('*').filter(function () {
        return $(this).data('filter_name') !== undefined;
    }).on('change', function () {
        $("form[name=" + $(this).data('filter_name') + "]").submit();
    });

    //раскрывающиеся списки ссылок в просмотре записи
    $('.btn--show').on('click', function () {
        $(this).children('.sublist').toggle();
    })

    //добавляет стили для чекбоксов множественного выбора
    $('.js-multiple-check').parent('div.form-check').addClass('check-row');
});