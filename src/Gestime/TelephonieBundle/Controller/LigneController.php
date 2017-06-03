<?php

/**
 * LigneController class file
 *
 * PHP Version 5.5
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\TelephonieBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Gestime\CoreBundle\Entity\Ligne;
use Gestime\TelephonieBundle\Form\Type\LigneType;

/**
 * Lignes
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class LigneController extends Controller
{
    /**
     * @Secure(roles="ROLE_GESTION_LIGNES")
     */
    private function _datatable()
    {
        $qb = $this->getDoctrine()->getManager()
            ->getRepository('GestimeCoreBundle:Ligne')
            ->getListLignes($this->getUser()->getSite());

        $datatable = $this->get('datatable')
                ->setFields(
                    array(
                        'Ligne'          => 'l.numero',
                        '_identifier_'   => 'l.idLigne', )
                )
                ->setSearch(true)
                ->setSearchFields(array(0, 1))
                ->setHasAction(true);

        $datatable->getQueryBuilder()->setDoctrineQueryBuilder($qb);

        return $datatable;
    }

    /**
     * @Route("/lignes/grille", name="lignes_grille")
     * @Method("GET")
     * @Secure("ROLE_GESTION_LIGNES")
     *
     * @return Template
     */
    public function grilleAction()
    {
        return $this->_datatable()->execute();
    }

    /**
     * @Route("/lignes", name="lignes_liste")
     * @Method("GET")
     * @Secure("ROLE_GESTION_LIGNES")
     * @Template("GestimeTelephonieBundle:lignes:index.html.twig")
     *
     * @return Template
     */
    public function indexAction()
    {
        $this->_datatable();

        return $this->render('GestimeTelephonieBundle:lignes:index.html.twig', array('menuactif' => 'Telephonie'));
    }

    /**
     * @Route("/lignes/ajouter", name="lignes_ajouter")
     * @Secure("ROLE_GESTION_LIGNES")
     * @Template("GestimeTelephonieBundle:lignes:ajouter.html.twig")
     *
     * @param request $request
     * @return Template
     */
    public function ajouterAction(Request $request)
    {
        $ligne = new Ligne($this->getUser()->getSite());
        $form = $this->createForm(new LigneType(), $ligne);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $ligneMgr = $this->container->get('gestime.ligne.manager');
            $ligneMgr->saveLigne($ligne);

            return $this->redirect($this->generateUrl('lignes_liste'));
        }
        $this->_datatable();

        return array('ligne' => $ligne,
                     'form' => $form->createView(),
                     'menuactif' => 'Telephonie',
        );
    }

    /**
     * @Route("/lignes/edit/{idLigne}", name="lignes_edit")
     * @Secure("ROLE_GESTION_LIGNES")
     * @Template("GestimeTelephonieBundle:lignes:editer.html.twig")
     *
     * @param request $request
     * @param Ligne   $ligne
     * @return Template
     */
    public function editAction(Request $request, Ligne $ligne)
    {
        $form = $this->createForm(new LigneType(), $ligne,
        array( 'action' => 'edit' ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $ligneMgr = $this->container->get('gestime.ligne.manager');
            $ligneMgr->saveLigne($ligne);

            return $this->redirect($this->generateUrl('lignes_liste'));
        }
        $this->_datatable();

        return array('form' => $form->createView(),
                      'menuactif' => 'Telephonie',
        );
    }

    /**
     * @Route("/lignes/suppr/{idLigne}", name="lignes_delete")
     * @Secure("ROLE_GESTION_LIGNES")
     * @Template("GestimeTelephonieBundle:lignes:supprimer.html.twig")
     *
     * @param request $request
     * @param Ligne   $ligne
     * @return Template
     */
    public function deleteAction(Request $request, Ligne $ligne)
    {
        $ligneMgr = $this->container->get('gestime.ligne.manager');
        $encoreAffectee = $ligneMgr->estAffectee($ligne);
        $frmOptions = ($encoreAffectee) ? array( 'action' => 'suppr_disabled') : array( 'action' => 'suppr');

        $form = $this->createForm(new LigneType(), $ligne, $frmOptions);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $ligneMgr->deleteLigne($ligne);

            return $this->redirect($this->generateUrl('lignes_liste'));
        }
        $this->_datatable();

        return array('encoreAffectee' => $encoreAffectee,
                      'action' => 'Supprimer une ligne',
                      'form' => $form->createView(),
                      'menuactif' => 'Telephonie',
        );
    }
}
