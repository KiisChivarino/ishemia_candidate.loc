import './app';

const Swal = require('sweetalert2')

const Toast = Swal.mixin({
    toast: true,
    position: 'top-right',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
})

//Инициализируем редактирование элементов таблицы DataTable
export function initialDataTableEdit() {
    $(document).ready(function () {
        $('div[data-datatable]').on('draw.dt', function () {
            editTableInit();
        });
    })
}

//Инициализируем редактирование элементов обычной таблицы
export function initialTableEdit() {
    editTableInit();
}

function editTableInit() {
    $('.xEditable').each(function (i, elem) {
        initialCLickEvent(elem);
    })
}

function initialCLickEvent(tableContent) {
    $(tableContent).on('click', function () {
        let url = $(tableContent).data('url');
        $.ajax({
            url: url,
            method: 'post',
            dataType: 'html',
            success: function (serverResponse) {
                let td = $(tableContent).parent('td');
                if (td === null) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Произошла ошибка, попробуйте повторить позже'
                    })
                    return null;
                }
                $(serverResponse).appendTo(td);
                $(td).find('.xEditable').css('display', 'none');
                let form = td.children('form');
                form.attr('data-url', url);
                let dataInput = form.find('.xEditableField');
                dataInput.focus();
                dataInput.on('focusout', () => {
                    sendForm(form);
                });
                form.on('submit', (event) => {
                    sendForm(form);
                    event.preventDefault();
                });
            }
        });
    })
}

function sendForm(form) {
    let urlRequest = form.data('url');
    $.ajax({
        url: urlRequest,
        data: form.serializeArray(),
        method: 'post',
        dataType: 'html',
        success: function (serverResponse) {
            let request = JSON.parse(serverResponse);
            if (request.code === 200) {
                Toast.fire({
                    icon: 'success',
                    title: request.message
                })
                if (form.parent('td') === null) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Произошла ошибка, попробуйте повторить позже'
                    })
                    return null;
                }
                let parentTd = form.parent('td');
                parentTd.find('.xEditable').html(request.renderValue).css('display', 'flex');
                form.remove();
            } else {
                let errorList = '';
                $.each(request.error, function (index, item) {
                    errorList += item;
                });
                Toast.fire({
                    icon: 'error',
                    title: errorList === '' ? "Произошла ошибка, попробуйте повторить позже" : errorList
                })
            }
        }
    });
}
