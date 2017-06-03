<?php

namespace Gestime\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Gestime\CoreBundle\Entity\Site;
use Gestime\CoreBundle\Entity\Abonne;
use Gestime\CoreBundle\Entity\Medecin;
use Gestime\CoreBundle\Entity\Ligne;
use Gestime\CoreBundle\Entity\Ville;
use Gestime\CoreBundle\Entity\Personne;
use Gestime\CoreBundle\Entity\Parametre;
use Gestime\CoreBundle\Entity\Evenement;
use Gestime\CoreBundle\Entity\RelationEvenement;
use Gestime\CoreBundle\Entity\Horaire;
use Gestime\CoreBundle\Entity\Repondeur;
use Gestime\CoreBundle\Entity\Fermeture;
use Gestime\CoreBundle\Entity\Categorie;

/**
 * Chergement de la base de tests
 */
class LoadUserData implements FixtureInterface
{

    private function creationSite(ObjectManager $manager)
    {
        //Creation du site par defaut
        $site = new Site();
        $site->setNom('SMF');
        $site->setRaisonSociale('Secrétariat Médical des Flandres');
        $site->setTelephone('03.20.10.16.10');
        $site->setFax('03.20.10.04.10');
        $site->setAdresse('5169, Route de Lille');
        $site->setVille('59270 Bailleul');
        $site->setEmail('equipe@smf-59.com');
        $manager->persist($site);
        $manager->flush();

        return $site;
    }

    private function creationCivilite(ObjectManager $manager)
    {
        $parametre = new Parametre();
        $parametre->setSite(1);
        $parametre->setType('Civilite');
        $parametre->setCode(1);
        $parametre->setArgument('');
        $parametre->setValue('Mr');
        $manager->persist($parametre);
        $manager->flush();

        return $parametre;
    }

    private function creationTypeRdv(ObjectManager $manager)
    {
        $typerdv = new Parametre();
        $typerdv->setSite(1);
        $typerdv->setType('TypeRdv');
        $typerdv->setCode('V');
        $typerdv->setArgument('event-violet');
        $typerdv->setValue('Visite');
        $manager->persist($typerdv);
        $manager->flush();

        return $typerdv;
    }

    private function creationJour(ObjectManager $manager)
    {
        $jour = new Parametre();
        $jour->setSite(1);
        $jour->setType('Jour');
        $jour->setCode(1);
        $jour->setArgument('');
        $jour->setValue('Lundi');
        $manager->persist($jour);
        $manager->flush();

        return $jour;
    }

    private function creationActivite(ObjectManager $manager)
    {
        $activite = new Parametre();
        $activite->setSite(1);
        $activite->setType('Activite');
        $activite->setCode('V');
        $activite->setArgument('');
        $activite->setValue('Visites');
        $manager->persist($activite);
        $manager->flush();

        return $activite;
    }

    private function creationClassActivite(ObjectManager $manager)
    {
        $classeActivite = new Parametre();
        $classeActivite->setSite(1);
        $classeActivite->setType('ClassActivite');
        $classeActivite->setCode(1);
        $classeActivite->setArgument('');
        $classeActivite->setValue('fond-bleu');
        $manager->persist($classeActivite);
        $manager->flush();

        return $classeActivite;
    }

    private function creationPatient(ObjectManager $manager)
    {
        $personne = new Personne();
        $personne->setCivilite(1);
        $personne->setNom('nomPatient');
        $personne->setPrenom('prenomPatient');
        $personne->setTelephone('0504030201');
        $personne->setEtat('V');
        $personne->setType('P');
        $manager->persist($personne);
        $manager->flush();

        return $personne;
    }

    private function creationEntreprise(ObjectManager $manager)
    {
        $entreprise = new Personne();
        $entreprise->setCivilite(1);
        $entreprise->setEntreprise('Laboratoire BAYER');
        $entreprise->setNom('nomVisiteur');
        $entreprise->setPrenom('prenomVisiteur');
        $entreprise->setTelephone('0102030405');
        $entreprise->setEtat('V');
        $entreprise->setType('P');
        $manager->persist($entreprise);
        $manager->flush();

        return $entreprise;
    }

    private function creationEvenement(ObjectManager $manager, Medecin $medecin, Personne $patient, Parametre $typeRdv)
    {
        $evenement = new Evenement();
        $evenement->setPatient($patient);
        $evenement->setMedecin($medecin);
        $evenement->setDebutRdv(new \DateTime('06/20/2015 10:00:00'));
        $evenement->setType($typeRdv);
        $evenement->setObjet('Evenement de test');
        $evenement->setFinRdv(new \DateTime('06/20/2015 10:20:00'));

        $manager->persist($evenement);
        $manager->flush();

        $relation = new RelationEvenement();
        $relation->setType('C');
        $relation->setEvenementPrecedent($evenement);
        $manager->flush();

        return $evenement;
    }

    private function creationVille(ObjectManager $manager)
    {
        $ville = new Ville();
        $ville->setCodePostal('62840');
        $ville->setNom('Laventie');
        $manager->persist($ville);
        $manager->flush();

        return $ville;
    }

    private function creationLigne(ObjectManager $manager, Site $site)
    {
        $ligne = new Ligne($site);
        $ligne->setNumero('0320101611');
        $manager->persist($ligne);
        $manager->flush();

        return $ligne;
    }

    private function creationAbonne(ObjectManager $manager, Site $site, Ligne $ligne, Medecin $medecin)
    {
        $abonne = new Abonne($site);
        $abonne->setLigne($ligne);
        $abonne->setRaisonSociale('Cabinet du Dr Bouquillon');
        $abonne->setAdresse('11 place Victor Hugo');
        $abonne->addMedecin($medecin);
        $manager->persist($abonne);
        $manager->flush();

        return $abonne;
    }


    private function creationRepondeur(ObjectManager $manager)
    {
        $repondeur = new Repondeur();
        $repondeur->setTag('Test');
        $repondeur->setCommentaire('Repondeur Test');
        $repondeur->setName('rep.wav');
        $manager->persist($repondeur);
        $manager->flush();

        return $repondeur;
    }

    private function creationFermeture(ObjectManager $manager, Repondeur $repondeur)
    {
        $fermeture = new Fermeture();
        $fermeture->setDatedebut(new \DateTime('12/24/2015'));
        $fermeture->setDatefin(new \DateTime('12/31/2015'));
        $fermeture->setHeuredebut('00:00');
        $fermeture->setHeurefin('00:00');
        $fermeture->setCommentaire('Noel');
        $fermeture->setRepondeur($repondeur);
        $manager->persist($fermeture);
        $manager->flush();

        return $fermeture;
    }

    private function creationHoraire(ObjectManager $manager, Parametre $jour, Parametre $activite, Parametre $classeActivite, Medecin $medecin)
    {
        $horaire = new Horaire();
        $horaire->setJour($jour);
        $horaire->setActivite($activite);
        $horaire->setMedecin($medecin);
        $horaire->setClasse($classeActivite);
        $horaire->setDebut('10:00');
        $horaire->setFin('17:00');
        $horaire->setCommentaire('Commentaire');
        $manager->persist($horaire);

        $medecin->addHoraire($horaire);
        $manager->persist($medecin);

        $manager->flush();

        return $horaire;
    }

    private function creationCategorie(ObjectManager $manager, Site $site)
    {
        $categorie = new Categorie();
        $categorie->setNom('Urgent');
        $categorie->setStyle('danger');
        $categorie->setSite($site);
        $manager->persist($categorie);

        $categorie = new Categorie();
        $categorie->setNom('Visites');
        $categorie->setStyle('warning');
        $categorie->setSite($site);
        $manager->persist($categorie);

        $manager->flush();

        return $categorie;
    }

    private function associationAbonneMedecin(ObjectManager $manager, Abonne $abonne, Medecin $medecin)
    {
        $abonne->addMedecin($medecin);
        $manager->persist($abonne);
        $medecin->setAbonne($abonne);
        $manager->persist($medecin);
        $manager->flush();

        return $abonne;
    }


    private function creationMedecin(ObjectManager $manager, Site $site)
    {
        $medecin = new Medecin($site);
        $medecin->setNom('Dr Bouquillon');
        $medecin->setPrenom('Sylvie');
        $medecin->setRemplacant(false);
        $medecin->setGeneraliste(false);
        $medecin->setDureeRdv(20);
        $medecin->setAbonneSms(false);
        $manager->persist($medecin);
        $manager->flush();

        return $medecin;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $site = $this->creationSite($manager);
        $ville = $this->creationVille($manager);
        $civilite=$this->creationCivilite($manager);
        $typeRdv=$this->CreationTypeRdv($manager);
        $ligne = $this->creationLigne($manager, $site);
        $medecin = $this->creationMedecin($manager, $site);
        $abonne = $this->creationAbonne($manager, $site, $ligne, $medecin);
        $patient = $this->creationPatient($manager);
        $entreprise = $this->creationEntreprise($manager);
        $evenement=$this->creationEvenement($manager, $medecin, $patient, $typeRdv);

        $jour = $this->creationJour($manager);
        $activite = $this->creationActivite($manager);
        $classeActivite=$this->creationClassActivite($manager);
        $horaire = $this->creationHoraire($manager, $jour, $activite, $classeActivite, $medecin);

        $repondeur = $this->creationRepondeur($manager);
        $fermeture=$this->CreationFermeture($manager, $repondeur);
        $abonneMedecin=$this->associationAbonneMedecin($manager, $abonne, $medecin);

        $categorie=$this->creationCategorie($manager, $site);
    }
}
