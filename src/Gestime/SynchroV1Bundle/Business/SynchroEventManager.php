<?php

namespace Gestime\SynchroV1Bundle\Business;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Gestime\CoreBundle\Entity\Evenement;
use Gestime\CoreBundle\Entity\Personne;
use Gestime\CoreBundle\Entity\Absence;
use Gestime\CoreBundle\Entity\Remplacement;


use Gestime\SynchroV1Bundle\Entity\Evenement as EvenementV1;
use Gestime\SynchroV1Bundle\Entity\Personne as PersonneV1;
use Gestime\CoreBundle\Entity\RelationEvenement;

/**
 * SynchroManager
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */


class SynchroEventManager
{
    /**
     * @var
     */
    protected $managerRegistry;
    protected $entityManagerV1;
    protected $entityManagerV2;
    /**
     * @var ContainerInterface
     */
    protected $container;


    /**
     * @param ContainerInterface $container
     * @param EntityManager $entityManager
     */
    public function __construct(ContainerInterface $container, ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
        $this->container = $container;
        $this->entityManagerV1 = $this->managerRegistry->getManagerForClass('Gestime\SynchroV1Bundle\Entity\Evenement');
        $this->entityManagerV2 = $this->managerRegistry->getManagerForClass('Gestime\CoreBundle\Entity\Evenement');
    }

    /**
     * @param $personne
     * @return mixed
     */
    private function creationPatient($personne) {

        $personneV1 = new PersonneV1();
        $personneV1->setType('');
        $personneV1->setAdresse('');
        $personneV1->setCivilite($personne->getCivilite());
        $personneV1->setCodePostal('');
        $personneV1->setEmail('');
        $personneV1->setEntreprise($personne->getEntreprise());
        $personneV1->setEtat('V');
        $personneV1->setType('P');
        $personneV1->setNom($personne->getNom());
        $personneV1->setPrenom($personne->getPrenom());
        $personneV1->setNomMarital($personne->getnomJF());
        $personneV1->setIdProprietaire(99);
        $personneV1->setTelephone($personne->getTelephone());

        $this->entityManagerV1->persist($personneV1);
        $this->entityManagerV1->flush();

        return $personneV1->getId();
    }

    private function getPersonneV2($evenementV1)
    {
        $patient = $this->entityManagerV2
            ->getRepository('GestimeCoreBundle:Personne')
            ->findOneByIdPers($evenementV1['c_id_pers']);

        if (!is_null($patient)){
            return $patient;
        } else {

            //le patient n'existe pas, on le crée
            $personneV2 = new Personne();
            $personneV2->setEntreprise($evenementV1['entreprise']);
            $personneV2->setNom($evenementV1['nom']);
            $personneV2->setPrenom($evenementV1['prenom']);
            $personneV2->setNomJF($evenementV1['prenom']);
            $personneV2->setTelephone($evenementV1['telephone']);
            $personneV2->setType('P');
            $personneV2->setCivilite($evenementV1['civilite']);
            $personneV2->setEtat('V');

            $this->entityManagerV2->persist($personneV2);
            $this->entityManagerV2->flush();

            return $personneV2;
        }
    }

    /**
     * @param array $evenementV1
     * @return Evenement
     */
    public function CreateEventReserveFromEventV1($evenementV1)
    {
        $eventMgr = $this->container->get('gestime.event.manager');
        $evenement = new Evenement();

        //Récupération du médecin
        $medecin = $this->entityManagerV2
            ->getRepository('GestimeCoreBundle:Medecin')
            ->findOneByIdAgenda($evenementV1['medecin']);
        $idMedecin= $medecin->getIdMedecin();
        $debut = $evenementV1['debutRdv'].' '.$evenementV1['heureDebut'];
        $fin = $evenementV1['debutRdv'].' '.$evenementV1['heureFin'];
        $eventMgr->setReserve($idMedecin, $debut, $fin, false, $evenementV1['id']);

        return $evenement;
    }

    /**
     * @param array $absenceV1
     * @return Absence
     */
    public function CreateAbsenceFromV1($absenceV1)
    {
        $absenceMgr = $this->container->get('gestime.absence.manager');
        $absence = new Absence();

        //Récupération du médecin
        $medecin = $this->entityManagerV2
            ->getRepository('GestimeCoreBundle:Medecin')
            ->findOneByIdAgenda($absenceV1['medecin']);

        $absence->setMedecin($medecin);
        $absence->setDebut(new \DateTime($absenceV1['debut']));
        $absence->setFin(new \DateTime($absenceV1['fin']));
        $absence->setCommentaire($absenceV1['commentaire']);
        $absence->setOldAbsenceId($absenceV1['id']);

        $absenceMgr->save_absence($absence);

        return $absence;
    }
    /**
     * @param array $absenceV1
     * @return Absence
     */
    public function DeleteAbsenceFromV1($idAbsenceV1)
    {
        $absenceMgr = $this->container->get('gestime.absence.manager');
        $absence = new Absence();

        //Récupération de l'absence
        $absence = $this->entityManagerV2
            ->getRepository('GestimeCoreBundle:Absence')
            ->findOneByOldAbsenceId($idAbsenceV1);

        $remplacementsAvantModif = $absenceMgr->getRemplacements($absence);

        $absenceMgr->save_deleted_absence($absence, $remplacementsAvantModif);

        return $absence;
    }
    /**
     * @param array $remplacementV1
     * @return Remplacement
     */
    public function CreateRemplacementFromV1($remplacementV1)
    {
        $absenceMgr = $this->container->get('gestime.absence.manager');

        //Récupération de l'absence
        $absence = $this->entityManagerV2
            ->getRepository('GestimeCoreBundle:Absence')
            ->findOneByOldAbsenceId($remplacementV1['id']);

        //Récupération du médecin remplacé
        $medecinRemplace = $absence->getMedecin();

        //Récupération du médecin remplacant
        $medecinRemplacant = $this->entityManagerV2
            ->getRepository('GestimeCoreBundle:Medecin')
            ->findByNomRemplacant($remplacementV1['medecinremplacant']);

        $remplacementsAvantModif = $absenceMgr->getRemplacements($absence);

        $remplacement = new Remplacement();
        $remplacement->setMedecinRemplacant($medecinRemplacant);
        $remplacement->setMedecinRemplace($medecinRemplace);
        $remplacement->setDebut(new \DateTime($remplacementV1['debut']));
        $remplacement->setFin(new \DateTime($remplacementV1['fin']));
        $remplacement->setAbsence($absence);

        $absence->addRemplacement($remplacement);
        $absenceMgr->save_edited_absence($absence, $remplacementsAvantModif);

        return $absence;
    }

    /**
     * @param array $evenementV1
     * @return Evenement
     */
    public function CreateEventFromEventV1($evenementV1)
    {
        $evenement = new Evenement();

        //Récupération du médecin
        $medecin = $this->entityManagerV2
          ->getRepository('GestimeCoreBundle:Medecin')
          ->findOneByIdAgenda($evenementV1['medecin']);
        $evenement->setMedecin($medecin);

        $evenement->setDebutRdv(new \DateTime($evenementV1['debutRdv'].' '.$evenementV1['heureDebut']));
        $evenement->setFinRdv(new \DateTime($evenementV1['debutRdv'].' '.$evenementV1['heureFin']));
        $evenement->setObjet($evenementV1['objet']);
        $evenement->setOldRdvId($evenementV1['id']);

        //Récupération du type de Rdv
        switch ($evenementV1['type']) {
            case 'VM' :
                $typeV1 = 'L';
                break;
            case 'R' :
                $typeV1 = 'R';
                break;
            default:
                $typeV1 = $evenementV1['type'];
        }
        $typeV2 = $this->entityManagerV2
          ->getRepository('GestimeCoreBundle:Parametre')
          ->getParamByTypeAndCode('TypeRdv', $typeV1)
          ->getQuery()->getResult()[0];
        $evenement->setType($typeV2);
        $evenement->setRappel($evenementV1['rappel']);

        //Récupération du patient
        if ($evenementV1['medecin'] != 'P') {
            $evenement->setPatient($this->getPersonneV2($evenementV1));
        }

        $this->entityManagerV2->persist($evenement);
        $this->entityManagerV2->flush();

        return $evenement;
    }

    /**
     * @param Evenement $evenement
     * @return EvenementV1
     */
    private function CreateEventV1FromEvent(Evenement $evenement)
    {
        $evenementV1 = new EvenementV1();
        $evenementV1->setIdPersonne($evenement->getMedecin()->getIdAgenda());
        $evenementV1->setDateRdv($evenement->getDebutRdv());
        $evenementV1->setHdeb($evenement->getDebutRdv()->format('H'));
        $evenementV1->setMdeb($evenement->getDebutRdv()->format('i'));
        $evenementV1->setHfin($evenement->getFinRdv()->format('H'));
        $evenementV1->setMfin($evenement->getFinRdv()->format('i'));
        $evenementV1->setObjet($evenement->getObjet());
        $evenementV1->setDateModification(new \DateTime('NOW'));
        $evenementV1->setRappel($evenement->getRappel());

        switch ($evenement->getType()) {
            case 'L' :
                $evenementV1->setType('VM');
                break;
            case 'C' :
                $evenementV1->setType('R');
                break;
            case 'R' :
                $evenementV1->setType('R');
                break;
            case 'V' :
                $evenementV1->setType('R');
                break;
            default:
                $evenementV1->setType($evenement->getType());
        }

        $evenementV1->setEtat('V');
        $evenementV1->setCreateur(92);

        return $evenementV1;
    }

    /**
     * @param Evenement $evenement
     * @return EvenementV1
     */
    public function create_evenement(Evenement $evenement)
    {
        $evenementV1 = $this->CreateEventV1FromEvent($evenement);

        if ($evenement->getType() != 'P') {
            if (! is_null($evenement->getPatient()->getIdPers())) {
                $evenementV1->setIdContact($evenement->getPatient()->getIdPers());
            } else
            {
                //Creation du nouveau patient dans GestimeV1
                $idPers = $this->creationPatient($evenement->getPatient());
                $evenementV1->setIdContact($idPers);
            }
        } else {
            $evenementV1->setIdContact(299280);
        }

        $this->entityManagerV1->persist($evenementV1);
        $this->entityManagerV1->flush();

        $evenement->setOldRdvId($evenementV1->getId());
        $this->entityManagerV2->persist($evenement);
        $this->entityManagerV2->flush();

        return $evenementV1;
    }

    /**
     * @param Evenement $evenement
     */
    public function update_evenement(Evenement $eventSource, Evenement $eventCible)
    {
        $evtV1Source = $this->delete_evenement($eventSource);
        $evtV1Cible = $this->create_evenement($eventCible);

        $evtV1Cible->setIdEvtPrec($evtV1Source->getId());
        $this->entityManagerV1->persist($evtV1Cible);
        $this->entityManagerV1->flush();
    }

    /**
     * @param Evenement $evenement
     */
    public function DeleteEventFromEventV1($id)
    {
        $evenement = $this->entityManagerV2
            ->getRepository('GestimeCoreBundle:Evenement')
            ->findOneByOldRdvId($id);

        $evenement->setEtat('A');
        $this->entityManagerV2->persist($evenement);

        $relation = new RelationEvenement();
        $relation->setType('S');
        $relation->setEvenementPrecedent($evenement);
        $this->entityManagerV2->persist($relation);

        $this->entityManagerV2->flush();

        return $evenement;
    }

    /**
     * @param Evenement $evenement
     */
    public function delete_evenement(Evenement $evenement)
    {
        $evtV1Sources = $this->entityManagerV1
                        ->getRepository('GestimeSynchroV1Bundle:Evenement')
                        ->getEvent($evenement->getoldRdvId());
        $evtV1Source = $evtV1Sources[0];

        $evtV1Source->setEtat('A');
        $this->entityManagerV1->persist($evtV1Source);
        $this->entityManagerV1->flush();

        return $evtV1Source;
    }
}
