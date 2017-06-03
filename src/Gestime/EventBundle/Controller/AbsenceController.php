<?php

/**
 * AbsenceController class file
 *
 * PHP Version 5.5
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\EventBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Gestime\CoreBundle\Entity\Absence;
use Gestime\EventBundle\Form\Type\AbsenceType;

/**
 * Absence
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class AbsenceController extends Controller
{
    /**
     * @return \Ali\DatatableBundle\Util\Datatable
     */
    private function _datatable()
    {
        $controllerInstance = $this;
        $qb = $this->getDoctrine()->getManager()
            ->getRepository('GestimeCoreBundle:Absence')
            ->getAbsencesUser(
                $this->getUser()->hasRole('ROLE_VISU_AGENDA_TOUS'),
                $this->getUser()->getSite(),
                $this->getUser()->getId()
            );

        $datatable = $this->get('datatable')
                ->setFields(
                    array(
                        'Médecin'       => 'm.nom',
                        'Début'         => 'a.debut',
                        'Fin'           => 'a.fin',
                        'Commentaire'   => 'a.commentaire',
                        '_identifier_'  => 'a.idAbsence', )
                )
                ->setRenderer(
                    function (&$data) use ($controllerInstance) {
                        foreach ($data as $key => $value) {
                            if ($key == 1 || $key == 2) {
                                $data[$key] = $controllerInstance
                                        ->get('templating')
                                        ->render(
                                            'GestimeCoreBundle:common:date.html.twig',
                                            array('data' => $value)
                                        );
                            }
                        }
                    }
                )
                ->setSearch(true)
                ->setSearchFields(array(0, 1))
                ->setHasAction(true);
        $datatable->getQueryBuilder()->setDoctrineQueryBuilder($qb);

        return $datatable;
    }

    /**
     * @Route("/grille", name="absence_grille")
     * @Method("GET")
     * @Secure("ROLE_GESTION_ABSENCES")
     *
     * @return json
     */
    public function grilleAction()
    {
        return $this->_datatable()->execute();
    }

    /**
     * @Route("/", name="absences")
     * @Method("GET")
     * @Secure("ROLE_GESTION_ABSENCES")
     * @Template("GestimeEventBundle:ABSENCES:index.html.twig")
     *
     * @return Template
     */
    public function indexAction()
    {
        $this->_datatable();

        return $this->render('GestimeEventBundle:absences:index.html.twig',
            array('menuactif' => 'Agenda')
        );
    }

    /**
    * @Route("/ajouter", name="absence_ajouter")
    * @Secure("ROLE_GESTION_ABSENCES")
    * @Template("GestimeEventBundle:absences:page.html.twig")
    *
    * [ajouterAction description]
    * @param Request $request
    * @return Template
    */
    public function ajouterAction(Request $request)
    {
        $absence = new Absence();
//        $request = $this->getRequest();

        $form = $this->createForm(new AbsenceType(), $absence, array(
                'attr' => array('user' => $this->getUser()), ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $absenceMgr = $this->container->get('gestime.absence.manager');
            $absenceMgr->save_absence($absence);

            return $this->redirect($this->generateUrl('absences'));
        }
        $this->_datatable();

        return array('Absence' => $absence,
                      'action' => 'Ajouter une absence',
                      'form' => $form->createView(),
                      'menuactif' => 'Agenda',
        );
    }
    /**
     * @Route("/edit/{idAbsence}", name="absence_edit")
     * @Secure("ROLE_GESTION_ABSENCES")
     * @Template("GestimeEventBundle:absences:page.html.twig")
     *
     * [editAction description]
     * @param Request $request
     * @param Absence $absence
     * @return Template
     */
    public function editAction(Request $request, Absence $absence)
    {
        $absenceMgr = $this->container->get('gestime.absence.manager');
        $remplacementsAvantModif = $absenceMgr->getRemplacements($absence);

        $form = $this->createForm(new AbsenceType(), $absence, array(
                'attr' => array('user' => $this->getUser()), ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $absenceMgr->save_edited_absence($absence, $remplacementsAvantModif);

            return $this->redirect($this->generateUrl('absences'));
        }
        $this->_datatable();

        return array('action' => 'Modifier une absence',
                      'form' => $form->createView(),
                      'menuactif' => 'Agenda',
        );
    }

    /**
     * @Route("/suppr/{idAbsence}", name="absence_delete")
     * @Secure("ROLE_GESTION_ABSENCES")
     * @Template("GestimeEventBundle:absences:page.html.twig")
     *
     * [deleteAction description]
     * @param Request $request
     * @param Absence $absence
     * @return Template
     */
    public function deleteAction(Request $request, Absence $absence)
    {
        $absenceMgr = $this->container->get('gestime.absence.manager');
        $remplacements = $absenceMgr->getRemplacements($absence);

        $form = $this->createForm(new AbsenceType(), $absence, array(
                'validation_groups' => false,
                'attr' => array('user' => $this->getUser()), ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $absenceMgr->save_deleted_Absence($absence, $remplacements);

            return $this->redirect($this->generateUrl('absences'));
        }
        $this->_datatable();

        return array('action' => 'Supprimer une absence',
                      'form' => $form->createView(),
                      'menuactif' => 'Agenda',
        );
    }
}
