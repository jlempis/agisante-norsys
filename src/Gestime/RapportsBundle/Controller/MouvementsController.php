<?php

/**
 * Mouvements
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
class MouvementsController extends Controller
{

    private $medecinId;
    private $debut;
    private $fin;

    /**
     * Formatte une ligne du tableau des mouvments ( Valeur avant, Valeur après)
     * @return array
     */
    private function formatteData($data, $columnId)
    {
        //Si ce n'est pas une modification, on sort (Attn Format Date)
        $nbElements = 5;
        switch ($columnId) {
            case 0:
                return array('type' => 'typemvt', 'avant' => $data[$columnId], 'apres' => null);
                break;
            case 1:
                return array('type' => 'createur', 'avant' => $data[$columnId], 'apres' => $data[$columnId+$nbElements]);
                break;
            case 2:
                return array('type' => 'datedebut', 'avant' => $data[$columnId], 'apres' => $data[$columnId+$nbElements]);
                break;
            case 3:
                return array('type' => 'datefin', 'avant' => $data[$columnId], 'apres' => $data[$columnId+$nbElements]);
                break;
            case 4:
                return array('type' => 'patient', 'avant' => $data[$columnId], 'apres' => $data[$columnId+$nbElements]);
                break;
            case 5:
                return array('type' => 'objet', 'avant' => $data[$columnId], 'apres' => $data[$columnId+$nbElements]);
                break;
            default:
                return $data;
                break;
        }
    }

    /**
     * Peuplement de la liste des mouvements
     * @return datatable Ensemble des mouvements
     */
    private function _datatableDetail($medecinId, $debut, $fin)
    {
        $rapportMgr = $this->container->get('gestime.rapports.manager');
        $liste = $rapportMgr->getListeMouvements($medecinId, $debut, $fin);

        $controllerInstance = $this;
        $qbTable = $this->get('datatable')
              ->setFields(
                  array(
                    'Mvt'              => 'Relation.type',
                    'Créateur'         => 'gPrecName',
                    'debut Rdv'        => 'EvenementPrecedent.debutRdv',
                    'fin Rdv'          => 'EvenementPrecedent.finRdv',
                    'Patient'          => 'Concat(CivilitePatientPrecedent.value," ", PatientPrecedent.prenom, " ", PatientPrecedent.nom) as ContactPrecedent',
                    'Objet'            => 'EvenementPrecedent.objet',
                    'Créateur2'        => 'gSuivName',
                    'datedebut2'       => 'EvenementSuivant.debutRdv',
                    'datefin2'         => 'EvenementSuivant.finRdv',
                    'civilitePatient2' => 'Concat(CivilitePatientSuivant.value," ", PatientSuivant.prenom, " ", PatientSuivant.nom) as ContactSuivant',
                    'Objet2'           => 'EvenementSuivant.objet',
                    '_identifier_'     => 'Relation.id')
              )
                ->setRenderer(
                    function (&$data) use ($controllerInstance) {
                        foreach ($data as $key => $value) {
                            if ($key < 6) {
                                $data[$key] = $controllerInstance
                                        ->get('templating')
                                        ->render(
                                            'GestimeRapportsBundle:Mouvements:templates/mouvements.html.twig',
                                            array('data' => $this->formatteData($data, $key))
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
     * @Route("/mouvements/grille/{medecinId}/{debut}/{fin}", name="mvts_grille")
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
     * @Route("/mouvements", name="rapports_mvts",options={"expose"=true} )
     * @Template("GestimeRapportsBundle:Mouvements:page.html.twig")
     *
     * @param request $request
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
