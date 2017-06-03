<?php

namespace Gestime\HomeBundle\Business;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Gestime\CoreBundle\Entity\Evenement;
use Gestime\CoreBundle\Gmap\Geocoder;
use Gestime\CoreBundle\Entity\Medecin;

/**
 * HomeManager
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class HomeManager
{
    protected $entityManager;
    protected $container;

    /**
     * [__construct description]
     * @param ContainerInterface $container
     * @param EntityManager      $entityManager
     */
    public function __construct(ContainerInterface $container, $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->container = $container;
    }

    /**
     * getAdresseAbonne
     * @param Medecin $medecin
     * @return Geocoder
     */
    public function getAdresseAbonne($medecin)
    {
        $adresse =  urlencode(preg_replace('/\r|\n/', ' ', $medecin->getAbonne()->getAdresse()));

        return Geocoder::getLocation($adresse);
    }



    /**
     * getVisites
     * @param Medecin  $medecin
     * @param datetime $date
     * @return array
     */
    public function getVisites($medecin, $date)
    {
        $datedebut = $date;
        $datefin = new \DateTime();
        $datefin->add(new \DateInterval('P1D'));

        $qbresult = $this->entityManager
                       ->getRepository('GestimeCoreBundle:Evenement')
                       ->getRendezVousByType($medecin,
                           $datedebut->format('Y-m-d'),
                           $datefin->format('Y-m-d'),
                           'V'
                       )
                   ->getQuery()
                   ->getResult();

        $visites = [];
        $indexVisite = 0;

        foreach ($qbresult as $key => $value) {
            if (is_object($value->getPatient()->getAdresses()[0])) {
                $address = urlencode($value->getPatient()->getAdresses()[0]->getAdresseComplete());
                $visites[$indexVisite]['coordonnees'] = Geocoder::getLocation($address);
                $visites[$indexVisite]['nom'] = 'ABCD';
            }
            $indexVisite++;
        }

        if (count($visites) < 2) {
            $visites[$indexVisite]['coordonnees'] = $this->getAdresseAbonne($medecin);
            $visites[$indexVisite]['nom'] = 'ABCD';
        }

        return $visites;
    }

    /**
     * getFirstLastNextEvent
     * @param Medecin  $medecin
     * @param datetime $date
     * @return array
     */
    public function getFirstLastNextEvent(Medecin $medecin, $date)
    {
        $sysdate = localtime(time(), true);
        $firstEvent = $this->entityManager
            ->getRepository('GestimeCoreBundle:Evenement')
            ->getFirstRdv($medecin, $date, '00:00');

        $firstEventDetail=array();

        if (count($firstEvent)) {
            $nomComplet = $firstEvent[0][0]['objet'];
            if (!is_null($firstEvent[0][0]['patient'])) {
                $nomComplet = $firstEvent[0][0]['patient']['entreprise'].' '.$firstEvent[0][0]['patient']['prenom'].' '.$firstEvent[0][0]['patient']['nom'];

            }
            $firstEventDetail=array(
                'debut'     => $firstEvent[0][0]['debutRdv'],
                'fin'       => $firstEvent[0][0]['finRdv'],
                'memejour'  => ($firstEvent[0][0]['finRdv']->format('Y-m-d') == new \DateTime($date)),
                'nom'       =>  $nomComplet
            );
        }

        $lastEvent = $this->entityManager
            ->getRepository('GestimeCoreBundle:Evenement')
            ->getLastRdv($medecin, $date, '23:59');

        $lastEventDetail=array();
        if (count($lastEvent)) {
            $nomComplet = $lastEvent[0][0]['objet'];
            if (!is_null($lastEvent[0][0]['patient'])) {
                $nomComplet = $lastEvent[0][0]['patient']['entreprise'].' '.$lastEvent[0][0]['patient']['prenom'].' '.$lastEvent[0][0]['patient']['nom'];
            }

            $lastEventDetail=array(
                'debut' =>  $lastEvent[0][0]['debutRdv'],
                'fin'   =>  $lastEvent[0][0]['finRdv'],
                'memejour'   => ($lastEvent[0][0]['finRdv']->format('Y-m-d') == new \DateTime($date)),
                'nom'       =>  $nomComplet
            );
        }

        $nextEvent = $this->entityManager
            ->getRepository('GestimeCoreBundle:Evenement')
            ->getFirstRdv($medecin, $date, $sysdate['tm_hour'].':'.$sysdate['tm_min']);

        $nextEventDetail=array();
        if (count($nextEvent)) {
            $nomComplet = $nextEvent[0][0]['objet'];
            if (count($nextEvent[0][0]['patient']) >0) {
                $nomComplet = $nextEvent[0][0]['patient']['entreprise'].' '.$nextEvent[0][0]['patient']['prenom'].' '.$nextEvent[0][0]['patient']['nom'];
            }
            $nextEventDetail=array(
                'debut'     =>  $nextEvent[0][0]['debutRdv'],
                'fin'       =>  $nextEvent[0][0]['finRdv'],
                'memejour'  =>  ($nextEvent[0][0]['debutRdv'] < new \DateTime($date.' 23:59')),
                'nom'       =>  $nomComplet,
                'type'      =>  $nextEvent[0]['value']
            );
        }

        return array('first' => $firstEventDetail, 'last' => $lastEventDetail, 'next' => $nextEventDetail);
    }
}
