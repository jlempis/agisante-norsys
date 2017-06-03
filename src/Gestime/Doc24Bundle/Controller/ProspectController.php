<?php

namespace Gestime\Doc24Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Gestime\CoreBundle\Entity\Prospect;
use Gestime\Doc24Bundle\Form\Type\ProspectType;

class ProspectController  extends Controller{

/**
 * prospects
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

  /**
   * @Secure(roles="ROLE_GESTION_FERMETURES")
   */
  private function _datatable()
  {
    $qb = $this->getDoctrine()->getManager()
      ->getRepository('GestimeCoreBundle:Prospect')
      ->getProspects();

    $datatable = $this->get('datatable')
      ->setFields(
        array(
          'Specialite'   => 'spe_nom',
          'Raison Sociale'   => 'p_raisonsoc',
          'Nom'          => 'p.nom',
          'Prenom'       => 'p.prenom',
          'Adresse'      => 'p.adresse3',
          'CP'           => 'p.codePostal',
          'Ville'        => 'p.ville',
          'Telephone'    => 'p.telephone',
          '_identifier_'   => 'p.idProspect', )
      )
      ->setSearch(true)
      ->setSearchFields(array(0, 1))
      ->setHasAction(true);

    $datatable->getQueryBuilder()->setDoctrineQueryBuilder($qb);

    return $datatable;
  }

  /**
   * @Route("/prospects/grille", name="prospects_grille")
   * @Method("GET")
   * @Secure("ROLE_GESTION_FERMETURES")
   *
   * @return json
   */
  public function grilleAction()
  {
    return $this->_datatable()->execute();
  }

  /**
   * @Route("/prospects", name="prospects_liste")
   * @Method("GET")
   * @Secure("ROLE_GESTION_FERMETURES")
   * @Template("GestimeDoc24Bundle:prospects:index.html.twig")
   *
   * @return Template
   */
  public function indexAction()
  {
    $this->_datatable();

    return $this->render('GestimeDoc24Bundle:prospects:index.html.twig', array('menuactif' => 'Doc24'));
  }

  /**
   * @Route("/prospects/ajouter", name="prospects_ajouter")
   * @Secure("ROLE_GESTION_FERMETURES")
   * @Template("GestimeDoc24Bundle:prospects:page.html.twig")
   *
   * [ajouterAction description]
   * @param Request $request [description]
   * @return [type]           [description]
   */
  public function ajouterAction(Request $request)
  {
    $doc24Mgr = $this->container->get('gestime.doc24.manager');

    $prospect = new Prospect();
    $prospect->setIdentifiant($doc24Mgr->gen_uuid());
    
    $form = $this->createForm(new ProspectType(), $prospect);
    $form->handleRequest($request);

    if ($form->isValid()) {
      $entityManager = $this->getDoctrine()->getManager();
      $entityManager->persist($prospect);
      $entityManager->flush();

      return $this->redirect($this->generateUrl('prospects_liste'));
    }
    $this->_datatable();

    return array('form' => $form->createView(),
      'action' => 'Ajouter un prospect',
      'menuactif' => 'Doc24',
    );
  }

  /**
   * @Route("/prospects/edit/{idProspect}", name="prospects_edit")
   * @Secure("ROLE_GESTION_FERMETURES")
   * @Template("GestimeDoc24Bundle:prospects:page.html.twig")
   *
   * [editAction description]
   * @param Request   $request
   * @param Prospect  $prospect
   * @return Template
   */
  public function editAction(Request $request, Prospect $prospect)
  {
    $form = $this->createForm(new ProspectType(), $prospect);
    $form->handleRequest($request);

    if ($form->isValid()) {
      $entityManager = $this->getDoctrine()->getManager();
      $entityManager->persist($prospect);
      $entityManager->flush();

      return $this->redirect($this->generateUrl('prospects_liste'));
    }

    $this->_datatable();

    return array('form' => $form->createView(),
      'action' => 'Modifier un prospect',
      'menuactif' => 'Doc24',
    );
  }

  /**
   * @Route("/prospects/suppr/{idProspect}", name="prospects_delete")
   * @Secure("ROLE_GESTION_FERMETURES")
   * @Template("GestimeDoc24Bundle:prospects:page.html.twig")
   *
   * [deleteAction description]
   * @param Request   $request
   * @param Prospect  $prospect
   * @return Template
   */
  public function deleteAction(Request $request, Prospect $prospect)
  {
    $frmOptions = array( 'action' => 'suppr');
    $form = $this->createForm(new ProspectType(), $prospect);
    $form->handleRequest($request);

    if ($form->isValid()) {
      $entityManager = $this->getDoctrine()->getManager();
      $entityManager->remove($prospect);
      $entityManager->flush();

      return $this->redirect($this->generateUrl('prospects_liste'));
    }
    $this->_datatable();

    return array('form' => $form->createView(),
      'action' => 'Supprimer un prospect',
      'menuactif' => 'Doc24',
    );
  }
}
