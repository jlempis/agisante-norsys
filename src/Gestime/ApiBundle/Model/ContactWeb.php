<?php

namespace  Gestime\ApiBundle\Model;

class ContactWeb {

  protected $nom;
  protected $prenom;
  protected $email;
  protected $telephone;
  protected $message;

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
  public function getMessage() {
    return $this->message;
  }

  /**
   * @param mixed $message
   */
  public function setMessage($message) {
    $this->message = $message;
  }


}
