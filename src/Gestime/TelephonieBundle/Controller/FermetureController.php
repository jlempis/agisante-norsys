<?php

/**
 * Fermeture·Controller class file
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
use Gestime\CoreBundle\Entity\Fermeture;
use Gestime\TelephonieBundle\Form\Type\FermetureType;

/**
 * Fermetures
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class FermetureController extends Controller
{
    /**
     * @Secure(roles="ROLE_GESTION_FERMETURES")
     */
    private function _datatable()
    {
        $controllerInstance = $this;

        return $this->get('datatable')
                ->setEntity('GestimeCoreBundle:Fermeture', 'x')
                ->setFields(
                    array(
                        'Date de début'     => 'x.datedebut',
                        'H. deb'            => 'x.heuredebut',
                        'Date de fin'       => 'x.datefin',
                        'H. fin'            => 'x.heurefin',
                        'Repondeur'         => 'r.tag',
                        'Commentaire'       => 'x.commentaire',
                        '_identifier_'      => 'x.idFermeture', )
                )
                ->addJoin('x.repondeur', 'r', \Doctrine\ORM\Query\Expr\Join::LEFT_JOIN)
                ->setRenderer(
                    /**
                     * @param $data
                     */
                    function (&$data) use ($controllerInstance) {
                        foreach ($data as $key => $value) {
                            if ($key == 0 || $key == 2) {
                                $data[$key] = $controllerInstance
                                        ->get('templating')
                                        ->render(
                                            'GestimeTelephonieBundle:fermetures:coldate_table.html.twig',
                                            array('data' => $value)
                                        );
                            }
                        }
                    }
                )
                ->setSearch(true)
                ->setOrder('x.datedebut', 'desc')
                ->setHasAction(true);
    }

    /**
     * @Route("/fermetures/grille", name="fermetures_grille")
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
     * @Route("/fermetures", name="fermetures_liste")
     * @Method("GET")
     * @Secure("ROLE_GESTION_FERMETURES")
     * @Template("GestimeTelephonieBundle:fermetures:index.html.twig")
     *
     * @return Template
     */
    public function indexAction()
    {
        $this->_datatable();

        return $this->render('GestimeTelephonieBundle:fermetures:index.html.twig', array('menuactif' => 'Telephonie'));
    }

    /**
     * @Route("/fermetures/ajouter", name="fermetures_ajouter")
     * @Secure("ROLE_GESTION_FERMETURES")
     * @Template("GestimeTelephonieBundle:fermetures:page.html.twig")
    *
    * [ajouterAction description]
    * @param Request $request [description]
    * @return [type]           [description]
    */
    public function ajouterAction(Request $request)
    {
        $fermeture = new Fermeture();
        $form = $this->createForm(new FermetureType(), $fermeture);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($fermeture);
            $entityManager->flush();

            return $this->redirect($this->generateUrl('fermetures_liste'));
        }
        $this->_datatable();

        return array('form' => $form->createView(),
                     'action' => 'Ajouter une période de fermeture',
                     'menuactif' => 'Telephonie',
        );
    }

    /**
     * @Route("/fermetures/edit/{idFermeture}", name="fermetures_edit")
     * @Secure("ROLE_GESTION_FERMETURES")
     * @Template("GestimeTelephonieBundle:fermetures:page.html.twig")
     *
     * [editAction description]
     * @param Request   $request
     * @param Fermeture $fermeture
     * @return Template
     */
    public function editAction(Request $request, Fermeture $fermeture)
    {
        $form = $this->createForm(new FermetureType(), $fermeture);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($fermeture);
            $entityManager->flush();

            return $this->redirect($this->generateUrl('fermetures_liste'));
        }

        $this->_datatable();

        return array('form' => $form->createView(),
                      'action' => 'Modifier une période de fermeture',
                      'menuactif' => 'Telephonie',
        );
    }

    /**
     * @Route("/fermetures/suppr/{idFermeture}", name="fermetures_delete")
     * @Secure("ROLE_GESTION_FERMETURES")
     * @Template("GestimeTelephonieBundle:fermetures:page.html.twig")
     *
     * [deleteAction description]
     * @param Request   $request
     * @param Fermeture $fermeture
     * @return Template
     */
    public function deleteAction(Request $request, Fermeture $fermeture)
    {
        $frmOptions = array( 'action' => 'suppr');
        $form = $this->createForm(new FermetureType(), $fermeture);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($fermeture);
            $entityManager->flush();

            return $this->redirect($this->generateUrl('fermetures_liste'));
        }
        $this->_datatable();

        return array('form' => $form->createView(),
                      'action' => 'Supprimer une période de fermeture',
                      'menuactif' => 'Telephonie',
        );
    }
}
