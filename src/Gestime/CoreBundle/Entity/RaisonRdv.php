<?php

namespace Gestime\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;



/**
 * RaisonRdv
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Gestime\CoreBundle\Entity\RaisonRdvRepository")
 */
class RaisonRdv
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
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="Gestime\CoreBundle\Entity\Specialite", inversedBy="$raisonRdvs")
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
     * Set description
     *
     * @param string $description
     * @return RaisonRdv
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
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
