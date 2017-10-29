var formIsSubmitted = false;

function setCopiedEventsDraggable() {
    $('.epingle-event').each(function() {
        var eventObject;
        eventObject = {
            title: $.trim($(this).text()),
            type: $(this).data("type"),
            idType: $(this).data("id"),
            duree: $(this).data("duree"),
            couleur: $(this).data("couleur"),
            libelle: $(this).data("libelle"),
            evtcopie: $(this).data("copy")
        };
        if ($(this).data("copy") == '1') {
            eventObject.title = $(this).data("libelle");
        }
        $(this).data('eventObject', eventObject);
        $(this).draggable({
            zIndex: 999,
            revert: true,
            revertDuration: 0
        });
    });
}

function editEvent_(rdvId) {
    var $rdv = $.parseJSON(getEvenement(rdvId))[0];
    var $rdvParam = $.parseJSON(getEvenement(rdvId))[2];
    var $nomutimodif = '';
    if ($rdv.updatedBy !== null) {
        $nomutimodif = $rdv.updatedBy.prenom + ' ' + $rdv.updatedBy.nom;
    }

    var eventObject = {
        idEvenement: $rdv.idEvenement,
        medecinId: $rdv.medecin.idMedecin,
        type: $rdv.type,
        nonExcuse: $rdv.nonExcuse,
        rappel: $rdv.rappel,
        nouveauPatient: $rdv.nouveauPatient,
        start: moment($rdv.debutRdv.date),
        end: moment($rdv.finRdv.date),
        idType: $rdvParam.idParametre,
        objet: $rdv.objet,
        nomutimodif: $nomutimodif,
        datemodif: moment($rdv.updated.date).format('DD/MM/YYYY à HH:mm')
    };

    if ($rdv.patient !== null) {
        var eventPatientObject = {
            idCivilite: $rdv.patient.civilite,
            nom: $rdv.patient.nom,
            prenom: $rdv.patient.prenom,
            nomJF: $rdv.patient.nomJF,
            telephone: $rdv.patient.telephone,
            adresse: $rdv.patient.adresse
        };
        var eventObject = $.extend({}, eventObject, eventPatientObject);

    }

    $('#btn-priseRdv').click();
    bindFormRdv(eventObject);
}

function editEvent(e, rdvId) {
    e.preventDefault();
    e.stopPropagation();
    $(e.target).closest('.qtip').qtip().hide();
    editEvent_(rdvId);
    return false;
}

function afficheBoutonSuppr(rdv) {
    //Affichage des boutons Suppr et Epingler que si on est en modif
    if (rdv.idEvenement > 0) {
        $$('#groupe-boutons-modif').show()
    } else {
        $$('#groupe-boutons-modif').hide();
    }
}

function texteBtnEnregistrer(rdv) {
    //Affichage des boutons Suppr et Epingler que si on est en modif
    if ((typeof rdv.idEvenement !== "undefined") && (rdv.idEvenement >0)) {
        $$('#btn_enregistrer').html('Modifier le rendez-vous');
    } else {
        $$('#btn_enregistrer').html('Créer le rendez-vous');
    }
}

function afficheDerniereModification(rdv) {
    var infos = $('#infos');
    infos.html('');
    if ((typeof rdv.nomutimodif !== "undefined") && (rdv.nomutimodif !== '')) {
        infos.html('Dernière modification le ' + rdv.datemodif + ' par ' + rdv.nomutimodif);
    }
}

function initFormRdv(idmedecin) {
    $$('#evenement_idEvenement').val('');
    $$('#evenement_medecin').select2("val", idmedecin);
    $$('#evenement_debutRdv_datepicker').datepicker('setDate', new Date());
    $$('#evenement_heureFin').val('');
    $$('#evenement_type').select2("val", 35); //29=index Consultation
    $$('#evenement_objet').val('');
    $$('#evenement_patient_civilite').select2();
    $$('#evenement_patient_nomJF').val('');
    $$('#evenement_patient_nom').val('');
    $$('#evenement_patient_prenom').val('');
    $$('#evenement_patient_telephone').val('');
    $$('#evenement_heureDebut').val('').focus();
    $$('#evenement_patient_adresse').val('');

    $$("#evenement_rappel").iCheck('uncheck');
    $$('#evenement_nonExcuse').iCheck('uncheck');

    $('#consignes').html('');
    $('#nonExcuses').html('');
}

function bindFormRdv(rdv) {
    var objet = $('#evenement_objet');

    $$('#evenement_idEvenement').val(rdv.idEvenement);
    $$('#evenement_medecin').select2("val", rdv.medecinId);
    $$('#evenement_debutRdv_datepicker').datepicker('setDate', rdv.start.toDate());
    $$('#evenement_heureDebut').val(rdv.start.format('HH:mm'));
    $$('#evenement_heureFin').val(rdv.end.format('HH:mm'));
    $$('#evenement_type').select2("val", rdv.idType);
    objet.val(rdv.objet);
    $$('#evenement_patient_civilite').select2("val", rdv.idCivilite);
    $$('#evenement_patient_nomJF').val(rdv.nomJF);
    $$('#evenement_patient_nom').val(rdv.nom);
    $$('#evenement_patient_prenom').val(rdv.prenom);
    $$('#evenement_patient_telephone').val(rdv.telephone);


    if (rdv.rappel) {
        $$("#evenement_rappel").iCheck('check');
    }
    if (rdv.nonExcuse) {
        $$("#evenement_nonExcuse").iCheck('check');
    }
    if (rdv.nouveauPatient) {
        $$("#evenement_nouveauPatient").iCheck('check');
    }

    $$('#evenement_patient_adresse').val(rdv.adresse);

    texteBtnEnregistrer(rdv)
    afficheBoutonSuppr(rdv);
    afficheDerniereModification(rdv);
    objet.focus();
}

function supprimeEvent(e, rdvId) {
    if (confirm("Confirmez-vous la suppression de ce rendez-vous ?")) {
        deleteEvent(rdvId);
        $(e.target).closest('.qtip').qtip().hide();
        rafraichissementAgenda();
    }
}

//***********************************************
//*  Gestion des rendez-vous épinglés
//*  Mise en session, Suppression de la session
//*  Affichage apres modification
//***********************************************

function epingleRdv(e, $eventId) {
    saveEventInSession(e, $eventId);
    $(e.target).closest('.qtip').qtip().hide();
}

function enleveRdvEpingle($eventId) {
    deleteEventInSession($eventId);
}

function afficheRdvEpingles($events) {
    if ($events !== false) {
        $('#zone-depot-rdv').html('');

        $.each($events, function(index, event) {

            var $html;
            $html = '<div class="epingle-event  ui-draggable ui-draggable-handle external-event ' + event.couleur + '" style="position: relative;" data-type="';
            $html = $html + event.type + '" data-duree="' + event.duree + '" data-couleur="' + event.couleur + '"  data-id="' + event.id + '" data-libelle="' + event.libelle + '" data-copy="1" style="text-align:left;">';
            $html = $html + '<div class="epingle-drag"><i class="drag-event icon-move"></i></div>';
            $html = $html + '<div class="epingle-infos"><i class=" icon-calendar"></i> ' + moment(event.debut.date).format("D/M/YYYY");
            $html = $html + '<br /><i class=" icon-time"></i> ' + moment(event.debut.date).format("HH:mm");
            $html = $html + '-' + moment(event.fin.date).format("HH:mm") + '</div></div>';
            $html = $html + '<div class="epingle-actions"><a href="#" onclick="return editEvent(event,' + event.id + ')"> Editer</a><a href="#" onclick="return enleveRdvEpingle(' + event.id + ')"> Enlever</a></div></div>';
            $('#zone-depot-rdv').append($html);
        });

        setCopiedEventsDraggable();
    }
}

//***********************************************
//*
//***********************************************

function rafraichissementAgenda(full) {

    full = (typeof full === "undefined") ? false : full;

    var calendar = $('#calendar-holder');
    calendar.fullCalendar('refetchEvents');
    if (full) {
        var currentView = calendar.fullCalendar('getView').name;
        var currentDate = calendar.fullCalendar('getDate');
        calendar.fullCalendar('destroy');
        calendar.fullCalendar(optionsCalendar);
        calendar.fullCalendar('changeView', currentView);
        calendar.fullCalendar('gotoDate', currentDate);
    }
}

function saiseEntrepriseAutocomplete(item) {
    saiseAutocomplete(item);
    $('#evenement_patient_nom').val(item.Nom);
}

function saiseAutocomplete(item) {
    $$('#evenement_patient_prenom').val(item.Prenom);
    $$('#evenement_patient_telephone').val(item.Telephone);
    $$('#evenement_patient_civilite').select2("val", item.IdCivilite);
    $$('#evenement_patient_nomJF').val(item.nomJF);
    $$('#evenement_patient_adresse').val(item.adresse);
    setRappelSMS();


}

$(".tooltip-wrapper").hover(function() {
    $(this).addClass("hover");
}, function() {
    $(this).removeClass("hover");
});

function applyStyle() {
    $('.icheck').iCheck({
        checkboxClass: 'icheckbox_flat-aero',
        radioClass: 'iradio_flat-aero'
    });
}

function afficheConsignes($idPatient) {
    var consignes = getConsignesPatient($idPatient);
    if (consignes.length > 0) {
        var $txtHtml = '<div class="alert alert-danger" style="margin:10px;"><button type="button" class="close" data-dismiss="alert">×</button>';
        $.each(consignes, function() {
            $txtHtml += this.description + '<br>';
        });
        $txtHtml += '</div>';
        $('#consignes').html($txtHtml);
    } else {
        $('#consignes').html('');
    }
}

function afficheNonExcuses($idMedecin, $idPatient) {
    var nonExcuses = getNonExcusesPatient($idMedecin, $idPatient);
    if (nonExcuses.length > 0) {
        var $txtHtml = '<div class="alert alert-info" style="margin:10px;"><button type="button" class="close" data-dismiss="alert">×</button>';
        $.each(nonExcuses, function() {
            $txtHtml += ' <strong>' + moment(this.debutRdv.date).locale("fr").format('LLLL') + '</strong><br>';
        });
        $txtHtml += '</div>';
        $('#nonExcuses').html($txtHtml);
    } else {
        $('#nonExcuses').html('');
    }
}

function coloreJoursAbsences($idMedecin) {
    var $listAbsences = getAbsencesMedecin($idMedecin);
    $(".Gestimedatepicker").datepicker('option', 'beforeShowDay', function(d) {
        return renderCalendarCallback(d, $listAbsences)
    });
}

function renderCalendarCallback(d, $listAbsences) {
    var datestring = jQuery.datepicker.formatDate('yy-mm-dd', d);
    if (typeof datestring !== "undefined") {
        var hindex = $.inArray(datestring, $listAbsences[0]);
        if (hindex > -1) {
            return [true, 'ui-datepicker-absence', $listAbsences[1][hindex]];
        }
        return [true, ''];
    }
}

function effaceErreurs(clearStatus) {
    $("[id^=error_]").addClass('invisible').removeClass('erreur').html('');
    if (clearStatus) {
        $('#status').html('');
    }
}

function afficheErreur(key, value) {
    $("[id$=" + 'error_' + key + "]").html(value).removeClass('invisible').addClass('erreur');
    var elt = $("[id^=" + 'error_' + "].erreur").first().data("element");
    $('#' + elt).focus();
}

function parcoursErreurs(erreurs) {
    $.each(erreurs, function(key, value) {
        if (key == "patient") {
            $.each(value, function(key, value) {
                afficheErreur(key, value);
            });
        }
        afficheErreur(key, value);
    });
}

function submitEvent($form, callback) {
    var $url = $form.attr('action');
    var values = {};
    $.each($form.serializeArray(), function(i, field) {
        values[field.name] = field.value;
    });
    $.ajax({
        type: "POST",
        url: $url,
        data: values
    }).done(function(data) {
        if (data.status == 'success') {
            $('#status').html('Rendez-vous enregistré avec succès.');
            rafraichissementAgenda();
            fermeFenetreRendezVous(event);
        } else {
            $('#status').html('Merci de corriger les données en erreur.');
            parcoursErreurs(data.errors);
        }
    });
}

function fermeFenetreRendezVous(e) {
    if ($('body.modal-open').length) {
        e.preventDefault();
        e.stopPropagation();
        $('.modal').modal('hide');
    }
}

function setRappelSMS() {
    if ($$("#evenement_patient_telephone").val().substr(0, 2) == "06" || $("#evenement_patient_telephone").val().substr(0, 2) == "07") {
        $$("#evenement_rappel").iCheck('check');
    } else {
        $$("#evenement_rappel").iCheck('uncheck');
    }
}
function displayMonth(event) {

    var html ='';

    if (event.type !='P') {
        html += '<div class="dc-event dc-event-type-aggregate dc-dom-resource-element ' + 'type-' + event.type + '">';
        html += '<div class="dc-event-inner" style="position: initial;    padding-bottom: 2px;">';
        html += '<div class="dc-event-title">';
        html += '<span>' + event.title + '</span>';
        html += '</div>';
        html += '<div class="dc-event-notes"></div>';
        html += '    </div>';
        html += '    </div>';
    } else {
        html += '<div class="dc-event dc-event-type-blck dc-dom-resource-element ' + 'type-' + event.type + '">';
        html += '<div class="dc-event-inner"  style="position: initial;">';
        html += '   <div class="dc-event-title">';
        html += '    <span class="dc-event-time">' + event.start.format('HH:mm') + '</span>';
        html += '<span>' + event.objet + '</span>';
        html += '</div>';
        html += '<div class="dc-event-notes"></div>';
        html += '    </div>';
        html += '    </div>';
    }

    return html;
}

function displayEvent(event) {
    var html =  '<div class="dc-events-global ';

    if (event.type != 'P') {
        html += 'type-rdv ' + 'type-' + event.type;
    } else {
        html += 'type-Absence';
    }
    html += '">';
    html += '<div class="dc-events">';
    html += '<div class="dc-event dc-event-type-appt  dc-dom-resource-element">';
    html += '<div class="dc-event-inner" style="position: absolute;">';
    html += '<div class="dc-event-title">';
    html += '<span class="dc-event-time">' + event.start.format('HH:mm') + '</span>';

    if (event.type != 'P') {
        html += '<span class="dc-event-title-last-name">' + event.nom + '</span>';
        html += '<wbr>';
        html += '<span class="dc-event-title-first-name">' + event.prenom + '</span>';
    }
    if (event.nouveauPatient) {
        html += '<i class="icon-user dc-event-new-patient"></i>';
    }

    if (event.type == 'W') {
        html += '<span class="dc-event-internet">@</span>';
    }

    html += '</div>';
    html += '<div class="dc-event-notes">' + event.objet + '</div>';
    html += '</div></div></div></div></div>';

    return html;
}
function agendaDateChange() {
    $('#agenda_dateAgenda_datepicker').val($('#calendar-holder').fullCalendar('getDate').format('DD/MM/YYYY'));
}

$(document).ready(function() {

    optionsCalendar.slotDuration = '00:' + getDureeConsultationAjax($$('#agenda_medecin').val()) + ':01';

    $("#agenda_dateAgenda_datepicker").on("change", function(e) {
        var newdate = moment($("#agenda_dateAgenda_datepicker").val(), "DD/MM/YYYY").format("YYYY-MM-DD");
        $('#calendar-holder').fullCalendar('gotoDate', newdate);
    });

    $('.main-content').on('mouseup', fermeFenetreRendezVous);

    $("#evenement_patient_telephone").on("change", function(e) {
        setRappelSMS();
    });

    $("#btn-priseRdv").on("click", function() {
        initFormRdv(optionsCalendar.medecinId);
    });

    $$("#evenement_medecin").on("change", function(e) {
        coloreJoursAbsences($("#evenement_medecin").val());
    });

    $$('#agenda_medecin').on("change", function(e) {
        optionsCalendar.medecinId = $$('#agenda_medecin').val();
        optionsCalendar.defaultTimedEventDuration = optionsCalendar.slotDuration;
        optionsCalendar.slotDuration = '00:' + getDureeConsultationAjax($$('#agenda_medecin').val()) + ':01';
        rafraichissementAgenda(true);
    });

    $$('#evenement_patient_entreprise').autocomplete({
        select: function(event, ui) {
            $$('#evenement_patient_entreprise').val(ui.item.Entreprise);
            saiseEntrepriseAutocomplete(ui.item);

            return false;
        }
    });

    $('#evenement_patient_nom').autocomplete({
        select: function(event, ui) {
            saiseAutocomplete(ui.item);
            afficheConsignes(ui.item.Id);
            afficheNonExcuses($$("#evenement_medecin").val(), ui.item.Id);

            return false;
        }
    });

    $('#evenement_heureDebut').blur(function() {
        var duree = getDureeConsultation($$("#evenement_medecin").val());
        var debut = $$('#evenement_heureDebut').val();
        var fin = $$('#evenement_heureFin').val();
        var regexHeure = '(2[0-3]|[01][0-9]):[0-5][0-9]';

        if (debut.match(regexHeure) && (fin === '')) {
            $$('#evenement_heureFin').val(moment(debut, 'HH:mm').add(duree, 'minutes').format('HH:mm'));
        }
    });

    $('#external-events').find('.external-event').each(function() {
        var eventObject = {
            title: $.trim($(this).text()),
            type: $(this).data("type"),
            idType: $(this).data("id"),
            couleur: $(this).data("couleur")
        };
        $(this).data('eventObject', eventObject);
        $(this).draggable({
            zIndex: 999,
            revert: true,
            revertDuration: 0
        });
    });

    //Soumission du formulaire de rendez-vous
    $('#EventForm').submit(function(e) {

        e.preventDefault();
        effaceErreurs(false);
        submitEvent($(this), function(response) {});
        formIsSubmitted=true;
        return false;
    });

    $('#btn-refresh').on("click", function(e) {
        rafraichissementAgenda();
    });

    //Suppression d'un rendez-vous
    $('#btn_suppr').on("click", function(e) {
        idEvent = $('#evenement_idEvenement').val();
        if (idEvent >0 ) {
            swal({   title: "Confirmez-vous la suppression de ce Rendez-vous ?",   text: "",
                type: "warning",   showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Oui",
                cancelButtonText: "Non",
                closeOnConfirm: false }, function(){
                    loadON();
                    deleteEvent(idEvent);
                    loadOFF();
                    swal("Supprimé !", "Le rendez-vous est supprimé.", "success");
                    rafraichissementAgenda();
                    fermeFenetreRendezVous(e);
                });
        }
    });

    //Sortie du formulaire de saisie
    $('#btn_quitter').on("click", function(e) {
        if (formIsSubmitted) {
            loadON();
            effaceErreurs(true);
            initFormRdv(optionsCalendar.medecinId);
            rafraichissementAgenda();
            formIsSubmitted =  false;
        }
        fermeFenetreRendezVous(e);
        loadOFF();
    });

    $('#btn_epingler').on("click", function(e) {
        idEvent = $('#evenement_idEvenement').val();
        epingleRdv(event, idEvent)
    });

    optionsCalendar.medecinId = $$('#agenda_medecin').val();
    optionsCalendar.slotDuration = '00:' + getDureeConsultation($$('#agenda_medecin').val()) + ':01';
    optionsCalendar.defaultTimedEventDuration = optionsCalendar.slotDuration;
    $$('#calendar-holder').fullCalendar(optionsCalendar);

    getEventInSession();

    $('.fc-prev-button').click(function() {
        agendaDateChange();
    });
    $('.fc-next-button').click(function() {
        agendaDateChange();
    });


    GestimeDatePicker.initDatePicker();
    agendaDateChange();
    applyStyle();
    coloreJoursAbsences($$("#evenement_medecin").val());



});
