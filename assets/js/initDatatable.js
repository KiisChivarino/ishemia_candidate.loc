$(document).ready(function () {
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
                $('.pagination a').addClass('item');
            },
        }).then(function(dt) {
        dt.on('draw', function() {
            // Выделение строки красным
            $('.redRow').each(function () {
                $(this).parent().parent().attr('class', 'zapredel')
            })
        })
    });
});