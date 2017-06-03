<?php

/**
 * @category Entities
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Affectation
 *
 * @ORM\Entity(repositoryClass="Gestime\CoreBundle\Entity\AffectationRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Affectation
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var integer $id
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Abonne", inversedBy="affectations")
     * @ORM\JoinColumn(name="abonne_id", referencedColumnName="id")
     */
    protected $abonne;

    /**
     * @ORM\ManyToOne(targetEntity="Ligne", inversedBy="affectations")
     * @ORM\JoinColumn(name="ligne_id", referencedColumnName="id")
     */
    protected $ligne;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false, name="debut")
     */
    private $debut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true, name="fin")
     */
    private $fin;

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
     * Set debut
     *
     * @param \DateTime $debut
     * @return Affectation
     */
    public function setDebut($debut)
    {
        $this->debut = $debut;

        return $this;
    }

    /**
     * Get debut
     *
     * @return \DateTime
     */
    public function getDebut()
    {
        return $this->debut;
    }

    /**
     * Set fin
     *
     * @param \DateTime $fin
     * @return Affectation
     */
    public function setFin($fin)
    {
        $this->fin = $fin;

        return $this;
    }

    /**
     * Get fin
     *
     * @return \DateTime
     */
    public function getFin()
    {
        return $this->fin;
    }

    /**
     * Set Abonne
     *
     * @param Abonne $abonne
     * @return Abonné
     */
    public function setAbonne(Abonne $abonne)
    {
        $this->abonne = $abonne;
    }

    /**
     * Get Abonne
     *
     * @return Abonne
     */
    public function getAbonne()
    {
        return $this->abonne;
    }

    /**
     * Set Ligne
     * @param Ligne $ligne
     * @return \DateTime
     */
    public function setLigne($ligne)
    {
        $this->ligne = $ligne;
    }

    /**
     * Get Ligne
     *
     * @return Abonne
     */
    public function getLigne()
    {
        return $this->ligne;
    }
}
