// calendar
$(document).ready(function () {
    $('.datepicker').datepicker({
        startDate: new Date('Mart 12, 2020'),
    })

    const $datepicker = $('.js-datepicker');
    const curentTime = $datepicker.val().split('.')
    let datepicker = $datepicker.datepicker({
        startDate: new Date(curentTime[2], curentTime[1] - 1, curentTime[0])
    });
    datepicker.selectDate(new Date(curentTime[2], curentTime[1] - 1, curentTime[0]));
})

$(document).ready(function () {
    $('.closed-popup').on('click', function () {
        $('.popup').removeClass('active');
    })
})
