const FILE_EXAMPLE_ELEMENT_ID_PREFIX = 'jsFileWidgetExample';
const FILE_ELEMENT_CLASS = 'jsFile';
const FILE_VIEW_ELEMENT_ID_PREFIX = 'jsView';
const FILE_NAME_ELEMENT_ID_PREFIX = 'jsName';
const FILE_REMOVE_ELEMENT_ID_PREFIX = 'jsRemove';
const FILE_REMOVE_BUTTON_CLASS = 'js-remove-file';
const FILE_WIDGET_ELEMENT_ID_PREFIX = 'jsWidget';
const FILE_ADD_ELEMENT_ID_PREFIX = 'jsAddFile';
const FILE_UPLOAD_BUTTON_ID_PREFIX = 'jsBtnUpload';

/**
 * Create html for adding new file
 * @param filePos
 */
function createAddFile(filePos) {
    const FILE_EXAMPLE_TEMPLATE = '__name__';
    let newWidget = $('#' + FILE_EXAMPLE_ELEMENT_ID_PREFIX + '>div:first-child').html();
    newWidget = newWidget.replaceAll(FILE_EXAMPLE_TEMPLATE, filePos);
    let newFile =
        '<div class="'+ FILE_ELEMENT_CLASS +'" data-pos="' + filePos + '">' +
            '<div id="' + FILE_VIEW_ELEMENT_ID_PREFIX + filePos + '" style="display: none;">' +
                '<span id="' + FILE_NAME_ELEMENT_ID_PREFIX + filePos + '"></span>' +
                '<span id="'+ FILE_REMOVE_ELEMENT_ID_PREFIX + filePos +'">' +
                    '<button type="button" class="'+ FILE_REMOVE_BUTTON_CLASS +'"><i class="fa fa-times" aria-hidden="true"></i></button>' +
                '</span>' +
            '</div>' +
            '<div id="' + FILE_WIDGET_ELEMENT_ID_PREFIX + filePos +'" style="display: none;">' + newWidget + '</div>' +
            '<div id="' + FILE_ADD_ELEMENT_ID_PREFIX + filePos +'">' +
                '<button type="button" id="' + FILE_UPLOAD_BUTTON_ID_PREFIX + filePos + '">' + '<i class="fa fa-plus"></i> Файл' + '</button>' +
            '</div>' +
        '</div>';
    $("#fileBox").append(newFile);
    let fileInput = $('#' + FILE_WIDGET_ELEMENT_ID_PREFIX + filePos).find('input');
    $('#' + FILE_UPLOAD_BUTTON_ID_PREFIX + filePos).on('click', function () {
        fileInput.trigger('click');
    });

    fileInput.on('change', function () {
        let fileName = $(this).prop('files')[0].name;
        $('#' + FILE_NAME_ELEMENT_ID_PREFIX + filePos).append(fileName);
        $('#' + FILE_ADD_ELEMENT_ID_PREFIX + filePos).hide(); // todo попробовать удалять
        $('#' + FILE_VIEW_ELEMENT_ID_PREFIX + filePos).show();
        createAddFile(parseInt(filePos) + 1);
    });
}

$(document).ready(function () {
    const DEFAULT_FIRST_POSITION = 0;
    let fileBox = $("#fileBox");
    if (fileBox.length) {
        let lastFilePos = fileBox.children('.' + FILE_ELEMENT_CLASS).last().data('pos');
        createAddFile(typeof lastFilePos === "undefined" ? DEFAULT_FIRST_POSITION : ++lastFilePos);
        fileBox.on('click', '.' + FILE_REMOVE_BUTTON_CLASS, function removeFile() {
            $(this).closest('.' + FILE_ELEMENT_CLASS).remove();
        });
    }
});