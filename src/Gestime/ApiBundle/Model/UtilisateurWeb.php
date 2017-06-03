<?php

namespace  Gestime\ApiBundle\Model;

class UtilisateurWeb {

  protected $nom;

  protected $prenom;

  protected $email;

  protected $password;

  protected $naissance;

  protected $sexe;

  protected $dateInscription;

  protected $nbrdv;

  protected $notif;

  protected $etat;
  
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
  public function getEmail() {
    return $this->email;
  }

  /**
   * @return mixed
   */
  public function getPassword() {
    return $this->password;
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
   * @return mixed
   */
  public function getDateInscription() {
    return $this->dateInscription;
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
   * @param mixed $email
   */
  public function setEmail($email) {
    $this->email = $email;
  }

  /**
   * @param mixed $password
   */
  public function setPassword($password) {
    $this->password = $password;
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
   * @param mixed $dateInscription
   */
  public function setDateInscription($dateInscription) {
    $this->dateInscription = $dateInscription;
  }


  /**
   * @return mixed
   */
  public function getNbrdv() {
    return $this->nbrdv;
  }

  /**
   * @param mixed $nbrdv
   */
  public function setNbrdv($nbrdv) {
    $this->nbrdv = $nbrdv;
  }

  /**
   * @return mixed
   */
  public function getNotif() {
    return $this->notif;
  }

  /**
   * @param mixed $notif
   */
  public function setNotif($notif) {
    $this->notif = $notif;
  }

  /**
   * @return mixed
   */
  public function getEtat() {
    return $this->etat;
  }

  /**
   * @param mixed $etat
   */
  public function setEtat($etat) {
    $this->etat = $etat;
  }


}
