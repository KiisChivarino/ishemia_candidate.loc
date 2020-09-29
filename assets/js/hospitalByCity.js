$(document).ready(function () {
//begin управление пациентом: добавление фильтра по городу в выбор больниц
    if ($('select').is('#form_patient_city')) {
        let city = $('#form_patient_city');
        console.log(city);
        setCity(city.val());
        city.on('change', (function () {
            setCity(city.val());
            $('#form_patient_hospital').select2entity();
        }));
    }
//end управление пациентом: добавление фильтра по городу в выбор больниц
//set city value
    function setCity(cityValue) {
        let formPatientHospital = $('#form_patient_hospital');
        let hospitalUrlString = formPatientHospital.data('ajax--url');
        formPatientHospital.data('ajax--url', hospitalUrlString.replace(/city=\d*/, "city=" + cityValue));
    }
});