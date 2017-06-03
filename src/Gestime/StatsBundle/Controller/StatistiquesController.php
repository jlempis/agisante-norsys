<?php

/**
 * StatistiquesController class file
 *
 * PHP Version 5.5
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\StatsBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Gestime\CoreBundle\Entity\AppelRecu;
use Gestime\CoreBundle\Entity\AppelRecuSearch;
use Gestime\StatsBundle\Form\Type\AppelRecuSearchType;

/**
 * Statistiques
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class StatistiquesController extends Controller
{
    /**
     * @Secure("ROLE_VISU_STATISTIQUES")
     */
    /**
     * Peuplement de la liste des appels recus
     * @return datatable Ensemble des appels recus
     */
    private function _datatableDetail($debut = null, $fin = null)
    {
        $debut === null ? date('Y-m-d') : $debut;
        $fin === null ? date('Y-m-d') : $fin;
        if (!is_object($fin)) {
            $fin .= ' 23:59:59';
        } else {
            $fin->modify('+1 day');
        }

        $qb = $this->getDoctrine()->getManager()
            ->getRepository('GestimeCoreBundle:AppelRecu')->DetailAppelsRecus($debut, $fin);

        $controllerInstance = $this;
        $qbTable = $this->get('datatable')
                ->setFields(
                    array(
                        'Date'          => 'a.callDay',
                        'SDA'           => 'a.sda',
                        'Origine'       => 'a.clid',
                        'Qui'           => 'a.agent',
                        'Abonné'        => 'ab.raisonSociale',
                        'Attente'       => 'a.dureeSonerie',
                        'Durée'         => 'a.dureeConversation',
                        'Total'         => 'a.dureeTotale',
                        'Médecin  '     => 'med.nom',
                        'Transfert'     => 'a.transfert',
                        '_identifier_'  => 'a.id', )
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
                ->setGroupBy('a.sda')
                ->setHasAction(false);

        $qbTable->getQueryBuilder()->setDoctrineQueryBuilder($qb);

        return $qbTable;
    }

    /**
     * @Route("/appel_recus_grille/{debut}/{fin}", name="appel_recus_detail_grille")
     * @Secure("ROLE_VISU_STATISTIQUES")
     *
     * @param string $debut
     * @param string $fin
     * @return json
     */
    public function appelsRecusDetailGrilleAction($debut, $fin)
    {
        return $this->_datatableDetail($debut, $fin)->execute();
    }

    /**
     * @Route("/appel_recus/detail", name="appel_recus_detail_liste")
     * @Secure("ROLE_VISU_STATISTIQUES")
     * @Template("GestimeStatsBundle:appels_recus_detail:page.html.twig")
     *
     * appelsRecusDetailAction
     * @param Request $request
     * @return Template
     */
    public function appelsRecusDetailAction(Request $request)
    {
        $search = new AppelRecuSearch();
        $form = $this->createForm(new AppelRecuSearchType(), $search);
        $form->handleRequest($request);

        $this->_datatableDetail($form->getData()->datedebut,
            $form->getData()->datefin
        );

        return array('form' => $form->createView(),
                      'menuactif' => 'Statistiques',
        );
    }

    /**
     * @Route("/appel_recus/groupe", name="appel_recus_groupe_liste")
     * @Secure("ROLE_VISU_STATISTIQUES")
     * @Template("GestimeStatsBundle:appels_recus_groupe:page.html.twig")
     *
     * appelsRecusGroupeAction
     * @param Request $request
     * @return Template
     */
    public function appelsRecusGroupeAction(Request $request)
    {
        $search = new AppelRecuSearch();
        $form = $this->createForm(new AppelRecuSearchType(), $search);
        $form->handleRequest($request);

        $debut = $form->getData()->datedebut;
        $fin = $form->getData()->datefin;
        $debut === null ? date('Y-m-d') : $debut;
        $fin === null ? date('Y-m-d') : $fin;
        if (!is_object($fin)) {
            $fin .= ' 23:59:59';
        } else {
            $fin->modify('+1 day');
        }
        $qbcountAappelsRecus = $this->getDoctrine()->getManager()
            ->getRepository('GestimeCoreBundle:AppelRecu')->countAappelsRecus($debut, $fin);

        $qbappelsRecusByJourByAbonne = $this->getDoctrine()->getManager()
            ->getRepository('GestimeCoreBundle:AppelRecu')->appelsRecusByJourByAbonne($debut, $fin);

        $qbappelsRecusByJour = $this->getDoctrine()->getManager()
            ->getRepository('GestimeCoreBundle:AppelRecu')->appelsRecusByJour($debut, $fin);

        $qbappelsRecusByAbonne = $this->getDoctrine()->getManager()
            ->getRepository('GestimeCoreBundle:AppelRecu')->appelsRecusByAbonne($debut, $fin);

        return array('form' => $form->createView(),
                      'nbAppelsPeriode' => $qbcountAappelsRecus->getQuery()->getSingleScalarResult(),
                      'dataByJourByAbonne' => $qbappelsRecusByJourByAbonne->getQuery()->getResult(),
                      'dataByJour' => $qbappelsRecusByJour->getQuery()->getResult(),
                      'dataByAbonne' => $qbappelsRecusByAbonne->getQuery()->getResult(),
                      'menuactif' => 'Statistiques',
        );
    }
}
