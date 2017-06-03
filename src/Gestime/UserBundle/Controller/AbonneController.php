<?php

/**
 * AbonneController class file
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
use Gestime\CoreBundle\Entity\Abonne;
use Gestime\UserBundle\Form\Type\AbonneType;

/**
 * Abonné
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class AbonneController extends Controller
{
    /**
     * @Secure(roles="ROLE_GESTION_ABONNES")
     *
     * Peuplement de la liste des abonnés
     * @return datatable Ensemble des abonnés
     */
    private function _datatable()
    {
        return $this->get('datatable')
                ->setEntity('GestimeCoreBundle:Abonne', 'x')
                ->setFields(
                    array(
                        'Raison sociale' => 'x.raisonSociale',
                        'Ligne'          => 'l.numero',
                        '_identifier_'   => 'x.idAbonne', )
                )
                ->addJoin('x.affectations', 'a', \Doctrine\ORM\Query\Expr\Join::LEFT_JOIN)
                ->addJoin('a.ligne', 'l', \Doctrine\ORM\Query\Expr\Join::LEFT_JOIN)
                ->setWhere('a.fin is NULL')
                ->setSearch(true)
                ->setSearchFields(array(0, 1))
                ->setOrder('x.raisonSociale', 'desc')
                ->setHasAction(true);
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




  /**
     * @Route("/grille", name="abonnes_grille")
     * @Method("GET")
     * @Secure("ROLE_GESTION_ABONNES")
     *
     * @return json
     */
    public function grilleAction()
    {
        return $this->_datatable()->execute();
    }

    /**
     * @Route("/", name="abonnes_liste")
     * @Method("GET")
     * @Secure("ROLE_GESTION_ABONNES")
     * @Template("GestimeUserBundle:abonnes:index.html.twig")
     *
     * @return Template
     */
    public function indexAction()
    {
        $this->_datatable();

        return $this->render('GestimeUserBundle:abonnes:index.html.twig',
        array('menuactif' => 'Utilisateurs'));
    }

    /**
     * @Route("/ajouter", name="abonnes_ajouter")
     * @Method({"GET", "POST"})
     * @Secure("ROLE_GESTION_ABONNES")
     * @Template("GestimeUserBundle:abonnes:page.html.twig")
     *
     * @param request $request
     * @return Template
    */
    public function ajouterAction(Request $request)
    {
        $abonne = new Abonne($this->getUser()->getSite());
        $request = $this->getRequest();

        $form = $this->createForm(new AbonneType(), $abonne);
        $form->handleRequest($request);

        $entityManager = $this->getDoctrine()->getManager();

        if ($form->isValid()) {
            $abonneMgr = $this->container->get('gestime.abonne.manager');
            $abonneMgr->save_abonne($abonne);

            return $this->redirect($this->generateUrl('abonnes_liste'));
        }
        $this->_datatable();

        return array('medecinExiste' => null,
                      'abonne' => $abonne,
                      'action' => 'Ajouter un abonné',
                      'form' => $form->createView(),
                      'menuactif' => 'Utilisateurs',
        );
    }

    /**
     * @Route("/edit/{idAbonne}", name="abonnes_edit")
     * @Method({"GET", "POST"})
     * @Secure("ROLE_GESTION_ABONNES")
     * @Template("GestimeUserBundle:abonnes:page.html.twig")
     *
     * @param request $request
     * @param Abonne  $abonne
     * @return Template
    */
    public function editAction(Request $request, Abonne $abonne)
    {
        $abonneMgr = $this->container->get('gestime.abonne.manager');
        $periodesAvantModif = $abonneMgr->getPeriodes($abonne);

        $form = $this->createForm(new AbonneType(), $abonne);
        $form->handleRequest($request);
        $entityManager = $this->getDoctrine()->getManager();

        if ($form->isValid()) {
            $abonneMgr->save_edited_abonne($abonne, $periodesAvantModif);

            return $this->redirect($this->generateUrl('abonnes_liste'));
        }
        $this->_datatable();

        return array('medecinExiste' => null,
                      'action' => 'Modifier un abonné',
                      'form' => $form->createView(),
                      'menuactif' => 'Utilisateurs',
        );
    }

    /**
     * @Route("/suppr/{idAbonne}", name="abonnes_delete")
     * @Method({"GET", "POST"})
     * @Secure("ROLE_GESTION_ABONNES")
     * @Template("GestimeUserBundle:abonnes:page.html.twig")
     *
     * @param request $request
     * @param Abonne  $abonne
     * @return Template
    */
    public function deleteAction(Request $request, Abonne $abonne)
    {
        $abonneMgr = $this->container->get('gestime.abonne.manager');
        $medecinExiste = ($abonne->getMedecins()->count() > 0);
        $affectation = $abonne->getAffectation();

        $form = $this->createForm(new AbonneType(), $abonne,
            array('validation_groups' => false)
        );
        $form->handleRequest($request);

        if ($form->isValid()) {
            $abonneMgr->save_deleted_abonne($abonne, $affectation);

            return $this->redirect($this->generateUrl('abonnes_liste'));
        }
        $this->_datatable();

        return array('medecinExiste' => $medecinExiste,
                      'action' => 'Supprimer un abonné',
                      'form' => $form->createView(),
                      'menuactif' => 'Utilisateurs',
        );
    }
}
