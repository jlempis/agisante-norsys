<?php

namespace Gestime\Doc24Bundle\Business;

use Gestime\ApiBundle\Model\RdvWeb;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Gestime\UserBundle\Business\MedecinManager;
use Gestime\EventBundle\Business\EventManager;
use Gestime\CoreBundle\Entity\Medecin;
use Gestime\CoreBundle\Entity\Evenement;
use Symfony\Component\Security\Core\Util\SecureRandom;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class Doc24Manager {
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
   * @param $debut
   * @param $days
   * @return \Gestime\UserBundle\Business\DateTime
   */
  private function addDateExcludeWE($debut, $days)
  {

    $d = new \DateTime( $debut->format('Y-m-d'));
    $t = $d->getTimestamp();

    for($i=0; $i<$days; $i++){

      $addDay = 86400;
      $nextDay = date('w', ($t+$addDay));

      // if it's Saturday or Sunday get $i-1
      if($nextDay == 0 || $nextDay == 6) {

        $i--;
      }

      $t = $t+$addDay;
    }
    $d->setTimestamp($t);

    return $d;
  }

  private function isPeriodeDisponible($RdvsPeriode, $jour, $debutRendezVous, $finRendezVous)
  {
    foreach ($RdvsPeriode as $RdvPeriode )
    {
      if ($RdvPeriode['debutRdv']->format('w') == $jour)
      {
        if (($debutRendezVous <= $RdvPeriode['debutRdv'] ) && ($finRendezVous > $RdvPeriode['debutRdv']))
        {
          return false;
        }
        if (($finRendezVous >= $RdvPeriode['finRdv']) && ( $debutRendezVous < $RdvPeriode['finRdv']))
        {
          return false;
        }
        if (($finRendezVous <= $RdvPeriode['finRdv']) && ( $debutRendezVous > $RdvPeriode['debutRdv']))
        {
          return false;
        }
      }
    }

    return true;
  }

  private function secondes($timestring)
  {
    return intval(substr($timestring, 0,2))*3600 + intval(substr($timestring, 3,2))*60;
  }

  private function inIntervalle($debutPeriode, $finPeriode, $debutMoment, $finMoment)
  {

    if (($debutMoment >= $debutPeriode) && ($debutMoment < $finPeriode))
    {
      //dump($debutPeriode, $finPeriode, $debutMoment, $finMoment);
      return true;
    }
    if (($finMoment > $debutPeriode) && ($finMoment <= $finPeriode))
    {
      return true;
    }
    return false;
  }

  private function RdvInternetAutoriseDansPeriode($periodesTravaillees, $heureDebutRendezVous, $heurefinRendezVous )
  {
    foreach( $periodesTravaillees as $periodeTravaillee )
    {
      if (intval($periodeTravaillee['jour']['code']) == intval($heureDebutRendezVous->format('w'))) {


        //dump($periodeTravaillee['debut'],$periodeTravaillee['fin'],$heureDebutRendezVous->format('H:i'), $heurefinRendezVous->format('H:i') );
        if ($this->inIntervalle($this->secondes($periodeTravaillee['debut']),
                                   $this->secondes($periodeTravaillee['fin']),
                                   $this->secondes($heureDebutRendezVous->format('H:i')),
                                   $this->secondes($heurefinRendezVous->format('H:i'))))
        {

          return $periodeTravaillee['internet'];
        }
      }
    }

    return true;
  }

  private function AbsentCeJour($periodesAbsences, \Datetime $date)
  {
    foreach( $periodesAbsences as $periodeAbsence )
    {
      $dateAbsence= new \Datetime();
      $dateAbsenceArray = explode ('-', $periodeAbsence['date']);
      $dateAbsence->setDate($dateAbsenceArray[0], $dateAbsenceArray[1], $dateAbsenceArray[2]);

      $dateAbsence->setTime(0,0,0);

      $dateTemp = new \Datetime($date->format('Y-m-d'));
      $dateTemp->setTime(0,0,0);

      if ($dateAbsence == $dateTemp) {
        return true;
      }
    }

    return false;
  }

  private function aujourdhui() {
    $aujourdhui = new \DateTime('now');
    $aujourdhui->setTime(0,0);

    return $aujourdhui;
  }

  private function getRdvInternetLibres_( $joursPeriode, $dureeRdv, $periodesInternet, $RdvPeriode, $periodesAbsence, $periodesTravaillees  ) {
    $rdvlibres = array();


    $logger = $this->container->get('logger');
    $logger->info('getRdvInternetLibres_debut');

    //$logger->info(json_encode($joursPeriode) . json_encode($dureeRdv) .  json_encode($periodesInternet) .  json_encode($RdvPeriode) .  json_encode($periodesAbsence) .  json_encode($periodesTravaillees));


    $logger->info('$periodeSInternet'.'->'.json_encode($periodesInternet));

    foreach ( $joursPeriode as $jourPeriode )
    {
      //Si le jour en cours est travaillé
      {

        $logger->info('$jourPeriode'.'->'.json_encode($jourPeriode));

        //Boucle sur toutes les periodes travaillées
        foreach( $periodesInternet as $periodeInternet )
        {

          $logger->info('$periodeInternet'.'->'.json_encode($periodeInternet));

          $rdvDispos = 0;
          if ($jourPeriode->format('w') == $periodeInternet['jour']['code'] )
          {
            //Boucle toutes les n minutes (n= durée d'un rendez-vous)


            $debut = new \DateTime($jourPeriode->format('Y-m-d').' '.$periodeInternet['debut']);
            $fin = new \DateTime($jourPeriode->format('Y-m-d').' '.$periodeInternet['fin']);

            $debut = new \DateTime($periodeInternet['debut']);
            $fin = new \DateTime($periodeInternet['fin']);

            $logger->info('debut / fin '.json_encode($debut). ', '.json_encode($fin));

            $interval = new \DateInterval('PT'.strval($dureeRdv).'M');
            $intervallesRendezVous = new \DatePeriod($debut, $interval, $fin);

            foreach( $intervallesRendezVous as $debutRendezVous )
            {
              $logger->info('debutRendezVous'.'->'.json_encode($debutRendezVous));

              //On ajoute la date en cours et le debut de Rdv (H:m)
              $heureDebutRendezVous = new \DateTime();
              $heureDebutRendezVous->setTimestamp($jourPeriode->getTimeStamp() + $debutRendezVous->getTimeStamp() - $this->aujourdhui()->getTimeStamp());

              //On calcule la date de fin en ajoutant la durée standard d'un rdv
              $addTempsRdv = 60*$dureeRdv;
              $heurefinRendezVous = new \DateTime();
              $heurefinRendezVous->setTimestamp($heureDebutRendezVous->getTimestamp() + $addTempsRdv);

              //La periode est elle disponible ?
              //dump($heureDebutRendezVous->format('Y-m-d H:i'), $heurefinRendezVous->format('Y-m-d H:i'));
              if ($this->isPeriodeDisponible($RdvPeriode, $periodeInternet['jour']['code'], $heureDebutRendezVous, $heurefinRendezVous))
              {
                //La plage est dispo. Un Rdv Internet est il autorisé ?
                if ($this->RdvInternetAutoriseDansPeriode($periodesTravaillees, $heureDebutRendezVous, $heurefinRendezVous ))
                {
                  if (!$this->AbsentCeJour($periodesAbsence, $heureDebutRendezVous, $heurefinRendezVous ))
                  {
                    $rdvDispos++;
                    if ($rdvDispos <= $periodeInternet['nbRdvMax']) {
                      $rdvlibres[] = array('debut'=>$heureDebutRendezVous->format('Y-m-d H:i'),  'fin'=>$heurefinRendezVous->format('Y-m-d H:i'));
                      $logger->info('OK dispo :'.'debut '. $heureDebutRendezVous->format('Y-m-d H:i').' / fin '.$heurefinRendezVous->format('Y-m-d H:i'));
                    }
                    else
                    {
                      $logger->info('quota dépassé :'.$periodeInternet['nbRdvMax']);
                      //dump('quota dépassé');
                    }
                  }
                  else
                  {
                    $logger->info('absent');
                   //dump('absent');
                  }
                }
                else
                {
                  $logger->info('non autorisé');
                  //dump( 'non autorisé');
                }
              }
              else
              {
                $logger->info('indisponible');
                //dump ('indisponible');
              }
            }
          }
        }
      }
    }
    $logger->info('Rdv libres : '.json_encode($rdvlibres));
    $logger->info('getRdvInternetLibres_fin');
    return $rdvlibres;
  }

  /**
   * @param $medecinId
   * @param $debut
   * @param $fin
   * @param int $nbJoursMax
   * @return array
   */
  public function getRdvInternetLibres($medecinId, $debut, $fin, $nbJoursMax=50)
  {
    //Récupération des infos du médecin
    $medecin = $this->entityManager->getRepository('GestimeCoreBundle:Medecin')
                                    ->findOneByIdMedecin($medecinId);

    if (!$medecin->isRdvInternet())
    {
      return array();
    }

    $dateDebut = new \DateTime($debut);

    $debutReel = $this->addDateExcludeWE($this->aujourdhui(), $medecin->getCarence());

    if ($debutReel->getTimestamp() < $dateDebut->getTimestamp()) {


        $debutReel = $dateDebut;
    }

    $interval = new \DateInterval('P1D');
    $dateFin = new \DateTime($fin);
    $dateMax = new \DateTime($fin);
    $dateMax->add(new \DateInterval('P'.$nbJoursMax.'D'));

    $medecinMgr = $this->container->get('gestime.medecin.manager');
    $eventMgr = $this->container->get('gestime.event.manager');

    $rdvlibres=array();
    $semaineCourante = true;
    while (count($rdvlibres) == 0 && $dateFin < $dateMax) {

      $periode = new \DatePeriod($debutReel, $interval, $dateFin);


      $rdvlibres = $this->getRdvInternetLibres_($periode,
                                                $medecinMgr->getDureeConsultation($medecin),
                                                $medecinMgr->getHorairesInternetMedecin($medecin),
                                                $eventMgr->getRendezVous($debutReel,  $dateFin->format('Y-m-d'), $medecin),
                                                $eventMgr->getAbsencesMedecinByPeriode($medecin->getIdMedecin(),$debutReel->format('Y-m-d'), $dateFin->format('Y-m-d')),
                                                $medecinMgr->getHorairesMedecin($medecin)
      );


      //si on est dans la semaine courante,les RDV sont dans le tableau Rdv disponible
      //sinon, on prend le prochain dispo et on le met dans la zone prochain RDV

      $debutReel->add($interval);
      $dateFin->add($interval);
      if (count($rdvlibres) == 0) {
        $semaineCourante = false;
      }
    }
    $prochainRdv = "";
    if (!$semaineCourante) {
      if (count($rdvlibres) > 0) {
        $prochainRdv = $rdvlibres[0]['debut'];
      }
      $rdvlibres = array();
    }
    return array('prochain'=>$prochainRdv, 'rdvs'=>$rdvlibres);
  }

  public function getCode($length) {
    $generator = new SecureRandom();
    $random = $generator->nextBytes($length);
    return bin2hex($random);
  }

  public function envoiSmsInscription($numero) {
      $serviceMgr = $this->container->get('gestime.sms');

      $humanReadableString = $this->getCode(3);

      $datefinvalidation = new \DateTime('now');
      $datefinvalidation->add(new \DateInterval('PT30M'));

      $texte = "Doc24: Votre code d'activation est le ".$humanReadableString.". Il est valable jusqu'au ".$datefinvalidation->format("d/m/Y à H:i") .". Merci de votre inscription sur Doc24.fr .";

      $serviceMgr->sendMessage($texte, $numero, 0);
      return array("code" =>$humanReadableString, "expiration"=>$datefinvalidation);
  }

  public function envoiSmsPriseRdv($numero, RdvWeb $rdv) {
    $serviceMgr = $this->container->get('gestime.sms');
    $medecinMgr = $this->container->get('gestime.medecin.manager');
    $medecin = $medecinMgr->getMedecinbyId($rdv->getMedecinId());
    $specialite = $medecinMgr->getSpecialitebyId($rdv->getSpecialiteId());

    if ($medecin) {
      $texte = "Le secrétariat du ".$medecin->getNom().', '.$specialite. ' vous confirme votre rendez-vous le '.$rdv->getDateRdv()->format("d/m/Y à H:i");
      $serviceMgr->sendMessage($texte, $numero, 0);

      return true;
    }

    return false;
  }

  public function envoiSmsAnnulationRdv($numero, Evenement $rdv) {
    $serviceMgr = $this->container->get('gestime.sms');
    $medecinMgr = $this->container->get('gestime.medecin.manager');
    $medecin = $medecinMgr->getMedecinbyId($rdv->getMedecin()->getIdMedecin());
    $specialite = $medecinMgr->getSpecialitebyId($rdv->getSpecialite()->getId());

    if ($medecin) {
      $texte = "Le secrétariat du ".$medecin->getNom().', '.$specialite. ' vous confirme l\'annulation de votre rendez-vous du '.$rdv->getDebutRdv()->format("d/m/Y à H:i");
      $serviceMgr->sendMessage($texte, $numero, 0);

      return true;
    }

    return false;
  }

  public function gen_uuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
      // 32 bits for "time_low"
      mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

      // 16 bits for "time_mid"
      mt_rand( 0, 0xffff ),

      // 16 bits for "time_hi_and_version",
      // four most significant bits holds version number 4
      mt_rand( 0, 0x0fff ) | 0x4000,

      // 16 bits, 8 bits for "clk_seq_hi_res",
      // 8 bits for "clk_seq_low",
      // two most significant bits holds zero and one for variant DCE1.1
      mt_rand( 0, 0x3fff ) | 0x8000,

      // 48 bits for "node"
      mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
  }

}
