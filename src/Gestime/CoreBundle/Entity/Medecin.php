<?php

/**
 * @category Entities
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Gestime\CoreBundle\Entity\Specialite;

/**
 * Medecin
 * Un médecin peut etre titulaire ou remplacant
 * Un médecin titulaire possède un agenda, appartient à un abonné
 *
 */

/**
 * @ORM\Entity(repositoryClass="Gestime\CoreBundle\Entity\MedecinRepository")
 */

class Medecin
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idMedecin;

    /**
     * @ORM\ManyToOne(targetEntity="Site")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id", nullable=false)
     */
    private $site;

    /**
     * @ORM\Column(type="string", length=255, nullable=false, name="nom")
     *
     * @Assert\Length(min="5", max="255")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, name="prenom")
     */
    private $prenom;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $remplacant;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $rdvInternet;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $generaliste;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $agenda;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $idAgenda;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $abonneSms;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $dureeRdv;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $msgRappel;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $tempsAvantRappel;

    /**
     * @ORM\ManyToMany(targetEntity="Specialite", inversedBy="medecins")
     * @ORM\JoinTable(
     *     name="medecin2specialite",
     *     joinColumns={@ORM\JoinColumn(name="medecin_id", referencedColumnName="id", nullable=false)},
     *     inverseJoinColumns={@ORM\JoinColumn(name="specialite_id", referencedColumnName="id", nullable=false)}
     * )
     */
    private $specialites;

    /**
     * @ORM\OneToMany(targetEntity="Telephone", mappedBy="medecin",  cascade={"persist", "remove"})
     * @Assert\Valid()
     */
    private $telephones;

    /**
     * @ORM\ManyToOne(targetEntity="Abonne", inversedBy="medecins")
     * @ORM\JoinColumn(name="abonne_id", referencedColumnName="id", nullable=true)
     */
    private $abonne;

    /**
     * @ORM\ManyToMany(targetEntity="Message", mappedBy="medecins")
     * @ORM\JoinTable(name="message2medecin")
     */
    private $messages;

    /**
     * @ORM\OneToMany(targetEntity="LogMessage", mappedBy="medecin",  cascade={"persist", "remove"})
     */
    private $sms;

    /**
     * @ORM\ManyToMany(targetEntity="Utilisateur", mappedBy="medecins")
     */
    private $utilisateurs;

    /**
     * @ORM\OneToMany(targetEntity="Evenement", mappedBy="medecin",cascade={"persist", "remove"})
     */
    private $rendezvous;

    /**
     * @ORM\OneToMany(targetEntity="Absence", mappedBy="medecin",cascade={"persist", "remove"})
     */
    private $absences;

    /**
     * @ORM\OneToMany(targetEntity="Consigne", mappedBy="medecin",cascade={"persist", "remove"})
     */
    private $consignes;

    /**
     * @ORM\OneToMany(targetEntity="Remplacement", mappedBy="medecinRemplacant",cascade={"persist", "remove"})
     */
    private $remplacants;

    /**
     * @ORM\OneToMany(targetEntity="Remplacement", mappedBy="medecinRemplace",cascade={"persist", "remove"})
     */
    private $remplaces;

    /**
     * @ORM\OneToMany(targetEntity="Horaire", mappedBy="medecin",cascade={"persist", "remove"})
     */
    private $horaires;

    /**
     * @ORM\OneToMany(targetEntity="HoraireInternet", mappedBy="medecin",cascade={"persist", "remove"})
     */
    private $horairesInternet;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $carence;

    /**
     * @ORM\ManyToMany(targetEntity="Personne", mappedBy="medecins")
     */
    protected $patients;

    /**
     * @ORM\OneToOne(targetEntity="InfosDoc24", mappedBy="medecin",cascade={"persist", "remove"})
     */
    private $infosDoc24;

    /**
     * @ORM\ManyToOne(targetEntity="Parametre")
     * @ORM\JoinColumn(name="abonnement_id", referencedColumnName="id")
     */
    private $abonnement;

    /**
     * [__construct description]
     * @param Site $site
     */
    public function __construct($site)
    {
        $this->site = $site;
        $this->telephones = new ArrayCollection();
        $this->utilisateurs = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getIdMedecin()
    {
        return $this->idMedecin;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Medecin
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Get nomPrenom
     *
     * @return string
     */
    public function getNomPrenom()
    {
        return $this->nom.' '.$this->prenom;
    }
    /**
     * Set prenom
     *
     * @param string $prenom
     *
     * @return Medecin
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get idExterne
     * @param integer $idExterne
     * @return integer
     */
    public function getIdExterne($idExterne)
    {
        return $this->idExterne;
    }

    /**
     * Set idExterne
     *
     * @param integer $idExterne
     *
     * @return Medecin
     */
    public function setIdExterne($idExterne)
    {
        $this->idExterne = $idExterne;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set agenda
     *
     * @param boolean $agenda
     *
     * @return Medecin
     */
    public function setAgenda($agenda)
    {
        $this->agenda = $agenda;

        return $this;
    }

    /**
     * Get agenda
     *
     * @return boolean
     */
    public function hasAgenda()
    {
        return $this->agenda;
    }

    /**
     * Set generaliste
     *
     * @param boolean $generaliste
     *
     * @return Medecin
     */
    public function setGeneraliste($generaliste)
    {
        $this->generaliste = $generaliste;

        return $this;
    }

    /**
     * Get generaliste
     *
     * @return boolean
     */
    public function isGeneraliste()
    {
        return $this->generaliste;
    }

    /**
     * Get rdvInternet
     *
     * @return boolean
     */
    public function isRdvInternet()
    {
        return $this->rdvInternet;
    }

    /**
     * Set remplacant
     *
     * @param boolean $remplacant
     *
     * @return Medecin
     */
    public function setRemplacant($remplacant)
    {
        $this->remplacant = $remplacant;

        return $this;
    }

    /**
     * Get remplacant
     *
     * @return boolean
     */
    public function isRemplacant()
    {
        return $this->remplacant;
    }

    /**
     * Set idAgenda
     *
     * @param integer $idAgenda
     *
     * @return Medecin
     */
    public function setIdAgenda($idAgenda)
    {
        $this->idAgenda = $idAgenda;

        return $this;
    }

    /**
     * Get idAgenda
     *
     * @return integer
     */
    public function getIdAgenda()
    {
        return $this->idAgenda;
    }

    /**
     * Set abonneSms
     *
     * @param boolean $abonneSms
     *
     * @return Medecin
     */
    public function setAbonneSms($abonneSms)
    {
        $this->abonneSms = $abonneSms;

        return $this;
    }

    /**
     * Get abonneSms
     *
     * @return boolean
     */
    public function isAbonneSms()
    {
        return $this->abonneSms;
    }

    /**
     * Get Nb Rendez-Vous
     *
     * @return boolean
     */
    public function getNbRdv()
    {
        return count($this->rendezvous);
    }

    /**
     * Set dureeRdv
     *
     * @param integer $dureeRdv
     *
     * @return Medecin
     */
    public function setDureeRdv($dureeRdv)
    {
        $this->dureeRdv = $dureeRdv;

        return $this;
    }

    /**
     * Get dureeRdv
     *
     * @return integer
     */
    public function getDureeRdv()
    {
        return $this->dureeRdv;
    }

    /**
     * Get Temps Avant Rappel
     *
     * @return integer
     */
    public function getTempsAvantRappel()
    {
        return $this->tempsAvantRappel;
    }

    /**
     * Set Temps Avant Rappel
     *
     * @param integer $tempsAvantRappel
     *
     * @return Medecin
     */
    public function setTempsAvantRappel($tempsAvantRappel)
    {
        $this->tempsAvantRappel = $tempsAvantRappel;

        return $this;
    }


    /**
     * Set rdvInternet
     *
     * @param boolean $rdvInternet
     *
     * @return Medecin
     */
    public function setRdvInternet($rdvInternet)
    {
        $this->rdvInternet = $rdvInternet;

        return $this;
    }

    /**
     * Set msgRappel
     *
     * @param string $msgRappel
     *
     * @return Medecin
     */
    public function setMsgRappel($msgRappel)
    {
        $this->msgRappel = $msgRappel;

        return $this;
    }

    /**
     * Get msgRappel
     *
     * @return string
     */
    public function getMsgRappel()
    {
        return $this->msgRappel;
    }

    /**
     * Add Horaires
     *
     * @param \Gestime\CoreBundle\Entity\Horaire $horaire
     *
     * @return Medecin
     */
    public function addHoraire(Horaire $horaire)
    {
        $this->horaires[] = $horaire;

        return $this;
    }

    /**
     * Remove Horaires
     *
     * @param Horaire $horaire
     */
    public function removeHoraire(Horaire $horaire)
    {
        $this->horaires->removeElement($horaire);
    }

    /**
     * Get Horaires
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHoraires()
    {
        return $this->horaires;
    }


  /**
   * Add Horaires
   *
   * @param \Gestime\CoreBundle\Entity\Horaire $horaire
   *
   * @return Medecin
   */
  public function addHorairesInternet(HoraireInternet $horaire)
  {
    $this->horairesInternet[] = $horaire;

    return $this;
  }

  /**
   * Remove Horaires
   *
   * @param Horaire $horaire
   */
  public function removeHorairesInternet(HoraireInternet $horaire)
  {
    $this->horairesInternet->removeElement($horaire);
  }

  /**
   * Get Horaires
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getHorairesInternet()
  {
    return $this->horairesInternet;
  }

    /**
     * Add Telephones
     *
     * @param \Gestime\CoreBundle\Entity\Telephone $telephones
     *
     * @return Medecin
     */
    public function addTelephone(Telephone $telephones)
    {
        foreach ($telephones as $telephone) {
            $telephone->SetMedecin($this);
        }

        $this->telephones[] = $telephones;

        return $this;
    }

    /**
     * Remove Telephones
     *
     * @param Telephone $telephones
     */
    public function removeTelephone(Telephone $telephones)
    {
        $this->telephones->removeElement($telephones);
    }

    /**
     * Get Telephones
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTelephones()
    {
        return $this->telephones;
    }

    /**
     * Get Telephone SMS
     *
     * @return Telephone
     */
    public function getTelephoneSMS()
    {
        foreach ($this->telephones->toArray() as $telephone) {
            if ($telephone->hasEnvoiSMS()) {
                return $telephone;
            }
        }
    }

    /**
     * Set abonne
     *
     * @param Abonne $abonne
     *
     * @return Medecin
     */
    public function setAbonne($abonne)
    {
        $this->abonne = $abonne;

        return $this;
    }

    /**
     * Get abonne
     *
     * @return \Gestime\CoreBundle\Entity\Abonne
     */
    public function getAbonne()
    {
        return $this->abonne;
    }

    /**
     * Add utilisateurs
     *
     * @param Utilisateur $utilisateurs
     *
     * @return Medecin
     */
    public function addUtilisateur(Utilisateur $utilisateurs)
    {
        $this->utilisateurs[] = $utilisateurs;

        return $this;
    }

    /**
     * Remove utilisateurs
     *
     * @param Utilisateur $utilisateurs
     */
    public function removeUtilisateur(Utilisateur $utilisateurs)
    {
        $this->utilisateurs->removeElement($utilisateurs);
    }

    /**
     * Get utilisateurs
     *
     * @return Collection
     */
    public function getUtilisateurs()
    {
        return $this->utilisateurs;
    }

    /**
     * Ajout d'une specialité
     *
     * @param Specialite $specialites
     * @return Specialite
     */
    public function addSpecialite(Specialite $specialites)
    {
        $this->specialites[] = $specialites;

        return $this;
    }

    /**
     * Remove specialites
     *
     * @param Specialite $specialites
     */
    public function removeSpecialite(Specialite $specialites)
    {
        $this->specialites->removeElement($specialites);
    }

    /**
     * Get specialites
     *
     * @return Collection
     */
    public function getSpecialites()
    {
        return $this->specialites;
    }


    /**
     * Get Site
     *
     * @return string
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function __toString()
    {
        return $this->nom;
    }

    /**
     * @return mixed
     */
    public function getCarence() {
      return $this->carence;
    }

    /**
     * @param mixed $carence
     */
    public function setCarence($carence) {
      $this->carence = $carence;
    }

    /**
     * @return mixed
     */
    public function getInfosDoc24() {
        return $this->infosDoc24;
    }

    /**
     * @param mixed $infosDoc24
     */
    public function setInfosDoc24($infosDoc24) {
        $this->infosDoc24 = $infosDoc24;
    }

    /**
     * @return mixed
     */
    public function getAbonnement() {
        return $this->abonnement;
    }

    /**
     * @param mixed $abonnement
     */
    public function setAbonnement($abonnement) {
        $this->abonnement = $abonnement;
    }

}
