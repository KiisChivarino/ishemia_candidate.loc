const {initCustomDataTables} = require("./initDatatable");
$(document).ready(function () {
    //initializes all datatables on the page
    $('div[data-datatable]').each(function () {
            initCustomDataTables($(this));
        }
    );
});