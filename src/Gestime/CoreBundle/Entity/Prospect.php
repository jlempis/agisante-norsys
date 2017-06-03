<?php

namespace Gestime\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gestime\CoreBundle\Entity\Specialite;
use Gestime\CoreBundle\Entity\SousSpecialite;


/**
 * Prospect
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Gestime\CoreBundle\Entity\ProspectRepository")
 */
class Prospect
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idProspect;

    /**
     * @var string
     *
     * @ORM\Column(name="identifiant", type="string", length=10)
     */
    private $identifiant;

    /**
     * @var string
     *
     * @ORM\Column(name="RaisonSociale1", type="string", length=255, nullable=true)
     */
    private $raisonSociale1;

    /**
     * @var string
     *
     * @ORM\Column(name="RaisonSociale2", type="string", length=255, nullable=true)
     */
    private $raisonSociale2;

    /**
     * @var string
     *
     * @ORM\Column(name="Adresse1", type="string", length=255, nullable=true)
     */
    private $adresse1;

    /**
     * @var string
     *
     * @ORM\Column(name="Adresse2", type="string", length=255, nullable=true)
     */
    private $adresse2;

    /**
     * @var string
     *
     * @ORM\Column(name="Adresse3", type="string", length=255)
     */
    private $adresse3;

    /**
     * @var string
     *
     * @ORM\Column(name="Adresse4", type="string", length=255, nullable=true)
     */
    private $adresse4;

    /**
     * @var string
     *
     * @ORM\Column(name="codePostal", type="string", length=10)
     */
    private $codePostal;

    /**
     * @var string
     *
     * @ORM\Column(name="ville", type="string", length=255)
     */
    private $ville;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=15, nullable=true)
     */
    private $telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="fax", type="string", length=15, nullable=true)
     */
    private $fax;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=15, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="categorie", type="integer")
     */
    private $categorie;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=255)
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="latitude", type="float")
     */
    private $latitude;

    /**
     * @var string
     *
     * @ORM\Column(name="longitude", type="float")
     */
    private $longitude;

    /**
     * @var string
     *
     * @ORM\Column(name="affichage", type="boolean")
     */
    private $affichage;

    /**
     * @ORM\ManyToMany(targetEntity="Specialite")
     * @ORM\JoinTable(
     *     name="prospect2specialite",
     *     joinColumns={@ORM\JoinColumn(name="prospect_id", referencedColumnName="id", nullable=false)},
     *     inverseJoinColumns={@ORM\JoinColumn(name="specialite_id", referencedColumnName="id", nullable=false)}
     * )
     */
    private $specialites;

    /**
     * @return int
     */
    public function getIdProspect() {
        return $this->idProspect;
    }

    /**
     * Set identifiant
     *
     * @param string $identifiant
     *
     * @return Prospect
     */
    public function setIdentifiant($identifiant)
    {
        $this->identifiant = $identifiant;

        return $this;
    }

    /**
     * Get identifiant
     *
     * @return string
     */
    public function getIdentifiant()
    {
        return $this->identifiant;
    }

    /**
     * Set raisonSociale1
     *
     * @param string $raisonSociale1
     *
     * @return Prospect
     */
    public function setRaisonSociale1($raisonSociale1)
    {
        $this->raisonSociale1 = $raisonSociale1;

        return $this;
    }

    /**
     * Get raisonSociale1
     *
     * @return string
     */
    public function getRaisonSociale1()
    {
        return $this->raisonSociale1;
    }

    /**
     * Set raisonSociale2
     *
     * @param string $raisonSociale2
     *
     * @return Prospect
     */
    public function setRaisonSociale2($raisonSociale2)
    {
        $this->raisonSociale2 = $raisonSociale2;

        return $this;
    }

    /**
     * Get raisonSociale2
     *
     * @return string
     */
    public function getRaisonSociale2()
    {
        return $this->raisonSociale2;
    }

    /**
     * Set adresse1
     *
     * @param string $adresse1
     *
     * @return Prospect
     */
    public function setAdresse1($adresse1)
    {
        $this->adresse1 = $adresse1;

        return $this;
    }

    /**
     * Get adresse1
     *
     * @return string
     */
    public function getAdresse1()
    {
        return $this->adresse1;
    }

    /**
     * Set adresse2
     *
     * @param string $adresse2
     *
     * @return Prospect
     */
    public function setAdresse2($adresse2)
    {
        $this->adresse2 = $adresse2;

        return $this;
    }

    /**
     * Get adresse2
     *
     * @return string
     */
    public function getAdresse2()
    {
        return $this->adresse2;
    }

    /**
     * Set adresse3
     *
     * @param string $adresse3
     *
     * @return Prospect
     */
    public function setAdresse3($adresse3)
    {
        $this->adresse3 = $adresse3;

        return $this;
    }

    /**
     * Get adresse3
     *
     * @return string
     */
    public function getAdresse3()
    {
        return $this->adresse3;
    }

    /**
     * Set adresse4
     *
     * @param string $adresse4
     *
     * @return Prospect
     */
    public function setAdresse4($adresse4)
    {
        $this->adresse4 = $adresse4;

        return $this;
    }

    /**
     * Get adresse4
     *
     * @return string
     */
    public function getAdresse4()
    {
        return $this->adresse4;
    }

    /**
     * Set codePostal
     *
     * @param string $codePostal
     *
     * @return Prospect
     */
    public function setCodePostal($codePostal)
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    /**
     * Get codePostal
     *
     * @return string
     */
    public function getCodePostal()
    {
        return $this->codePostal;
    }

    /**
     * @return string
     */
    public function getVille() {
        return $this->ville;
    }

    /**
     * @param string $ville
     */
    public function setVille($ville) {
        $this->ville = $ville;
    }

    /**
     * @return string
     */
    public function getTelephone() {
        return $this->telephone;
    }

    /**
     * @param string $telephone
     */
    public function setTelephone($telephone) {
        $this->telephone = $telephone;
    }

    /**
     * @return string
     */
    public function getFax() {
        return $this->fax;
    }

    /**
     * @param string $fax
     */
    public function setFax($fax) {
        $this->fax = $fax;
    }

    /**
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email) {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getCategorie() {
        return $this->categorie;
    }

    /**
     * @param string $categorie
     */
    public function setCategorie($categorie) {
        $this->categorie = $categorie;
    }

    /**
     * @return string
     */
    public function getNom() {
        return $this->nom;
    }

    /**
     * @param string $nom
     */
    public function setNom($nom) {
        $this->nom = $nom;
    }

    /**
     * @return string
     */
    public function getPrenom() {
        return $this->prenom;
    }

    /**
     * @param string $prenom
     */
    public function setPrenom($prenom) {
        $this->prenom = $prenom;
    }

    /**
     * @return string
     */
    public function getLatitude() {
        return $this->latitude;
    }

    /**
     * @param string $latitude
     */
    public function setLatitude($latitude) {
        $this->latitude = $latitude;
    }

    /**
     * @return string
     */
    public function getLongitude() {
        return $this->longitude;
    }

    /**
     * @param string $longitude
     */
    public function setLongitude($longitude) {
        $this->longitude = $longitude;
    }

    /**
     * @return string
     */
    public function getAffichage() {
        return $this->affichage;
    }

    /**
     * @param string $affichage
     */
    public function setAffichage($affichage) {
        $this->affichage = $affichage;
    }

    /**
     * @return mixed
     */
    public function getSpecialites() {
        return $this->specialites;
    }

    /**
     * @param mixed $specialites
     */
    public function setSpecialites($specialites) {
        $this->specialites = $specialites;
    }


}

