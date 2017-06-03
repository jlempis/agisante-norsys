<?php

namespace Gestime\EventBundle\Business;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Gestime\CoreBundle\Entity\Absence;
use Gestime\CoreBundle\Entity\Medecin;

/**
 * AbsenceManager
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class AbsenceManager
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
     * Créé une absence en base
     * @param Absence $absence
     * @return integer id de l'absence crée
     */
    public function save_absence(Absence $absence)
    {
        foreach ($absence->getRemplacements() as $remplacement) {
            $remplacement->setMedecinRemplace($absence->getMedecin());
            $remplacement->setAbsence($absence);
            $this->entityManager->persist($remplacement);
        }
        $this->entityManager->persist($absence);
        $this->entityManager->flush();

        return $absence->getidAbsence();
    }

    /**
     * Modifie une absence en base
     * @param Absence $absence
     * @param Array   $remplacementsAvantModif
     * @return integer id de l'absence modifiee
     */
    public function save_edited_absence(Absence $absence, $remplacementsAvantModif)
    {
        //Suppression des remplacements si besoin
        foreach ($absence->getRemplacements() as $remplacement) {
            foreach ($remplacementsAvantModif as $key => $toDel) {
                if ($toDel->getId() === $remplacement->getId()) {
                    unset($remplacementsAvantModif[$key]);
                }
            }
        }
        foreach ($remplacementsAvantModif as $remplacement) {
            $this->entityManager->remove($remplacement);
        }

        foreach ($absence->getRemplacements() as $remplacement) {
            $remplacement->setAbsence($absence);
            $remplacement->setMedecinRemplace($absence->getMedecin());
            $this->entityManager->persist($remplacement);
        }

        $this->entityManager->persist($absence);
        $this->entityManager->flush();

        return $absence->getidAbsence();
    }

    /**
     * [getRemplacements description]
     * @param Absence $absence
     * @return Array
     */
    public function getRemplacements(Absence $absence)
    {
        $remplacementsAvantModif = array();
        foreach ($absence->getRemplacements() as $remplacement) {
            $remplacementsAvantModif[] = $remplacement;
        }

        return $remplacementsAvantModif;
    }

    /**
     * Insere les jours fériès (chaque année ou à la création d'un médecin)
     * @param  Medecin $medecin
     * @param  integer $annee
     * @return boolean
     */
    public function majFeries(Medecin $medecin, $annee=0)
    {
        if ($annee == 0) {
            $annee = date('Y');
        }
        $jour = 3600*24;

        //Dates fixes
        $fixes = [
        ['date' => '01/01'.'/'.$annee,'description' => 'Jour de l\'an'],
        ['date' => '08/05'.'/'.$annee,'description' => 'Armistice 39-45'],
        ['date' => '01/11'.'/'.$annee,'description' => 'Toussaint'],
        ['date' => '15/08'.'/'.$annee,'description' => 'Assomption'],
        ['date' => '01/05'.'/'.$annee,'description' => 'Fête du travail'],
        ['date' => '11/11'.'/'.$annee,'description' => 'Armistice 14-18'],
        ['date' => '14/07'.'/'.$annee,'description' => 'Fête nationale'],
        ['date' => '25/12'.'/'.$annee,'description' => 'Noël']
        ];

        //Dates mobiles
        $mobiles = [
        ['date' => date('d', easter_date($annee)+1*$jour).'/'.date('m', easter_date($annee)+1*$jour).'/'.$annee,  'description' => 'Lundi de Pâques'],
        ['date' => date('d', easter_date($annee)+39*$jour).'/'.date('m', easter_date($annee)+39*$jour).'/'.$annee,'description' => 'Jeudi de l\'ascenscion'],
        ['date' => date('d', easter_date($annee)+50*$jour).'/'.date('m', easter_date($annee)+50*$jour).'/'.$annee,'description' => 'Lundi de Pentecôte']
        ];

        $feries = array_merge($fixes, $mobiles);

        foreach ($feries as $ferie) {
            //On vérifie que la date en cours n'existe pas déjà ..
            $dateFerie = new \DateTime($annee.'-'.substr($ferie['date'], 3, 2).'-'.substr($ferie['date'], 0, 2));
            $existe = count($this->entityManager->getRepository('GestimeCoreBundle:Absence')
                           ->getAbsenceMdecinByDate($medecin->getIdMedecin(), $dateFerie, $dateFerie));

            if ($existe == 0) {
                $absence = New Absence();
                $absence->setDebut($dateFerie);
                $absence->setFin($dateFerie);
                $absence->setCommentaire($ferie['description']);
                $absence->setMedecin($medecin);
                $this->entityManager->persist($medecin);
                $this->entityManager->persist($absence);
            }

        }
        $this->entityManager->flush();

        return true;
    }

    /**
     * Supprime une absence
     * @param Absence $absence
     * @param array   $remplacements
     * @return boolean
     */
    public function save_deleted_absence(Absence $absence, $remplacements)
    {
        if (count($remplacements) >0) {
            foreach ($remplacements as $remplacement) {
                $this->entityManager->remove($remplacement);
            }
        }
        $this->entityManager->remove($absence);
        $this->entityManager->flush();

        return true;
    }


}
