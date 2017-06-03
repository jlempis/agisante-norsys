<?php

namespace Gestime\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Consigne
 * Une consigne est  un texte associé à un médeci et un patient
 * Une consigne est affichée pendant la saisie d'un RDV pour un patient particulier
 *
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Gestime\CoreBundle\Entity\ConsigneRepository")
 * @Assert\GroupSequence({"Chevauchement", "Consigne"})
 */
class Consigne
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idConsigne;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="debut", type="datetime", nullable=false)
     */
    private $debut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fin", type="datetime", nullable=false)
     */
    private $fin;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     * @Assert\NotBlank(message="La description est obligatoire")
     */
    private $description;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=true)
     */
    private $visible;

    /**
     * @var boolean
     *
     * @ORM\Column(name="bloquante", type="boolean", nullable=true)
     */
    private $bloquante;

    /**
     * @ORM\ManyToOne(targetEntity="Gestime\CoreBundle\Entity\Medecin", inversedBy="consignes")
     * @ORM\JoinColumn(name="medecin_id", referencedColumnName="id", nullable=false)
     */
    private $medecin;

    /**
     * @ORM\ManyToOne(targetEntity="Gestime\CoreBundle\Entity\Personne", inversedBy="consignes")
     * @ORM\JoinColumn(name="patient_id", referencedColumnName="id", nullable=false)
     */
    private $patient;

    private $patientFormId;
    private $patientFormNom;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setDebut(new \DateTime('now'));
        $this->setFin(new \DateTime('2019-12-31'));
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getIdConsigne()
    {
        return $this->idConsigne;
    }

    /**
     * [setPatientFormId description]
     * @param [type] $patientFormId [description]
     *
     * @return [type] [description]
     */
    public function setPatientFormId($patientFormId)
    {
        $this->patientFormId = $patientFormId;

        return $this;
    }

    /**
     * [getPatientFormId description]
     *
     * @return [type] [description]
     */
    public function getPatientFormId()
    {
        return $this->patientFormId;
    }

    /**
     * [setPatientFormNom description]
     * @param [type] $patientFormNom [description]
     *
     * @return [type] [description]
     */
    public function setPatientFormNom($patientFormNom)
    {
        $this->patientFormNom = $patientFormNom;

        return $this;
    }

    /**
     * [getPatientFormNom description]
     * @return [type] [description]
     */
    public function getPatientFormNom()
    {
        return $this->patientFormNom;
    }

    /**
     * Set debut
     *
     * @param \DateTime $debut
     * @return Consigne
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
     * @return Consigne
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
     * Set description
     *
     * @param string $description
     * @return Consigne
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     * @return Consigne
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get visible
     *
     * @return boolean
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Set bloquante
     *
     * @param boolean $bloquante
     * @return Consigne
     */
    public function setBloquante($bloquante)
    {
        $this->bloquante = $bloquante;

        return $this;
    }

    /**
     * Get bloquante
     *
     * @return boolean
     */
    public function getBloquante()
    {
        return $this->bloquante;
    }

    /**
     * Get Medecin
     *
     * @return string
     */
    public function getMedecin()
    {
        return $this->medecin;
    }

    /**
     * Get Patient
     *
     * @return string
     */
    public function getPatient()
    {
        return $this->patient;
    }

    /**
     * Set Medecin
     *
     * @param \Gestime\CoreBundle\Entity\Medecin $medecin
     * @return Consigne
     */
    public function setMedecin(Medecin $medecin)
    {
        $this->medecin = $medecin;

        return $this;
    }

    /**
     * Set Patient
     *
     * @param \Gestime\CoreBundle\Entity\Patient $patient
     * @return Consigne
     */
    public function setPatient($patient)
    {
        $this->patient = $patient;

        return $this;
    }
    /**
     * @Assert\IsTrue(message="La fin de validité doit être > au début.", groups={"Chevauchement"})
     *
     * [isFinSupDebut description]
     * @return boolean [description]
     */
    public function isFinSupDebut()
    {
        return ((strtotime($this->debut->format('Y-m-d'))  < (strtotime($this->fin->format('Y-m-d')))));
    }

    /**
     * @Assert\IsTrue(message="Le début de validité doit être >= à maintenant.", groups={"Chevauchement"})
     *
     * [isSupNow description]
     * @return boolean [description]
     */
    public function isSupNow()
    {
        return ((strtotime($this->debut->format('Y-m-d'))  >= strtotime('today midnight')));
    }

    /**
     * @Assert\IsTrue(message="Le nom saisi ne correspond à aucun patient.", groups={"Chevauchement"})
     *
     * [isValide description]
     * @return boolean [description]
     */
    public function isValide()
    {
        return ($this->getPatientFormId() >0);
    }
}
