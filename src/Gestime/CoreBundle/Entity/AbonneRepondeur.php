<?php

/**
 * @category Entities
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gestime\CoreBundle\Validator as SmfAssert;

/**
 * AbonneRepondeur
 *
 * @ORM\Entity(repositoryClass="Gestime\CoreBundle\Entity\AbonneRepondeurRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Assert\GroupSequence({"Format", "Chevauchement", "AbonneRepondeur"})
 */
class AbonneRepondeur
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var integer $id
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Abonne", inversedBy="periodes")
     * @ORM\JoinColumn(name="abonne_id", referencedColumnName="id")
     */
    protected $abonne;

    /**
     * @ORM\ManyToOne(targetEntity="Repondeur")
     * @ORM\JoinColumn(name="Repondeur_id", referencedColumnName="id")
     */
    protected $repondeur;

    /**
     * @ORM\ManyToOne(targetEntity="Parametre")
     * @ORM\JoinColumn(name="jour_id", referencedColumnName="id")
     */
    private $jour;

    /**
     * @var \Time
     *
     * @ORM\Column(type="string", length=5, nullable=false, name="debut")
     */
    private $debut;

    /**
     * @var \Time
     *
     * @ORM\Column(type="string", length=5, nullable=false, name="fin")
     */
    private $fin;

    private $indexPeriode;
    /* ===================================================================
    * Fonctions de gestion du chevauchement de dates
    *  ==================================================================*/
    private function addDayToHour($day, $hour)
    {
        return sprintf('2000-01-%02d ', $day).$hour;
    }

    private function getTableauPeriodesSoeurs($periodes)
    {
        foreach ($periodes as $periodeAbonneParent) {
            //  AJout de la notion de jour pour la  comparaison
                // Le ctrl de chevauchement se fait sur jour-debut-fin
                $tableauPeriodesAValider[] = array(
                    'start' => $this->addDayToHour(intval($periodeAbonneParent->getJour()->getCode()),
                        $periodeAbonneParent->getDebut()),
                    'end' => $this->addDayToHour(intval($periodeAbonneParent->getJour()->getCode()),
                        $periodeAbonneParent->getFin()),
                );
        }

        return $tableauPeriodesAValider;
    }
    private function getPeriodesAComparer($tableauComplet, $indiceASupprimer)
    {
        unset($tableauComplet[$indiceASupprimer]);

        return $tableauComplet;
    }

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
     * @param [type] $index [description]
     *
     * @return Abonne
     */
    public function setIndexPeriode($index)
    {
        $this->indexPeriode = $index;

        return $this;
    }

    /**
     * [getIndexPeriode description]
     * @return [type] [description]
     */
    public function getIndexPeriode()
    {
        return $this->indexPeriode;
    }

    /**
     * Set debut
     *
     * @param \DateTime $debut
     * @return AbonneRepondeur
     */
    public function setDebut($debut)
    {
        $this->debut = $debut;

        return $this;
    }

    /**
     * @SmfAssert\HourFormat(groups={"Format"})
     * @return [type] [description]
     */
    public function getDebut()
    {
        return $this->debut;
    }

    /**
     * Set fin
     *
     * @param \DateTime $fin
     * @return AbonneRepondeur
     */
    public function setFin($fin)
    {
        $this->fin = $fin;

        return $this;
    }

    /**
     * @SmfAssert\HourFormat(groups={"Format"})
     * @return \Time
     */
    public function getFin()
    {
        return $this->fin;
    }

    /**
     * @SmfAssert\UniquePeriode(groups={"Chevauchement"})
     *
     * [isPeriodesValides description]
     * @return boolean [description]
     */
    public function isPeriodesValides()
    {
        if ($this->indexPeriode === null) {
            //Nous ne sommes pas  dans un formulaire abonné ..
            return false;
        }

        return array( $this->addDayToHour($this->getJour()->getCode(), $this->debut),
                      $this->addDayToHour($this->getJour()->getCode(), $this->fin),
                      $this->getPeriodesAComparer($this->getTableauPeriodesSoeurs($this->abonne->getPeriodes()),
                      $this->indexPeriode),
                      null,
        );
    }
    /**
     * @Assert\IsTrue(message="L'heure de fin doit être > à l'heure de début.", groups={"Format"})
     *
     * @return AbonneRepondeur
     */
    public function isFinSupDebut()
    {
        return (strtotime($this->debut) < strtotime($this->fin));
    }

    /**
     * Set jour
     *
     * @param Parametre $parametre
     * @return AbonneRepondeur
     */
    public function setJour($parametre)
    {
        $this->jour = $parametre;

        return $this;
    }

    /**
     * Get jour
     *
     * @return Parametre
     */
    public function getJour()
    {
        return $this->jour;
    }

    /**
     * Set fin
     *
     * @param Abonne $abonne
     * @return Abonné
     */
    public function setAbonne(Abonne $abonne)
    {
        $this->abonne = $abonne;
    }

    /**
     * Get Abonne
     *
     * @return Abonne
     */
    public function getAbonne()
    {
        return $this->abonne;
    }

    /**
     * Set Repondeur
     * @param Repondeur $repondeur
     * @return \DateTime
     */
    public function setRepondeur($repondeur)
    {
        $this->repondeur = $repondeur;
    }

    /**
     * Get Repondeur
     *
     * @return Repondeur
     */
    public function getRepondeur()
    {
        return $this->repondeur;
    }
}
