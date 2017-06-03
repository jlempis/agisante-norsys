<?php

/**
 * RepondeurController class file
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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Gestime\CoreBundle\Entity\Repondeur;
use Gestime\TelephonieBundle\Form\Type\RepondeurType;

/**
 * Repondeur
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class RepondeurController extends Controller
{
    /**
     * @Secure(roles="ROLE_GESTION_REPONDEURS")
     */
    private function _datatable()
    {
        return $this->get('datatable')
                ->setEntity('GestimeCoreBundle:Repondeur', 'x')
                ->setFields(
                    array(
                        'Tag'               => 'x.tag',
                        'Nom du fichier'    => 'x.name',
                        'Commentaire'       => 'x.commentaire',
                        '_identifier_'      => 'x.idRepondeur', )
                )
                ->setSearch(true)
                ->setSearchFields(array(0, 1))
                ->setOrder('x.tag', 'desc')
                ->setHasAction(true);
    }

    /**
     * @Route("/repondeurs/grille", name="repondeurs_grille")
     * @Method("GET")
     * @Secure("ROLE_GESTION_REPONDEURS")
     *
     * @return json
     */
    public function grilleAction()
    {
        return $this->_datatable()->execute();
    }

    /**
     * @Route("/repondeurs", name="repondeurs_liste")
     * @Method("GET")
     * @Secure("ROLE_GESTION_REPONDEURS")
     * @Template("GestimeTelephonieBundle:repondeurs:index.html.twig")
     *
     * @return Template
     */
    public function indexAction()
    {
        $this->_datatable();

        return array('menuactif' => 'Telephonie');
    }

    /**
     * @Route("/repondeurs/ajouter", name="repondeurs_ajouter")
     * @Secure("ROLE_GESTION_REPONDEURS")
     * @Template("GestimeTelephonieBundle:repondeurs:page.html.twig")
    *
    * [ajouterAction description]
    * @param Request $request
    * @return Template
    */
    public function ajouterAction(Request $request)
    {
        $repondeur = new Repondeur();
        $form = $this->createForm(new RepondeurType(), $repondeur);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $repondeurMgr = $this->container->get('gestime.repondeur.manager');
            $repondeurMgr->save_repondeur($repondeur);

            return $this->redirect($this->generateUrl('repondeurs_liste'));
        }
        $this->_datatable();

        return array('repondeur' => '',
                      'action' => 'Ajouter un répondeur',
                      'actif' => false,
                      'form' => $form->createView(),
                      'menuactif' => 'Telephonie',
        );
    }

    /**
     * @Route("/repondeurs/edit/{idRepondeur}", name="repondeurs_edit")
     * @Secure("ROLE_GESTION_REPONDEURS")
     * @Template("GestimeTelephonieBundle:repondeurs:page.html.twig")
     *
     * [editAction description]
     * @param Request   $request
     * @param Repondeur $repondeur
     * @return Template
     */
    public function editAction(Request $request, Repondeur $repondeur)
    {
        $form = $this->createForm(new RepondeurType(), $repondeur);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $repondeurMgr = $this->container->get('gestime.repondeur.manager');
            $repondeurMgr->save_edited_repondeur($repondeur);

            return $this->redirect($this->generateUrl('repondeurs_liste'));
        }

        $this->_datatable();

        return array('repondeur' => $repondeur->getNameWithoutExt(),
                      'action' => 'Modifier un répondeur',
                      'actif' => true,
                      'form' => $form->createView(),
                      'menuactif' => 'Telephonie',
        );
    }

    /**
     * @Route("/repondeurs/suppr/{idRepondeur}", name="repondeurs_delete")
     * @Secure("ROLE_GESTION_REPONDEURS")
     * @Template("GestimeTelephonieBundle:repondeurs:page.html.twig")
     *
     * [editAction description]
     * @param Request   $request
     * @param Repondeur $repondeur
     * @return Template
     */
    public function deleteAction(Request $request, Repondeur $repondeur)
    {
        $repondeurMgr = $this->container->get('gestime.repondeur.manager');
        $estactif=$repondeurMgr->isRepondeurActif($repondeur);
        $frmOptions = ($estactif) ? array( 'action' => 'suppr_disabled') : array( 'action' => 'suppr');

        $form = $this->createForm(new RepondeurType(), $repondeur, $frmOptions);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $repondeurMgr->save_deleted_repondeur($repondeur);

            return $this->redirect($this->generateUrl('repondeurs_liste'));
        }
        $this->_datatable();

        return array('repondeur' => $repondeur->getNameWithoutExt(),
                      'action' => 'Supprimer un répondeur',
                      'actif' => $estactif,
                      'form' => $form->createView(),
                      'menuactif' => 'Telephonie',
        );
    }
}
