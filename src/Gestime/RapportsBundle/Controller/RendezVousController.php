<?php

/**
 * Rapport : Rendez-Vous
 *
 * PHP Version 5.5
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\RapportsBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Gestime\CoreBundle\Entity\Medecin;
use Gestime\CoreBundle\Entity\RapportFilter;
use Gestime\RapportsBundle\Form\Type\RapportFilterType;

/**
 * Recherche Controller
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class RendezVousController extends Controller
{
    /**
     * @Secure("ROLE_GESTION_RDV")
     */
    /**
     * Peuplement de la liste des appels recus
     * @return datatable Ensemble des appels recus
     */


    private $medecinId;
    private $debut;
    private $fin;




    private function _datatableDetail($medecinId, $debut, $fin)
    {
        $rapportMgr = $this->container->get('gestime.rapports.manager');
        $liste = $rapportMgr->getListeRendezVous($medecinId, $debut, $fin);

        $controllerInstance = $this;
        $qbTable = $this->get('datatable')
              ->setFields(
                  array(
                      'Date'            => 'rdv.debutRdv',
                      'Médecin'         => 'med.nom',
                      'Patient'         => "patient.nomcomplet",
                      'Objet'           => 'rdv.objet',
                      '_identifier_'    => 'rdv.idEvenement', )
              )
                ->setRenderer(
                    function (&$data) use ($controllerInstance) {
                        foreach ($data as $key => $value) {
                            if ($key == 0) {
                                $data[$key] = $controllerInstance
                                        ->get('templating')
                                        ->render(
                                            'GestimeCoreBundle:common:dateheure.html.twig',
                                            array('data' => $value)
                                        );
                            }
                        }
                    }
                )
              ->setHasAction(false);


        $qbTable->getQueryBuilder()->setDoctrineQueryBuilder($liste);

        return $qbTable;
    }

    /**
     * @Route("/rendezvous/grille/{medecinId}/{debut}/{fin}", name="rdvs_grille")
     * @Method("GET")
     * @Secure("ROLE_GESTION_RDV")
     *
     * @param integer  $medecinId
     * @param datetime $debut
     * @param datetime $fin
     * @return json
     */
    public function rechercheGrilleAction($medecinId, $debut, $fin)
    {
        return $this->_datatableDetail($medecinId, $debut, $fin)->execute();
    }

    /**
     * @Route("/rendezvous", name="rapports_rdvs",options={"expose"=true} )
     * @Template("GestimeRapportsBundle:RendezVous:page.html.twig")
     *
     * @param Request $request
     * @param string  $searchText
     * @return Template
     */
       public function rechercheAction(Request $request, $searchText = null)
    {
        $search = new RapportFilter();
        $form = $this->createForm(new RapportFilterType(), $search, array(
            'attr' => array('user' => $this->getUser()), ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $medecinId = (!$form->getData()->medecin instanceof Medecin) ? 0 : $form->getData()->medecin->getIdMedecin();
            if ($medecinId == 0 && !$this->getUser()->hasRole('ROLE_VISU_AGENDA_TOUS')) {
                $medecinId = $this->getUser()->getMedecindefault()->getIdMedecin();
            }

            $this->setMedecinId($medecinId);
            $this->setDebut($form->getData()->debut);
            $this->setFin($form->getData()->fin);

        } else {

            $this->setMedecinId($this->getUser()->getMedecindefault()->getIdMedecin());
            $this->setDebut('[]');
            $this->setFin('[]');

        }

        $this->_datatableDetail($this->getMedecinId(), $this->getDebut(), $this->getFin());

        return array('action' => 'Rapports - Mouvements',
            'form' => $form->createView(),
            'menuactif' => 'Rapports',        );
    }


    /**
     * @return mixed
     */
    public function getMedecinId()
    {
        return $this->medecinId;
    }

    /**
     * @param mixed $medecinId
     */
    public function setMedecinId($medecinId)
    {
        $this->medecinId = $medecinId;
    }

    /**
     * @return mixed
     */
    public function getDebut()
    {
        return $this->debut;
    }

    /**
     * @param mixed $debut
     */
    public function setDebut($debut)
    {
        $this->debut = $debut;
    }

    /**
     * @return mixed
     */
    public function getFin()
    {
        return $this->fin;
    }

    /**
     * @param mixed $fin
     */
    public function setFin($fin)
    {
        $this->fin = $fin;
    }

}
