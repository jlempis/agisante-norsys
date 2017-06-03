function getAnnotations($idMedecin, $debut, $fin) {
    var $url = Routing.generate('ajax_annotations', {
        idMedecin: $idMedecin,
        debut: $debut,
        fin: $fin
    });
    var annotation = null;
    $.ajax({
        type: "POST",
        url: $url,
        async: false,
    }).done(function(result) {
        annotation = result;
    });

    return annotation;
}

function getDureeConsultationAjax($idMedecin) {
    var $url = Routing.generate('ajax_duree_consultation', {
        idMedecin: $idMedecin
    });
    var duree_rdv = 0;
    $.ajax({
        type: "POST",
        url: $url,
        async: false,
    }).done(function(result) {
        duree_rdv = result.dureeRdv;
    });

    return duree_rdv;
}

function getDureeConsultation($idMedecin) {
    return optionsCalendar.slotDuration.substr(3,2);
}

function getAbsencesMedecin($idMedecin) {
    var $url = Routing.generate('ajax_absences_medecin', {
        idMedecin: $idMedecin
    });
    var absences = null;
    $.ajax({
        type: "POST",
        url: $url,
        async: false,
    }).done(function(result) {
        absences = result;
    });
    return absences;
}

function getAbsencesMedecinByPeriode($idMedecin) {
    var $url = Routing.generate('ajax_absences_medecin_periode', {
        idMedecin: $idMedecin,
        debut: $startDate,
        fin: $endDate
    });
    var absencesPeriode = null;
    $.ajax({
        type: "POST",
        url: $url,
        async: false,
    }).done(function(result) {
        absencesPeriode = result;
    });
    return absencesPeriode;
}

function getConsignesPatient($idPatient) {
    var $url = Routing.generate('ajax_consignes_patient', {
        idPatient: $idPatient
    });
    var consignes = null;
    $.ajax({
        type: "POST",
        url: $url,
        async: false,
    }).done(function(result) {
        consignes = result;
    });
    return consignes;
}

function getNonExcusesPatient($idMedecin, $idPatient) {
    var $url = Routing.generate('ajax_non_excuses_patient', {
        idPatient: $idPatient,
        idMedecin: $idMedecin
    });
    var nonExcuses = null;
    $.ajax({
        type: "POST",
        url: $url,
        async: false,
    }).done(function(result) {
        nonExcuses = result;
    });
    return nonExcuses;
}

function getComboVillesByCpostal($codePostal) {
    var $url = Routing.generate('ajax_villes_code_postal', {
        codePostal: $codePostal
    });
    var villes = null;
    $.ajax({
        type: "POST",
        url: $url,
        async: false,
    }).done(function(result) {
        villes = result;
    });
    return villes;
}

function getEvenement($eventId) {
    var evenement;
    var $url = Routing.generate('ajax_evenement', {
        idEvent: $eventId
    });
    evenement = $.ajax({
        type: "POST",
        url: $url,
        async: false,
    }).responseText;

    return evenement;
}

function deleteEvent($eventId) {
    var $url = Routing.generate('ajax_delete_event', {
        idEvent: $eventId
    });

    $.ajax({
        type: "POST",
        url: $url,
        async: false,
    }).done(function(statut) {
        return statut;
    });
}

function changeEvent($medecinId, $eventId, $newStartDate, $newEndDate) {
    console.log($medecinId, $eventId, $newStartDate, $newEndDate);
    var $url = Routing.generate('change_event', {
        idMedecin: $medecinId,
        eventId: $eventId,
        newStartDate: $newStartDate,
        newEndDate: $newEndDate
    });
    var reponse;
    reponse = $.ajax({
        type: "POST",
        url: $url,
        async: false,
    });

    return reponse.done(function(statut) {}).responseText;
}

function getEventInSession(e) {
    var $url = Routing.generate('ajax_get_event_in_session');
    var statut = null;
    $.ajax({
        type: "POST",
        url: $url,
        async: false,
    }).done(function(statut) {
        afficheRdvEpingles(statut);
    });
}

function saveEventInSession(e, $eventId) {
    var $url = Routing.generate('ajax_copy_event_in_session', {
        idEvenement: $eventId
    });
    var statut = null;
    $.ajax({
        type: "POST",
        url: $url,
        async: false,
    }).done(function(statut) {
        afficheRdvEpingles(statut);
    });
}

function deleteEventInSession($eventId) {
    var $url = Routing.generate('ajax_delete_event_in_session', {
        idEvenement: $eventId
    });
    var statut = null;
    $.ajax({
        type: "POST",
        url: $url,
        async: false,
    }).done(function(statut) {
        afficheRdvEpingles(statut);
    });
}
