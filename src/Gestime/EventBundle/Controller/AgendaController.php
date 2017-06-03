<?php

/**
 * AgendaController class file
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
use Gestime\CoreBundle\Entity\Evenement;
use Symfony\Component\HttpFoundation\JsonResponse;
use Gestime\EventBundle\Form\Type\MedecinAgendaListType;
use Gestime\CoreBundle\ErrorSerializer\FormErrorsSerializer;



/**
 * Agenda Controller
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class AgendaController extends Controller
{
    /**
    * @Route("/", name="agenda")
    * @Template("GestimeEventBundle:agenda:page.html.twig")
    *
    * @param Request $request
    * @return Template
    */
    public function viewAction(Request $request)
    {
        $eventMgr = $this->get('gestime.event.manager');
        $event = new Evenement();
        $formListeMedecin = $this->createForm(new MedecinAgendaListType(),
            $event,
            array('attr' => array('user' => $this->getUser()))
        );

        $form = $this->createForm($this->container->get('gestime_event.form.type.evenement'),
            $event,
            array('attr' => array('user' => $this->getUser(),
                                'nexcuse' => true, ))
        );

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            $response = new JsonResponse();

            if ($form->isValid()) {

                $response->setContent(json_encode(array('status' => 'success')));
                $eventMgr->saveEvent($event);
            } else {

                $errors = $this->get('form_serializer')->getErrorMessages($form);
                $response->setContent(json_encode(array(
                    'status' => 'error',
                    'datas' => $form->all(),
                    'errors' => $errors,
                    ))
                );
            }

            return $response;
        }

        return array('action' => 'Agenda',
                    'form' => $form->createView(),
                    'listeMedecins' => $formListeMedecin->createView(),
                    'menuactif' => 'Agenda',
                    'listeTypeRendezVous' => $eventMgr->getlisteTypeRendezVous(),
                    'afficheModale' => (false),
        );
    }
}
