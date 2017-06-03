<?php

namespace  Gestime\ApiBundle\Model;

class RdvWeb {

  protected $email;
  protected $telephone;
  protected $medecinId;
  protected $specialiteId;
  protected $dateRdv;
  protected $codeActivation;
  protected $dejaVenu;
  protected $raison;
  protected $sexe;
  protected $naissance;

  /**
   * @return mixed
   */
  public function getEmail() {
    return $this->email;
  }

  /**
   * @param mixed $email
   */
  public function setEmail($email) {
    $this->email = $email;
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
  public function getRaison() {
    return $this->raison;
  }

  /**
   * @param mixed $raison
   */
  public function setRaison($raison) {
    $this->raison = $raison;
  }

  /**
   * @return mixed
   */
  public function getMedecinId() {
    return $this->medecinId;
  }

  /**
   * @param mixed $medecinId
   */
  public function setMedecinId($medecinId) {
    $this->medecinId = $medecinId;
  }

  /**
   * @return mixed
   */
  public function getDateRdv() {
    return $this->dateRdv;
  }

  /**
   * @param mixed $dateRdv
   */
  public function setDateRdv($dateRdv) {
    $this->dateRdv = $dateRdv;
  }

  /**
   * @return mixed
   */
  public function getCodeActivation() {
    return $this->codeActivation;
  }

  /**
   * @param mixed $codeActivation
   */
  public function setCodeActivation($codeActivation) {
    $this->codeActivation = $codeActivation;
  }

  /**
   * @return mixed
   */
  public function getDejaVenu() {
    return $this->dejaVenu;
  }

  /**
   * @param mixed $dejaVenu
   */
  public function setDejaVenu($dejaVenu) {
    $this->dejaVenu = $dejaVenu;
  }

  /**
   * @return mixed
   */
  public function getSpecialiteId() {
    return $this->specialiteId;
  }

  /**
   * @param mixed $specialiteId
   */
  public function setSpecialiteId($specialiteId) {
    $this->specialiteId = $specialiteId;
  }

  /**
   * @return mixed
   */
  public function getSexe() {
    return $this->sexe;
  }

  /**
   * @param mixed $sexe
   */
  public function setSexe($sexe) {
    $this->sexe = $sexe;
  }

  /**
   * @return mixed
   */
  public function getNaissance() {
    return $this->naissance;
  }

  /**
   * @param mixed $naissance
   */
  public function setNaissance($naissance) {
    $this->naissance = $naissance;
  }

}
