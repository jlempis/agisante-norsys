<?php

namespace Gestime\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Adresse
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Gestime\CoreBundle\Entity\AdresseRepository")
 * @Assert\GroupSequence({ "Personne", "Adresse"})
 */
class Adresse
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
     * @ORM\Column(name="complement", type="string", length=255, nullable=true)
     */
    private $complement;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="La voie est obligatoire.", groups={"Adresse"})
     * @ORM\Column(name="voie", type="string", length=255)
     */
    private $voie;

    /**
     * @var string
     *
     * @ORM\Column(name="codePostal", type="string", length=10)
     * @Assert\NotBlank(message="Le code postal est obligatoire.", groups={"Adresse"})
     */
    private $codePostal;

    /**
     * @ORM\ManyToOne(targetEntity="Gestime\CoreBundle\Entity\Ville", inversedBy="adresses")
     * @ORM\JoinColumn(name="ville_id", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank(message="La ville est obligatoire.", groups={"Adresse"})
     */
    private $ville;

    /**
     * @var string
     *
     * @ORM\Column(name="Pays", type="string", length=255, nullable=true)
     */
    private $pays;

    /**
     * @ORM\ManyToOne(targetEntity="Gestime\CoreBundle\Entity\Personne", inversedBy="adresses")
     * @ORM\JoinColumn(name="personne_id", referencedColumnName="id", nullable=false)
     */
    private $personne;

    /**
     * @var float
     *
     * @ORM\Column(name="latitude", type="float")
     */
    private $latitude;

    /**
     * @var float
     *
     * @ORM\Column(name="longitude", type="float")
     */
    private $longitude;

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
     * Set complement
     *
     * @param string $complement
     * @return Adresse
     */
    public function setComplement($complement)
    {
        $this->complement = $complement;

        return $this;
    }

    /**
     * Get Adressecomplete
     *
     * @return string
     */
    public function getAdresseComplete()
    {
        return $this->voie.' '.$this->complement.' '.$this->codePostal.' '.$this->ville;
    }

    /**
     * Get complement
     *
     * @return string
     */
    public function getComplement()
    {
        return $this->complement;
    }

    /**
     * Set voie
     *
     * @param string $voie
     * @return Adresse
     */
    public function setVoie($voie)
    {
        $this->voie = $voie;

        return $this;
    }

    /**
     * Get voie
     *
     * @return string
     */
    public function getVoie()
    {
        return $this->voie;
    }

    /**
     * Set codePostal
     *
     * @param string $codePostal
     * @return Adresse
     */
    public function setCodePostal($codePostal)
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    /**
     * Get codePostal
     *
     * @return string
     */
    public function getCodePostal()
    {
        return $this->codePostal;
    }

    /**
     * Set ville
     *
     * @param string $ville
     * @return Adresse
     */
    public function setVille($ville)
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * Get ville
     *
     * @return string
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * Set pays
     *
     * @param string $pays
     * @return Adresse
     */
    public function setPays($pays)
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * @return float
     */
    public function getLatitude() {
      return $this->latitude;
    }

    /**
     * @param float $latitude
     */
    public function setLatitude($latitude) {
      $this->latitude = $latitude;
    }

    /**
     * @return float
     */
    public function getLongitude() {
      return $this->longitude;
    }

    /**
     * @param float $longitude
     */
    public function setLongitude($longitude) {
      $this->longitude = $longitude;
    }

    /**
     * Get pays
     *
     * @return string
     */
    public function getPays()
    {
        return $this->pays;
    }

    /**
     * Set Personne
     *
     * @param \Gestime\CoreBundle\Entity\Personne $personne
     * @return Adresse
     */
    public function setPersonne(Personne $personne)
    {
        $this->personne = $personne;

        return $this;
    }

    /**
     * Get Personne
     *
     * @return Personne
     */
    public function getPersonne()
    {
        return $this->personne;
    }

}
