  $(document).ready(function() {

    var $container = $('div#medecin_telephones');
    var $elements=[ 'numero', 'type', 'sms', 'Token'];
    var indexTelephones = $container.find(':input').length;
    indexTelephones=indexTelephones / $elements.length;
    if (indexTelephones != 0) {
        $container.find('.rowTelephones').each(function() {
        ajouterLienSuppression($(this), '.rowTelephones');
      });
    }

   var $container = $('div#medecin_horaires');
    var $elements=[ 'jour', 'activite', 'debut', 'fin', 'texte', 'classe', 'internet'];
    var indexHoraires = $container.find(':input').length;
        console.log(indexHoraires);

    indexHoraires=indexHoraires / $elements.length;
      console.log('2', indexHoraires);
    if (indexHoraires != 0) {
        $container.find('.rowHoraires').each(function() {
        ajouterLienSuppression($(this), '.rowHoraires');
      });
    }

    applyStyle();

   $( ".btn-telephone" ).click(function(e) {
      var $container = $('div#medecin_telephones');
      ajouterTelephone($container, '.rowTelephones');
      applyStyle();
      e.preventDefault();

      return false;
    });

   $( ".btn-horaire" ).click(function(e) {
      var $container = $('div#medecin_horaires');
      ajouterHoraire($container, '.rowHoraires');
      applyStyle();
      e.preventDefault();

      return false;
    });

    function applyStyle() {
      $(".sel2").select2();
      $('.Gestimetimepicker').timepicker({ 'step': 15, 'timeFormat': 'H:i' });
      GestimeDatePicker.initDatePicker();
        $('.icheck').iCheck({
        checkboxClass: 'icheckbox_flat-aero',
        radioClass: 'iradio_flat-aero'
      });
    }

   function ajouterTelephone($container, $children) {
      indexTelephones++;
      var $prototype = $($container.attr('data-prototype').replace(/__tel_prot__/g, indexTelephones));
      ajouterLienSuppression($prototype, $children);
      $container.append($prototype);
    }

   function ajouterHoraire($container, $children) {
      indexHoraires++;
      var $prototype = $($container.attr('data-prototype').replace(/__hor_prot__/g, indexHoraires));
      ajouterLienSuppression($prototype, $children);
      $container.append($prototype);
    }

    function ajouterLienSuppression($prototype, $children) {
      $lienSuppression = $('<a href="#" class="btn btn-danger">Supprimer</a><div class="clear">');
      $($prototype).find('.btnSupp').append($lienSuppression);
      $lienSuppression.click(function(e) {
        $prototype.remove();
        e.preventDefault();

        return false;
      });
    }

  });

