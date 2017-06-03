<?php

namespace Gestime\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Categorie
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Gestime\CoreBundle\Entity\CategorieRepository")
 */
class Categorie
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
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="style", type="string", length=255)
     */
    private $style;

    /**
     * @ORM\ManyToOne(targetEntity="Site")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id", nullable=false)
     */
    private $site;

    /**
     * @ORM\ManyToMany(targetEntity="Message", mappedBy="categories")
     * @ORM\JoinTable(name="message2categorie")
     */
    private $messages;

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
     * Set nom
     *
     * @param string $nom
     * @return Categorie
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Get style
     *
     * @return string
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * Set Style
     * @param string $style
     * @return Categorie
     */
    public function setStyle($style)
    {
        $this->style = $style;
    }

    /**
     * Set Site
     * @param Site $site
     * @return Categorie
     */
    public function setSite($site)
    {
        $this->site = $site;
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

    /**
     * Get name
     *
     * @return string
     */
    public function __toString()
    {
        return $this->nom;
    }
}
