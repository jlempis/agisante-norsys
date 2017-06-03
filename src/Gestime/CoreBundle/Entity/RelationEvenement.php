<?php

namespace Gestime\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * RelationEvenement
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Gestime\CoreBundle\Entity\RelationEvenementRepository")
 */
class RelationEvenement
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
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=2)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="Evenement", inversedBy="precedent",cascade={"persist", "remove"}))
     * @ORM\JoinColumn(name="event_prec_id", referencedColumnName="id")
     */
    private $evenementPrecedent;

    /**
     * @ORM\ManyToOne(targetEntity="Evenement", inversedBy="suivant",cascade={"persist", "remove"}))
     * @ORM\JoinColumn(name="event_suiv_id", referencedColumnName="id")
     */
    private $evenementSuivant;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

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
     * Set type
     *
     * @param string $type
     * @return RelationEvenement
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set evenementPrecedent
     *
     * @param string $evenementPrecedent
     * @return RelationEvenement
     */
    public function setEvenementPrecedent(Evenement $evenementPrecedent)
    {
        $this->evenementPrecedent = $evenementPrecedent;

        return $this;
    }

    /**
     * Get evenementPrecedent
     *
     * @return string
     */
    public function getEvenementPrecedent()
    {
        return $this->evenementPrecedent;
    }

    /**
     * Set evenementSuivant
     *
     * @param string $evenementSuivant
     * @return RelationEvenement
     */
    public function setEvenementSuivant(Evenement $evenementSuivant)
    {
        $this->evenementSuivant = $evenementSuivant;

        return $this;
    }

    /**
     * Get evenementSuivant
     *
     * @return string
     */
    public function getEvenementSuivant()
    {
        return $this->evenementSuivant;
    }
}
