import './app';

require('jquery-mask-plugin');
require('datatables');
import './select2';
import './tabs';
import '../css/admin.scss'
import './mask';
import './hospitalByCity';

require('../tinymce/langs/ru.js');
import 'tinymce/tinymce.min'
import "tinymce/themes/silver/theme"

require('tinymce/skins/ui/oxide/content.css')
require('tinymce/skins/ui/oxide/skin.css')
require('tinymce/icons/default/icons.min')

require('../images/operation-icon-1.svg');
require('../images/operation-icon-2.svg');
require('../images/operation-icon-3.svg');
require('../images/favicons/adm-fav.ico');

tinymce.init({
    selector: '.tinymce',
    language: 'ru',
});

$(document).ready(function () {
    //view hospitals for ROLE_DOCTOR_HOSPITAL
    let hospitals = $('#form_staff_hospital');
    let roles = $('#form_onlyRole_roles');
    if (roles.val() === 'ROLE_DOCTOR_HOSPITAL') {
        hospitals.attr('required', true);
    } else {
        hospitals.parent('li').hide();
    }
    roles.on('change', function () {
        if (roles.val() === 'ROLE_DOCTOR_HOSPITAL') {
            hospitals.parent('li').show();
            hospitals.attr('required', true);
        } else {
            hospitals.parent('li').hide();
            hospitals.empty();
            hospitals.removeAttr('required');
        }
    });

    let datatable = $('#datatable');
    datatable.initDataTables(datatable.data('table_settings'),
        {
            searching: true,
            drawCallback: function (settings) {
                this.api().rows({page: 'current'}).column(0, {
                    search: 'applied',
                    order: 'applied'
                }).nodes().each(function (cell, i) {
                    cell.innerHTML = settings._iDisplayStart + i + 1;
                });
            }
        });

    //фильтр по выбору select
    $('*').filter(function () {
        return $(this).data('filter_name') !== undefined;
    }).on('change', function () {
        $("form[name=" + $(this).data('filter_name') + "]").submit();
    });

    //раскрытие текущего пункта меню
    $(".sublist .active").closest('ul').show();
    //выпадающий список в меню
    $(".sublist").on("click", (function () {
        $(this).children("ul").slideToggle()
    }));

    //раскрывающиеся списки ссылок в просмотре записи
    $('.btn--show').on('click', function () {
        $(this).children('.sublist').toggle();
    })

    //добавляет стили для чекбоксов множественного выбора
    $('.js-multiple-check').parent('div.form-check').addClass('check-row');
});