$(document).ready(function () {
    //initializes all datatables on the page
    $('div[data-datatable]').each(function () {
            initCustomDataTables($(this));
        }
    );
});

/**
 * Init datatables one
 * @param datatableElement
 */
function initCustomDataTables(datatableElement) {
    datatableElement.initDataTables(datatableElement.data('table_settings'),
        {
            searching: true,
            drawCallback: function (settings) {
                this.api().rows({page: 'current'}).column(0, {
                    search: 'applied',
                    order: 'applied'
                }).nodes().each(function (cell, i) {
                    cell.innerHTML = settings._iDisplayStart + i + 1;
                });
                $('.pagination a').addClass('item');
            },
        }).then(function (dt) {
            dt.on('draw', function () {
                    // Выделение красным строки пациента с анализами, вышедшими за пределы нормальных значений
                    $('.redRow').each(function () {
                        $(this).closest().attr('class', 'zapredel')
                    });
                });
        });
}