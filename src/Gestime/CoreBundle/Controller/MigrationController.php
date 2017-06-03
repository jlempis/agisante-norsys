<?php
namespace Gestime\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Gestime\CoreBundle\Entity\Ligne;
use Gestime\CoreBundle\Entity\Abonne;
use Gestime\CoreBundle\Entity\Affectation;
use Gestime\CoreBundle\Entity\Medecin;
use Gestime\CoreBundle\Entity\Telephone;
use Gestime\CoreBundle\Entity\AbonneRepondeur;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Import des données de Gestime V1
 *
 */
class MigrationController extends Controller
{
    protected $site;

    private function getSite($id = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('GestimeCoreBundle:Site');
        $site = $repository->find($id);

        return $site;
    }

    private function getGroup($group)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('GestimeCoreBundle:Group');
        $groupe = $repository->findByName($group);

        return $groupe;
    }

    /**
     * Retrouve la table liée à une entité et fait un Truncate
     * @param string $className Classe dont la table associée est à vider
     */
    private function eraseEntity($className)
    {
        $cmd = $this->getDoctrine()->getManager()->getClassMetadata($className);
        $connection = $this->getDoctrine()->getManager()->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $connection->beginTransaction();
        try {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
            $q = $dbPlatform->getTruncateTableSql($cmd->getTableName());
            $connection->executeUpdate($q);
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollback();
        }
    }

    /**
     * Appelle pour chacune des entités la fonction eraseEntity pour la truncater
     */
    private function eraseAllData()
    {
        $this->eraseEntity('GestimeCoreBundle:Medecin');
        $this->eraseEntity('GestimeCoreBundle:Ligne');
        $this->eraseEntity('GestimeCoreBundle:Abonne');
        $this->eraseEntity('GestimeCoreBundle:Telephone');
        $this->eraseEntity('GestimeCoreBundle:Affectation');
    }

    /**
     * Crée une nouvelle ligne de téléphone (Numero SDA)
     * @param string $numero 4 derniers caractères du n° Ex: 1611
     * @param string $entete 6 premiers caractères du N°
     *
     * @return Ligne Renvoie l'objet Ligne créé
     */
    private function creationLigne($numero, $entete = '032010')
    {
        $em = $this->getDoctrine()->getManager();
        $ligne = new Ligne($this->getSite());
        $ligne->setNumero($entete.$numero);
        $em->persist($ligne);

        return $ligne;
    }

    /**
     * Crée un nouvel abonné (Cabinet médical)
     * @param string $nomAbonne  Raison sociale de l'abonne
     * @param string $typeAbonne S=Specialiste G=Generaliste
     * @param string $debut      Début de validité
     * @param string $fin        Fin de validité
     *
     * @return Abonne Renvoie l'objet Abonne créé
     */
    private function creationAbonne($nomAbonne, $typeAbonne, $debut = '2003-01-01', $fin = '2020-12-31')
    {
        $em = $this->getDoctrine()->getManager();
        $abonne = new Abonne($this->getSite());
        $abonne->setRaisonSociale($nomAbonne);
        $abonne->setDebutValidite(new \DateTime($debut));
        $abonne->setFinValidite(new \DateTime($fin));
        $em->persist($abonne);

        return $abonne;
    }
    /**
     * Crée un nouvel utilisateur (Peut se logguer)
     * @param string $nomAbonne  Raison sociale de l'abonne
     * @param string $typeAbonne S=Specialiste G=Generaliste
     * @param string $debut      Début de validité
     * @param string $fin        Fin de validité
     *
     * @return Abonne Renvoie l'objet Abonne créé
     */
    private function creationUtilisateur($idAgenda, $code, $password, $prenom, $nom, $droits)
    {
        $em = $this->getDoctrine()->getManager();

        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->createUser();

        $user->setUsername($code);
        $user->setPlainPassword($password);
        $user->setPrenom($prenom);
        $user->setNom($nom);
        $user->setEmail(strtolower($prenom.'.'.$nom.'@gestime.net'));
        $user->setSite($this->getUser()->getSite());
        $user->setEnabled(true);

        if ($droits == 'admin') {
            $user->addGroups($this->getGroup('ADMIN_SECRETAIRE')[0]);
        };
        if ($droits == 'user') {
            $user->addGroups($this->getGroup('SECRETAIRE')[0]);
        };

        $userManager->updateUser($user);

        return $user;
    }

    private function associationUtilisateurMedecin($utilisateur, $medecins)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('GestimeCoreBundle:Medecin');

        foreach ($medecins as $nom) {
            $medecin = $repository->getMedecinByNom($nom)->getQuery()->getResult();
            if ($medecin !== null) {
                $utilisateur->addMedecin($medecin[0]);
            }
        }
        $em->persist($utilisateur);

        return $utilisateur;
    }
    /**
     * Crée un nouveau médecin
     * @param string $nomMedecin      Nom du Medecin
     * @param string $typeMedecin     P=Permanant R=Remplacant
     * @param string $activiteMedecin Spécialité du médecin
     *
     * @return Medecin Renvoie l'objet Medecin créé
     */
    private function creationMedecin($nomMedecin, $typeMedecin, $activiteMedecin, $dureeRdv = 15)
    {
        $em = $this->getDoctrine()->getManager();
        $medecin = new Medecin($this->getSite());
        $medecin->setNom($nomMedecin);
        $medecin->setPrenom('');

        if ($typeMedecin == 'P') {
            $medecin->setRemplacant(false);
        } else {
            $medecin->setRemplacant(true);
        }

        if ($typeMedecin == 'Généraliste') {
            $medecin->setGeneraliste(true);
        } else {
            $medecin->setGeneraliste(false);
        }

        $medecin->setDureeRdv($dureeRdv);
        $medecin->setAbonneSms(false);
        $em->persist($medecin);

        return $medecin;
    }

    /**
     * Ajoute un numéro de téléphone à un médecin
     * @param string  $numero     Numéro de téléphone
     * @param string  $typeNumero P=Portable, F=Fixe, T=Telecopie (Fax)
     * @param Medecin $medecin    Objet Medecin auquel rattacher le numéro
     *
     * @return numtel Renvoie l'objet NumerosTelephone créé
     */
    private function creationNumerosTelephone($numero, $typeNumero, $medecin)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('GestimeCoreBundle:Parametre');

        $typetel = $repository->getParamByTypeAndCode('Telephone', $typeNumero)->getQuery()->getResult();
        $numtel = new Telephone();
        $numtel->setNumero($numero);
        if ($typeNumero == 2) {
            $numtel->setEnvoiSMS(true);
        } else {
            $numtel->setEnvoiSMS(false);
        }
        $numtel->setType($typetel[0]);
        $numtel->setMedecin($medecin);
        $em->persist($numtel);

        return $numtel;
    }

    /**
     * Ajoute un numéro de téléphone à un médecin
     * @param Abonne  $abonne  Objet Abonné auquel rattacher le médecin
     * @param Medecin $medecin Objet Medecin à rattacher à l'abonné
     *
     * @return numtel Renvoie l'objet NumerosTelephone créé
     */
    private function associationAbonneMedecin($abonne, $medecin)
    {
        $em = $this->getDoctrine()->getManager();
        $abonne->addMedecin($medecin);
        $em->persist($abonne);
        $medecin->setAbonne($abonne);

        return $abonne;
    }

    /**
     * Renseigne l'ancien numero d'Agenda
     * @param string $medecinStr Nom du médecin
     * @param integer idAgenda identifiant de l'ancien agenda
     *
     * @return medecin Renvoie l'objet Medecin créé
     */
    private function affectationIdAgenda($medecinStr, $idAgenda)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('GestimeCoreBundle:Medecin');
        $medecin = $repository->getMedecinByNom($medecinStr)->getQuery()->getResult()[0];
        $medecin->setIdAgenda($idAgenda);
        $em->persist($medecin);

        return $medecin;
    }

    /**
     * Associe une ligne à un abonné (Numero sur lequel la ligne est renvoyée
     * @param Abonne $abonne Objet Abonné auquel rattacher le médecin
     * @param Ligne  $ligne  Objet Medecin à rattacher à l'abonné
     * @param string $debut  Début de validité
     * @param string $fin    Fin de validité
     *
     * @return numtel Renvoie l'objet NumerosTelephone créé
     */
    private function affectationLigne($abonne, $ligne, $debut = '2001-01-01', $fin = '2020-12-31')
    {
        $em = $this->getDoctrine()->getManager();

        $affectation = new Affectation();
        $affectation->setLigne($ligne);
        $affectation->setAbonne($abonne);
        $affectation->setDebut(new \DateTime($debut));
        $affectation->setFin(null);
        $em->persist($affectation);

        return true;
    }

    private function creationRepondeursWeekEnd($abonne, $repondeur)
    {
        $joursWeekEnd = ['Samedi','Dimanche'];
        foreach ($joursWeekEnd as $jour) {
            $util = $this->creationPeriodeNonTravaillee($abonne, $repondeur, $jour, '00:00', '23:59');
        }
    }

    private function getJoursSemaine()
    {
        return ['Lundi','Mardi', 'Mercredi', 'Jeudi', 'Vendredi'];
    }

    private function findRepondeur($entityManager, $abonneStr)
    {
        $repository = $em->getRepository('GestimeCoreBundle:Repondeur');
        $repondeur = $repository->getRepondeurByTag($repondeurStr)->getQuery()->getResult()[0];

        return $repondeur;
    }

    private function deleteABonneRepondeur($abonne)
    {
        $qb = $em->createQueryBuilder()
            ->delete('Gestime\CoreBundle\Entity\AbonneRepondeur', 'p')
            ->where('p.abonne = :abonne')
            ->setParameter('abonne', $abonne)->getQuery();
        $qb->execute();
    }

    private function findAbonne($entityManager, $abonneStr)
    {
        $repository = $entityManager->getRepository('GestimeCoreBundle:Abonne');
        $abonne = $repository->getAbonneByName($abonneStr)->getQuery()->getResult()[0];

        return $abonne;
    }

    private function creationSemaineTypeGeneraliste($abonneStr, $repondeurStr)
    {
        $abonne = $this->findAbonne($this->getDoctrine()->getManager(), $abonneStr);
        $this->deleteABonneRepondeur($abonne);
        $repondeur = $this->findRepondeur($this->getDoctrine()->getManager(), $repondeurStr);

        foreach ($this->getJoursSemaine() as $jour) {
            $util = $this->creationPeriodeNonTravaillee($abonne, $repondeur, $jour, '00:00', '07:59');
            $util = $this->creationPeriodeNonTravaillee($abonne, $repondeur, $jour, '12:30', '13:29');
            $util = $this->creationPeriodeNonTravaillee($abonne, $repondeur, $jour, '19:00', '23:59');
        }
        $util = $this->creationRepondeursWeekEnd($abonne, $repondeur);
    }

    private function creationSemaineTypeSpecialiste($abonneStr, $repondeurStr)
    {
        $abonne = $this->findAbonne($this->getDoctrine()->getManager(), $abonneStr);
        $this->deleteABonneRepondeur($abonne);
        $repondeur = $this->findRepondeur($this->getDoctrine()->getManager(), $repondeurStr);

        foreach ($this->getJoursSemaine() as $jour) {
            $util = $this->creationPeriodeNonTravaillee($abonne, $repondeur, $jour, '00:00', '08:59');
            $util = $this->creationPeriodeNonTravaillee($abonne, $repondeur, $jour, '12:00', '13:59');
            $util = $this->creationPeriodeNonTravaillee($abonne, $repondeur, $jour, '19:00', '23:59');
        }
        $util = $this->creationRepondeursWeekEnd($abonne, $repondeur);
    }

    private function creationPeriodeNonTravaillee($abonne, $repondeur, $jourStr, $debut, $fin)
    {
        $em = $this->getDoctrine()->getManager();

        $repository = $em->getRepository('GestimeCoreBundle:Parametre');
        $jour = $repository->getParamByTypeAndValue('Jour', $jourStr)->getQuery()->getResult()[0];
        $periode = new abonneRepondeur();
        $periode->setAbonne($abonne);
        $periode->setRepondeur($repondeur);
        $periode->setJour($jour);
        $periode->setDebut($debut);
        $periode->setFin($fin);
        $em->persist($periode);

        return true;
    }

    private function associationIdAgendaMedecin()
    {
        $assoc = $this->affectationIdAgenda('Dr Borys', 330176);
        $assoc = $this->affectationIdAgenda('Dr Montjean', 316893);
        $assoc = $this->affectationIdAgenda('Dr Fontaine', 287332);
        $assoc = $this->affectationIdAgenda('Dr Carissimo', 194785);
        $assoc = $this->affectationIdAgenda('Dr Courchelle', 194786);
        $assoc = $this->affectationIdAgenda('Dr Tourbier', 194792);
        $assoc = $this->affectationIdAgenda('Dr Pachy', 194787);
        $assoc = $this->affectationIdAgenda('Dr Stocq', 1133);
        $assoc = $this->affectationIdAgenda('Dr Bouquillon', 94);
        $assoc = $this->affectationIdAgenda('Dr Broly', 420);
        $assoc = $this->affectationIdAgenda('Psychologue', 349672);
        $assoc = $this->affectationIdAgenda('Dieteticienne', 349671);
        $assoc = $this->affectationIdAgenda('Dr Bourteel', 353787);

        $assoc = $this->affectationIdAgenda('Dr Bernard', 102732);
        $assoc = $this->affectationIdAgenda('Dr Courchelle', 194786);

        $assoc = $this->affectationIdAgenda('Dr Sartorius', 1083);
        $assoc = $this->affectationIdAgenda('Dr Lagon', 95);
        $assoc = $this->affectationIdAgenda('Dr Dandoy', 606);
        $assoc = $this->affectationIdAgenda('Dr Demory', 19571);
        $assoc = $this->affectationIdAgenda('Dr Jacaton', 19627);
        $assoc = $this->affectationIdAgenda('Dr Accary', 1132);
        $assoc = $this->affectationIdAgenda('Dr Doutrelant', 193379);
        $assoc = $this->affectationIdAgenda('Dr Duvinage', 146101);
        $assoc = $this->affectationIdAgenda('Dr Breuvart', 302212);
        $assoc = $this->affectationIdAgenda('Dr Leblond', 23856);
        $assoc = $this->affectationIdAgenda('Dr Delsol', 134042);
        $assoc = $this->affectationIdAgenda('Dr Lemal', 31698);
        $assoc = $this->affectationIdAgenda('Dr Jacquart', 104815);
        $assoc = $this->affectationIdAgenda('Dr Pavy', 346542);
        $assoc = $this->affectationIdAgenda('Dr Hoornaert', 135762);
        $assoc = $this->affectationIdAgenda('Dr Whyts', 277755);
        $assoc = $this->affectationIdAgenda('Dr Gianioro', 60964);
        $assoc = $this->affectationIdAgenda('Dr Croizier', 27516);
        $assoc = $this->affectationIdAgenda('Dr Guffroy', 233566);
        $assoc = $this->affectationIdAgenda('Dr Hiault', 70454);
        $assoc = $this->affectationIdAgenda('Dr Bennameur', 132502);
        $assoc = $this->affectationIdAgenda('Dr Boute', 132500);
        $assoc = $this->affectationIdAgenda('Dr Lawerier', 72236);
        $assoc = $this->affectationIdAgenda('Dr Gosset Karine', 146463);
        $assoc = $this->affectationIdAgenda('Dr Vasseur', 80825);
        $assoc = $this->affectationIdAgenda('Dr Descamps', 84083);
        $assoc = $this->affectationIdAgenda('Dr Bouhassoun', 127443);
        $assoc = $this->affectationIdAgenda('Dr Chuffart', 164832);
        $assoc = $this->affectationIdAgenda('Dr Bennaceur', 166338);
        $assoc = $this->affectationIdAgenda('Dr Dujardin', 310613);
        $assoc = $this->affectationIdAgenda('Dr Gosset Olivier', 169643);
        $assoc = $this->affectationIdAgenda('Dr André', 218485);
        $assoc = $this->affectationIdAgenda('Dr Grepinez', 308339);
        $assoc = $this->affectationIdAgenda('Dr Perez', 197762);
        $assoc = $this->affectationIdAgenda('Dr Henry', 194829);
    }

    /**
     * @Route("/repondeurs", name="migration_repondeurs",options={"expose"=true})
     *
     * [populateRepondeursAction description]
     * @return URL
     */
    public function populateRepondeursAction()
    {
        $util = $this->associationIdAgendaMedecin();

        $em = $this->getDoctrine()->getManager();

        $em->flush();

        return $this->redirect($this->generateUrl('abonnes_liste'));
    }

    /**
     * @Route("/utilisateurs", name="migration_utilisateurs",options={"expose"=true})
     *
     * @return URL
     */
    public function populateUtilisateursAction()
    {
        $em = $this->getDoctrine()->getManager();
        $util = $this->creationUtilisateur(96, 'sophie', 'soge', 'Sophie', 'Gendrin', 'admin');
        $util = $this->creationUtilisateur(97, 'Sylvie2', 'syde', 'Sylvie', 'Degoul', 'admin');
        $util = $this->creationUtilisateur(19973, 'dorothee', 'doda', 'Dorothee', 'Dhallewyn', 'admin');
        $util = $this->creationUtilisateur(127884, 'BABETH', 'BABI', 'Elisabeth', 'Bidault', 'admin');
        $util = $this->creationUtilisateur(211705, 'claudine', 'cloclo', 'Claudine', 'Halut', 'admin');

        $util = $this->creationUtilisateur(95, 'LAGON', '1618', 'Stéphane', 'Lagon', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Lagon'));

        $util = $this->creationUtilisateur(94, 'sylvie', '1611', 'Sylvie', 'Bouquillon', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Bouquillon'));

        $util = $this->creationUtilisateur(606, 'DANDOY', '1619', 'Dr', 'Dandoy', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Dandoy'));

        $util = $this->creationUtilisateur(420, 'BROLY', '1613', 'Dr', 'Broly', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Broly'));

        $util = $this->creationUtilisateur(1083, 'sarto', '1617', 'Dr', 'Sartorius', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Sartorius'));

        $util = $this->creationUtilisateur(1132, 'caroline', '1621', 'Caroline', 'Accary', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Accary'));

        $util = $this->creationUtilisateur(1133, 'stocq', '1615', 'Dr', 'Stocq', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Stocq'));

        $util = $this->creationUtilisateur(19571, 'DEMORY', '1620', 'Bertrand', 'Demory', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Demory', 'Dr Jacaton'));

        $util = $this->creationUtilisateur(19627, 'Jacaton', '1620', 'Noëlla', 'Jacaton', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Demory', 'Dr Jacaton'));

        $util = $this->creationUtilisateur(23856, 'camelia', 'narcisse', 'Elisabeth', 'leblond', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Leblond'));

        $util = $this->creationUtilisateur(24218, 'TéLéPHON', 'Faxé', 'Frédérique', 'Dieulouard', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Dieulouard Frédérique'));

        $util = $this->creationUtilisateur(27516, 'croizier', '1628', 'Dr', 'Croizier', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Croizier'));

        $util = $this->creationUtilisateur(31698, 'soleil', 'bisou', 'Daniel', 'Lemal', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Lemal'));

        $util = $this->creationUtilisateur(39117, 'XAVIER', 'XD52', 'Dr', 'Xavier', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Xavier'));

        $util = $this->creationUtilisateur(60964, 'pierre', '4277', 'Pierre', 'Giagnorio', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Gianioro'));

        $util = $this->creationUtilisateur(310613, 'dujardin', 'paul', 'Paul', 'Dujardin', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Dujardin'));

        $util = $this->creationUtilisateur(70454, 'HIAULT', '9960', 'Dr', 'Hiault', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Hiault'));

        $util = $this->creationUtilisateur(72236, 'catherin', '9963', 'Catherine', 'Lauwerier', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Lawerier'));

        $util = $this->creationUtilisateur(80825, 'VASSEUR', '9965', 'Dr', 'Vasseur', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Vasseur'));

        $util = $this->creationUtilisateur(84083, 'DESCAMPS', '9965', 'Dr', 'Descamps', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Descamps'));

        $util = $this->creationUtilisateur(102732, 'bernard', '9962', 'Dr', 'Bernard', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Bernard'));

        $util = $this->creationUtilisateur(104815, 'jacquart', 'noël', 'Dr', 'Jacquart', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Jacquart'));

        $util = $this->creationUtilisateur(127443, 'NASSIRA', 'concorde', 'Dr', 'Bouhassoun', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Bouhassoun'));

        $util = $this->creationUtilisateur(132500, 'KENNEDY1', '9961', 'Dr', 'Boute', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Boute', 'Dr Bennameur'));

        $util = $this->creationUtilisateur(132502, 'KENNEDY3', '9961', 'Dr', 'Benameur', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Boute', 'Dr Bennameur'));

        $util = $this->creationUtilisateur(346542, 'ANTOINE', '4273', 'Antoine', 'Pavy', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Pavy'));

        $util = $this->creationUtilisateur(134042, 'DELSOL', 'Valérie', 'Valérie', 'Delsol', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Delsol'));

        $util = $this->creationUtilisateur(135762, 'HOORNAER', '1973', 'Dr', 'Hoornaert', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Hoornaert'));

        $util = $this->creationUtilisateur(146101, 'DUDU', '1624', 'Dr', 'Duvinage', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Duvinage'));

        $util = $this->creationUtilisateur(146463, 'karine', '9964', 'Karine', 'Gosset', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Gosset'));

        $util = $this->creationUtilisateur(164832, 'CHUFFART', 'Brigitte', 'Brigitte', 'Chuffart', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Chuffart'));

        $util = $this->creationUtilisateur(166338, 'Pascale', '6291', 'Pascale', 'Bennaceur', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Bennaceur'));

        $util = $this->creationUtilisateur(169643, 'OLIVIER', '6293', 'Olivier', 'Gosset', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Gosset'));

        $util = $this->creationUtilisateur(193379, 'jj', '1622', 'Jean-Jacques', 'Doutrelant', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Doutrelant'));

        $util = $this->creationUtilisateur(194785, 'cari', 'cari', 'Patrice', 'Carissimo', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Carissimo', 'Dr Courchelle', 'Dr Tourbier', 'Dr Pachy'));

        $util = $this->creationUtilisateur(194786, 'éric', 'rico', 'Eric', 'Courchelle', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Carissimo', 'Dr Courchelle', 'Dr Tourbier', 'Dr Pachy'));

        $util = $this->creationUtilisateur(194787, 'manu', 'manu', 'Dr', 'Pachy', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Carissimo', 'Dr Courchelle', 'Dr Tourbier', 'Dr Pachy'));

        $util = $this->creationUtilisateur(194793, 'tourbier', 'corinne', 'Corinne', 'Tourbier', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Carissimo', 'Dr Courchelle', 'Dr Tourbier', 'Dr Pachy'));

        $util = $this->creationUtilisateur(194829, 'HENRY', 'Isabelle', 'Isabelle', 'Henry', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Henry'));

        $util = $this->creationUtilisateur(197762, 'STEPHANE', '6297', 'Stéphane', 'Perez', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Perez'));

        $util = $this->creationUtilisateur(218485, 'plout', '6294', 'Agnes', 'André', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr André', 'Dr Grepinez'));

        $util = $this->creationUtilisateur(308339, 'caro', 'caro', 'Dr', 'Grepinet', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr André', 'Dr Grepinez'));

        $util = $this->creationUtilisateur(233566, 'sabine', '4279', 'Sabine', 'Guffroy', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Guffroy'));

        $util = $this->creationUtilisateur(277755, 'wyts', 'lolo', 'Laurence', 'Wyts', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Whyts'));

        $util = $this->creationUtilisateur(284966, 'celiobe', 'dom', 'Dr', 'Celiobe', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Celiobe'));

        $util = $this->creationUtilisateur(299280, 'laura', 'lapi', 'Laura', 'Pilosio', 'user');

        $util = $this->creationUtilisateur(302212, 'christin', 'angelo', 'Dr', 'Breuvart', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Breuvart'));

        $util = $this->creationUtilisateur(287332, 'fontaine', '6296', 'Philippe', 'Fontaine', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Fontaine'));

        $util = $this->creationUtilisateur(316893, 'MONTJEAN', '6295', 'Frédéric', 'Montjean', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Montjean'));

        $util = $this->creationUtilisateur(330176, 'BORYS', '6299', 'Dr', 'Borys', 'user');
        $habilitation = $this->associationUtilisateurMedecin($util, array('Dr Borys'));

        $em->flush();

        return $this->redirect($this->generateUrl('abonnes_liste'));
    }

    /**
     * @Route("/", name="migration",options={"expose"=true})
     *
     * @return URL
     */
    public function populateAction()
    {
        $this->associationIdAgendaMedecin();
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->redirect($this->generateUrl('abonnes_liste'));
    }
}
