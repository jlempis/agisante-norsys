<?php

/**
 * AjaxEventController class file
 *
 * PHP Version 5.5
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\EventBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Ajax Event Controller
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class AjaxEventController extends Controller
{
    /**
     * @Route("/event/{idEvent}", name="ajax_evenement",options={"expose"=true})
     * @Secure("ROLE_GESTION_RDV")
     *
     * @param integer $idEvent
     * @return json
     */
    public function ajaxEvenementAction($idEvent)
    {
        $eventMgr = $this->container->get('gestime.event.manager');
        $evenement = $eventMgr->getEvenement($idEvent);
        $response = new JsonResponse();
        $response->setContent(json_encode($evenement));

        return $response;
    }

    /**
     * @Route("/event/delete/{idEvent}", name="ajax_delete_event",options={"expose"=true})
     * @Secure("ROLE_GESTION_RDV")
     *
     * @param integer $idEvent
     * @return json
     */
    public function ajaxDeleteEventAction($idEvent)
    {
        $eventMgr = $this->container->get('gestime.event.manager');
        $statut = $eventMgr->deleteEvent($idEvent);
        $response = new JsonResponse();
        $response->setContent(json_encode($statut));

        return $response;
    }

    /**
     * @Route("/event/reserve/{idMedecin}/{debut}/{fin}", name="ajax_set_reserve",options={"expose"=true})
     * @Secure("ROLE_GESTION_RDV")
     *
     * @param integer  $idMedecin
     * @param datetime $debut
     * @param datetime $fin
     * @return json
     */
    public function ajaxSetTempsReserveAction($idMedecin, $debut, $fin)
    {
        $eventMgr = $this->container->get('gestime.event.manager');
        $result = $eventMgr->setReserve($idMedecin, $debut, $fin);
        $response = new JsonResponse();
        $response->setContent(json_encode($result));

        return $response;
    }

    /**
     * @Route("/medecin/duree_rdv/{idMedecin}", name="ajax_duree_consultation",options={"expose"=true})
     * @Method("POST")
     * @Secure("ROLE_GESTION_RDV")
     *
     * @param integer $idMedecin
     * @return json
     */
    public function ajaxDureeConsultationAction($idMedecin)
    {
        $eventMgr = $this->container->get('gestime.event.manager');
        $dureeConsultation = $eventMgr->getDureeConsultation($idMedecin);
        $response = new JsonResponse();
        $response->setContent(json_encode($dureeConsultation));

        return $response;
    }

    /**
     * @Route("/patient/{idPatient}/consignes", name="ajax_consignes_patient",options={"expose"=true})
     * @Method("POST")
     * @Secure("ROLE_GESTION_RDV")
     *
     * @param integer $idPatient
     * @return json
     */
    public function ajaxConsignesPatientAction($idPatient)
    {
        $eventMgr = $this->container->get('gestime.event.manager');
        $consignes = $eventMgr->getConsignesPatient($idPatient);
        $response = new JsonResponse();
        $response->setContent(json_encode($consignes));

        return $response;
    }

    /**
     * @Route("/patient/{idMedecin}/{idPatient}/nonExcuses", name="ajax_non_excuses_patient",options={"expose"=true})
     * @Method("POST")
     * @Secure("ROLE_GESTION_RDV")
     *
     * @param integer $idMedecin
     * @param integer $idPatient
     * @return json
     */
    public function ajaxNonExcusesPatientAction($idMedecin, $idPatient)
    {
        $eventMgr = $this->container->get('gestime.event.manager');
        $nonExcuses = $eventMgr->getNonExcusesPatient($idMedecin, $idPatient);
        $response = new JsonResponse();
        $response->setContent(json_encode($nonExcuses));

        return $response;
    }

    /**
     * @Route("/event/changedate/{idMedecin}/{eventId}/{newStartDate}/{newEndDate}", name="change_event",options={"expose"=true})
     * @Secure("ROLE_GESTION_RDV")
     *
     * @param integer  $idMedecin
     * @param integer  $eventId
     * @param datetime $newStartDate
     * @param datetime $newEndDate
     * @return json
     */
    public function ajaxChangeEventAction($idMedecin, $eventId, $newStartDate, $newEndDate)
    {
        $eventMgr = $this->container->get('gestime.event.manager');
        $statut = $eventMgr->changeEvent($idMedecin, $eventId, $newStartDate, $newEndDate);

        $response = new JsonResponse();
        $response->setContent(json_encode($statut));

        return $response;
    }
}
