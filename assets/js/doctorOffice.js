require('datatables');
import './select2';
import './select2entity';
import '../css/select2.min.css'
import './hospitalByCity';
import '../css/doctorOffice.css';
import './initDatatable';
import './fileUpload';
import './app';
import './mask';
import swal from 'sweetalert2';

require('../images/operation-icon-1.svg');
require('../images/operation-icon-2.svg');
require('../images/operation-icon-3.svg');
require('../images/favicons/doc-fav.ico');
require('fancybox')($);

let acc = document.getElementsByClassName("accordion");
let i;
for (i = 0; i < acc.length; i++) {
    if ($(acc[i]).hasClass('active')) {
        let panel = acc[i].nextElementSibling;
        panel.style.maxHeight = panel.scrollHeight + "px";
    }
    acc[i].addEventListener("click", function () {
        this.classList.toggle("active");
        let panel = this.nextElementSibling;
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

    //set datatable search from header input
    $('.js-main-search input').on('keyup', function () {
        $('#dt_filter input')
            .val($(this).val())
            .trigger('keyup');
    })

    // Creates delete button and hides empty selects for template items
    $('.deletable-parameter').each(function () {
        if ($(this).children('option').length <= 1) {
            $(this).hide()
        }
        $(this).prev().children().after('<button class="remove-parameter" style="margin-left: 5px">X</button>')
    })

    // Removes template item
    $('.remove-parameter').on('click', function () {
        $(this).parent().parent().remove()
    })

    //раскрытие текущего пункта меню
    $(".sublist .active").closest('ul').show();
    //выпадающий список в меню
    $(".sublist").on("click", (function () {
        $(this).children("ul").slideToggle()
    }));
});

// Функция уменьшающая количество уведомлений в меню на 1 для СМС пациента
function changeMenuPatientSmsCount() {
     var currentMessagesCount = parseInt($('.patientSMSCount').html());
     if (currentMessagesCount > 1) {
         $('.patientSMSCount').html(currentMessagesCount - 1);
     } else if (currentMessagesCount === 1) {
         $('.patientSMSCount').hide()
     }
}
// Подтверждение СМС пациента
$(document).on("click", ".processPatientSMS", function(){
    // Формируем ссылку для апи обработки сообщений пвциента на основе атрибута data-id в таблице пацента
    var request = $(this).attr('data-href');
    // Свал для подтверждения врачом, что он уверен, что хочет обработаь сообщение ппациента
    swal.fire({
        text: 'Подвердите обработку сообщения от пациента: ' + $(this).attr('data-name'),
        showCancelButton: true,
        confirmButtonText: 'Подтвердить',
        cancelButtonText: 'Отмена',
        icon: "info",
        confirmButtonColor: '#047df7',
        cancelButtonColor: '#b7b7b7',
        showLoaderOnConfirm: true,
        // При нажатии на кнопку Подтвердить отправялем реквест, и получаем ответ в json
        preConfirm: () => {
            return fetch(`${request}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(response.statusText)
                    }
                    return response.json()
                })
                .catch(error => {
                    swal.showValidationMessage(
                        `Упс! Что-то пошло не так...`
                    )
                })
        },
        allowOutsideClick: () => !swal.isLoading()
    }).then((result) => {
        // Распарсиваем ответ в зависимости от кода ошибки и запускаем свалы
        switch (result.value.code) {
            case 200:
                swal.fire('Успешно!', 'Уведомление пацеинта: ' + $(this).attr('data-name') + ' помечано прочитанным!', 'success')
                // Обновляем таблицу
                $("#dt").DataTable().ajax.reload();
                // Уменьшаем на 1 количество уведомлений в меню
                changeMenuPatientSmsCount()
                break;
            case 300:
                swal.fire('Ошибочка вышла!', 'Уведомление пацеинта: ' + $(this).attr('data-name') + ' уже подтвердил кто-то другой!', 'warning')
                $("#dt").DataTable().ajax.reload();
                changeMenuPatientSmsCount()
                break;
            default:
                swal.fire({
                    icon: 'error',
                    title: `Упс!`,
                    text: 'Что-то пошло не так... Попробуйте еще раз.',
                })
                $("#dt").DataTable().ajax.reload();
                break;
        }
    })
});



