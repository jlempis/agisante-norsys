<?php

/**
 * @category Entities
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Evenement
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Gestime\CoreBundle\Entity\EvenementRepository")
 * @Assert\GroupSequence({"TempsReserve", "Evenement", "Format"})
 */
class Evenement
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idEvenement;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="debutRdv", type="datetime")
     */
    private $debutRdv;

    /**
     * @var \datetime
     *
     * @ORM\Column(name="finRdv", type="datetime")
     */
    private $finRdv;
    private $dateRdvTimeStamp;
    private $heureDebut;
    private $heureFin;

    /**
     * @var string
     * @Assert\NotBlank(message="L'objet est obligatoire.", groups={"TempsReserve", "Specialiste"})
     * @ORM\Column(name="objet", nullable=true, type="string", length=255)
     */
    private $objet;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=2)
     */
    private $type;

    /**
     * @var boolean
     *
     * @ORM\Column(name="non_excuse", type="boolean", nullable=true)
     */
    private $nonExcuse;

    /**
     * @var boolean
     *
     * @ORM\Column(name="rappel", nullable=true, type="boolean")
     */
    private $rappel;

    /**
     * @var dateRappel
     *
     * @ORM\Column(name="date_rappel", nullable=true, type="datetime")
     */
    private $dateRappel;

    /**
     * @var string
     *
     * @ORM\Column(name="etat", type="string", length=1)
     */
    private $etat;

    /**
     * @var string
     *
     * @ORM\Column(name="nouveau", nullable=true, type="boolean")
     */
    private $nouveauPatient;

    /**
     * @ORM\ManyToOne(targetEntity="Gestime\CoreBundle\Entity\Specialite")
     * @ORM\JoinColumn(name="specialite_id", referencedColumnName="id", nullable=true)
     */
    private $specialite;

    /**
     * @ORM\ManyToOne(targetEntity="Gestime\CoreBundle\Entity\Medecin", inversedBy="rendezvous")
     * @ORM\JoinColumn(name="medecin_id", referencedColumnName="id", nullable=false)
     */
    private $medecin;

    /**
     * @ORM\ManyToOne(targetEntity="Personne",  cascade={"persist"}))
     * @ORM\JoinColumn(name="patient_id", referencedColumnName="id", nullable=true)
     * @Assert\Valid()
     */
    private $patient;
    private $patientFormId;
    private $patientFormNom;

    /**
     * @ORM\OneToMany(targetEntity="Gestime\CoreBundle\Entity\RelationEvenement", mappedBy="evenementPrecedent",cascade={"persist", "remove"})
     */
    private $precedent;

    /**
     * @ORM\OneToMany(targetEntity="Gestime\CoreBundle\Entity\RelationEvenement", mappedBy="evenementSuivant",cascade={"persist", "remove"})
     */
    private $suivant;

    /******************************************************************
    /* les deux elements suivants sont utiisés pour le lien avec les
    /* identifiants de gestime V1
    ******************************************************************/
    /*
     * @var integer
     *
     * @ORM\Column(name="oldPatientId", nullable=false, type="integer")
     */

    private $oldPatientId;
    /**
     * @var integer
     *
     * @ORM\Column(name="oldRdvId", nullable=true, type="integer")
     */
    private $oldRdvId;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Utilisateur")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     */
    private $createdBy;

    /**
     * @Gedmo\Blameable(on="update")
     * @ORM\ManyToOne(targetEntity="Utilisateur")
     * @ORM\JoinColumn(name="updated_by", referencedColumnName="id")
     */
    private $updatedBy;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime $updatedAt
     */
    protected $updatedAt;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->etat = 'V';
        $this->oldPatientId = 0;
        $this->oldRdvId = 0;

        $this->patient  = new Personne();
    }
    /**
     * Get id
     *
     * @return integer
     */
    public function getIdEvenement()
    {
        return $this->idEvenement;
    }


    /**
     * [setIdEvenement description]
     * @param [type] $idEvenement [description]
     *
     * @return [type] [description]
     */
    public function setIdEvenement($idEvenement)
    {
        $this->idEvenement = intval($idEvenement);

        return $this;
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
     * Utilisé par l'autocomplete
     * @return [type] [description]
     */
    public function getPatientFormId()
    {
        return $this->patientFormId;
    }

    /**
     * Utilisé par l'autocomplete
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
     * Utilisé par l'autocomplete
     * @return [type] [description]
     */
    public function getPatientFormNom()
    {
        return $this->patientFormNom;
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
     * Set Patient
     *
     * @param \Gestime\CoreBundle\Entity\Personne $patient
     * @return Consigne
     */
    public function setPatient($patient)
    {
        $this->patient = $patient;

        return $this;
    }

    /**
     * Set debutRdv
     *
     * @param datetime $debutRdv
     * @return Evenement
     */
    public function setDebutRdv($debutRdv)
    {
        $this->debutRdv = $debutRdv;

        return $this;
    }

    /**
     * Get debutRdv
     *
     * @return datetime
     */
    public function getDebutRdv()
    {
        return $this->debutRdv;
    }

    /**
     * Set finRdv
     *
     * @param datetime $finRdv
     * @return Evenement
     */
    public function setFinRdv($finRdv)
    {
        $this->finRdv = $finRdv;

        return $this;
    }

    /**
     * Get finRdv
     *
     * @return datetime
     */
    public function getFinRdv()
    {
        return $this->finRdv;
    }

    /**
     * @Assert\IsTrue(message="La date du rendez-vous doit être au format jj/mm/aaaa", groups={"TempsReserve", "Specialiste", "Evenement"})
     *
     * @return boolean [description]
     */
    public function isDateRDVValide()
    {
        return true;
    }

    /**
     * @Assert\IsTrue(message="L'heure de début doit être au format HH:MM", groups={"TempsReserve", "Specialiste", "Evenement"})
     *
     * @return boolean [description]
     */
    public function isHeureDebutValide()
    {
        return (preg_match('/(2[0-3]|[01][0-9]):[0-5][0-9]/', $this->heureDebut) == 1);
    }

    /**
     * @Assert\IsTrue(message="L'heure de fin doit être au format HH:MM", groups={"TempsReserve", "Specialiste", "Evenement"})
     *
     * @return boolean [description]
     */
    public function isHeureFinValide()
    {
        return (preg_match('/(2[0-3]|[01][0-9]):[0-5][0-9]/', $this->heureFin) == 1);
    }

    /**
     * @Assert\IsTrue(message="L'heure de fin doit superieure à l'heure de début", groups={"TempsReserve", "Specialiste", "Evenement"})
     *
     * @return boolean [description]
     */
    public function isHeureCoherente()
    {
        if ($this->isHeureDebutValide() && $this->isHeureFinValide()) {
            $hdeb = intval(substr($this->heureDebut, 0, 2));
            $mdeb = intval(substr($this->heureDebut, 3, 2));
            $timestampHdeb = ($hdeb * 3600) + ($mdeb * 60);

            $hfin = intval(substr($this->heureFin, 0, 2));
            $mfin = intval(substr($this->heureFin, 3, 2));
            $timestampHfin = ($hfin * 3600) + ($mfin * 60);

            return ($timestampHfin > $timestampHdeb);
        } else {
            return true;
        }
    }

    /**
     * Get heureDebut
     *
     * @return string
     */
    public function getHeureDebut()
    {
        if (! is_null($this->debutRdv)) {
            return $this->debutRdv->format('H:i');
        } else {
            return '';
        }
    }

    /**
     * Set heureDebut
     *
     * @param string $heureDebut
     *
     * @return Evenement
     */
    public function SetHeureDebut($heureDebut)
    {
        $this->heureDebut = $heureDebut;
        if ($this->isHeureDebutValide()) {
            $hdeb = intval(substr($heureDebut, 0, 2));
            $mdeb = intval(substr($heureDebut, 3, 2));
            $timestampHdeb = ($hdeb * 3600) + ($mdeb * 60);
            $this->dateRdvTimeStamp = $this->debutRdv->getTimestamp();
            $this->debutRdv->setTimestamp($this->dateRdvTimeStamp + $timestampHdeb);
        }

        return $this;
    }

    /**
     * Set heureFin
     *
     * @param string $heureFin
     * @return Evenement
     */
    public function SetHeureFin($heureFin)
    {
        $this->heureFin = $heureFin;
        if ($this->isHeureFinValide()) {
            $hfin = intval(substr($heureFin, 0, 2));
            $mfin = intval(substr($heureFin, 3, 2));
            $timestampHfin = ($hfin * 3600) + ($mfin * 60);
            $this->finRdv = new \DateTime();
            $this->finRdv->setTimestamp($this->dateRdvTimeStamp + $timestampHfin);
        }

        return $this;
    }

    /**
     * Get heureFin
     *
     * @return string
     */
    public function getHeureFin()
    {
        if (! is_null($this->finRdv)) {
            return $this->finRdv->format('H:i');
        } else {
            return '';
        }
    }

    /**
     * Set objet
     *
     * @param string $objet
     * @return Evenement
     */
    public function setObjet($objet)
    {
        $this->objet = $objet;

        return $this;
    }

    /**
     * Get objet
     *
     * @return string
     */
    public function getObjet()
    {
        return $this->objet;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Evenement
     */
    public function setType(Parametre $type)
    {
        $this->type = $type->getCode();

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
     * Set rappel
     *
     * @param boolean $rappel
     * @return Evenement
     */
    public function setRappel($rappel)
    {
        $this->rappel = $rappel;

        return $this;
    }

    /**
     * Get rappel
     *
     * @return boolean
     */
    public function getRappel()
    {
        return $this->rappel;
    }

    /**
     * Set DateRappel
     *
     * @param datetime $dateRappel
     * @return Evenement
     */
    public function setDateRappel($dateRappel)
    {
        $this->dateRappel = $dateRappel;

        return $this;
    }

    /**
     * Get rappel
     *
     * @return datetime
     */
    public function getDateRappel()
    {
        return $this->dateRappel;
    }


    /**
     * Set etat
     *
     * @param string $etat
     * @return Evenement
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get etat
     *
     * @return string
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * Get oldRdvId
     *
     * @return string
     */
    public function getoldRdvId()
    {
        return $this->oldRdvId;
    }

    /**
     * Get oldPatientId
     *
     * @return string
     */
    public function getoldPatientId()
    {
        return $this->oldPatientId;
    }

    /**
     * Set Non excusé
     *
     * @param boolean $nonExcuse
     *
     * @return Evenement
     */
    public function setNonExcuse($nonExcuse)
    {
        $this->nonExcuse = $nonExcuse;

        return $this;
    }

    /**
     * Set oldRdv
     *
     * @param integer $oldRdvId
     *
     * @return Evenement
     */
    public function setOldRdvId($oldRdvId)
    {
        $this->oldRdvId = $oldRdvId;

        return $this;
    }

    /**
     * Set newRdv
     *
     * @param integer $newRdvId
     *
     * @return Evenement
     */
    public function setNewRdvId($newRdvId)
    {
        $this->newRdvId = $newRdvId;

        return $this;
    }

    /**
     * Get Non Excuse
     *
     * @return boolean
     */
    public function isNonExcuse()
    {
        return $this->nonExcuse;
    }

    /**
     * Set Medecin
     *
     * @param \Gestime\CoreBundle\Entity\Medecin $medecin
     * @return Evenement
     */
    public function setMedecin(Medecin $medecin)
    {
        $this->medecin = $medecin;

        return $this;
    }

    /**
     * [changeDate description]
     * @param string $newStartDate [description]
     * @param string $newEndDate   [description]
     * @return [type]               [description]
     */
    public function changeDate($newStartDate, $newEndDate)
    {
        $this->debutRdv = $newStartDate;
        $this->finRdv = $newEndDate;
    }

    /**
     * [getPrecedent description]
     * @return [type] [description]
     */
    public function getPrecedent()
    {
        return $this->precedent[count($this->precedent)-1];
    }
    /**
     * Add Precedent
     * @param Affectation $precedent
     */
    public function setPrecedent($precedent)
    {
        $this->precedent[] = $precedent;
    }

    /**
     * [getSuivant description]
     * @return [type] [description]
     */
    public function getSuivant()
    {
        return $this->suivant[count($this->suivant)-1];
    }
    /**
     * Add Suivant
     * @param Suivant $suivant
     */
    public function setSuivant($suivant)
    {
        $this->suivant[] = $suivant;
    }
    /**
     * @param Datetime $creationDate
     */
    public function setCreated($creationDate)
    {
        $this->created = $creationDate;
        $this->updated = $creationDate;
    }
    /**
     * @param Datetime $updateDate
     */
    public function setUpdated($updateDate)
    {
        $this->updated = $updateDate;
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

    /**
     * @return mixed
     */
    public function getSpecialite() {
      return $this->specialite;
    }

    /**
     * @param mixed $specialite
     */
    public function setSpecialite($specialite) {
      $this->specialite = $specialite;
    }

    /**
     * @return mixed
     */
    public function getNouveauPatient()
    {
        return $this->nouveauPatient;
    }

    /**
     * @param mixed $nouveauPatient
     */
    public function setNouveauPatient($nouveauPatient)
    {
        $this->nouveauPatient = $nouveauPatient;
    }

    /**
     * [getCreated description]
     * @return [type] [description]
     */
    public function getCreated()
    {
        return $this->created;
    }
    /**
     * [getCreated description]
     * @return [type] [description]
     */
    public function getUpdated()
    {
        return $this->updated;
    }
    /**
     * [getCreated description]
     * @return [type] [description]
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }
    /**
     * [getCreated description]
     * @return [type] [description]
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }
}
