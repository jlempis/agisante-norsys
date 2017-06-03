<?php

namespace Gestime\ApiBundle\Controller\Rest;

use FOS\RestBundle\Controller\Annotations as FOSRest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;

use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;

use FOS\RestBundle\Controller\Annotations\Route,
    FOS\RestBundle\Controller\Annotations\NoRoute,
    FOS\RestBundle\Controller\Annotations\Get,
    FOS\RestBundle\Controller\Annotations\Post;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Gestime\CoreBundle\Entity\Evenement;

/**
 * EventController
 *
 * @author Jean-Loup Empis <jlempis@gmail.com>
 *
 * @FOSRest\NamePrefix("api_")
 */

class EventController extends Controller
{
    /**
     * @throws AccessDeniedException
     * @return array
     *
     * @FOSRest\View(serializerGroups={"list"})
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Création de rendez-vous",
     *  filters={
     *      {"name"="a-filter", "dataType"="integer"},
     *  }
     * )
     *
     */
    public function putEventAction($id)
    {
        if (!$this->isGranted('API')) {
            throw new AccessDeniedException();
        }
        $event = new Evenement();
        $form = $this->createForm($this->container->get('gestime_event.form.type.evenement'),
            $event,
            array('attr' => array('user' => $this->getUser(),
                'nexcuse' => true, ))
        );
    }

    /**
     *
     * @var Request $request
     * @return array
     * @FOSRest\View(statusCode=201)
     * @Post("/event")
     */
    public function postEventAction(Request $request)
    {
        $eventV1 = unserialize($request->request->get('evenement'));
        $synchroV1Mgr = $this->get('gestime.synchro.evenement.manager');
        $event = $synchroV1Mgr->CreateEventFromEventV1($eventV1);
    }

    /**
     *
     * @var Request $request
     * @return array
     * @FOSRest\View(statusCode=201)
     * @Post("/absence")
     */
    public function postAbsenceAction(Request $request)
    {
        $absenceV1 = unserialize($request->request->get('absence'));
        $synchroV1Mgr = $this->get('gestime.synchro.evenement.manager');
        $absence = $synchroV1Mgr->CreateAbsenceFromV1($absenceV1);
    }

    /**
     *
     * @var Request $request
     * @return array
     * @FOSRest\View(statusCode=201)
     * @Delete("/absence/{id}")
     */
    public function deleteAbsenceAction($id)
    {
        $synchroV1Mgr = $this->get('gestime.synchro.evenement.manager');
        $absence = $synchroV1Mgr->DeleteAbsenceFromV1($id);
    }
    /**
     *
     * @var Request $request
     * @return array
     * @FOSRest\View(statusCode=201)
     * @Post("/absence/remplacement")
     */
    public function postRemplacementAction(Request $request)
    {
        $remplacementV1 = unserialize($request->request->get('remplacement'));
        $synchroV1Mgr = $this->get('gestime.synchro.evenement.manager');
        $remplacement = $synchroV1Mgr->CreateRemplacementFromV1($remplacementV1);
    }

    /**
     *
     * @var Request $request
     * @return array
     * @FOSRest\View(statusCode=201)
     * @Post("/reserve")
     */
    public function postReserveAction(Request $request)
    {
        $eventV1 = unserialize($request->request->get('evenement'));
        $synchroV1Mgr = $this->get('gestime.synchro.evenement.manager');
        $event = $synchroV1Mgr->CreateEventReserveFromEventV1($eventV1);
    }

    /**
     *
     * @FOSRest\View(serializerGroups={"list"})
     */
    public function getEventAction($id)
    {
        if (!$this->isGranted('API')) {
            throw new AccessDeniedException();
        }
    }

    /**
     *
     * @FOSRest\View(serializerGroups={"list"})
     */
    public function getEventsAction()
    {
        if (!$this->isGranted('API')) {
            throw new AccessDeniedException();
        }
    }

    /**
     *
     * @var Request $request
     * @return array
     * @throws AccessDeniedException
     * @FOSRest\View(statusCode=201)
     * @Delete("/event/{id}")
     */
    public function deleteEventAction($id)
    {
        $synchroV1Mgr = $this->get('gestime.synchro.evenement.manager');
        $event = $synchroV1Mgr->DeleteEventFromEventV1($id);
    }
}
