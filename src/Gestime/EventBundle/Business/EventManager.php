<?php

namespace Gestime\EventBundle\Business;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Gestime\CoreBundle\Entity\Adresse;
use Gestime\CoreBundle\Entity\Evenement;
use Gestime\CoreBundle\Entity\Personne;
use Gestime\CoreBundle\Entity\RelationEvenement;
use Gestime\CoreBundle\Entity\Utilisateur;
use Gestime\CoreBundle\Entity\Medecin;
use Gestime\ApiBundle\Model\RdvWeb;

/**
 * EventManager
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class EventManager
{
    protected $entityManager;
    protected $container;
    protected $synchroEventMgr;

    /**
     * [__construct description]
     * @param ContainerInterface $container
     * @param entityManager      $entityManager
     */
    public function __construct(ContainerInterface $container, $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->container = $container;
        $this->synchroEventMgr = $this->container->get('gestime.synchro.evenement.manager');
    }

    /**
     * getListeEntreprises (Utilisé dans l'autocomplete)
     * @param integer $medecinId id du Medecin sélectionné
     * @param string  $value     Raison sociale à rechercher
     * @return querybuilder
     */
    public function getListeEntreprises($medecinId, $value)
    {
        return $this->entityManager
            ->getRepository('GestimeCoreBundle:Personne')
            ->findEntreprisesLike($medecinId, $value);
    }

    /**
     * getListePatients (Utilisé dans l'autocomplete)
     * @param integer $medecinId id du Medecin sélectionné
     * @param string  $value     Nom à rechercher
     * @return querybuilder
     */
    public function getListePatients($medecinId, $value)
    {
        return $this->entityManager
            ->getRepository('GestimeCoreBundle:Personne')
            ->findPatientsLike($medecinId, $value);
    }

    /**
     * Fetourne la liste des types de rendez vous possibles
     * @return array
     */
    public function getlisteTypeRendezVous()
    {
        return $this->entityManager
            ->getRepository('GestimeCoreBundle:Parametre')
            ->getParamByType('TypeRdv')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * Retourne la liste des annotations (colorations de l'agenda)
     * Rzetourne également les absences de la semaine
     * @param integer  $idMedecin
     * @param datetime $debut     jour du debut de la vue agenda en cours
     * @param datetime $fin       jour du fin de la vue agenda en cours
     * @return array
     */
    public function getAnnotations($idMedecin, $debut, $fin)
    {
        $annotations =  $this->entityManager
            ->getRepository('GestimeCoreBundle:Medecin')
            ->getAnnotations($idMedecin);

        foreach ($annotations as $key => $annotation) {
            switch (strtolower($annotation['day'])) {
                case 'lundi':
                    $annotation['day'] = 'mon';
                    break;
                case 'mardi':
                    $annotation['day'] = 'tue';
                    break;
                case 'mercredi':
                    $annotation['day'] = 'wed';
                    break;
                case 'jeudi':
                    $annotation['day'] = 'thu';
                    break;
                case 'vendredi':
                    $annotation['day'] = 'fri';
                    break;
                case 'samedi':
                    $annotation['day'] = 'sat';
                    break;
                case 'dimanche':
                    $annotation['day'] = 'sun';
                    break;
            }
            $annotation['text'] = (is_null($annotation['text']) ? $annotation['activite'] : $annotation['text']);
            $annotation['end'] = $annotation['fin'];
            $annotations[$key] = $annotation;
        }
        $annotations = array_merge($annotations, $this->getAbsencesMedecinByPeriode($idMedecin, $debut, $fin));

        return $annotations;
    }

    /**
     * Retourne la durée de consultation standard d'un médecin
     * @param integer $idMedecin
     * @return integer
     */
    public function getDureeConsultation($idMedecin)
    {
        return $this->entityManager
            ->getRepository('GestimeCoreBundle:Medecin')
            ->getDureeConsultation($idMedecin);
    }

    /**
     * Retourne les absences d'un médecin
     * @param integer $idMedecin
     * @return array
     */
    public function getAbsencesMedecin($idMedecin)
    {
        $absences =  $this->entityManager
            ->getRepository('GestimeCoreBundle:Absence')
            ->getAbsencesMedecin($idMedecin);

        $listDatesAbsences = array();
        $listCommentairesAbsences = array();
        foreach ($absences as $absence) {
            $begin = new \DateTime($absence['debut']->format('Y-m-d'));
            $end = new \DateTime($absence['fin']->format('Y-m-d'));
            $commentaire = $absence['commentaire'];

            $begin->setTime(0, 0);
            $end->setTime(12, 0);

            $interval = \DateInterval::createFromDateString('1 day');
            $period = new \DatePeriod($begin, $interval, $end);

            foreach ($period as $dt) {
                if ($dt instanceof \Datetime) {
                    $listDatesAbsences[] = $dt->format('Y-m-d');
                    $listCommentairesAbsences[] = $commentaire;
                }
            }
        }

        return [$listDatesAbsences, $listCommentairesAbsences];
    }

    private function getRemplacements($idMedecin, $date)
    {
        $remplacement = $this->entityManager
            ->getRepository('GestimeCoreBundle:Absence')
            ->getRemplacements($idMedecin, $date);

        if (sizeof($remplacement) >0) {
            return $remplacement[0]['nom'];
        }

        return '';
    }

    /**
     * Retourne les absences d'un médecin pour une periode donnée
     * @param integer  $idMedecin
     * @param datetime $debut
     * @param datetime $fin
     * @return array
     */
    public function getAbsencesMedecinByPeriode($idMedecin, $debut, $fin)
    {
        $absences =  $this->entityManager
            ->getRepository('GestimeCoreBundle:Absence')
            ->getAbsencesMedecin($idMedecin, $debut, $fin);

        $listDatesAbsences = array();

        $debutPeriode = new \DateTime($debut);
        $finPeriode = new \DateTime($debut);
        $finPeriode->add(new \DateInterval('P5D'));


        foreach ($absences as $absence) {
            $begin = new \DateTime($absence['debut']->format('Y-m-d'));
            $end = new \DateTime($absence['fin']->format('Y-m-d'));

            if ($begin < $debutPeriode) {
                $begin = $debutPeriode;
            }
            if ($end > $finPeriode) {
                $end = $finPeriode;
            }
            $commentaire = $absence['commentaire'];

            $begin->setTime(0, 0);
            $end->setTime(12, 0);

            $interval = \DateInterval::createFromDateString('1 day');
            $period = new \DatePeriod($begin, $interval, $end);

            $listDatesAbsences = array();
            foreach ($period as $dt) {
                if ($dt instanceof \Datetime) {
                    $nomRemplacant = $this->getRemplacements($idMedecin, $dt->format('Y-m-d'));
                    $cmt=$commentaire;
                    if (strlen(trim($nomRemplacant)) > 0) {
                        $cmt =$commentaire.' (Remplacant: '.$nomRemplacant.')';
                    }
                    $absence = array(  'date'        => $dt->format('Y-m-d'),
                                       'day'         => strtolower($dt->format('D')),
                                       'start'       => '00:00',
                                       'end'         => '23:59',
                                       'activite'    => '',
                                       'cssClass'    => 'fond-absence',
                                       'text'        => $cmt,
                                     );
                    array_push($listDatesAbsences, $absence);
                }
            }
        }

        return $listDatesAbsences;
    }

    /**
     * Retourne un évenement
     * @param integer $idEvent
     * @return Evenement
     */
    public function getEvenementById($idEvent)
    {
        $event = $this->entityManager
          ->getRepository('GestimeCoreBundle:Evenement')
          ->getRendezVousById($idEvent)
          ->getQuery()
          ->getResult();

        return $event;
    }

    /**
     * Retourne un évenement
     * @param integer $idEvent
     * @return Evenement
     */
    public function getEvenement($idEvent)
    {
        $event = $this->entityManager
            ->getRepository('GestimeCoreBundle:Evenement')
            ->getRendezVousById($idEvent)
            ->getQuery()
            ->getArrayResult();

        return $event;
    }

    /**
     * Retourne les consignes d'un patient
     * @param integer $idPatient
     * @return array
     */
    public function getConsignesPatient($idPatient)
    {
        return $this->entityManager
            ->getRepository('GestimeCoreBundle:Consigne')
            ->getListConsignesBbyPatient($idPatient)
            ->getQuery()
            ->getArrayResult();
    }

  /**
   * @param $debut
   * @param $fin
   * @param $medecin
   * @return mixed
   */
  public function getRendezVous($debut, $fin, $medecin)
    {
      return $this->entityManager
        ->getRepository('GestimeCoreBundle:Evenement')
        ->getRendezVous($debut, $fin, $medecin);
    }

    /**
     * Retourne les rendez vous non excusés d'un patient chez un médecin
     * @param integer $idMedecin
     * @param integer $idPatient
     * @return array
     */
    public function getNonExcusesPatient($idMedecin, $idPatient)
    {
        return $this->entityManager
            ->getRepository('GestimeCoreBundle:Evenement')
            ->getNonExcusesById($idMedecin, $idPatient)
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * Retourne la liste des médecins gérés par un utilisateur
     * @param Utilisateur $user
     * @return array
     */
    public function getMedecinsUser(Utilisateur $user)
    {
        return $this->entityManager
                ->getRepository('GestimeCoreBundle:Medecin')
                ->getMedecinsUser($user->hasRole('ROLE_VISU_AGENDA_TOUS'),
                    $user->getSite(),
                    $user->getId()
                )
                ->getQuery()
                ->getArrayResult();
    }

    /**
     * Retourne la liste des villes d'un code postal
     * @param string $codePostal
     * @return array
     */
    public function getVillesByCpostal($codePostal)
    {
        $lstVilles = $this->entityManager
            ->getRepository('GestimeCoreBundle:Ville')
            ->getVillesByCpostal($codePostal)
            ->getQuery()
            ->getArrayResult();

        $htmlOptions = '';
        $premiere = 'selected="selected"';
        foreach ($lstVilles as $optionVille) {
            $htmlOptions .= '<option value="'.$optionVille['id'].'" '.$premiere.' >'.$optionVille['nom'].'</option>';
            $premiere = '';
        }

        return $htmlOptions;
    }

    /**
     * Crée une relation pour l'evenement
     * @param string    $type           (C:Creation, M:Modification, S:Suppression)
     * @param Evenement $eventPrecedent [description]
     * @param Evenement $eventSuivant   [description]
     */
    private function setRelation($type, $eventPrecedent, $eventSuivant = null)
    {
        $relation = new RelationEvenement();
        $relation->setType($type);
        $relation->setEvenementPrecedent($eventPrecedent);
        if (!is_null($eventSuivant)) {
            $relation->setEvenementSuivant($eventSuivant);
        }
        $this->entityManager->persist($relation);
    }

    /**
     * [getEvent description]
     * @param integer $eventId [description]
     * @return Evenement
     */
    private function getEvent($eventId)
    {
        $eventSourceArray = $this->entityManager
            ->getRepository('GestimeCoreBundle:Evenement')
            ->findByIdEvenement($eventId);

        if (count($eventSourceArray) > 0) {
            return $eventSourceArray[0];
        }
    }

    /**
     * [annuletRemplaceEvent description]
     * @param Evenement $eventSource
     * @param Evenement $eventCible
     * @return integer   Id Evenement cible
     */
    private function annuletRemplaceEvent(Evenement $eventSource, Evenement $eventCible)
    {
        $this->setRelation('M', $eventSource, $eventCible);
        $eventSource->setEtat('A');
        $this->entityManager->persist($eventSource);
        $this->entityManager->persist($eventCible);
        $this->entityManager->flush();

        //SynchroV1
        $this->synchroEventMgr->update_evenement($eventSource, $eventCible);

        return $eventCible->getIdEvenement();
    }

    /**
     * [deleteEvent description]
     * @param integer $eventId
     * @return boolean
     */
    public function deleteEvent($eventId, $synchro=true)
    {
        $event = $this->getEvent($eventId);
        $event->setEtat('A');
        $this->entityManager->persist($event);
        $this->setRelation('S', $event);
        $this->entityManager->flush();

        //SynchroV1
        if ($synchro) {
            if (!is_null($event->getMedecin()->getIdAgenda())) {
                $this->synchroEventMgr->delete_evenement($event);
            }
        }

        return true;
    }

    /**
     * [changeEvent description]
     * @param integer $idMedecin
     * @param integer $eventId
     * @param string  $newStartDate
     * @param string  $newEndDate
     * @return integer
     */
    public function changeEvent($idMedecin, $eventId, $newStartDate, $newEndDate)
    {
        $eventSource = $this->getEvent($eventId);
        $eventSource->setUpdated(new \Datetime('NOW'));
        $eventCible = clone $eventSource;
        $eventCible->setCreated(new \Datetime('NOW'));
        $medecin = $this->entityManager->getRepository('GestimeCoreBundle:Medecin')->findByIdMedecin($idMedecin);
        if (!is_null($medecin)) {
            $eventCible->setMedecin($medecin[0]);
        }
        $eventCible->changeDate(new \Datetime($newStartDate), new \Datetime($newEndDate));

        return $this->annuletRemplaceEvent($eventSource, $eventCible);
    }

    /**
     * [editEvent description]
     * @param integer $eventId
     * @return integer
     */
    public function editEvent($eventId)
    {
        $eventSource = $this->getEvent($eventId);
        $eventCible = clone $eventSource;

        return $this->annuletRemplaceEvent($eventSource, $eventCible);
    }

    /**
     * getGeoloc
     * @param Personne $patient
     * @return Personne
     */
    private function getGeoloc(Personne $patient) {
        $utils = $this->container->get('gestime_core.utilities');
        $latitude = $utils->getGeoLoc($patient->getAdresse())['lat'];
        $longitude = $utils->getGeoLoc($patient->getAdresse())['lng'];

        if (!is_null($longitude)) {
            $patient->setLongitude($longitude);
        } else {
            $patient->setLongitude(0);
        }

        if (!is_null($latitude)) {
            $patient->setLatitude($latitude);
        } else {
            $patient->setLatitude(0);
        }

        return $patient;
    }

    /**
    * [saveEvent description]
    * @param Evenement $event
    * @return integer   Id evenement crée
    */
    public function saveEvent(Evenement $event)
    {
        $utils = $this->container->get('gestime_core.utilities');

        //Faut il enregistrer un patient ? (Si temps réservé => non)
        if ($event->getType() != 'P') {
            $event->getPatient()->setEtat('V');
            $event->getPatient()->setType('P');

            /* Si un patient existe, on y accroche le Rendez-Vous */
            $arrayExistedPatients = $this->entityManager
                ->getRepository('GestimeCoreBundle:Personne')
                ->findByAllFields($event->getPatient()->getCivilite(),
                    $event->getPatient()->getEntreprise(),
                    $event->getPatient()->getAdresse(),
                    $event->getPatient()->getNom(),
                    $event->getPatient()->getPrenom(),
                    $event->getPatient()->getNomJF(),
                    $event->getPatient()->getEmail(),
                    $event->getPatient()->getTelephone(),
                    $event->getPatient()->getEtat(),
                    $event->getPatient()->getType()
                );

            if (count($arrayExistedPatients) > 0) {
                $patientExistant = $arrayExistedPatients[0];

                //Si une adresse est saisie et que le patient existe, on lui affecte la nouvelle adresse
                if ($event->getPatient()->getAdresse() !=null) {
                    $patientExistant->setAdresse($event->getPatient()->getAdresse());
                    $patientExistant = $this->getGeoloc($patientExistant);
                    $this->entityManager->persist($patientExistant);
                    $this->entityManager->flush();
                }
                $event->setPatient($patientExistant);
            } else {
                //Le patient n'existe pas, on le crée
                //On l'ajoute dans la patientele : On lie le medecin
                $event->getPatient()->addMedecin($event->getMedecin());
            }
        } else {
            //On est sur un temps réservé
            $event->setPatient(null); //On enregistre un temps réservé, donc sans patient
        }

        if (! is_null($event->getIdEvenement())) {
            $eventSource = $this->getEvent($event->getIdEvenement());
            $this->AnnuletRemplaceEvent($eventSource, $event);
        } else {
            $this->setRelation('C', $event);
            $this->entityManager->persist($event);
            $this->entityManager->flush();

            //Synchro avec la base V1
            if (!is_null($event->getMedecin()->getIdAgenda())){
                $this->synchroEventMgr->create_evenement($event);
            }
        }

        return $event->getIdEvenement();
    }

    /**
     * [getType description]
     * @param [type] $type [description]
     * @return [type]       [description]
     */
    private function getType($type)
    {
        return $this->entityManager
            ->getRepository('GestimeCoreBundle:Parametre')
            ->getParamByTypeAndCode('TypeRdv', $type)
            ->getQuery()
            ->getResult();
    }

    /**
     * [setReserve description]
     * @param integer $idMedecin
     * @param string  $debut
     * @param string  $fin
     * @return integer
     */
    public function setReserve($idMedecin, $debut, $fin, $synchro=true, $oldRdvId=0)
    {
        $event = new Evenement();
        $event->setType($this->getType('P')[0]);

        $event->setEtat('V');
        $event->setOldRdvId($oldRdvId);
        $event->setObjet('Temps réservé');
        $event->setPatient(null);

        $medecin = $this->entityManager->getRepository('GestimeCoreBundle:Medecin')->findByIdMedecin($idMedecin);
        if (!is_null($medecin)) {
            $event->setMedecin($medecin[0]);
        }
        $event->setDebutRdv(new \DateTime($debut));
        $event->setFinRdv(new \DateTime($fin));
        $this->entityManager->persist($event);
        $this->setRelation('C', $event);

        //Synchro avec la base V1
        if ($synchro) {
            if (!is_null($event->getMedecin()->getIdAgenda())) {
                $this->synchroEventMgr->create_evenement($event);
            }
        }


        $this->entityManager->flush();

        //On retourne l'Id de l'évènement
        return $event->getIdEvenement();
    }

    /**
     * [getEventInSession description]
     * @param Session $session
     * @return Session
     */
    public function getEventInSession($session)
    {
        return $session->get('copied_events', array());
    }

    /**
     * [saveEventInSession description]
     * @param Session $session
     * @param integer $eventId
     * @return boolean
     */
    public function saveEventInSession($session, $eventId)
    {
        $nbEventsToStore = 3;
        $events = $session->get('copied_events', array());
        $event = $this->getEvenement($eventId)[0];
        $eventCivilite = $this->getEvenement($eventId)[1];
        $eventCouleur = $this->getEvenement($eventId)[2];
        $diff = date_diff($event['finRdv'], $event['debutRdv']);
        $duree = intval($diff->format('%h'))*60+intval($diff->format('%i'));
        $libelle = ($event['type'] != 'P') ? $event['patient']['nom'].' '.$event['patient']['prenom'] : $event['objet'];

        $tempEvent = array('id'        => $event['idEvenement'],
                            'debut'     => $event['debutRdv'],
                            'libelle'   => $libelle,
                            'fin'       => $event['finRdv'],
                            'type'      => $event['type'],
                            'duree'     => $duree,
                            'couleur'   => $eventCouleur['argument'],
                            );

        if (!in_array($tempEvent, $events)) {
            array_unshift($events, $tempEvent);
            $session->set('copied_events', array_slice($events, 0, $nbEventsToStore));

            return $events;
        }

        return false;
    }

    /**
     * [deleteEventInSession description]
     * @param Session $session
     * @param integer $eventId
     * @return array
     */
    public function deleteEventInSession($session, $eventId)
    {
        $nbEventsToStore = 3;
        $events = $session->get('copied_events', array());

        foreach ($events as $key => $event) {
            if ($event['id'] == $eventId) {
                unset($events[$key]);
                break;
            }
        }
        $session->set('copied_events', array_slice($events, 0, $nbEventsToStore));

        return $events;
    }

  /**
   * @param \Gestime\CoreBundle\Entity\Utilisateur $user
   * @param \Gestime\ApiBundle\Model\RdvWeb $rdv
   * @return bool
   */
  public function saveRdvWeb(Utilisateur $user, RdvWeb $rdv) {
    $event= new Evenement();

    $medecin = $this->entityManager
      ->getRepository('GestimeCoreBundle:Medecin')
      ->find($rdv->getMedecinId());

    $event->setMedecin($medecin);

    //recherche du patient (Eviter les doublons)
    $patient = $this->entityManager
        ->getRepository('GestimeCoreBundle:Personne')
        ->findOneByEmail($user->getEmail());

    if (!$patient) {
        //Creation du patient
        $patient = new Personne();
        $patient->setCivilite($user->getSexe() ?1:2);
        $patient->setNom($user->getNom());
        $patient->setPreNom($user->getPreNom());
        $patient->setEmail($user->getEmail());

        $telephoneDoc24 = $user->getPhoneNumber();
        $patient->setTelephone($telephoneDoc24);
        $patient->setEtat('V');
        $patient->setType('P');
    }


    $event->setPatient($patient);

    $event->setType($this->getType('W')[0]);
    $event->setEtat('V');
    $event->setRappel('R');

    $specialite=$this->entityManager->getRepository('GestimeCoreBundle:Specialite')
      ->findOneById($rdv->getSpecialiteId());

    $event->setSpecialite($specialite);
    $event->setDebutRdv($rdv->getDateRdv());
    $dureeConsultation = new \DateInterval('PT'.$this->getDureeConsultation($rdv->getMedecinId())['dureeRdv'].'M');
    $dfin = new \Datetime($rdv->getDateRdv()->format('Y-M-d H:i'));
    $dfin->add($dureeConsultation);
    $event->setFinRdv($dfin);

    $event->setObjet($rdv->getDejaVenu(). '-'. $rdv->getRaison(). '-'. $rdv->getSexe(). '-'. $rdv->getNaissance());

    $idEvent = $this->saveEvent($event);

    return true;

  }

  public function getRdvWebByPatient($patient) {
    if (!$patient) {
        return false;
    }
    return $this->entityManager->getRepository('GestimeCoreBundle:Evenement')
                                    ->findRdvWebById($patient->getId());

  }

}
