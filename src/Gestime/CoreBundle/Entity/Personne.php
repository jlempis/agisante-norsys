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
 * Personne
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Gestime\CoreBundle\Entity\PersonneRepository")
 * @Assert\GroupSequence({"TempsReserve", "Personne", "Adresse"})
 */
class Personne
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
     * @ORM\Column(name="civilite_id", type="integer", length=1)
     */
    private $civilite;

    /**
     * @ORM\ManyToOne(targetEntity="Medecin")
     * @ORM\JoinColumn(name="medecin_id", referencedColumnName="id")
     */
    private $medecin;

    /**
     * @var string
     *
     * @ORM\Column(name="entreprise", nullable=true, type="string", length=255)
     */
    private $entreprise;

    /**
     * @ORM\OneToMany(targetEntity="Adresse", mappedBy="personne",  cascade={"persist", "remove"})
     * @Assert\Valid()
     */
    private $adresses;

    /**
     * @var string
     * @ORM\Column(name="adresse", type="string", nullable=true, length=255)
     */
    private $adresse;

    /**
     * @var string
     * @Assert\NotBlank(message="Le nom est obligatoire.")
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var string
     * @ORM\Column(name="prenom", nullable=true, type="string", length=255)
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="nomJF", nullable=true, type="string", length=255)
     */
    private $nomJF;

    /**
     * @var string
     *
     * @ORM\Column(name="email", nullable=true, type="string", length=255)
     */
    private $email;

    /**
     * @var string
     * @Assert\NotBlank(message="Le numéro de téléphone est obligatoire.", groups={"Personne", "Adresse"})
     * @ORM\Column(name="telephone", nullable=true, type="string", length=15)
     */
    private $telephone;

    /**
     * @var integer
     *
     * @ORM\Column(name="idPers", type="integer", nullable=true, length=1)
     */
    private $idPers;

    /**
     * @var string
     *
     * @ORM\Column(name="etat", type="string", length=1)
     */
    private $etat;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=1)
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity="Consigne", mappedBy="patient",cascade={"persist", "remove"})
     */
    private $consignes;

    /**
     * @ORM\ManyToMany(targetEntity="Medecin", inversedBy="patients")
     * @ORM\JoinTable(
     *     name="Patient",
     *     joinColumns={@ORM\JoinColumn(name="personne_id", referencedColumnName="id", nullable=true)},
     *     inverseJoinColumns={@ORM\JoinColumn(name="medecin_id", referencedColumnName="id", nullable=true)}
     * )
     */
    protected $medecins;

    private $entrepriseId;
    private $nomId;

    /**
     * @var string
     * @ORM\Column(name="adresse", nullable=true, type="string", length=255)
     */
    private adresse;

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
     * Set entreprise
     *
     * @param string $entreprise
     * @return Personne
     */
    public function setEntreprise($entreprise)
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    /**
     * [set_entreprise description]
     * @param string $entreprise
     * @return Personne
     */
    public function set_entreprise($entreprise)
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    /**
     * Get entreprise
     *
     * @return string
     */
    public function getEntreprise()
    {
        return $this->entreprise;
    }

    /**
     * [get_entreprise description]
     * @return string
     */
    public function get_entreprise()
    {
        return $this->entreprise;
    }

    /**
     * Get entrepriseId
     *
     * @return string
     */
    public function getEntrepriseId()
    {
        return $this->entrepriseId;
    }

    /**
     * [get_entrepriseId description]
     * @return string
     */
    public function get_entrepriseId()
    {
        return $this->entrepriseId;
    }

    /**
     * Add Adresses
     * Attention : Un patient n'a qu'une adresse à ce jour.
     * @param \Gestime\CoreBundle\Entity\Adresse $adresse
     * @return Personne
     */
    public function addAdress(Adresse $adresse)
    {
        $adresse->SetPersonne($this);
        $this->adresses[0] = $adresse;

        return $this;
    }

    /**
     * Remove Adresses
     * @param Adresse $adresses
     */
    public function removeAdress(Adresse $adresses)
    {
        $this->adresses->clear();
    }

    /**
     * Get Adresses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAdresses()
    {
        return $this->adresses;
    }

    /**
     * Set Nom
     *
     * @param string $nom
     * @return Personne
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * [setNomEntity description]
     * @param string $nom
     * @return Personne
     */
    public function setNomEntity($nom)
    {
        return $this;
    }

    /**
     * Get nomId
     *
     * @return string
     */
    public function getNomId()
    {
        return $this->nomId;
    }

    /**
     * Get _nomId
     *
     * @return string
     */
    public function get_nomId()
    {
        return $this->nomId;
    }

    /**
     * Get Nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Get NomEntity
     *
     * @return string
     */
    public function getNomEntity()
    {
        return $this->nom;
    }

    /**
     * Set idPers
     *
     * @param integer $idpers
     * @return Personne
     */
    public function setIdPers($idpers)
    {
        $this->idPers = $idpers;

        return $this;
    }

    /**
     * Get idPers
     *
     * @return integer
     */
    public function getIdPers()
    {
        return $this->idPers;
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
     * Get Nom Complet
     *
     * @return string
     */
    public function getNomComplet()
    {
        return $this->entreprise.' '.$this->nom.' '.$this->prenom;
    }

    /**
     * Set Prenom
     *
     * @param string $prenom
     * @return Personne
     */
    public function setPreNom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Set nomJF
     *
     * @param string $nomJF
     * @return Personne
     */
    public function setnomJF($nomJF)
    {
        $this->nomJF = $nomJF;

        return $this;
    }

    /**
     * Get nomJF
     *
     * @return string
     */
    public function getnomJF()
    {
        return $this->nomJF;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Personne
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     * @return Personne
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Personne
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
     * Set etat
     *
     * @param string $etat
     * @return Personne
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
     * Set civilite
     *
     * @param integer $civilite
     * @return Personne
     */
    public function setCivilite($civilite)
    {
        $this->civilite = $civilite;

        return $this;
    }

    /**
     * Get civilite
     *
     * @return string
     */
    public function getCivilite()
    {
        return $this->civilite;
    }
    /**
     * Ajout d'un medecin
     *
     * @param Medecin $medecins
     * @return Utilisateur
     */
    public function addMedecin(Medecin $medecins)
    {
        $this->medecins[] = $medecins;

        return $this;
    }

    /**
     * Remove medecins
     *
     * @param Medecin $medecins
     */
    public function removeMedecin(Medecin $medecins)
    {
        $this->medecins->removeElement($medecins);
    }

    /**
     * Get medecins
     *
     * @return Collection
     */
    public function getMedecins()
    {
        return $this->medecins;
    }

    /**
     * @return string
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * @param string $adresse
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;
    }

}
