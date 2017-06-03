<?php

/**
 * InfosDoc24Controller class file
 *
 * PHP Version 5.5
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Gestime\CoreBundle\Entity\Medecin;
use Gestime\CoreBundle\Entity\InfosDoc24;
use Gestime\UserBundle\Form\Type\InfosComplementairesType;

use Snc\RedisBundle;

/**
 * Absence
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class InfosDoc24Controller extends Controller
{
  private function _datatable()
  {
    $qb = $this->getDoctrine()->getManager()
      ->getRepository('GestimeCoreBundle:medecin')
      ->getMedecinsUser(
            $this->getUser()->hasRole('ROLE_VISU_AGENDA_TOUS'),
            $this->getUser()->getSite(),
            $this->getUser()->getId()
      );

    $datatable = $this->get('datatable')
      ->setFields(
        array(
          'Nom'            => 'm.nom',
          'Prenom'         => 'm.prenom',
          'Durée RDV'      => 'm.dureeRdv',
          'Remplacant'     => 'm.remplacant',
          'Généraliste'    => 'm.generaliste',
          'Rappel SMS'     => 'm.abonneSms',
          '_identifier_'   => 'm.idMedecin', )
      )
      ->setSearch(true)
      ->setSearchFields(array(0, 3))
      ->setOrder('x.nom', 'desc')
      ->setHasAction(true);
    $datatable->getQueryBuilder()->setDoctrineQueryBuilder($qb);

    return $datatable;
  }

  /**
   * @Route("/grille", name="infosDoc24_grille")
   * @Method("GET")
   * @Secure("ROLE_DOC24")
   *
   * @return json
   */
  public function grilleAction()
  {
    return $this->_datatable()->execute();
  }

  /**
   * @Route("/", name="infosDoc24")
   * @Method("GET")
   * @Secure("ROLE_DOC24")
   * @Template("GestimeUserBundle:infosDoc24:index.html.twig")
   *
   * @return Template
   */
  public function indexAction()
  {
    $this->_datatable();

    return $this->render('GestimeUserBundle:infosDoc24:index.html.twig',
      array('menuactif' => 'Doc24')
    );
  }

  /**
   * @Route("/edit/{idMedecin}", name="infosDoc24_edit")
   * @Secure("ROLE_DOC24")
   * @Template("GestimeUserBundle:infosDoc24:page.html.twig")
   *
   * [editAction description]
   * @param Request $request
   * @param Medecin $medecin
   * @return Template
   */
  public function editAction(Request $request, Medecin $medecin)
  {

    $medecinMgr = $this->container->get('gestime.medecin.manager');
    $telsAvantModif = $medecinMgr->getTelephones($medecin);
    $horairesAvantModif = $medecinMgr->getHoraires($medecin);
    $horairesInternetAvantModif = $medecinMgr->getHorairesInternet($medecin);
    $tarifsAvantModif = $medecinMgr->getInfosDoc24Tarifs($medecin);
    $transportsAvantModif = $medecinMgr->getInfosDoc24Transports($medecin);

    //Si c'est la premiere fois qu'on accede aux infos
    //On crée l'entité

    if (!$medecin->getInfosDoc24()) {
      $infos = new InfosDoc24();
      $infos->setMedecin($medecin);
      $medecin->setInfosDoc24($infos);
    }

    $form = $this->createForm(new InfosComplementairesType(), $medecin, array(
      'attr' => array('user' => $this->getUser()), ));
    $form->handleRequest($request);

    if ($form->isValid()) {
      $medecinMgr->saveEditedMedecin($medecin,
                                     $telsAvantModif,
                                     $horairesAvantModif,
                                     $horairesInternetAvantModif,
                                     $tarifsAvantModif,
                                     $transportsAvantModif);

      $redisClient = $this->get('snc_redis.default');
      $redisClient->flushdb();

      return $this->redirect($this->generateUrl('infosDoc24'));
    }
    $this->_datatable();

    return array('action' => 'Modifier les informations du '.$medecin->getNom().' '.$medecin->getPrenom(),
      'form' => $form->createView(),
      'menuactif' => 'Doc24',
    );
  }


}
