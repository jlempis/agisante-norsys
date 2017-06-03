<?php

/**
 * @category Entities
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AppelRecu
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Gestime\CoreBundle\Entity\AppelRecuRepository")
 */
class AppelRecu
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
     * @var \Datetime
     *
     * @ORM\Column(name="callDay", type="datetime")
     */
    private $callDay;

    /**
     * @var string
     *
     * @ORM\Column(name="agent", type="string", length=255)
     */
    private $agent;

    /**
     * @var boolean
     *
     * @ORM\Column(name="entrant", type="boolean")
     */
    private $entrant;

    /**
     * @var int
     *
     * @ORM\Column(name="dureeSonerie", type="integer")
     */
    private $dureeSonerie;

    /**
     * @var integer
     *
     * @ORM\Column(name="dureeConversation", type="integer")
     */
    private $dureeConversation;

    /**
     * @var integer
     *
     * @ORM\Column(name="dureeTotale", type="integer")
     */
    private $dureeTotale;

    /**
     * @var string
     *
     * @ORM\Column(name="transfert", type="string", length=255)
     */
    private $transfert;

    /**
     * @ORM\ManyToOne(targetEntity="Telephone")
     * @ORM\JoinColumn(name="telephone_id", referencedColumnName="id")
     */
    private $telephoneTransfert;

    /**
     * @var string
     *
     * @ORM\Column(name="clid", type="string", length=35)
     */
    private $clid;

    /**
     * @var string
     *
     * @ORM\Column(name="sda", type="string", length=35)
     */
    private $sda;

    /**
     * @ORM\ManyToOne(targetEntity="Ligne")
     * @ORM\JoinColumn(name="ligne_id", referencedColumnName="id")
     */
    private $ligne;

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
     * Set callDay
     *
     * @param \DateTime $callDay
     * @return AppelRecu
     */
    public function setCallDay($callDay)
    {
        $this->callDay = $callDay;

        return $this;
    }

    /**
     * Get callDay
     *
     * @return \DateTime
     */
    public function getCallDay()
    {
        return $this->callDay;
    }

    /**
     * Set agent
     *
     * @param string $agent
     * @return AppelRecu
     */
    public function setAgent($agent)
    {
        $this->agent = $agent;

        return $this;
    }

    /**
     * Get agent
     *
     * @return string
     */
    public function getAgent()
    {
        return $this->agent;
    }

    /**
     * Set entrant
     *
     * @param boolean $entrant
     * @return AppelRecu
     */
    public function setEntrant($entrant)
    {
        $this->entrant = $entrant;

        return $this;
    }

    /**
     * Get entrant
     *
     * @return boolean
     */
    public function getEntrant()
    {
        return $this->entrant;
    }

    /**
     * Set dureeSonerie
     *
     * @param integer $dureeSonerie
     * @return AppelRecu
     */
    public function setDureeSonerie($dureeSonerie)
    {
        $this->dureeSonerie = $dureeSonerie;

        return $this;
    }

    /**
     * Get dureeSonerie
     *
     * @return integer
     */
    public function getDureeSonerie()
    {
        return $this->dureeSonerie;
    }

    /**
     * Set dureeConversation
     *
     * @param integer $dureeConversation
     * @return AppelRecu
     */
    public function setDureeConversation($dureeConversation)
    {
        $this->dureeConversation = $dureeConversation;

        return $this;
    }

    /**
     * Get dureeConversation
     *
     * @return integer
     */
    public function getDureeConversation()
    {
        return $this->dureeConversation;
    }

    /**
     * Set dureeTotale
     *
     * @param integer $dureeTotale
     * @return AppelRecu
     */
    public function setDureeTotale($dureeTotale)
    {
        $this->dureeTotale = $dureeTotale;

        return $this;
    }

    /**
     * Get dureeTotale
     *
     * @return integer
     */
    public function getDureeTotale()
    {
        return $this->dureeTotale;
    }

    /**
     * Set transfert
     *
     * @param string $transfert
     * @return AppelRecu
     */
    public function setTransfert($transfert)
    {
        $this->transfert = $transfert;

        return $this;
    }

    /**
     * Get transfert
     *
     * @return string
     */
    public function getTransfert()
    {
        return $this->transfert;
    }

    /**
     * Set clid
     *
     * @param string $clid
     * @return AppelRecu
     */
    public function setClid($clid)
    {
        $this->clid = $clid;

        return $this;
    }

    /**
     * Get clid
     *
     * @return string
     */
    public function getClid()
    {
        return $this->clid;
    }

    /**
     * Set sda
     *
     * @param string $sda
     * @return AppelRecu
     */
    public function setSda($sda)
    {
        $this->sda = $sda;

        return $this;
    }

    /**
     * Get sda
     *
     * @return string
     */
    public function getSda()
    {
        return $this->sda;
    }

    /**
     * Set telephoneTransfert
     *
     * @param \Gestime\CoreBundle\Entity\Telephone $telephoneTransfert
     * @return AppelRecu
     */
    public function setTelephoneTransfert(\Gestime\CoreBundle\Entity\Telephone $telephoneTransfert = null)
    {
        $this->telephoneTransfert = $telephoneTransfert;

        return $this;
    }

    /**
     * Get telephoneTransfert
     *
     * @return \Gestime\CoreBundle\Entity\Telephone
     */
    public function getTelephoneTransfert()
    {
        return $this->telephoneTransfert;
    }

    /**
     * Set ligne
     *
     * @param \Gestime\CoreBundle\Entity\Ligne $ligne
     * @return AppelRecu
     */
    public function setLigne(\Gestime\CoreBundle\Entity\Ligne $ligne = null)
    {
        $this->ligne = $ligne;

        return $this;
    }

    /**
     * Get ligne
     *
     * @return \Gestime\CoreBundle\Entity\Ligne
     */
    public function getLigne()
    {
        return $this->ligne;
    }
}
