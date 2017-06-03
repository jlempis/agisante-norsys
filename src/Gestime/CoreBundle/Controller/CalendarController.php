<?php

namespace Gestime\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ADesigns\CalendarBundle\Event\CalendarEvent;
use ADesigns\CalendarBundle\Controller\CalendarController as BaseController;

/**
 * Classe de tests des IHMs absence
 *
 */
class CalendarController extends BaseController
{
    /**
     * @Route("/fc-load-events", name="fullcalendar_loader",options={"expose"=true})
     *
     * @param  Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loadCalendarAction(Request $request)
    {
        $startDatetime = new \DateTime();
        $startDatetime->setTimestamp(strtotime($request->get('start')));

        $endDatetime = new \DateTime();
        $endDatetime->setTimestamp(strtotime($request->get('end')));

        $events = $this->container->get('event_dispatcher')->dispatch(CalendarEvent::CONFIGURE, new CalendarEvent($startDatetime, $endDatetime, $request))->getEvents();

        $response = new \Symfony\Component\HttpFoundation\Response();
        $response->headers->set('Content-Type', 'application/json');

        $returnEvents = array();

        foreach ($events as $event) {
            $returnEvents[] = $event->toArray();
        }

        $response->setContent(json_encode($returnEvents));

        return $response;
    }
}
