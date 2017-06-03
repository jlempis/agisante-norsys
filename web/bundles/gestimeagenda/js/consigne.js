function applyStyle() {
    $(".sel2").select2();
    $(".sel2").select2();
    $('.icheck').iCheck({
      checkboxClass: 'icheckbox_flat-aero',
      radioClass: 'iradio_flat-aero'
    });
}


$(document).ready(function() {
    $('#evenement_patient_form_nom').autocomplete({
        select: function( event, ui ) {
            $('#evenement_patient_form_id').val(ui.item.Id);
            return false;
        },
    });

    applyStyle();
    GestimeDatePicker.initDatePicker();

});
