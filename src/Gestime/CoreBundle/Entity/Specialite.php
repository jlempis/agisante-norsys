<?php

namespace Gestime\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Gestime\CoreBundle\Entity\RaisonRdv;

/**
 * Specialite
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Gestime\CoreBundle\Entity\SpecialiteRepository")
 */
class Specialite
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
     * @ORM\Column(name="ordre_affichage", type="integer")
     */
    private $ordreAffichage;

    /**
     * @ORM\OneToMany(targetEntity="RaisonRdv", mappedBy="specialite",  cascade={"persist", "remove"})
     * @Assert\Valid()
     */
    private $raisonRdvs;

    /**
     * @ORM\ManyToMany(targetEntity="Medecin", mappedBy="specialites")
     * @ORM\JoinTable(name="medecin2specialite")
     */
    private $medecins;


    /**
     * [__construct description]
     * @param Site $site
     */
    public function __construct($site)
    {
      $this->site = $site;
      $this->sousSpecialites = new ArrayCollection();
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
     * Set nom
     *
     * @param string $nom
     * @return Specialite
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
     * Add raisonRdvs
     *
     * @param \Gestime\CoreBundle\Entity\RaisonRdv $raisonRdvs
     *
     * @return Medecin
     */
    public function addRaisonRdv(RaisonRdv $raisonRdvs)
    {
      foreach ($raisonRdvs as $raisonRdv) {
        $raisonRdv->SetMedecin($this);
      }

      $this->raisonRdvs[] = $raisonRdvs;

      return $this;
    }

    /**
     * Remove raisonRdvs
     *
     * @param RaisonRdv $raisonRdvs
     */
    public function removeRaisonRdv(RaisonRdv $raisonRdvs)
    {
      $this->raisonRdvs->removeElement($raisonRdvs);
    }

    /**
     * Get SousSpecialites
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSousSpecialites()
    {
      return $this->sousSpecialites;
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
