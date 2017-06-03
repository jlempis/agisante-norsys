  $(document).ready(function() {

    var $container = $('div#evenement_patient_adresses');
    var $elements=[ 'voie', 'complement', 'codePostal', 'Ville'];
    var indexAdresses = $container.find(':input').length;

    indexAdresses=indexAdresses / $elements.length;
    if (indexAdresses != 0) {
        $container.find('.rowAdresses').each(function() {
        ajouterLienSuppression($(this), '.rowAdresses');
      });
    }

    applyStyle();

   $( ".btn-adresse" ).click(function(e) {
      if (indexAdresses == 0) {
        var $container = $('div#evenement_patient_adresses');
        ajouterAdresse($container, '.rowAdresses');
        applyStyle();
        e.preventDefault();
      }
      $('#evenement_patient_adresses_1_codePostal').change(function() {
        $('#evenement_patient_adresses_1_ville').select2().html(getComboVillesByCpostal($('#evenement_patient_adresses_1_codePostal').val())).trigger("change");      });

      return false;
    });

   $( ".btn-adresse-suppr" ).click(function(e) {
      if (indexAdresses == 0) {
        var $container = $('div#evenement_patient_adresses');
        ajouterAdresse($container, '.rowAdresses');
        applyStyle();
        e.preventDefault();
      }

      return false;
    });

    function applyStyle() {
      $(".sel2").select2();
    }

    function ajouterAdresse($container, $children) {
      indexAdresses++;
      var $prototype = $($container.attr('data-prototype').replace(/__adr_prot__/g, indexAdresses));
      ajouterLienSuppression($prototype, $children);
      $container.append($prototype);
      $( ".btn-adresse" ).hide();
      $( "#btn-supp" ).show();
    }

    function ajouterLienSuppression($prototype, $children) {
      $lienSuppression = $('<div id="btn-supp" style="text-align:center"><a href="#" class="btn btn-danger">Supprimer l\'adresse</a></div><div class="clear">');
      $('#btnSupp').html($lienSuppression);
      $lienSuppression.click(function(e) {
        $prototype.remove();
        e.preventDefault();
        indexAdresses=0;
        $( "#btn-supp" ).hide();
        $( ".btn-adresse" ).show();

        return false;
      });
    }

  });

