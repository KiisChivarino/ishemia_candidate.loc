let button = $('#confirmedByStaff')
let checkbox = $('.isProcessedByStaffType');

checkbox.closest('li').css('display', 'none');

button.click(function () {
    checkbox.trigger('click');
    $('.btn--mode').trigger('click');
});
