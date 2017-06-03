<?php

namespace Gestime\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LogMessage
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Gestime\CoreBundle\Entity\LogMessageRepository")
 */
class LogMessage
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
     * @var integer
     *
     * @ORM\Column(name="message_id", type="integer")
     */
    private $messageId;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_envoi", type="string", length=20)
     */
    private $numeroEnvoi;

    /**
     * @ORM\ManyToOne(targetEntity="Gestime\CoreBundle\Entity\Medecin", inversedBy="sms")
     * @ORM\JoinColumn(name="medecin_id", referencedColumnName="id", nullable=false)
     */
    private $medecin;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=1)
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var integer
     *
     * @ORM\Column(name="campagne_id", type="string", length=255, nullable=true)
     */
    private $campagneId;

    /**
     * @var string
     *
     * @ORM\Column(name="texte", type="string", length=255)
     */
    private $texte;

    /**
     * @var string
     *
     * @ORM\Column(name="statut", type="string", length=1 )
     */
    private $statut;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_essais", type="integer")
     */
    private $nbEssais;

    /**
     * @var integer
     *
     * @ORM\Column(name="resultat", type="integer")
     */
    private $resultat;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_envoi", type="datetimetz", nullable=true)
     */
    private $dateEnvoi;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_reception", type="datetimetz", nullable=true)
     */
    private $dateReception;

    /**
     * @var integer
     *
     * @ORM\Column(name="patient_id", type="integer", nullable=true)
     */
    private $patientId;

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
     * [setMessageId description]
     * @param [type] $messageId [description]
     *
     * @return LogMessage
     */
    public function setMessageId($messageId)
    {
        $this->messageId = $messageId;

        return $this;
    }

    /**
     * Get messageId
     *
     * @return integer
     */
    public function getMessageId()
    {
        return $this->messageId;
    }

    /**
     * Set numeroEnvoi
     *
     * @param string $numeroEnvoi
     * @return LogMessage
     */
    public function setNumeroEnvoi($numeroEnvoi)
    {
        $this->numeroEnvoi = $numeroEnvoi;

        return $this;
    }

    /**
     * Get numeroEnvoi
     *
     * @return string
     */
    public function getNumeroEnvoi()
    {
        return $this->numeroEnvoi;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return LogMessage
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return LogMessage
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set campagneId
     *
     * @param string $campagneId
     * @return LogMessage
     */
    public function setCampagneId($campagneId)
    {
        $this->campagneId = $campagneId;

        return $this;
    }

    /**
     * Get campagneId
     *
     * @return string
     */
    public function getCampagneId()
    {
        return $this->campagneId;
    }

    /**
     * Set resultat
     *
     * @param integer $resultat
     * @return LogMessage
     */
    public function setResultat($resultat)
    {
        $this->resultat = $resultat;

        return $this;
    }

    /**
     * Get resultat
     *
     * @return integer
     */
    public function getResultat()
    {
        return $this->resultat;
    }

    /**
     * Set statut
     *
     * @param integer $statut
     * @return LogMessage
     */
    public function setStatut($statut)
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * Get statut
     *
     * @return integer
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * Set texte
     *
     * @param string $texte
     * @return LogMessage
     */
    public function setTexte($texte)
    {
        $this->texte = $texte;

        return $this;
    }

    /**
     * Get texte
     *
     * @return string
     */
    public function getTexte()
    {
        return $this->texte;
    }

    /**
     * Set nbEssais
     *
     * @param integer $nbEssais
     * @return LogMessage
     */
    public function setNbEssais($nbEssais)
    {
        $this->nbEssais = $nbEssais;

        return $this;
    }

    /**
     * Get nbEssais
     *
     * @return integer
     */
    public function getNbEssais()
    {
        return $this->nbEssais;
    }

    /**
     * Set dateReception
     *
     * @param \DateTime $dateReception
     * @return LogMessage
     */
    public function setDateReception($dateReception)
    {
        $this->dateReception = $dateReception;

        return $this;
    }

    /**
     * Get dateReception
     *
     * @return \DateTime
     */
    public function getDateReception()
    {
        return $this->dateReception;
    }

    /**
     * Set dateEnvoi
     *
     * @param \DateTime $dateEnvoi
     * @return LogMessage
     */
    public function setDateEnvoi($dateEnvoi)
    {
        $this->dateEnvoi = $dateEnvoi;

        return $this;
    }

    /**
     * Set dateEnvoi
     *
     * @return int
     */
    public function addEssai()
    {
        $this->nbEssais = $this->nbEssais +1;

        return $this->nbEssais;
    }

    /**
     * Get dateEnvoi
     *
     * @return \DateTime
     */
    public function getDateEnvoi()
    {
        return $this->dateEnvoi;
    }

    /**
     * Set patientId
     *
     * @param integer $patientId
     * @return LogMessage
     */
    public function setPatientId($patientId)
    {
        $this->patientId = $patientId;

        return $this;
    }

    /**
     * Get patientId
     *
     * @return integer
     */
    public function getPatientId()
    {
        return $this->patientId;
    }

    /**
     * Set Medecin
     *
     * @param \Gestime\CoreBundle\Entity\Medecin $medecin
     * @return LogMessage
     */
    public function setMedecin(Medecin $medecin)
    {
        $this->medecin = $medecin;

        return $this;
    }

    /**
     * Get Medecin
     *
     * @return Medecin
     */
    public function getMedecin()
    {
        return $this->medecin;
    }
}
