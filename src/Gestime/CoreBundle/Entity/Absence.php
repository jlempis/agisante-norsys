<?php

namespace Gestime\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Absence
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Gestime\CoreBundle\Entity\AbsenceRepository")
 * @Assert\GroupSequence({"Format", "Chevauchement", "Absence"})
 */
class Absence
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idAbsence;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="debut", type="datetime")
     */
    private $debut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fin", type="datetime")
     */
    private $fin;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire", type="string", length=255)
     */
    private $commentaire;

    /**
     * @ORM\ManyToOne(targetEntity="Gestime\CoreBundle\Entity\Medecin", inversedBy="absences")
     * @ORM\JoinColumn(name="medecin_id", referencedColumnName="id", nullable=false)
     */
    private $medecin;

    /**
     * @ORM\OneToMany(targetEntity="Gestime\CoreBundle\Entity\Remplacement", mappedBy="absence",cascade={"persist", "remove"})
     */
    private $remplacements;

    /**
     * @var integer
     *
     * @ORM\Column(name="oldAbsenceId", nullable=true, type="integer")
     */
    private $oldAbsenceId;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->remplacements = new ArrayCollection();
        foreach ($this->remplacements as $remplacement) {
            $remplacement->SetAbsence($this);
        }
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getIdAbsence()
    {
        return $this->idAbsence;
    }

    /**
     * Set debut
     *
     * @param \DateTime $debut
     * @return Absence
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
     * @return Absence
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
     * Set medecin
     *
     * @param Medecin $medecin
     * @return Absence
     */
    public function setMedecin($medecin)
    {
        $this->medecin = $medecin;

        return $this;
    }

    /**
     * Get medecin
     *
     * @return string
     */
    public function getMedecin()
    {
        return $this->medecin;
    }

    /**
     * Set commentaire
     *
     * @param string $commentaire
     * @return Absence
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
     * Get remplacements
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRemplacements()
    {
        return $this->remplacements;
    }

    /**
     * Add remplacement
     *
     * @param Remplacement $remplacements
     *
     * @return Absence
     */
    public function addRemplacement(Remplacement $remplacements)
    {
        foreach ($remplacements as $remplacement) {
            $remplacement->SetAbsence($this);
        }

        $this->remplacements[] = $remplacements;

        return $this;
    }

    /**
     * Remove remplacement
     *
     * @param Remplacement $remplacement
     */
    public function removeRemplacement(Remplacement $remplacement)
    {
        $this->remplacements->removeElement($remplacement);
    }

    /**
     * @Assert\IsTrue(message="La fin de l'absence doit être > à son début.", groups={"Format"})
     *
     * [isFinSupDebut description]
     * @return boolean [description]
     */
    public function isFinSupDebut()
    {
        return (strtotime($this->debut->format('Y-m-d')) <= strtotime($this->fin->format('Y-m-d')));
    }

    /**
     * @Assert\IsTrue(message="Le début de l'absence doit être >= à maintenant.", groups={"Format"})
     *
     * [isSupNow description]
     * @return boolean [description]
     */
    public function isSupNow()
    {
        $maintenant = new \DateTime();

        return (strtotime($this->debut->format('Y-m-d')) >= $maintenant->getTimestamp()-3600);
    }

    /**
     * @return int
     */
    public function getOldAbsenceId()
    {
        return $this->oldAbsenceId;
    }

    /**
     * @param int $oldAbsenceId
     */
    public function setOldAbsenceId($oldAbsenceId)
    {
        $this->oldAbsenceId = $oldAbsenceId;
    }

}
