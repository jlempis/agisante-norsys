  $(".sel2").select2();
    GestimeDatePicker.initDatePicker();

  $('#btn-impression').click(function(event) {
    event.preventDefault();
    event.stopPropagation();
    var $route='pdf_'+$('#idRapport').data('type');
    if ($('#rapportFilter_medecin').select2().val() !="") {
      var $url = Routing.generate($route,  { medecinId:$('#rapportFilter_medecin').select2().val(),
                                                 debut:$('#rapportFilter_debut').val(),
                                                 fin:$('#rapportFilter_fin').val()
                                              }
                                  );
      location.replace($url);
    } else {
      alert('Vous devez sélectionner un médecin pour imprimer.')
    }
  });
