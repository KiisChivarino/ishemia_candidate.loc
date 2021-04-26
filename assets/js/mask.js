require('jquery-mask-plugin');
$(document).ready(function () {
    $('.phone_us').mask('+7 (000) 000-0000');
    $('.snils_us').mask('00-000-000-00');

    // очищаем маски 🧹
    $(document).on('submit','form',function(){
        $('.phone_us').unmask();
    });
});
