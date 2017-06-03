<?php

namespace  Gestime\ApiBundle\Model;

class Compte {

  protected $idRdv;
  protected $idMedecin;
  protected $idSpecialite;
  protected $nomMedecin;
  protected $prenomMedecin;
  protected $specialite;
  protected $email;

  /**
   * @return mixed
   */
  public function getIdRdv() {
    return $this->idRdv;
  }

  /**
   * @param mixed $idRdv
   */
  public function setIdRdv($idRdv) {
    $this->idRdv = $idRdv;
  }

  /**
   * @return mixed
   */
  public function getIdMedecin() {
    return $this->idMedecin;
  }

  /**
   * @param mixed $idMedecin
   */
  public function setIdMedecin($idMedecin) {
    $this->idMedecin = $idMedecin;
  }

  /**
   * @return mixed
   */
  public function getIdSpecialite() {
    return $this->idSpecialite;
  }

  /**
   * @param mixed $idSpecialite
   */
  public function setIdSpecialite($idSpecialite) {
    $this->idSpecialite = $idSpecialite;
  }

  /**
   * @return mixed
   */
  public function getNomMedecin() {
    return $this->nomMedecin;
  }

  /**
   * @param mixed $nomMedecin
   */
  public function setNomMedecin($nomMedecin) {
    $this->nomMedecin = $nomMedecin;
  }

  /**
   * @return mixed
   */
  public function getPrenomMedecin() {
    return $this->prenomMedecin;
  }

  /**
   * @param mixed $prenomMedecin
   */
  public function setPrenomMedecin($prenomMedecin) {
    $this->prenomMedecin = $prenomMedecin;
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
  public function getEmail() {
    return $this->email;
  }

  /**
   * @param mixed $email
   */
  public function setEmail($email) {
    $this->email = $email;
  }

}
