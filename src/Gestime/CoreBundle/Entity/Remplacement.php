<?php

/**
 * @category Entities
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Remplacement
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Gestime\CoreBundle\Entity\RemplacementRepository")
 * @Assert\GroupSequence({"Format", "Chevauchement", "Remplacement"})
 */
class Remplacement
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Gestime\CoreBundle\Entity\Medecin", inversedBy="remplaces")
     * @ORM\JoinColumn(name="remplace_id", referencedColumnName="id", nullable=false)
     */
    private $medecinRemplace;

    /**
     * @ORM\ManyToOne(targetEntity="Gestime\CoreBundle\Entity\Medecin", inversedBy="remplacants")
     * @ORM\JoinColumn(name="remplacant_id", referencedColumnName="id", nullable=false)
     */
    private $medecinRemplacant;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="debut", type="date")
     */
    private $debut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fin", type="date")
     */
    private $fin;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire", type="string", length=255, nullable=true)
     */
    private $commentaire;

    /**
     * @ORM\ManyToOne(targetEntity="Gestime\CoreBundle\Entity\Absence", inversedBy="remplacements")
     * @ORM\JoinColumn(name="absence_id", referencedColumnName="id", nullable=false)
     */
    private $absence;

    private $indexPeriode;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * [setIndexPeriode description]
     * @param index $index
     * @return Remplacement
     */
    public function setIndexPeriode($index)
    {
        $this->indexPeriode = $index;

        return $this;
    }

    /**
     * [getIndexPeriode description]
     * @return integer index de la periode en cours (Utilisé par les controles de chevauchement)
     */
    public function getIndexPeriode()
    {
        return $this->indexPeriode;
    }

    /**
     * Set debut
     *
     * @param \DateTime $debut
     * @return Remplacement
     */
    public function setDebut($debut)
    {
        $this->debut = $debut;

        return $this;
    }

    /**
     * Get debut
     *
     * @return \DateTime
     */
    public function getDebut()
    {
        return $this->debut;
    }

    /**
     * Set fin
     *
     * @param \DateTime $fin
     * @return Remplacement
     */
    public function setFin($fin)
    {
        $this->fin = $fin;

        return $this;
    }

    /**
     * Get fin
     *
     * @return \DateTime
     */
    public function getFin()
    {
        return $this->fin;
    }

    /**
     * Set commentaire
     *
     * @param string $commentaire
     * @return Remplacement
     */
    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    /**
     * Get commentaire
     *
     * @return string
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }

    /**
     * Set medecinRemplace
     * @param Repondeur $medecinRemplace
     */
    public function setMedecinRemplace($medecinRemplace)
    {
        $this->medecinRemplace = $medecinRemplace;
    }

    /**
     * Get MedecinRemplace
     *
     * @return Medecin
     */
    public function getMedecinRemplace()
    {
        return $this->medecinRemplace;
    }

    /**
     * Get Medecin
     *
     * @return Medecin
     */
    public function getAbsence()
    {
        return $this->absence;
    }
    /**
     * Set Absence
     * @param Absence $absence
     */
    public function setAbsence($absence)
    {
        $this->absence = $absence;
    }

    /**
     * Set MedecinRemplacant
     * @param Medecin $medecinRemplacant
     */
    public function setMedecinRemplacant($medecinRemplacant)
    {
        $this->medecinRemplacant = $medecinRemplacant;
    }

    /**
     * Get MedecinRemplacant
     *
     * @return Remplacement
     */
    public function getMedecinRemplacant()
    {
        return $this->medecinRemplacant;
    }

    /**
     * [time_overlap description]
     * @param datetime $startTime
     * @param datetime endTime
     * @param integer  $times
     * @return boolean
     */
    private function time_overlap($startTime, $endTime, $times)
    {
        $ustart = strtotime($startTime);
        $uend   = strtotime($endTime);
        foreach ($times as $time) {
            $start = strtotime($time['start']);
            $end   = strtotime($time['end']);
            if ($ustart < $end && $uend > $start) {
                return true;
            }
        }

        return false;
    }

    /**
     * @Assert\IsTrue(message="Cette période chevauche une autre période", groups={"Format"})
     *
     * @return boolean
     */
    public function isPeriodesValides()
    {
        if ($this->indexPeriode === null) {
            //Nous ne sommes pas  dans un formulaire abonné ..
            return true;
        }

        return (!$this->time_overlap(
            $this->debut->format('Y-m-d'),
            $this->fin->format('Y-m-d'),
            $this->getPeriodesAComparer(
            $this->getTableauPeriodesSoeurs($this->absence->getRemplacements()), $this->indexPeriode),
        $this->getTableauPeriodesSoeurs($this->absence->getRemplacements())));
    }

    private function getTableauPeriodesSoeurs($periodes)
    {
        $tableauPeriodesAValider = array();
        foreach ($periodes as $periodeAbsenceParente) {
            $tableauPeriodesAValider[] = array( 'start' => $periodeAbsenceParente->getDebut()->format('Y-m-d'),
                                                    'end' =>  $periodeAbsenceParente->getFin()->format('Y-m-d'), );
        }

        return $tableauPeriodesAValider;
    }

    private function getPeriodesAComparer($tableauComplet, $indiceASupprimer)
    {
        unset($tableauComplet[$indiceASupprimer]);

        return $tableauComplet;
    }

    /**
     * @Assert\IsTrue(message="La date de fin doit être > à la date de début.", groups={"Format"})
     *
     * @return boolean
     */
    public function isFinSupDebut()
    {
        return (strtotime($this->debut->format('Y-m-d')) <= strtotime($this->fin->format('Y-m-d')));
    }

    /**
     * @Assert\IsTrue(message="Le remplacement ne peut pas commencer avant l'absence.", groups={"Format"})
     *
     * @return boolean
     */
    public function isDebutSupDebAbsence()
    {
        return (strtotime($this->getAbsence()->getDebut()->format('Y-m-d')) <= strtotime($this->debut->format('Y-m-d')));
    }
    /**
     * @Assert\IsTrue(message="Le remplacement ne peut pas finir après l'absence.", groups={"Format"})
     *
     * @return boolean
     */
    public function isFinSupFinAbsence()
    {
        return (strtotime($this->getAbsence()->getFin()->format('Y-m-d')) >= strtotime($this->fin->format('Y-m-d')));
    }
}
