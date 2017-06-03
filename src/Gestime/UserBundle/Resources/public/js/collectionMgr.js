  $(document).ready(function() {

    var $container = $('div#medecin_telephones');
    var $elements=[ 'numero', 'type', 'sms', 'Token'];
    var index = $container.find(':input').length;
    index=index / $elements.length;
    if (index != 0) {
        $container.find('.rowTelephones').each(function() {
        ajouterLienSuppression($(this), '.rowTelephones');
      });
    }

   var $container = $('div#medecin_horaires');
    var $elements=[ 'jour', 'activite', 'debut', 'fin', 'texte'];
    var index = $container.find(':input').length;
    index=index / $elements.length;
    if (index != 0) {
        $container.find('.rowHoraires').each(function() {
        ajouterLienSuppression($(this), '.rowHoraires');
      });
    }

    var $container = $('div#medecin_horairesInternet');
    var $elements=[ 'jour', 'debut', 'fin', 'nbRdvMax'];
    var index = $container.find(':input').length;
    index=index / $elements.length;
    if (index != 0) {
      $container.find('.rowHorairesInternet').each(function() {
          ajouterLienSuppression($(this), '.rowHorairesInternet');
      });
    }

    applyStyle();

   $( ".btn-telephone" ).click(function(e) {
      var $container = $('div#medecin_telephones');
      ajouterPeriode($container, '.rowTelephones');
      applyStyle();
      e.preventDefault();

      return false;
    });

   $( ".btn-horaire" ).click(function(e) {
      var $container = $('div#medecin_horaires');
      ajouterPeriode($container, '.rowHoraires');
      applyStyle();
      e.preventDefault();

      return false;
    });

    $( ".btn-horaireInternet" ).click(function(e) {
      var $container = $('div#medecin_horairesInternet');
      ajouterPeriode($container, '.rowHorairesInternet');
      applyStyle();
      e.preventDefault();

      return false;
    });

    function applyStyle() {
      $(".sel2").select2();
    $('.icheck').iCheck({
      checkboxClass: 'icheckbox_flat-aero',
      radioClass: 'iradio_flat-aero'
    });
    }

   function ajouterPeriode($container, $children) {
      var $prototype = $($container.attr('data-prototype').replace(/__name__/g, index));
      ajouterLienSuppression($prototype, $children);
      $container.append($prototype);
      index++;
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

