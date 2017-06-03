<?php

namespace Gestime\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gestime\CoreBundle\Entity\InfosDoc24;

/**
 * Tarification
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Gestime\CoreBundle\Entity\TarificationRepository")
 */
class Tarification
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
     * @ORM\Column(name="Acte", type="string", length=255)
     */
    private $acte;

    /**
     * @var float
     *
     * @ORM\Column(name="tarif_mini", type="float")
     */
    private $tarifMini;

    /**
     * @var float
     *
     * @ORM\Column(name="tarif_maxi", type="float")
     */
    private $tarifMaxi;

    /**
     * @ORM\ManyToOne(targetEntity="Gestime\CoreBundle\Entity\InfosDoc24", inversedBy="tarifications")
     * @ORM\JoinColumn(name="info_id", referencedColumnName="id", nullable=false)
     */
    private $info;

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
     * Set acte
     *
     * @param string $acte
     *
     * @return Tarification
     */
    public function setActe($acte)
    {
        $this->acte = $acte;

        return $this;
    }

    /**
     * Get acte
     *
     * @return string
     */
    public function getActe()
    {
        return $this->acte;
    }

    /**
     * Set tarifMini
     *
     * @param float $tarifMini
     *
     * @return Tarification
     */
    public function setTarifMini($tarifMini)
    {
        $this->tarifMini = $tarifMini;

        return $this;
    }

    /**
     * Get tarifMini
     *
     * @return float
     */
    public function getTarifMini()
    {
        return $this->tarifMini;
    }

    /**
     * Set tarifMaxi
     *
     * @param float $tarifMaxi
     *
     * @return Tarification
     */
    public function setTarifMaxi($tarifMaxi)
    {
        $this->tarifMaxi = $tarifMaxi;

        return $this;
    }

    /**
     * Get tarifMaxi
     *
     * @return float
     */
    public function getTarifMaxi()
    {
        return $this->tarifMaxi;
    }

    /**
     * Set InfoDoc24
     *
     * @param \Gestime\CoreBundle\Entity\InfosDoc24 $info
     * @return Tarification
     */
    public function setInfo(InfosDoc24 $info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Get InfoDoc24
     *
     * @return InfosDoc24
     */
    public function getInfo()
    {
        return $this->info;
    }
}

