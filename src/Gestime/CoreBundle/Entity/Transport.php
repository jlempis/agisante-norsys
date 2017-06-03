<?php

namespace Gestime\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Transport
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Transport
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
     * @ORM\ManyToOne(targetEntity="Parametre")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="numero_ligne", type="string", length=255)
     */
    private $numeroLigne;

    /**
     * @var string
     *
     * @ORM\Column(name="arret", type="string", length=255)
     */
    private $arret;

    /**
     * @ORM\ManyToOne(targetEntity="Gestime\CoreBundle\Entity\InfosDoc24", inversedBy="transports")
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
     * Set numeroLigne
     *
     * @param integer $numeroLigne
     *
     * @return Transport
     */
    public function setNumeroLigne($numeroLigne)
    {
        $this->numeroLigne = $numeroLigne;

        return $this;
    }

    /**
     * Get numeroLigne
     *
     * @return integer
     */
    public function getNumeroLigne()
    {
        return $this->numeroLigne;
    }

    /**
     * Set arret
     *
     * @param string $arret
     *
     * @return Transport
     */
    public function setArret($arret)
    {
        $this->arret = $arret;

        return $this;
    }

    /**
     * Get arret
     *
     * @return string
     */
    public function getArret()
    {
        return $this->arret;
    }

    /**
     * Set type
     *
     * @param Parametre $parametre
     * @return AbonneRepondeur
     */
    public function setType($parametre)
    {
        $this->type = $parametre;

        return $this;
    }

    /**
     * Get type
     *
     * @return Parametre
     */
    public function getType()
    {
        return $this->type;
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

