<?php

namespace  Gestime\ApiBundle\Model;

class InfoUserWeb {

  protected $id;

  protected $nom;

  protected $prenom;

  protected $naissance;

  protected $sexe;

  protected $email;

  /**
   * @return mixed
   */
  public function getId() {
    return $this->id;
  }

  /**
   * @param mixed $id
   */
  public function setId($id) {
    $this->id = $id;
  }

  /**
   * @return mixed
   */
  public function getNom() {
    return $this->nom;
  }

  /**
   * @return mixed
   */
  public function getPrenom() {
    return $this->prenom;
  }

  /**
   * @return mixed
   */
  public function getNaissance() {
    return $this->naissance;
  }

  /**
   * @return mixed
   */
  public function getSexe() {
    return $this->sexe;
  }

  /**
   * @param mixed $nom
   */
  public function setNom($nom) {
    $this->nom = $nom;
  }

  /**
   * @param mixed $prenom
   */
  public function setPrenom($prenom) {
    $this->prenom = $prenom;
  }

  /**
   * @param mixed $naissance
   */
  public function setNaissance($naissance) {
    $this->naissance = $naissance;
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
