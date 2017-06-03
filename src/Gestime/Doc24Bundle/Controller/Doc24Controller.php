<?php

namespace Gestime\Doc24Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class Doc24Controller  extends Controller {

  /**
   * @Route("/rdvlibres/{idMedecin}/{debut}/{fin}", name="rdv_internet_libre")
   * @Method("GET")
   * @Secure("ROLE_GESTION_MEDECINS")
   * @Template("GestimeDoc24Bundle:Default:index.html.twig")
   *
   * @return Template
  */

  public function rdvInternetAction($idMedecin, $debut, $fin) {
    $doc24Mgr = $this->container->get('gestime.doc24.manager');
    $medecinMgr = $this->container->get('gestime.medecin.manager');

    $rdvDispos = $doc24Mgr->getRdvInternetLibres($idMedecin, $debut, $fin);
/*
    $latitude= 50.624568;
    $longitude = 2.767283;
    $distance = 10;

    $abonnesProches = $medecinMgr->getMedecinsProches( $latitude, $longitude, $distance, 1);
*/


    $response = new JsonResponse();
    $response->setContent(
      json_encode(
        array(
          'data' => $rdvDispos,
        )
      )
    );

    $response->headers->set('Content-Type', 'application/json');

    return array('action' => 'Ajouter un médecin',

    );

  }

  /**
   * @Route("/mailins", name="iscription_mail")
   * @Method("GET")
   * @Secure("ROLE_GESTION_ABONNES")
   * @Template("GestimeDoc24Bundle:Mail:inscription.html.twig")
   *
   * @return Template
   */
  public function mailAction() {
    return $this->render('GestimeDoc24Bundle:Mail:inscription.html.twig',
      array('menuactif' => 'Utilisateurs'));
  }


}
