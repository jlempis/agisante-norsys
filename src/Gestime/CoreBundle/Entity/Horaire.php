<?php

/**
 * @category Entities
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gestime\CoreBundle\Validator as SmfAssert;

/**
 * Horaire
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Gestime\CoreBundle\Entity\HoraireRepository")
 * @Assert\GroupSequence({"Format", "Horaire"})
 */
class Horaire
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
     * @ORM\ManyToOne(targetEntity="Gestime\CoreBundle\Entity\Medecin", inversedBy="horaires")
     * @ORM\JoinColumn(name="medecin_id", referencedColumnName="id", nullable=false)
     */
    private $medecin;

    /**
     * @ORM\ManyToOne(targetEntity="Parametre")
     * @ORM\JoinColumn(name="jour_id", referencedColumnName="id")
     */
    private $jour;

    /**
     * @ORM\ManyToOne(targetEntity="Parametre")
     * @ORM\JoinColumn(name="activite_id", referencedColumnName="id")
     */
    private $activite;

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
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $internet;

    /**
     * @var string
     *
     * @ORM\Column(name="texte", type="string", length=255, nullable=true)
     */
    private $texte;

    /**
     * @ORM\ManyToOne(targetEntity="Parametre")
     * @ORM\JoinColumn(name="classe_id", referencedColumnName="id")
     */
    private $classe;

    /**
     * @var string
     *
     * @ORM\Column(name="Commentaire", type="string", length=255, nullable=true)
     */
    private $commentaire;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="Activation", type="datetime")
     */
    private $activation;

    /**
     * Constructor
     */
    public function __construct()
    {
        if ($this->activation === null) {
            $this->activation =  new \DateTime('now');
        }
    }
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
     * Set Medecin
     *
     * @param \Gestime\CoreBundle\Entity\Medecin $medecin
     * @return Horaire
     */
    public function setMedecin(Medecin $medecin)
    {
        $this->medecin = $medecin;

        return $this;
    }

    /**
     * Get Medecin
     *
     * @return Medecin
     */
    public function getMedecin()
    {
        return $this->medecin;
    }

    /**
     * Set jour
     *
     * @param string $jour
     * @return Horaire
     */
    public function setJour($jour)
    {
        $this->jour = $jour;

        return $this;
    }

    /**
     * Get jour
     *
     * @return string
     */
    public function getJour()
    {
        return $this->jour;
    }

    /**
     * Set activite
     *
     * @param string $activite
     * @return Horaire
     */
    public function setActivite($activite)
    {
        $this->activite = $activite;

        return $this;
    }

    /**
     * Get activite
     *
     * @return string
     */
    public function getActivite()
    {
        return $this->activite;
    }

    /**
     * Set classe
     *
     * @param string $classe
     * @return Horaire
     */
    public function setClasse($classe)
    {
        $this->classe = $classe;

        return $this;
    }

    /**
     * Get classe
     *
     * @return string
     */
    public function getClasse()
    {
        return $this->classe;
    }

    /**
     * Set debut
     *
     * @param string $debut
     * @return Horaire
     */
    public function setDebut($debut)
    {
        $this->debut = $debut;

        return $this;
    }

    /**
     * @SmfAssert\HourFormat(groups={"Format"})
     * @return Horaire
     */
    public function getDebut()
    {
        return $this->debut;
    }

    /**
     * Set fin
     *
     * @param string $fin
     * @return Horaire
     */
    public function setFin($fin)
    {
        $this->fin = $fin;

        return $this;
    }

    /**
     * @SmfAssert\HourFormat(groups={"Format"})
     * @return \Time
     */
    public function getFin()
    {
        return $this->fin;
    }

    /**
     * Set texte
     *
     * @param string $texte
     * @return Horaire
     */
    public function setTexte($texte)
    {
        $this->texte = $texte;

        return $this;
    }

    /**
     * Get texte
     *
     * @return string
     */
    public function getTexte()
    {
        return $this->texte;
    }

    /**
     * Set commentaire
     *
     * @param string $commentaire
     * @return Horaire
     */
    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    /**
     * Get commentaire
     *
     * @return string
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }

    /**
     * Set activation
     *
     * @param \DateTime $activation
     * @return Horaire
     */
    public function setActivation($activation)
    {
        $this->activation = $activation;

        return $this;
    }

    /**
     * Get activation
     *
     * @return \DateTime
     */
    public function getActivation()
    {
        return $this->activation;
    }

    /**
     * @return mixed
     */
    public function getInternet() {
      return $this->internet;
    }

    /**
     * @param mixed $internet
     */
    public function setInternet($internet) {
      $this->internet = $internet;
    }

}
