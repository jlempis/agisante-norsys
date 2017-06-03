  $(document).ready(function() {

      var $container = $('div#infosComplementaires_horairesInternet');
      var $elements=[ 'jour', 'debut', 'fin', 'nbRdvMax'];
      var indexHorairesInternet = $container.find(':input').length;

      indexHorairesInternet =indexHorairesInternet / $elements.length;
      if (indexHorairesInternet != 0) {
          $container.find('.rowHorairesInternet').each(function() {
              ajouterLienSuppression($(this), '.rowHorairesInternet');
          });
      }

      var $container = $('div#infosComplementaires_infosDoc24_tarification');
      var $elements=[ 'acte', 'mini', 'maxi'];
      var indexTarifs = $container.find(':input').length;

      indexTarifs =indexTarifs / $elements.length;
      if (indexTarifs != 0) {
          $container.find('.rowTarif').each(function() {
              ajouterLienSuppression($(this), '.rowTarif');
          });
      }

      var $container = $('div#infosComplementaires_infosDoc24_transport');
      var $elements=[ 'type', 'numeroLigne', 'arret'];
      var indexTransports = $container.find(':input').length;

      indexTransports = indexTransports / $elements.length;
      if (indexTransports != 0) {
          $container.find('.rowTransport').each(function() {
              ajouterLienSuppression($(this), '.rowTransport');
          });
      }

      var $container = $('div#infosComplementaires_infosDoc24_photosCabinets');
      var $elements=[ 'photo'];
      var indexPhotosCabinet = $container.find(':input').length;

      indexPhotosCabinet = indexPhotosCabinet / $elements.length;
      if (indexTransports != 0) {
          $container.find('.rowPhotoCabinet').each(function() {
              ajouterLienSuppression($(this), '.rowPhotoCabinet');
          });
      }


    applyStyle();

    $( ".btn-horaireInternet" ).click(function(e) {
      var $container = $('div#infosComplementaires_horairesInternet');
      ajouterHoraireInternet($container, '.rowHorairesInternet');
      applyStyle();
      e.preventDefault();

      return false;
    });

    $( ".btn-tarif" ).click(function(e) {
      var $container = $('div#infosComplementaires_infosDoc24_tarification');
        ajouterTarif($container, '.rowTarif');
      applyStyle();
      e.preventDefault();

      return false;
    });

    $( ".btn-transport" ).click(function(e) {
      var $container = $('div#infosComplementaires_infosDoc24_transport');
      ajouterTransport($container, '.rowTransport');
      applyStyle();
      e.preventDefault();

      return false;
    });

    $( ".btn-photoCabinet" ).click(function(e) {
      var $container = $('div#infosComplementaires_infosDoc24_photosCabinets');
      ajouterPhotoCabinet($container, '.rowPhotoCabinet');
      applyStyle();
      e.preventDefault();

      return false;
    });

    function applyStyle() {
        $(".sel2").select2();
        $('.Gestimetimepicker').timepicker({ 'step': 15, 'timeFormat': 'H:i' });
        $('.fileinput').uniform()
        $('.icheck').iCheck({
        checkboxClass: 'icheckbox_flat-aero',
        radioClass: 'iradio_flat-aero'
      });
    }

    function ajouterHoraireInternet($container, $children) {
      indexHorairesInternet++;
      var $prototype = $($container.attr('data-prototype').replace(/__horin_prot__/g, indexHorairesInternet));
      ajouterLienSuppression($prototype, $children);
      $container.append($prototype);
    }

    function ajouterTarif($container, $children) {
      indexTarifs++;
      var $prototype = $($container.attr('data-prototype').replace(/__tarif_prot__/g, indexTarifs));
      ajouterLienSuppression($prototype, $children);
      $container.append($prototype);
    }

    function ajouterTransport($container, $children) {
      indexTransports++;
      var $prototype = $($container.attr('data-prototype').replace(/__transport_prot__/g, indexTransports));
      ajouterLienSuppression($prototype, $children);
      $container.append($prototype);
    }

    function ajouterPhotoCabinet($container, $children) {
        indexPhotosCabinet++;
        var $prototype = $($container.attr('data-prototype').replace(/__photoCabinet_prot__/g, indexPhotosCabinet));
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

