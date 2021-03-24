//View hospitals for ROLE_DOCTOR_HOSPITAL in Admin
$(document).ready(function () {
    let hospitals = $('[data-hospital]');
    let roles = $('[data-form_staf_role_id]');
    if (roles.val() === 'ROLE_DOCTOR_HOSPITAL') {
        hospitals.attr('required', true);
    } else {
        hospitals.parent('li').hide();
    }
    roles.on('change', function () {
        if (roles.val() === 'ROLE_DOCTOR_HOSPITAL') {
            hospitals.parent('li').show();
            hospitals.attr('required', true);
        } else {
            hospitals.parent('li').hide();
            hospitals.empty();
            hospitals.removeAttr('required');
        }
    });
});