var optionsCalendar = {

    // eventAfterAllRender est obligatoire, c’est lui qui nous permet d’afficher les annotations
    eventAfterAllRender: function(view) {
        if (view.name == 'agendaWeek' || view.name == 'agendaDay') {
            annotations(view, view.intervalStart, view.intervalEnd);
        }
    },
    medecinId: null,
    contentHeight: 'auto',
    allDaySlot: false,
    eventSources: [{
        url: Routing.generate('fullcalendar_loader'),
        type: 'POST',
        async: false,
        data: {
            "medecin": function() {
                return optionsCalendar.medecinId;
            }
        },
        error: function() {}
    }],
    dayClick: function(date, event, view) {
        var originalEventObject = $(this).data('eventObject');
        var copiedEventObject = $.extend({}, originalEventObject);
        copiedEventObject.start = date;
        copiedEventObject.end = moment(date).add(parseInt(optionsCalendar.slotDuration.substring(3, 5)), 'minutes');
        copiedEventObject.medecinId = optionsCalendar.medecinId;
        copiedEventObject.objet = "";

        event.stopPropagation();
        $('#btn-priseRdv').click();
        bindFormRdv(copiedEventObject);

        return false;
    },
    editable: true,
    durationEditable: true,
    droppable: true,
    dropAccept: function(elt) {
        return (elt.hasClass( "external-event" ) || elt.hasClass( "epingle-event" )) ;
    },

    drop: function(date) {
        var originalEventObject = $(this).data();
        var copiedEventObject = $.extend({}, originalEventObject);
        copiedEventObject.start = date;
        copiedEventObject.medecinId = optionsCalendar.medecinId;
        copiedEventObject.objet = "";

        if (copiedEventObject.copy != '1') {
            //Drop d'un rdv rapide
            copiedEventObject.end = moment(date).add(parseInt(optionsCalendar.slotDuration.substring(3, 5)), 'minutes');
            if (copiedEventObject.type != 'P') { //P=Temps Réservé
                $('#btn-priseRdv').click();
                bindFormRdv(copiedEventObject);
            } else {
                //Creation de l'évènement réservé
                copiedEventObject.objet = "Temps réservé";
                var $url = Routing.generate('ajax_set_reserve', {
                    idMedecin: copiedEventObject.medecinId,
                    debut: copiedEventObject.start,
                    fin: copiedEventObject.end
                });
                var resultat = null;
                $.ajax({
                    type: "POST",
                    url: $url,
                    async: false
                }).done(function(result) {
                    resultat = result;
                });
                copiedEventObject.id = resultat;
                copiedEventObject.qtip = true;
            }
        } else {
            //Drop d'un rdv epinglé
            //1 : On crée un nouveau rendez-vous (Par modification du rdv epinglé)
            //2 : On supprime le Rdv de la liste des rdv epinglés

            console.log('drop externe');
            copiedEventObject.end = moment(date).add(parseInt(copiedEventObject.duree), 'minutes');
            resultat = changeEvent(copiedEventObject.medecinId,
                copiedEventObject.id,
                copiedEventObject.start.format(),
                copiedEventObject.end.format());
            copiedEventObject.qtip = true;
            enleveRdvEpingle(copiedEventObject.id);
        }
        rafraichissementAgenda();
        //$('#calendar-holder').fullCalendar('renderEvent', copiedEventObject, true);

    },

    eventDrop: function(event, delta, revertFunc, jsEvent, ui, view) {
        if (!confirm("Confirmez-vous le déplacement du rendez-vous ?")) {
            revertFunc();
        } else {
            changeEvent(optionsCalendar.medecinId, event.id, event.start.format(), event.end.format());
            rafraichissementAgenda();
        }

    },
    eventResize: function(event, delta, revertFunc) {
        if (!confirm("Confirmez-vous le changement de la durée du rendez-vous ?")) {
            revertFunc();
        } else {
            changeEvent(optionsCalendar.medecinId, event.id, event.start.format(), event.end.format());
            rafraichissementAgenda();
        }
    },
    eventClick: function(event, jsEvent, view ) {
        editEvent_(event.id);
    },
    eventMouseout: function(event, jsEvent, view ) {
        var html =  '';

        $( '#detailRdv' ).html(html);
    },
    eventMouseover: function(event, jsEvent, view ) {
            var html =  '';
            if (event.type != 'P') {
                html += '<div><i class="icon-user"></i> ' + event.title + '</div>';
                html += '<div><i class="icon-phone"></i> ' + event.telephone + '</div>';
            }
            html +=     '<div><i class="icon-info-sign"></i> ' + event.objet + '</div>';
            html +=     '<div><i class="icon-time"></i> de :' + event.start.format('HH:mm') + ' à ' + event.end.format('HH:mm') + '</div>';
        $( '#detailRdv' ).html(html);
    },
    eventRender: function(event, element, view) {
        element.attr('data-id', event.id);
        element.addClass(event.couleur);
        var html = '';
        switch (view.name) {
            case 'agendaDay':
            {
                html = displayEvent(event);
                break;
            }
            case 'agendaWeek':
            {
                html = displayEvent(event);
                break;
            }
            case 'month':
            {
                html = displayMonth(event);
            }
                break;
        }

      return $(html);
    },

    viewRender: function(view, element) {
        var calendrier = $('#calendar-holder');
        var jourAgenda = moment(calendrier.fullCalendar('getDate')).format('YYYY/MM/DD');
        if (view.name == 'agendaDay' && jourAgenda == moment().format('YYYY/MM/DD')) {
            calendrier.fullCalendar({
                scrollTime: moment().format("HH:mm")
            });
        }
    },
    lang: 'fr',
    buttonText: {
        today: 'Aujourd\'hui',
        month: 'Mois',
        day: 'Jour',
        week: 'Semaine'
    },
    header: {
        left: 'prev, next',
        center: 'title',
        right: 'today, agendaDay,agendaWeek, month'
    },
    titleFormat: {
        month: 'MMMM YYYY',
        week: "D MMMM YYYY",
        day: 'dddd D MMMM YYYY'
    },
    columnFormat: {
        month: 'dddd',
        week: 'ddd D MMM',
        day: ''
    },
    slotEventOverlap: false,
    nowIndicator: true,
    minTime: '07:00:00',
    maxTime: '22:00:00',
    defaultView: 'agendaWeek',
    axisFormat: 'HH:mm',
    timeFormat: 'HH:mm',
    hiddenDays: [0],
    firstDay: 1,
    slotDuration: '00:20:01',
    scrollTime: '09:00:00',
    lazyFetching: false

};
