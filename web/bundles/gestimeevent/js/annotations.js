function annotations(view, debut, fin){

  function toMinutes(h, m){
    return parseInt(m) + parseInt(h) * 60;
  }
  var tab=getAnnotations(optionsCalendar.medecinId, debut.format('YYYY-MM-DD'), fin.format('YYYY-MM-DD'));

  var annotations = tab,
    container = $(view.el).find('.fc-time-grid'),
    annotationTemplate = '',
    hiddenDays = view.opt('hiddenDays')?view.opt('hiddenDays'):[],
    firstDay = view.opt('firstDay')?view.opt('firstDay'):0,
    minT = view.opt('minTime').split(':'),
    minTime = toMinutes(minT[0], minT[1]),
    maxT = view.opt('maxTime').split(':'),
    maxTime = toMinutes(maxT[0], maxT[1]),
    slot = view.opt('slotDuration').split(':'),
    slotDuration = toMinutes(slot[0], slot[1]);
    days = {
      '0': 'sun',
      '1': 'mon',
      '2': 'tue',
      '3': 'wed',
      '4': 'thu',
      '5': 'fri',
      '6': 'sat',
    };

  if(view.name === 'agendaWeek')
  {
    annotationTemplate = ''+
      '<table>'+
        '<tbody'+
          '<tr>'+
            '<td class="ann-first" style="width: '+view.axisWidth+'px;"></td>';
            for(var i in days){
              var order = (parseInt(i)+firstDay)%7;
              annotationTemplate+=($.inArray(order, hiddenDays) > -1)?'':'<td class="ann-day ann-'+days[order]+'"></td>';
            }
    annotationTemplate+='</tr>'+
        '</tbody>'+
      '</table>';

  }
  else if(view.name === 'agendaDay')
  {

    annotationTemplate = ''+
      '<table>'+
        '<tbody>'+
          '<tr>'+
            '<td class="ann-first" style="width: '+view.axisWidth+'px;"></td>'+
            '<td class="ann-day ann-'+days[view.start.format('E')]+'"></td>'+
          '</tr>'+
        '</tbody>'+
      '</table>';
  }

  var annotationContainer = container.find('.annotationContainer').length?container.find('.annotationContainer'):$('<div class="annotationContainer">').appendTo(container);
  annotationContainer.html(annotationTemplate);

  for(var i=0; i < annotations.length; i++) {
      var ann = annotations[i],
        day = annotationContainer.find('.ann-'+ann.day),
        startT = ann.start.split(':'),
        startTime = toMinutes(startT[0], startT[1]),
        start = startTime<minTime?0:Math.round((startTime-minTime)/slotDuration),
        endT = ann.end.split(':'),
        endTime = toMinutes(endT[0], endT[1]),
        end = endTime>maxTime?Math.round((maxTime-minTime)/slotDuration):Math.round((endTime-minTime)/slotDuration);

      if(day.length){
        day.prepend(
          $('<div class="ann">').css({
            'background': (ann.background?ann.background:''),
            'top': view.timeGrid.slatTops[start]-1+'px',
            'bottom': -view.timeGrid.slatTops[end]+'px',
            'color': (ann.color?ann.color:''),
          })
          .addClass((ann.cssClass?ann.cssClass:''))
          .text(ann.text?ann.text:'')
        );
      }
    }
  }