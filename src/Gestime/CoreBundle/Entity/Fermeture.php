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
 * Fermeture
 * Periode de fermeture exceptionnelle du secretariat
 * Tous les appels sont redirigés vers le répondeur choisi
 */

/**
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Gestime\CoreBundle\Entity\FermetureRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Assert\GroupSequence({"Format", "Chevauchement", "Fermeture"})
 */
class Fermeture
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idFermeture;

    /**
     * @var \Date
     *
     * @ORM\Column(name="datedebut", type="date")
     */
    private $datedebut;

    /**
     * @var \Time
     *
     * @ORM\Column(name="heuredebut", length=5, nullable=false)
     */
    private $heuredebut;

    /**
     * @var \Date
     *
     * @ORM\Column(name="datefin", type="date")
     */
    private $datefin;

    /**
     * @var string
     *
     * @ORM\Column(name="heurefin", length=5, nullable=false)
     */
    private $heurefin;

    /**
     * @ORM\ManyToOne(targetEntity="Repondeur")
     * @ORM\JoinColumn(name="Repondeur_id", referencedColumnName="id")
     */

    protected $repondeur;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire", type="string", length=255)
     */
    private $commentaire;

    /**
     * Get id
     *
     * @return integer
     */
    public function getIdFermeture()
    {
        return $this->idFermeture;
    }

    /**
     * Set debut
     *
     * @param \DateTime $datedebut
     * @return Fermeture
     */
    public function setDatedebut($datedebut)
    {
        $this->datedebut = $datedebut;

        return $this;
    }

    /**
     * Get debut
     *
     * @return \DateTime
     */
    public function getDatedebut()
    {
        return $this->datedebut;
    }

    /**
     * Set fin
     *
     * @param \DateTime $datefin
     * @return Fermeture
     */
    public function setDatefin($datefin)
    {
        $this->datefin = $datefin;

        return $this;
    }

    /**
     * Get fin
     *
     * @return \DateTime
     */
    public function getDatefin()
    {
        return $this->datefin;
    }

    /**
     * Set heuredebut
     *
     * @param \time $heuredebut
     * @return Fermeture
     */
    public function setHeuredebut($heuredebut)
    {
        $this->heuredebut = $heuredebut;

        return $this;
    }

    /**
     * @SmfAssert\HourFormat(groups={"Format"})
     * @return \DateTime
     */
    public function getHeuredebut()
    {
        return $this->heuredebut;
    }

    /**
     * Set heurefin
     *
     * @param \Time $heurefin
     * @return Fermeture
     */
    public function setHeurefin($heurefin)
    {
        $this->heurefin = $heurefin;

        return $this;
    }

    /**
     * @SmfAssert\HourFormat(groups={"Format"})
     *
     * @return Fermeture
     */
    public function getHeurefin()
    {
        return $this->heurefin;
    }

    /**
     * Set commentaire
     *
     * @param string $commentaire
     * @return Fermeture
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
     * Set Repondeur
     * @param Repondeur $repondeur
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

    private function invalidForm()
    {
        return (!is_object($this->datedebut) || !is_object($this->datefin));
    }

    /**
     * @SmfAssert\UniquePeriode(groups={"Chevauchement"})
     * @return boolean
     */
    public function isPeriodesValides()
    {
        if ($this->invalidForm()) {
            return false;
        }

        return (array(
                $this->datedebut->format('Y-m-d ').$this->heuredebut,
                $this->datefin->format('Y-m-d ').$this->heurefin,
                null,
                $this->idFermeture, )
        );
    }

    /**
     * @Assert\IsTrue(message="Le moment de fin doit être > au moment de début.", groups={"Format"})
     * @return boolean
     */
    public function isFinSupDebut()
    {
        if ($this->invalidForm()) {
            return false;
        }

        return ((strtotime($this->datedebut->format('Y-m-d')) + strtotime('1970-01-01 '.$this->heuredebut)) < (strtotime($this->datefin->format('Y-m-d')) + strtotime('1970-01-01 '.$this->heurefin)));
    }

    /**
     * @Assert\IsTrue(message="Le moment de début doit être >= à maintenant.", groups={"Format"})
     * @return boolean
     */
    public function isSupNow()
    {
        if ($this->invalidForm()) {
            return false;
        }
        $maintenant = new \DateTime();

        return ((strtotime($this->datedebut->format('Y-m-d')) + strtotime('1970-01-01 '.$this->heuredebut)) > $maintenant->getTimestamp()-3600);
    }
}
