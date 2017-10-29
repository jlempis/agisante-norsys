<?php

/**
 * AjaxController class file
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
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Ajax Controller
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class AjaxController extends Controller
{
    /**
     * @Route("/personnes/entreprise/medecin/{medecinId}", name="ajax_nom_entreprise",options={"expose"=true})
     * @Secure("ROLE_GESTION_RDV")
     *
     * @param integer $medecinId
     * @param Request $request
     * @return json
     */
    public function ajaxNomEntrepriseAction($medecinId, Request $request)
    {
        $eventMgr = $this->container->get('gestime.event.manager');
        $listEntreprises = $eventMgr->getListeEntreprises($medecinId, $request->get('term'));
        $response = new JsonResponse();
        $response->setContent(json_encode($listEntreprises));

        return $response;
    }

    /**
     * @Route("/personnes/patients/medecin/{medecinId}", name="ajax_nom_patient",options={"expose"=true})
     * @Secure("ROLE_GESTION_RDV")
     *
     * @param integer $medecinId
     * @param Request $request
     * @return json
     */
    public function ajaxNomPatientAction($medecinId, Request $request)
    {
        $eventMgr = $this->container->get('gestime.event.manager');
        $listPatients = $eventMgr->getListePatients($medecinId, $request->get('term'));
        $response = new JsonResponse();
        $response->setContent(json_encode($listPatients));

        return $response;
    }

    /**
     * @Route("/villes/{codePostal}", name="ajax_villes_code_postal",options={"expose"=true})
     * @Method("POST")
     * @Secure("ROLE_GESTION_RDV")
     *
     * @param string $codePostal
     * @return json
     */
    public function ajaxGetVillesByCpostalAction($codePostal)
    {
        $eventMgr = $this->container->get('gestime.event.manager');
        $villes = $eventMgr->getVillesByCpostal($codePostal);
        $response = new JsonResponse();
        $response->setContent(json_encode($villes));

        return $response;
    }

    /**
     * @Route("/admin/horaires/{idMedecin}/{debut}/{fin}", name="ajax_annotations",options={"expose"=true})
     * @Secure("ROLE_GESTION_RDV")
     *
     * @param integer  $idMedecin
     * @param datetime $debut
     * @param datetime $fin
     * @return json
     */
    public function ajaxGetAnnotationsAction($idMedecin, $debut, $fin)
    {
        $eventMgr = $this->container->get('gestime.event.manager');
        $annotations = $eventMgr->getAnnotations($idMedecin, $debut, $fin);
        $response = new JsonResponse();
        $response->setContent(json_encode($annotations));

        return $response;
    }

    /**
     * @Route("/admin/connecteduser", name="ajax_connected_user",options={"expose"=true})
     * @Secure("ROLE_GESTION_RDV")
     *
     * @return json
     */
    public function ajaxGetConnectedUserAction()
    {
        $eventMgr = $this->container->get('gestime.event.manager');
        $connectedUser = $eventMgr->getConnectedUser();
        $response = new JsonResponse();
        $response->setContent(json_encode($connectedUser));

        return $response;
    }

    /**
     * @Route("/admin/absence/medecin/{idMedecin}", name="ajax_absences_medecin",options={"expose"=true})
     * @Secure("ROLE_GESTION_RDV")
     *
     * @param integer $idMedecin
     * @return json
     */
    public function ajaxGetAbsencesMedecinAction($idMedecin)
    {
        $eventMgr = $this->container->get('gestime.event.manager');
        $absences = $eventMgr->getAbsencesMedecin($idMedecin);
        $response = new JsonResponse();
        $response->setContent(json_encode($absences));

        return $response;
    }

    /**
     * ajaxGetAbsencesMedecinByPeriodeAction
     *
     * @Route("/admin/absence/periode/{idMedecin}/{debut}/{fin}", name="ajax_absences_medecin_periode",options={"expose"=true})
     * @Secure("ROLE_GESTION_RDV")
     * @param  integer  $idMedecin
     * @param  datetime $debut
     * @param  datetime $fin
     * @return json
     */
    public function ajaxGetAbsencesMedecinByPeriodeAction($idMedecin, $debut, $fin)
    {
        $eventMgr = $this->container->get('gestime.event.manager');
        $absences = $eventMgr->getAbsencesMedecinByPeriode($idMedecin, $debut, $fin);
        $response = new JsonResponse();
        $response->setContent(json_encode($absences));

        return $response;
    }

    /**
     * @Route("/event/session/get/events", name="ajax_get_event_in_session",options={"expose"=true})
     * @Secure("ROLE_GESTION_RDV")
     *
     * @param Request $request
     * @return json
     */
    public function ajaxGetEventInSessionAction(Request $request)
    {
        $eventMgr = $this->container->get('gestime.event.manager');
        $result = $eventMgr->getEventInSession($this->getRequest()->getSession());
        $response = new JsonResponse();
        $response->setContent(json_encode($result));

        return $response;
    }

    /**
     * @Route("/event/session/copy/{idEvenement}", name="ajax_copy_event_in_session",options={"expose"=true})
     * @Secure("ROLE_GESTION_RDV")
     *
     * @param integer $idEvenement
     * @return json
     */
    public function ajaxCopyEventInSessionAction($idEvenement)
    {
        $eventMgr = $this->container->get('gestime.event.manager');
        $result = $eventMgr->saveEventInSession($this->getRequest()->getSession(),
            $idEvenement
        );
        $response = new JsonResponse();
        $response->setContent(json_encode($result));

        return $response;
    }

    /**
     * @Route("/event/session/delete/{idEvenement}", name="ajax_delete_event_in_session",options={"expose"=true})
     * @Secure("ROLE_GESTION_RDV")
     *
     * @param integer $idEvenement
     * @return json
     */
    public function ajaxDeleteEventInSessionAction($idEvenement)
    {
        $eventMgr = $this->container->get('gestime.event.manager');
        $result = $eventMgr->deleteEventInSession($this->getRequest()->getSession(),
            $idEvenement
        );
        $response = new JsonResponse();
        $response->setContent(json_encode($result));

        return $response;
    }

    /**
     * @Route("/event/delete/{idEvenement}", name="ajax_delete_event",options={"expose"=true})
     * @Secure("ROLE_GESTION_RDV")
     *
     * @param integer $idEvenement
     * @return json
     */
    public function ajaxDeleteEventAction($idEvenement)
    {
        $eventMgr = $this->container->get('gestime.event.manager');
        $result = $eventMgr->deleteEvent($idEvenement);

        $response = new JsonResponse();
        $response->setContent(json_encode($result));

        return $response;
    }
}
