<?php

namespace Gestime\ApiBundle\Controller\Rest;

use FOS\RestBundle\Controller\Annotations as FOSRest;

use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;


/**
 * CompteController
 *
 * @FOSRest\NamePrefix("api_compte")
 */
class CompteController extends Controller {

  /**
   * GET Route annotation.
   * @FOSRest\Get("/events/{idUtiisateur}")
   * @FOSRest\View(statusCode=201)
   *
   * @ApiDoc(
   *  description="Retourne les rendez-vous pris par le patient",
   *     statusCodes={
   *         200="Returned when successful",
   *
   *         404={
   *           "Returned when the user is not found",
   *           "Returned when something else is not found"
   *         }
   *     },
   *
   *  parameters={
   *      {"name"="$idUtiisateur", "dataType"="integer", "required"=true, "description"="Id de l'utilisateur"}
   *  }
   * )
   */
  public function getEventsAction($idUtiisateur)
  {
    $eventMgr = $this->container->get('gestime.event.manager');
    $user_manager = $this->get('gestime.utilisateur.manager');


    $logger = $this->get('monolog.logger.doc24');
    $logger->info(sprintf('Demande de liste : Email: %s, IP: %s',
      $idUtiisateur,
      ''));

    $user = $user_manager->findWebUserById($idUtiisateur);
    if (!$user) {
      return array("ValidUser"=>false);
    }
    $patient = $user_manager->getIdPatientByUser($user);

    return $eventMgr->getRdvWebByPatient($patient);

  }

  /**
   * @FOSRest\View(statusCode=201)
   */
  public function deleteEventAction($idEvent)
  {
    $eventMgr = $this->container->get('gestime.event.manager');
    $user_manager = $this->get('gestime.utilisateur.manager');
    $doc24Mgr = $this->container->get('gestime.doc24.manager');

    $logger = $this->get('monolog.logger.doc24');
    $logger->info(sprintf('Suppression de RDV : id: %d, IP: %s',
      $idEvent,
      ''));

    $rdv = $eventMgr->getEvenementById($idEvent)[0];

    if (!$rdv) {
      return array("ValidUser"=>false);
    }

    $patient = $rdv->getPatient();

    $result = $eventMgr->deleteEvent($idEvent);
    $code = $doc24Mgr->envoiSmsAnnulationRdv($patient->getTelephone(), $rdv);

    return $result;

  }

}
