<?php

namespace Gestime\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SousSpecialite
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Gestime\CoreBundle\Entity\SousSpecialiteRepository")
 */
class SousSpecialite
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
     * @var integer
     *
     * @ORM\Column(name="categorie", type="integer")
     */
    private $categorie;

    /**
     * @var string
     *
     * @ORM\Column(name="nomCategorie", type="string", length=255)
     */
    private $nomCategorie;

    /**
     * @ORM\ManyToOne(targetEntity="Gestime\CoreBundle\Entity\Specialite", inversedBy="sousSpecialites")
     * @ORM\JoinColumn(name="specialite_id", referencedColumnName="id", nullable=false)
     */
    private $specialite;

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
     * Set categorie
     *
     * @param integer $categorie
     *
     * @return SousSpecialite
     */
    public function setCategorie($categorie)
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Get categorie
     *
     * @return integer
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * Set nomCategorie
     *
     * @param string $nomCategorie
     *
     * @return SousSpecialite
     */
    public function setNomCategorie($nomCategorie)
    {
        $this->nomCategorie = $nomCategorie;

        return $this;
    }

    /**
     * Get nomCategorie
     *
     * @return string
     */
    public function getNomCategorie()
    {
        return $this->nomCategorie;
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

}

