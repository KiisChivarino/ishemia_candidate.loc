function createAddFile(fileCount, removeButton) {
    let newWidget = $("#patient_testing_patientTestingFiles").data('prototype');
    let template = '__name__';
    if (!newWidget) {
        if (fileCount === undefined) {
            fileCount = 1;
        } else {
            fileCount++;
        }
        template = '0';
        newWidget = $("#patient_testing_patientTestingFiles_0").html().toString();
        // newWidget = newWidget.replaceAll(template, fileCount);
    }
    newWidget = newWidget.replaceAll(template, fileCount);
    newWidget = "<div style='display:none'>" + newWidget + "</div>";
    let hideStuff = "";
    hideStuff += "<div id='jsRemove" + fileCount + "' style='display: none;'>";
    hideStuff += removeButton;
    hideStuff += "</div>";
    hideStuff += "<div id='jsPreview" + fileCount + "'>";
    hideStuff += "</div>";
    hideStuff += "<div>";
    hideStuff += "<button type='button' id='jsBtnUpload" + fileCount + "' class='btn btn-warning'>";
    hideStuff += "<i class='fa fa-plus'></i> Файл";
    hideStuff += "</button>";
    hideStuff += "</div>";

    $("#filesBox").append("<div>" + hideStuff + newWidget + "</div>");

    $("#jsBtnUpload" + fileCount).on('click', function (e) {
        $('#patient_testing_patientTestingFiles_' + fileCount + '_file').trigger('click');
    });

    $('#patient_testing_patientTestingFiles_' + fileCount + '_file').on('change', function () {
        let fileName = $(this).prop('files')[0].name;
        $("#jsPreview" + fileCount).append(fileName);
        $("#jsBtnUpload" + fileCount).hide();
        $("#jsRemove" + fileCount).show();
        createAddFile(parseInt(fileCount) + 1, removeButton);
    });
}

$(document).ready(function () {
    let fileBox = $("#filesBox");
    if (fileBox.length > 0) {
        let fileCount = $("#patient_testing_patientTestingFiles").data('filescount');
        let removeButton =
            "<button type='button' class='btn btn-danger btn-xs js-remove-file'>" +
            "<i class='fa fa-times' aria-hidden='true'></i>" +
            "</button>";
        createAddFile(fileCount, removeButton);
        fileCount++;
        fileBox.on('click', '.js-remove-file', function removeFile() {
            let fileNumber = $(this).parent().attr('id').replace('jsRemove', '');
            $(this).parent().parent().remove();
            $('#patient_testing_patientTestingFiles_' + fileNumber).parent('fieldset').remove();
        });
    }
});