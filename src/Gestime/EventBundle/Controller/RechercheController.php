<?php

/**
 * RechercheController class file
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
use Gestime\CoreBundle\Entity\Medecin;
use Gestime\CoreBundle\Entity\Recherche;
use Gestime\EventBundle\Form\Type\RechercheType;

/**
 * Recherche Controller
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class RechercheController extends Controller
{
    /**
     * @Secure("ROLE_GESTION_RDV")
     */
    /**
     * @return datatable Resultat de la recherche
     */
    private function _datatableDetail($medecinId, $nom, $prenom, $telephone)
    {
        $qb = $this->getDoctrine()->getManager()
          ->getRepository('GestimeCoreBundle:Evenement')->Recherche($medecinId,
              $nom,
              $prenom,
              $telephone
          );
        $controllerInstance = $this;
        $qbTable = $this->get('datatable')
              ->setFields(
                  array(
                      'Médecin'         => 'medNom',
                      'Nom'             => 'patientNom',
                      'Prénom'          => 'patientPrenom',
                      'Téléphone'       => 'patientTelephone',
                      'Type'            => 'rdvType',
                      'Date'            => 'rdvDebut',
                      '_identifier_'    => 'rdv.idEvenement', )
              )
                ->setRenderer(
                    function (&$data) use ($controllerInstance) {
                        foreach ($data as $key => $value) {
                            if ($key == 5) {
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

        $qbTable->getQueryBuilder()->setDoctrineQueryBuilder($qb);

        return $qbTable;
    }

    /**
     * @Route("/grille/{medecinId}/{nom}/{prenom}/{telephone}", name="recherche_grille")
     * @Method("GET")
     * @Secure("ROLE_GESTION_RDV")
     *
     * @param integer $medecinId
     * @param string  $nom
     * @param string  $prenom
     * @param string  $telephone
     * @return json
     */
    public function rechercheGrilleAction($medecinId, $nom, $prenom, $telephone)
    {
        return $this->_datatableDetail($medecinId, $nom, $prenom, $telephone)->execute();
    }

    /**
     * @Route("/", name="recherche",options={"expose"=true} )
     * @Route("/{searchText}", name="recherche_texte",options={"expose"=true} )
     * @Template("GestimeEventBundle:recherche:page.html.twig")
     *
     * @param Request $request
     * @param string  $searchText
     * @return Template
     */
    public function rechercheAction(Request $request, $searchText = null)
    {
        $search = new Recherche();

        if (is_numeric(substr($searchText, 0, 1))) {
            $search->telephone = $searchText;
        } else {
            $search->nom = $searchText;
        }

        if (!$this->getUser()->hasRole('ROLE_VISU_AGENDA_TOUS')) {
            $search->medecin = $this->getUser()->getMedecindefault();
        };

        $form = $this->createForm(new RechercheType(), $search, array(
                'attr' => array('user' => $this->getUser()), ));

        $this->_datatableDetail($this->getUser()->getMedecindefault()->getIdMedecin(), $search->nom, '[]', $search->telephone);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $medecinId = ($form->getData()->medecin instanceof Medecin) ? $form->getData()->medecin->getIdMedecin() : 0;
            $this->_datatableDetail($medecinId,
                $form->getData()->nom,
                $form->getData()->prenom,
                $form->getData()->telephone
            );
        }

        return array('action' => 'Recherche',
                      'form' => $form->createView(),
                      'menuactif' => 'Agenda',        );
    }
}
