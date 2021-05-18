const {initCustomDataTables} = require("./initDatatable");
$(document).ready(function () {
    //initializes all datatables on the page
    $('div[data-datatable_prescription]').each(function () {
            initCustomDataTables($(this), false, false);
        }
    );
});