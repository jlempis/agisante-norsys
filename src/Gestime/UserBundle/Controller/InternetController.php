<?php
/**
 * Created by PhpStorm.
 * User: jean-loup
 * Date: 17/05/15
 * Time: 09:09
 */

namespace Gestime\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class InternetController  extends Controller {

  /**
   * @Route("/rdvlibres/{idMedecin}/{debut}/{fin}", name="rdv_internet_libre")
   * @Method("GET")
   * @Secure("ROLE_GESTION_MEDECINS")
   *
   * @return Template
   */
  public function rdvInternetAction($idMedecin, $debut, $fin)
  {
    $medecinMgr = $this->container->get('gestime.medecin.manager');

    $rdvDispos = $medecinMgr->getRdvInternetLibres($idMedecin, $debut, $fin);

    $response = new JsonResponse();
    $response->setContent(json_encode(array(
      'data' => $rdvDispos,
    )));

    $response->headers->set('Content-Type', 'application/json');

    return $response;
  }


}
