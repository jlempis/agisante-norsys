<?php

/**
 * @category Entities
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Téléphone
 * Numéros de médecins
 * Un numéro appartien à un médecin
 */

/**
 * @ORM\Entity
 */

class Telephone
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $numero;

    /**
     * @ORM\ManyToOne(targetEntity="Gestime\CoreBundle\Entity\Medecin", inversedBy="telephones")
     * @ORM\JoinColumn(name="medecin_id", referencedColumnName="id", nullable=false)
     */
    private $medecin;

    /**
     * @ORM\ManyToOne(targetEntity="Parametre")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     */
    private $type;

    /**
     * @ORM\Column(type="boolean", length=15, nullable=true)
     */
    private $envoiSMS;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $token;

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
     * Set numero
     *
     * @param string $numero
     * @return Telephone
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
     * Set token
     *
     * @param string $token
     * @return Telephone
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
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
     * Set envoiSMS
     *
     * @param boolean $envoiSMS
     *
     * @return Medecin
     */
    public function setEnvoiSMS($envoiSMS)
    {
        $this->envoiSMS = $envoiSMS;

        return $this;
    }

    /**
     * Get envoiSMS
     *
     * @return boolean
     */
    public function hasEnvoiSMS()
    {
        return $this->envoiSMS;
    }

    /**
     * Set Medecin
     *
     * @param \Gestime\CoreBundle\Entity\Medecin $medecin
     * @return Telephone
     */
    public function setMedecin(Medecin $medecin)
    {
        $this->medecin = $medecin;

        return $this;
    }

    /**
     * Get Medecin
     *
     * @return Medecin
     */
    public function getMedecin()
    {
        return $this->medecin;
    }
}
