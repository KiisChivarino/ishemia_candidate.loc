const {initCustomDataTables} = require("./initDatatable");
$(function () {
    //initializes all datatables on the page
    $('div[data-datatable]').each(function () {
            initCustomDataTables($(this));
        }
    );
});