<?php

namespace Gestime\ApiBundle\Controller\Rest;

use FOS\RestBundle\Controller\Annotations as FOSRest;

use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Gestime\ApiBundle\Model\RdvWeb;
use Gestime\CoreBundle\Entity\Utilisateur;
use Gestime\ApiBundle\Form\Type\RdvWebType;


/**
 * ReservationController
 *
 * @FOSRest\NamePrefix("api2_")
 */
class ReservationController extends Controller {

  /**
   * PUT Route annotation.
   * @FOSRest\Put("/user/{email}/code/{numero}")
   */
  public function putSendSmsCodeAction($email, $numero)
  {
    $doc24Mgr = $this->container->get('gestime.doc24.manager');
    $userMgr = $this->get('gestime.utilisateur.manager');

    $authentification = $doc24Mgr->envoiSmsInscription($numero);
    $userMgr->majCodeAuthentification($email, $authentification['code'], $authentification['expiration'], $numero);

    $logger = $this->get('monolog.logger.doc24');
    $logger->info(sprintf('Demande de code : Email: %s, IP: %s, Numero: %s',
      $email,
      '',
      $numero));

    return array("smsSend"=>$authentification['code']);
  }


  /**
   * @param $numero
   * @return array
   * @FOSRest\View
   */
  public function getValidationCodeAction($numero)
  {
    $doc24Mgr = $this->container->get('gestime.doc24.manager');
    $code = $doc24Mgr->envoiSmsInscription($numero);
    $logger = $this->get('monolog.logger.doc24');
    $logger->info(sprintf('Demande de code : Numero: %s, IP: %s, Telephone: %s, Code Obtenu: %s',
      $numero,
      ''));
    return array("smsSend"=>$code);
  }

  /**
   * @param $idUtilisateur
   * @param $code
   * @return array
   *
   * @FOSRest\Get("/user/{idUtilisateur}/telephone/{numero}/code/{code}")
   * @FOSRest\View
   */
  public function getValidationCodeOkAction($idUtilisateur, $numero, $code) {
    $doc24Mgr = $this->container->get('gestime.doc24.manager');

    $logger = $this->get('monolog.logger.doc24');
    $logger->info(
      sprintf(
        'Verif de code : IP: %s, Code verifié: %s',
        $code,
        ''
      )
    );
    $user_manager = $this->get('gestime.utilisateur.manager');
    $user = $user_manager->findWebUserById($idUtilisateur);

    if ($user) {
      if ($this->CodeValidationExpire($user, $code) && $user->getPhoneNumber()==$numero) {
        return array("codeOk" => true);
      }
    }
    return array("codeOk" => false);
  }

  private function CodeValidationExpire(Utilisateur $user, $code) {
    $maintenant = new \DateTime('Now');
    return ($user->getAuthCode() == $code && $user->getAuthCodeExpiresAt() > $maintenant);
  }

  private function processRendezvousForm(Request $request, RdvWeb $rdv)
  {
    $logger = $this->get('monolog.logger.doc24');
    $logger->info(sprintf('PriseRdv: Email: %s, IP: %s, Telephone: %s, MedecinId: %d, DateRdv: %s, Code Activation: %s, Deja Venu: %s, Raison: %s.',
      $request->request->get('rdvWeb')['email'],
      $request->getClientIp(),
      $request->request->get('rdvWeb')['telephone'],
      $request->request->get('rdvWeb')['medecinId'],
      $request->request->get('rdvWeb')['dateRdv'],
      $request->request->get('rdvWeb')['codeActivation'],
      $request->request->get('rdvWeb')['dejaVenu'],
      $request->request->get('rdvWeb')['raison']));


    $userMgr = $this->get('gestime.utilisateur.manager');
    $eventMgr = $this->get('gestime.event.manager');
    $doc24Mgr = $this->container->get('gestime.doc24.manager');

    $utilisateurExiste = $userMgr->utilisateurExiste($request->request->get('rdvWeb')['email']);
    $statusCode = ($utilisateurExiste) ? 201 : 204;

    $form = $this->createForm(new RdvWebType(), $rdv);
    $form->handleRequest($request);

    if ($form->isValid()) {
      $response = new Response();
      $response->setStatusCode($statusCode);

      if ($utilisateurExiste == 201) {

        $user = $userMgr->findWebUserByEmail($request->request->get('rdvWeb')['email']);

        if (!$user || !$user->getUserWeb()) {
          $response->setStatusCode(204);
          return $response;
        }

        if (!$this->CodeValidationExpire($user, $rdv->getCodeActivation())) {
          $response->setStatusCode(205);
          return $response;
        }

        $result = $eventMgr->saveRdvWeb($user, $rdv);
        $code = $doc24Mgr->envoiSmsPriseRdv($rdv->getTelephone(), $rdv);
      }
      return $response;
    }

    return View::create($form, 201);
  }

  /**
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @return \FOS\RestBundle\View\View|\Symfony\Component\HttpFoundation\Response
   */
  public function postRdvAction(Request $request) {

    return $this->processRendezvousForm($request, new RdvWeb());
  }
}
