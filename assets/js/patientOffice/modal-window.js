$(function () {
    let modalButtons = $('.js-open-modal'),
        overlay = $('.overlay');

    modalButtons.each(function (index, element) {
        $(element).on("click", function (event) {

            event.preventDefault();
            event.stopPropagation();

            let modalId = $(this).attr("data-modal"),
                modalElem = $('.modal[data-modal="' + modalId + '"]');
            overlay.addClass('active');
            modalElem.addClass('active');

        })

    })

    $(document).on('keyup',function (e) {
        if (e.keyCode === 27) {
            $('.modal.active').removeClass('active');
            overlay.removeClass('active');
        }
    })

    overlay.on("click", function () {
        $('.modal.active').removeClass('active');
        overlay.removeClass('active');
    });

});
