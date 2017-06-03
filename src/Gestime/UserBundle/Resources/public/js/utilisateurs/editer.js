  function changeUserPassword($idUtilisateur, $newPassword) {
    var $url = Routing.generate('utilisateur_ajax_password',  { idUtilisateur:$idUtilisateur, newpassword:$newPassword } );
    var resultat = null;
    $.ajax({
      type: "POST",
      url: $url,
      async:false,
    }).done(function( result ) {
      console.log(result);
        resultat = result;
    });
    return resultat;
  }

  jQuery(document).ready(function () {
      var options = {
          onKeyUp: function (evt) {
              $(evt.target).pwstrength("outputErrorList");
          }
      };
      $('#password_password').pwstrength(options);

      $( "#changePassword" ).on( "submit", function( event ) {
          event.preventDefault();
          event.stopPropagation();
          resultat = changeUserPassword($('#password_userid').val() , $('#password_password').val() )
          if (resultat == 'success') {
            $('#statut').html('Mot de passe mis à jour.');
          }
      });
  });