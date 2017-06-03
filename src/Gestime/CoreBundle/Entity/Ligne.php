<?php

/**
 * @category Entities
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMSAnnotation;

/**
 * Ligne
 * Une ligne est un numero attribué par  France Telecom
 * Un ligne est composée d'un numéro direct (SDA)
 * Une ligne est affectée à un abonné
 */

/**
 * @ORM\Entity(repositoryClass="Gestime\CoreBundle\Entity\LigneRepository")
 *
 * @JMSAnnotation\ExclusionPolicy("all")
 */
class Ligne
{
    /**
     * @var integer
     *
     * @ORM\Column(type="integer", name="id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMSAnnotation\Expose
     */
    private $idLigne;

    /**
     * @ORM\ManyToOne(targetEntity="Site")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id", nullable=false)
     */
    private $site;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=15, nullable=true, name="numero")
     * @JMSAnnotation\Expose
     */
    private $numero;

    /**
     * @ORM\OneToMany(targetEntity="Affectation" , mappedBy="ligne" )
     * */
    protected $affectations;

    /**
     * Get id
     *
     * @return integer
     */
    public function getIdLigne()
    {
        return $this->idLigne;
    }

    /**
     * Set numero
     *
     * @param string $numero
     * @return Ligne
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * [__construct description]
     * @param Site $site [description]
     */
    public function __construct($site)
    {
        $this->site = $site;
        $this->affectations = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add affectations
     *
     * @param \Gestime\CoreBundle\Entity\Affectation $affectations
     * @return Ligne
     */
    public function addAffectation(\Gestime\CoreBundle\Entity\Affectation $affectations)
    {
        $this->affectations[] = $affectations;

        return $this;
    }

    /**
     * Remove affectations
     *
     * @param \Gestime\CoreBundle\Entity\Affectation $affectations
     */
    public function removeAffectation(\Gestime\CoreBundle\Entity\Affectation $affectations)
    {
        $this->affectations->removeElement($affectations);
    }

    /**
     * Get affectations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAffectations()
    {
        return $this->affectations;
    }

    /**
     * Get Site
     *
     * @return string
     */
    public function getSite()
    {
        return $this->site;
    }
}
