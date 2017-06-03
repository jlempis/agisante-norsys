<?php

namespace Gestime\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HoraireInternet
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Gestime\CoreBundle\Entity\HoraireInternetRepository")
 */
class HoraireInternet
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
   * @ORM\ManyToOne(targetEntity="Gestime\CoreBundle\Entity\Medecin", inversedBy="horairesInternet")
   * @ORM\JoinColumn(name="medecin_id", referencedColumnName="id", nullable=false)
   */
  private $medecin;

  /**
   * @ORM\ManyToOne(targetEntity="Parametre")
   * @ORM\JoinColumn(name="jour_id", referencedColumnName="id")
   */
  private $jour;

  /**
   * @var \Time
   *
   * @ORM\Column(type="string", length=5, nullable=false, name="debut")
   */
  private $debut;

  /**
   * @var \Time
   *
   * @ORM\Column(name="fin", type="string",nullable=false, length=5)
   */
  private $fin;

  /**
   * @var string
   *
   * @ORM\Column(name="nb_rdv_max", type="integer", nullable=true)
   */
  private $nbRdvMax;

  /**
   * @return int
   */
  public function getId() {
    return $this->id;
  }

  /**
   * @param int $id
   */
  public function setId($id) {
    $this->id = $id;
  }

  /**
   * @return mixed
   */
  public function getMedecin() {
    return $this->medecin;
  }

  /**
   * @param mixed $medecin
   */
  public function setMedecin($medecin) {
    $this->medecin = $medecin;
  }

  /**
   * @return mixed
   */
  public function getJour() {
    return $this->jour;
  }

  /**
   * @param mixed $jour
   */
  public function setJour($jour) {
    $this->jour = $jour;
  }

  /**
   * @return \Time
   */
  public function getDebut() {
    return $this->debut;
  }

  /**
   * @param \Time $debut
   */
  public function setDebut($debut) {
    $this->debut = $debut;
  }

  /**
   * @return \Time
   */
  public function getFin() {
    return $this->fin;
  }

  /**
   * @param \Time $fin
   */
  public function setFin($fin) {
    $this->fin = $fin;
  }

  /**
   * @return string
   */
  public function getNbRdvMax() {
    return $this->nbRdvMax;
  }

  /**
   * @param string $nbRdvMax
   */
  public function setNbRdvMax($nbRdvMax) {
    $this->nbRdvMax = $nbRdvMax;
  }

}
