function removeFile(ob)
{
    console.log('test');
    console.log(ob);
    ob.parent().parent().remove();
}

function createAddFile(fileCount, removeButton)
{
    // grab the prototype template
    let newWidget = $("#patient_testing_patientFiles").data('prototype');

    // replace the "__name__" used in the id and name of the prototype
    newWidget = newWidget.replace(/__name__/g, fileCount);

    newWidget = "<div style='display:none'>" + newWidget + "</div>";

    let hideStuff = "";
    hideStuff += "<div class='col col-xs-1' id='jsRemove" + fileCount + "' style='display: none;'>";
    hideStuff += removeButton;
    hideStuff += "</div>";

    hideStuff += "<div class='col col-xs-11' id='jsPreview" + fileCount + "'>";
    hideStuff += "</div>";

    hideStuff += "<div class='col col-xs-12'>";
    hideStuff += "<button type='button' id='jsBtnUpload" + fileCount + "' class='btn btn-warning'>";
    hideStuff += "<i class='fa fa-plus'></i> {{ 'document' | trans }}";
    hideStuff += "</button>";
    hideStuff += "</div>";

    $("#filesBox").append("<div class='row'>" + hideStuff + newWidget + "</div>");

    // $("#filesBox").append("<div class='row'>" + "<div class='col-md-1'>" + removeButton + "</div><div class='row'>" + "<div class='col-md-10'>" + newWidget + "</div></div>");

    $("#jsBtnUpload" + fileCount).on('click', function(e){
        $('#patient_testing_patientFiles_' + fileCount + '_file').trigger('click');
    });

    // Once the file is added
    $('#patient_testing_patientFiles_' + fileCount + '_file').on('change', function() {
        // Show its name
        let fileName = $(this).prop('files')[0].name;
        $("#jsPreview" + fileCount).append(fileName);
        // Hide the add file button
        $("#jsBtnUpload" + fileCount).hide();
        // Show the remove file button
        $("#jsRemove" + fileCount).show();
        // Create another instance of add file button and company
        createAddFile(parseInt(fileCount)+1, removeButton);
    });
}

$(document).ready(function(){
    let fileCount = $("#patient_testing_patientFiles").data('filescount');
    let removeButton = "<button type='button' class='btn btn-danger btn-xs js-remove-file'><i class='fa fa-times' aria-hidden='true'></i></button>";
    $("#filesBox").on('click', '.js-remove-file', function removeFile()
    {
        console.log(this);
        $(this).parent().parent().remove();
    });

    createAddFile(fileCount, removeButton);
    fileCount++;
});