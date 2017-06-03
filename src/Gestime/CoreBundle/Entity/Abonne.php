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

/**
 * Abonné
 * Un abonné est un cabinet médical
 * Un abonné est composé de un ou plusieurs médecins
 * Un abonné est un client
 * A un cabinet es associé un répondeur et une ligne (SDA)
 *
 * @ORM\Entity(repositoryClass="Gestime\CoreBundle\Entity\AbonneRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Abonne
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idAbonne;

    /**
     * @ORM\ManyToOne(targetEntity="Site")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id", nullable=false)
     */
    private $site;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, name="raisonSociale")
     */
    private $raisonSociale;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="La voie est obligatoire.", groups={"Adresse"})
     * @ORM\Column(name="voie", type="string", length=255)
     */
    private $voie;

    /**
     * @var string
     *
     * @ORM\Column(name="codePostal", type="string", length=10)
     * @Assert\NotBlank(message="Le code postal est obligatoire.", groups={"Adresse"})
     */
    private $codePostal;

    /**
     * @ORM\Column(name="ville", type="string", length=32)
     * @Assert\NotBlank(message="La ville est obligatoire.", groups={"Adresse"})
     */
    private $ville;

    /**
     * @ORM\Column(type="decimal", scale=6, precision=10, name="longitude")
     */
    private $longitude;

    /**
     * @ORM\Column(type="decimal", scale=6, precision=10, name="latitude")
     */
    private $latitude;

    /**
     * @ORM\Column(type="date", nullable=true, name="debutValidite")
     */
    private $debutValidite;

    /**
     * @ORM\Column(type="date", nullable=true, name="finValidite")
     */
    private $finValidite;

    /**
     * @ORM\OneToMany(targetEntity="Gestime\CoreBundle\Entity\Affectation", mappedBy="abonne",cascade={"persist", "remove"})
     */
    private $affectations;

    /**
     * @ORM\OneToMany(targetEntity="Gestime\CoreBundle\Entity\Medecin", mappedBy="abonne")
     */
    private $medecins;

    /**
     * @ORM\OneToMany(targetEntity="AbonneRepondeur", mappedBy="abonne",  cascade={ "persist", "remove"})
     * @Assert\Valid()
     */
    private $periodes;

    protected $lignes;

    /**
     * [__construct description]
     * @param [type] $site [site de l(utilisateur connecté)]
     */
    public function __construct($site)
    {
        $this->site = $site;

        $this->medecins = new ArrayCollection();
        $this->periodes = new ArrayCollection();

        $this->affectations = new ArrayCollection();
        $this->lignes = new ArrayCollection();

        foreach ($this->periodes as $periode) {
            $periode->SetAbonne($this);
        }

        if ($this->debutValidite === null) {
            $this->debutValidite =  new \DateTime('now');
        }
        if ($this->finValidite === null) {
            $this->finValidite =  new \DateTime('12/31/2019');
        }
    }

    /**
     * [getIdAbonne description]
     * @return [int] [Id de l'abonné]
     */
    public function getIdAbonne()
    {
        return $this->idAbonne;
    }

    /**
     * [getLigne description]
     * @return ArrayCollection [collection] [Liste des lignes de l'abonné]
     */
    public function getLigne()
    {
        $lignes = new ArrayCollection();

        foreach ($this->affectations as $affectation) {
            $lignes = $affectation->getLigne();
        }

        return $lignes;
    }

    /**
     * [setLigne description]
     * @param [type] $ligne [Numero SDA de l'abonné]
     */
    public function setLigne($ligne)
    {
        //si une ligne existe , on l'invalide en  y mettant une date de fin
        if (count($this->affectations)) {
            $this->affectations[count($this->affectations)-1]->setFin(new \DateTime('now'));
        }
        $affectation = new Affectation();
        $affectation->setAbonne($this);
        $affectation->setLigne($ligne);
        $affectation->setDebut(new \DateTime('now'));
        $this->addAffectation($affectation);
    }

    /**
     * [getAbonne description]
     * @return $this [type] [description]
     */
    public function getAbonne()
    {
        return $this;
    }

    /**
     * Get Affectation
     * Renvoie l'affectation (Il ne peut y en avoir qu'une)
     * Affectation est la table NN entre Abonne et Lignes
     * Une ligne est affectée si la date du jour est entre debut et fin
     *
     * @return Affectation
     */
    public function getAffectation()
    {
        return $this->affectations[count($this->affectations)-1];
    }

    /**
     * Get Affectation précédente
     * Renvoie l'affectation (Il ne peut y en avoir qu'une)
     * Affectation est la table NN entre Abonne et Lignes
     * Une ligne est affectée si la date du jour est entre debut et fin
     *
     * @return Affectation
     *
     */
    public function getAffectationPrecedente()
    {
        return $this->affectations[count($this->affectations)-2];
    }

    /**
     * Add Affectation
     * @param Affectation $affectation
     */
    public function addAffectation($affectation)
    {
        $this->affectations[] = $affectation;
    }

    /**
     * Remove Affectation
     * @param Affectation $affectation
     * @return Affectation
     */
    public function removeAffectation($affectation)
    {
        return $this->affectations->removeElement($affectation);
    }

    /**
     * Get adresse
     *
     * @return string
     */
    public function getAdresse()
    {
        return $this->voie.' '.$this->codePostal.' '.$this->ville;
    }

    /**
     * Get cpville
     *
     * @return string
     */
    public function getCpville()
    {
      return $this->codePostal.' '.$this->ville;
    }

    /**
     * Set raisonSociale
     *
     * @param string $raisonSociale
     *
     * @return Abonne
     */
    public function setRaisonSociale($raisonSociale)
    {
        $this->raisonSociale = $raisonSociale;

        return $this;
    }

    /**
     * Get raisonSociale
     *
     * @return string
     */
    public function getRaisonSociale()
    {
        return $this->raisonSociale;
    }


    /**
     * @param $debutvalidite
     * @return $this
     */
    public function setDebutValidite($debutvalidite)
    {
        $this->debutValidite = $debutvalidite;

        return $this;
    }

    /**
     * Get debutValidite
     *
     * @return string
     */
    public function getDebutValidite()
    {
        return $this->debutValidite;
    }

    /**
     * Set finValidite
     *
     * @param Date $finvalidite
     *
     * @return Abonne
     */
    public function setFinValidite($finvalidite)
    {
        $this->finValidite = $finvalidite;

        return $this;
    }

    /**
     * Get debutValidite
     *
     * @return string
     */
    public function getFinValidite()
    {
        return $this->finValidite;
    }

    /**
     * Add medecins
     *
     * @param Medecin $medecins
     *
     * @return Abonne
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
     * @internal param $ \Gestime/CoreBundle/Entity\Medecin $medecins
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
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->raisonSociale;
    }

    /**
     * Add periodes
     *
     * @param AbonneRepondeur $periodes
     *
     * @return Abonne
     */
    public function addPeriode(AbonneRepondeur $periodes)
    {
        foreach ($periodes as $periode) {
            $periode->SetAbonne($this);
        }

        $this->periodes[] = $periodes;

        return $this;
    }

    /**
     * Remove periodes
     *
     * @param AbonneRepondeur $periodes
     */
    public function removePeriode(AbonneRepondeur $periodes)
    {
        $this->periodes->removeElement($periodes);
    }

    /**
     * Get periodes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPeriodes()
    {
        return $this->periodes;
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
     * @return float
     */
    public function getLatitude() {
      return $this->latitude;
    }

    /**
     * @return string
     */
    public function getVoie() {
      return $this->voie;
    }

    /**
     * @param string $voie
     */
    public function setVoie($voie) {
      $this->voie = $voie;
    }

    /**
     * @return string
     */
    public function getCodePostal() {
      return $this->codePostal;
    }

    /**
     * @param string $codePostal
     */
    public function setCodePostal($codePostal) {
      $this->codePostal = $codePostal;
    }

    /**
     * @return mixed
     */
    public function getVille() {
      return $this->ville;
    }

    /**
     * @param mixed $ville
     */
    public function setVille($ville) {
      $this->ville = $ville;
    }

    /**
     * @param float $latitude
     */
    public function setLatitude($latitude) {
      $this->latitude = $latitude;
    }

    /**
     * @return float
     */
    public function getLongitude() {
      return $this->longitude;
    }

    /**
     * @param float $longitude
     */
    public function setLongitude($longitude) {
      $this->longitude = $longitude;
    }

}
