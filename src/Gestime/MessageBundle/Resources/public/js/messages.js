$().ready(function() {

  function AffichageCompteurs(compteurs) {
    if ((compteurs.nonlus) == 0) {
      $('#cptNonLusInbox').hide();
    } else {
      $('#cptNonLusInbox').show();
      $('#cptNonLusInbox').text(compteurs.nonlus);
    }
    if ((compteurs.nonluFavoris) === 0) {
      $('#cptNonLusFavoris').hide();
    } else {
      $('#cptNonLusFavoris').show();
      $('#cptNonLusFavoris').text(compteurs.nonluFavoris);
    }
    $('#cptMessages').html('Messages à lire : ' + compteurs.nonlus);
  }

  function majCompteurs(){
    var $url = Routing.generate('messages_ajax_compteurs');
        $.ajax({
          type: "POST",
          url: $url
        }).done(function( result ) {
            if(result.success) {
              AffichageCompteurs(result.compteurs);
            }
        });
  }

  $('.icheck').iCheck({
  checkboxClass: 'icheckbox_flat-aero',
  radioClass: 'iradio_flat-aero'
  });

  $('.icon-refresh').click(function() {
    location.reload();
  });

  $('#btn-read').click(function() {
    $('input[type=checkbox]').each(function () {
      if (this.checked) {
        $msgId = $(this).closest('tr').find('.icheck').val();
        $row = $(this).closest('tr');
        $set = "set";
        if ($set =="set" ) {
              $row.attr('class', "row read");
            }
            else {
              $row.attr('class', "row unread");
            }
        var $url = Routing.generate('messages_ajax_set_read', { set:$set , idMessage:$msgId } );
        $.ajax({
          type: "POST",
          url: $url
        }).done(function( result ) {
            if(result.success) {
              majCompteurs();
            }
        });
      }
    });
  });


  function suppr($idMessage) {
    var $url = Routing.generate('messages_ajax_suppr',  { idMessage:$idMessage } );
    $.ajax({
      type: "POST",
      url: $url
    }).done(function( result ) {
        if(result.success) {
          location.reload();
        }
    });
    return true;
  }

  $('#btn-suppr-view').click(function() {
        if (suppr($('#idMessage').text() )){

        }
  });

  $('#btn-suppr').click(function() {
    $('input[type=checkbox]').each(function () {
      if (this.checked) {
        if (suppr($(this).val() )){

        }
      }
    });
  });

  $('.favori').click(function(){
    $msgId = $(this).closest('tr').find('.icheck').val(); //Si on clique sur favori depuis la liste
    if (typeof $msgId == 'undefined') {
      $msgId = $('#idmessage').html();

    }
    $icon = $(this);
    $set = $icon.hasClass( "icon-star-empty" ) ? "set":"unset";
    var $url = Routing.generate('messages_ajax_set_favori', { set:$set , idMessage:$msgId } );
    $.ajax({
      type: "POST",
      url: $url
    }).done(function( result ) {
        if(result.success) {
          if ($set =="set" ) {
            $icon.attr('class', "favori icon-star");
          }
          else {
            $icon.attr('class', "favori icon-star-empty");
          }
          majCompteurs();
        }
    });
  });

  var selectMedecin = $("#MessageListSearch_medecin").select2();
    selectMedecin.on("change", function(e) {
      $("#form_recherche").submit();
  });

  var selectAction = $("#MessageListSearch_action").select2();
  selectAction.on("change", function(e) {
    $("#form_recherche").submit();
  });

  majCompteurs();

});
