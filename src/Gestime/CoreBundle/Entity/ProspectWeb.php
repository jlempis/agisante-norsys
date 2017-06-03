<?php

namespace Gestime\CoreBundle\Entity;

class ProspectWeb {

  protected $îd;
  protected $identifiant;
  protected $nom;
  protected $prenom;
  protected $adresse1;
  protected $adresse2;
  protected $adresse3;
  protected $adresse4;
  protected $codePostal;
  protected $ville;
  protected $telephone;
  protected $categorie;
  protected $latitude;
  protected $longitude;

  /**
   * @return mixed
   */
  public function getÎd() {
    return $this->îd;
  }

  /**
   * @param mixed $îd
   */
  public function setÎd($îd) {
    $this->îd = $îd;
  }

  /**
   * @return mixed
   */
  public function getIdentifiant() {
    return $this->identifiant;
  }

  /**
   * @param mixed $identifiant
   */
  public function setIdentifiant($identifiant) {
    $this->identifiant = $identifiant;
  }

  /**
   * @return mixed
   */
  public function getNom() {
    return $this->nom;
  }

  /**
   * @param mixed $nom
   */
  public function setNom($nom) {
    $this->nom = $nom;
  }

  /**
   * @return mixed
   */
  public function getPrenom() {
    return $this->prenom;
  }

  /**
   * @param mixed $prenom
   */
  public function setPrenom($prenom) {
    $this->prenom = $prenom;
  }

  /**
   * @return mixed
   */
  public function getAdresse1() {
    return $this->adresse1;
  }

  /**
   * @param mixed $adresse1
   */
  public function setAdresse1($adresse1) {
    $this->adresse1 = $adresse1;
  }

  /**
   * @return mixed
   */
  public function getAdresse2() {
    return $this->adresse2;
  }

  /**
   * @param mixed $adresse2
   */
  public function setAdresse2($adresse2) {
    $this->adresse2 = $adresse2;
  }

  /**
   * @return mixed
   */
  public function getAdresse3() {
    return $this->adresse3;
  }

  /**
   * @param mixed $adresse3
   */
  public function setAdresse3($adresse3) {
    $this->adresse3 = $adresse3;
  }

  /**
   * @return mixed
   */
  public function getAdresse4() {
    return $this->adresse4;
  }

  /**
   * @param mixed $adresse4
   */
  public function setAdresse4($adresse4) {
    $this->adresse4 = $adresse4;
  }

  /**
   * @return mixed
   */
  public function getCodePostal() {
    return $this->codePostal;
  }

  /**
   * @param mixed $codePostal
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
   * @return mixed
   */
  public function getTelephone() {
    return $this->telephone;
  }

  /**
   * @param mixed $telephone
   */
  public function setTelephone($telephone) {
    $this->telephone = $telephone;
  }

  /**
   * @return mixed
   */
  public function getCategorie() {
    return $this->categorie;
  }

  /**
   * @param mixed $categorie
   */
  public function setCategorie($categorie) {
    $this->categorie = $categorie;
  }

  /**
   * @return mixed
   */
  public function getLatitude() {
    return $this->latitude;
  }

  /**
   * @param mixed $latitude
   */
  public function setLatitude($latitude) {
    $this->latitude = $latitude;
  }

  /**
   * @return mixed
   */
  public function getLongitude() {
    return $this->longitude;
  }

  /**
   * @param mixed $longitude
   */
  public function setLongitude($longitude) {
    $this->longitude = $longitude;
  }




}

