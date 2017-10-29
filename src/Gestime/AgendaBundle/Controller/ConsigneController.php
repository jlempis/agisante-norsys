<?php

/**
 * ConsigneController class file
 *
 * PHP Version 5.5
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\AgendaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Gestime\CoreBundle\Entity\Consigne;
use Gestime\AgendaBundle\Form\Type\ConsigneType;

/**
 * Consigne
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class ConsigneController extends Controller
{
    /**
     * @Secure(roles="ROLE_GESTION_CONSIGNES")
     */
    private function _datatable()
    {
        $qb = $this->getDoctrine()->getManager()
            ->getRepository('GestimeCoreBundle:Consigne')
            ->getListConsignes();

        $controllerInstance = $this;
        $datatable = $this->get('datatable')
                ->setFields(
                    array(
                        'Medecin'           => 'm.nom',
                        'Patient'           => 'p.nom',
                        'Début'             => 'c.debut',
                        'Fin'               => 'c.fin',
                        'Description'       => 'c.description',
                        'Visible'           => 'c.visible',
                        'Bloquante'         => 'c.bloquante',
                        '_identifier_'      => 'c.idConsigne', )
                )
                ->setRenderer(
                    function (&$data) use ($controllerInstance) {
                        foreach ($data as $key => $value) {
                            if ($key == 2 || $key == 3) {
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
                ->setSearchFields(array(0, 3))
                ->setHasAction(true);
        $datatable->getQueryBuilder()->setDoctrineQueryBuilder($qb);

        return $datatable;
    }

    /**
     * @Route("/grille", name="consignes_grille")
     * @Method("GET")
     * @Secure("ROLE_GESTION_CONSIGNES")
     *
     * [grilleAction description]
     * @return json [description]
     */
    public function grilleAction()
    {
        return $this->_datatable()->execute();
    }

    /**
     * @Route("/", name="consignes")
     * @Method("GET")
     * @Secure("ROLE_GESTION_CONSIGNES")
     * @Template("GestimeAGendaBundle:consignes:index.html.twig")
     *
     * [indexAction description]
     * @return json [description]
     */
    public function indexAction()
    {
        $this->_datatable();

        return $this->render('GestimeAgendaBundle:consignes:index.html.twig', array('menuactif' => 'Agenda'));
    }

    /**
     * @Route("/ajax", name="ajax")
     * @Secure("ROLE_GESTION_CONSIGNES")
     *
     * [ajaxAction description]
     * @param Request $request
     * @return json
     */
    public function ajaxAction(Request $request)
    {
        $value = $request->get('term');

        $listPatients = $this->getDoctrine()->getManager()
            ->getRepository('GestimeCoreBundle:Personne')
            ->findPatientsLike($value);

        $response = new JsonResponse();
        $response->setContent(json_encode($listPatients));

        return $response;
    }

    /**
     * @Route("/ajouter", name="consignes_ajouter")
     * @Secure("ROLE_GESTION_CONSIGNES")
     * @Template("GestimeAgendaBundle:consignes:page.html.twig")
     *
     * [ajouterAction description]
     * @param Request $request
     * @return array
     */
    public function ajouterAction(Request $request)
    {
        $consigne = new Consigne();
        $entityManager = $this->getDoctrine()->getManager();

        $form = $this->createForm(new ConsigneType(), $consigne, array(
                'attr' => array('user' => $this->getUser()), ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $patient = $entityManager->getRepository('GestimeCoreBundle:Personne')
                         ->findById(intval($form->getData()->getPatientFormId()));

            if (count($patient) > 0) {
                $consigne->setPatient($patient[0]);
            }
            $entityManager->persist($consigne);
            $entityManager->flush();

            return $this->redirect($this->generateUrl('consignes'));
        }

        $this->_datatable();

        return array('action' => 'Ajouter une consigne',
                      'Consigne' => $consigne,
                      'form' => $form->createView(),
                      'menuactif' => 'Agenda',
        );
    }

    /**
     * @Route("/edit/{idConsigne}", name="consignes_edit")
     * @Secure("ROLE_GESTION_CONSIGNES")
     * @Template("GestimeAgendaBundle:consignes:page.html.twig")
     *
     * [editAction description]
     * @param Request  $request
     * @param Consigne $consigne
     * @return array
     */
    public function editAction(Request $request, Consigne $consigne)
    {
        $action = 'edit';
        $form = $this->createForm(
            new ConsigneType(),
            $consigne,
            array('read_only' => false,
                  'action' => $action,
                  'attr' => array('user' => $this->getUser()), )
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $patient = $entityManager->getRepository('GestimeCoreBundle:Personne')
                         ->findById(intval($form->getData()->getPatientFormId()));
            if (count($patient) > 0) {
                $consigne->setPatient($patient[0]);
            }

            $entityManager->persist($consigne);
            $entityManager->flush();

            return $this->redirect($this->generateUrl('consignes'));
        }

        $this->_datatable();

        return array('action' => 'Mettre à jour une consigne',
                      'form'   => $form->createView(),
                      'menuactif' => 'Agenda',
        );
    }

    /**
     * @Route("/delete/{idConsigne}", name="consignes_delete")
     * @Secure("ROLE_GESTION_CONSIGNES")
     * @Template("GestimeAgendaBundle:consignes:page.html.twig")
     *
     * [deleteAction description]
     * @param Request  $request
     * @param Consigne $consigne
     * @return array
     */
    public function deleteAction(Request $request, Consigne $consigne)
    {
        $action = 'suppr';
        $form = $this->createForm(
            new ConsigneType(),
            $consigne,
            array('read_only' => false,
                  'action' => $action,
                  'attr' => array('user' => $this->getUser()), )
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($consigne);
            $entityManager->flush();

            return $this->redirect($this->generateUrl('consignes'));
        }
        $this->_datatable();

        return array('action'  => 'Supprimer une consigne',
                      'form'    => $form->createView(),
                      'menuactif' => 'Agenda',
        );
    }
}
