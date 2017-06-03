<?php

/**
 * Controller Impression
 *
 * PHP Version 5.5
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\RapportsBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;

/**
 * Recherche Controller
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class PrintController extends Controller
{
    private function footer($medecin, $titre)
    {
        return array('footer-line'       => true,
                        'footer-center'     => $titre,
                        'footer-left'       => $medecin->getPrenom().' '.$medecin->getNom(),
                        'footer-right'      => 'Page [page] / [topage]',
                        'footer-font-name'  => 'Trebuchet MS',
                        'footer-font-size'  => '9', );
    }

    private function entete($site)
    {
        return array('nom'            => $site->getNom(),
                    'raisonSociale' => $site->getRaisonSociale(),
                    'telephone'     => $site->getTelephone(),
                    'fax'           => $site->getFax(), );
    }

    /**
     * @Route("/imprimer/nexc/{medecinId}/{debut}/{fin}", name="pdf_nexc",options={"expose"=true} )
     * @Secure("ROLE_GESTION_RDV")
     *
     * [printNonExcusesAction description]
     * @param integer   $medecinId [description]
     * @param datetime  $debut     [description]
     * @param datetime] $fin       [description]
     * @return Response            [description]
     */
    public function printNonExcusesAction($medecinId, $debut, $fin)
    {
        $rapportMgr = $this->container->get('gestime.rapports.manager');

        $liste = $rapportMgr->getListeNonExcuses($medecinId, $debut, $fin)
                          ->getQuery()
                          ->getResult();
        $medecin = $rapportMgr->getMedecinbyId($medecinId);

        $entete = $this->entete($this->getUser()->getSite());
        $options = $this->footer($medecin, 'Liste des patients non excuses');

        $html = $this->renderView('GestimeRapportsBundle:nonExcuses:pdf.html.twig',
            array('entete'     => $entete,
              'docteur'    => $medecin,
              'debut'      => $debut,
              'fin'        => $fin,
              'liste'      => $liste, ));

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html, $options),
            200,
            array(
              'Content-Type'          => 'application/pdf',
            )
        );
    }

    /**
     * @Route("/imprimer/mvts/{medecinId}/{debut}/{fin}", name="pdf_mvts",options={"expose"=true} )
     * @Secure("ROLE_GESTION_RDV")
    *
     * [printMouvementsAction description]
     * @param integer   $medecinId [description]
     * @param datetime  $debut     [description]
     * @param datetime] $fin       [description]
     * @return Response            [description]
     */
    public function printMouvementsAction($medecinId, $debut, $fin)
    {
        $rapportMgr = $this->container->get('gestime.rapports.manager');
        $liste = $rapportMgr->getListeMouvements($medecinId, $debut, $fin)
                          ->getQuery()
                          ->getArrayResult();

        $medecin = $rapportMgr->getMedecinbyId($medecinId);

        $entete = $this->entete($this->getUser()->getSite());
        $options = $this->footer($medecin, 'Liste des mouvements');

        $html = $this->renderView('GestimeRapportsBundle:Mouvements:pdf.html.twig',
            array('entete'     => $entete,
               'docteur'    => $medecin,
               'debut'      => $debut,
               'fin'        => $fin,
               'liste'      => $liste, ));

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html, $options),
            200,
            array(
                'Content-Type'          => 'application/pdf',
            )
        );
    }

    /**
     * @Route("/imprimer/rdvs/{medecinId}/{debut}/{fin}", name="pdf_rdvs",options={"expose"=true} )
     * @Secure("ROLE_GESTION_RDV")
     *
     * [printRendezVousAction description]
     * @param integer  $medecinId [description]
     * @param datetime $debut     [description]
     * @param datetime $fin       [description]
     * @return Response           [description]
     */
    public function printRendezVousAction($medecinId, $debut, $fin)
    {
        $rapportMgr = $this->container->get('gestime.rapports.manager');

        $liste = $rapportMgr->getListeRendezVous($medecinId, $debut, $fin)
                          ->getQuery()
                          ->getResult();
        $medecin = $rapportMgr->getMedecinbyId($medecinId);

        $entete = $this->entete($this->getUser()->getSite());
        $options = $this->footer($medecin, 'Liste des rendez-vous');

        $html = $this->renderView('GestimeRapportsBundle:RendezVous:pdf.html.twig',
            array('entete'     => $entete,
                 'docteur'    => $medecin,
                 'debut'      => $debut,
                 'fin'        => $fin,
                 'liste'      => $liste, ));

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html, $options),
            200,
            array(
                'Content-Type'          => 'application/pdf',
            )
        );
    }

    /**
     * @Route("/imprimer/messages/{medecinId}/{debut}/{fin}", name="pdf_messages",options={"expose"=true} )
     * @Secure("ROLE_GESTION_RDV")
     *
     * [printMessagesAction description]
     * @param integer   $medecinId [description]
     * @param datetime  $debut     [description]
     * @param datetime] $fin       [description]
     * @return Response            [description]
     */
    public function printMessagesAction($medecinId, $debut, $fin)
    {
        $rapportMgr = $this->container->get('gestime.rapports.manager');

        $liste = $rapportMgr->getListeMessages($medecinId, $debut, $fin)
                          ->getQuery()
                          ->getResult();
        $medecin = $rapportMgr->getMedecinbyId($medecinId);

        $entete = $this->entete($this->getUser()->getSite());
        $options = $this->footer($medecin, 'Liste des messages');

        $html = $this->renderView('GestimeRapportsBundle:Messages:pdf.html.twig',
            array('entete'     => $entete,
                 'docteur'    => $medecin,
                 'debut'      => $debut,
                 'fin'        => $fin,
                 'liste'      => $liste, ));

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html, $options),
            200,
            array(
                'Content-Type'          => 'application/pdf',
            )
        );
    }

    /**
     * @Route("/imprimer/sms/{medecinId}/{debut}/{fin}", name="pdf_sms",options={"expose"=true} )
     * @Secure("ROLE_GESTION_RDV")
     *
     * [printSmsAction description]
     * @param integer  $medecinId [description]
     * @param datetime $debut     [description]
     * @param datetime $fin       [description]
     * @return Response           [description]
     */
    public function printSmsAction($medecinId, $debut, $fin)
    {
        $rapportMgr = $this->container->get('gestime.rapports.manager');

        $liste = $rapportMgr->getListeSms($medecinId, $debut, $fin)
                          ->getQuery()
                          ->getResult();
        $medecin = $rapportMgr->getMedecinbyId($medecinId);

        $entete = $this->entete($this->getUser()->getSite());
        $options = $this->footer($medecin, 'Liste des Sms');

        $html = $this->renderView('GestimeRapportsBundle:Sms:pdf.html.twig',
            array('entete'     => $entete,
                 'docteur'    => $medecin,
                 'debut'      => $debut,
                 'fin'        => $fin,
                 'liste'      => $liste, ));

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html, $options),
            200,
            array(
                'Content-Type'          => 'application/pdf',
            )
        );
    }
}
