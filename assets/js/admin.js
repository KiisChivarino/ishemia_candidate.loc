import './app';

require('jquery-mask-plugin');
require('datatables');
import './select2';
import './tabs';
import '../css/admin.scss'
import './mask';

require('../images/operation-icon-1.svg');
require('../images/operation-icon-2.svg');
require('../images/operation-icon-3.svg');
require('../images/favicons/adm-fav.ico');

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
    //begin управление пациентом: добавление фильтра по городу в выбор больниц
    if ($('select').is('#form_patient_city')) {
        let city = $('#form_patient_city');
        setCity(city.val());
        city.on('change', (function () {
            setCity(city.val());
            $('#form_patient_hospital').select2entity();
        }));
    }
    //end управление пациентом: добавление фильтра по городу в выбор больниц

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

//set city value
function setCity(cityValue) {
    let formPatientHospital = $('#form_patient_hospital');
    let hospitalUrlString = formPatientHospital.data('ajax--url');
    formPatientHospital.data('ajax--url', hospitalUrlString.replace(/city=\d*/, "city=" + cityValue));
}