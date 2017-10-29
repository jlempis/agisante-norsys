<?php

/**
 * Page d'accueil
 *
 * PHP Version 5.5
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Accueil
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class HomeController extends Controller
{
    /**
     * @Route("/", name="gestime_home")
     * @Method("GET")
     *
     * @return Response
     */
    public function indexAction()
    {
        $homeMgr = $this->get('gestime.home.manager');
        $messageMgr = $this->container->get('gestime.message.manager');
        $medecin = $this->getUser()->getMedecindefault();


        $listMessagesArray = $messageMgr->getMessagesListePaginee(
            $this->getUser(),
            null,
            'Reception',
            '',
            't',
            1,
            10
        );

        if ($this->getUser()->hasGroup('MEDECIN') || $this->getUser()->hasGroup('SECRETAIRE-SITE')) {
            $aujourdhui = new \DateTime();
            $infosRdv=$homeMgr->getFirstLastNextEvent($medecin, $aujourdhui->format('Y-m-d'));

            if ($medecin->isGeneraliste()) {
                return $this->render('GestimeHomeBundle:dashboard:medecinGeneraliste.html.twig',
                    array('menuactif'        => 'Accueil',
                             'messagesCount'    => $listMessagesArray['messagesCount'],
                             'adresse_cabinet'  => $homeMgr->getAdresseAbonne($medecin),
                             'visites'          => $homeMgr->getVisites($medecin, new \DateTime()),
                             'infosRdv'         => $infosRdv,
                             'action'           => 'Accueil', )
                );
            } else {



                return $this->render('GestimeHomeBundle:dashboard:medecinSpecialiste.html.twig',
                    array('menuactif'        => 'Accueil',
                             'messagesCount'    => $listMessagesArray['messagesCount'],
                             'messages'         => $listMessagesArray['messagesList'],
                             'infosRdv'         => $infosRdv,
                             'action'           => 'Accueil', )
                );
            }
        }

        if ($this->getUser()->hasGroup('SECRETAIRE-CABINET') || $this->getUser()->hasGroup('DOC24')) {
            $adresseAbonneGeoloc = $homeMgr->getAdresseAbonne($medecin);
            $aujourdhui = new \DateTime();
            $visites = $homeMgr->getVisites($medecin, $aujourdhui);
            $infosRdv=$homeMgr->getFirstLastNextEvent($medecin, $aujourdhui->format('Y-m-d'));
            $listMessagesArray = $messageMgr->getMessagesListePaginee($this->getUser(),
              false,
              'Reception',
              '',
              1,
              10
            );
            return $this->render('GestimeHomeBundle:dashboard:secretaire-cabinet.html.twig',
                array('menuactif'        => 'Accueil',
                         'adresse_cabinet'  => $adresseAbonneGeoloc,
                         'infosRdv'         => $infosRdv,
                         'messages'         => $listMessagesArray['messagesList'],
                         'messagesCount'    => $listMessagesArray['messagesCount'],
                         'visites'          => $visites,
                         'action'           => 'Accueil', )
            );
        }

        if ($this->getUser()->hasGroup('SECRETAIRE-SIE')  ) {
            $adresseAbonneGeoloc = $homeMgr->getAdresseAbonne($medecin);
            $aujourdhui = new \DateTime();
            $visites = $homeMgr->getVisites($medecin, $aujourdhui);

            return $this->render('GestimeHomeBundle:dashboard:secretaire-cabinet.html.twig',
                array('menuactif'        => 'Accueil',
                         'adresse_cabinet'  => $adresseAbonneGeoloc,
                         'visites'          => $visites,
                         'action'           => 'Accueil', )
            );
        }
    }
}
