<?php

/**
 * @category Entities
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\SynchroV1Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Table(name="PERSONNES")
 * @ORM\Entity(repositoryClass="Gestime\SynchroV1Bundle\Entity\PersonneRepository")
 */
class Personne
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="c_id_pers")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true, name="c_id_pers_prop")
     */
    private $idProprietaire;

    /**
     * @ORM\Column(type="string", length=4, nullable=true, name="c_civilite")
     */
    private $civilite;

    /**
     * @ORM\Column(type="string", length=32, nullable=true, name="l_entreprise")
     */
    private $entreprise;

    /**
     * @ORM\Column(type="string", length=32, nullable=true, name="l_nom")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=32, nullable=true, name="l_prenom")
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=32, nullable=true, name="l_nom_marital")
     */
    private $nomMarital;

    /**
     * @ORM\Column(type="string", length=50, nullable=true, name="l_email")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=1, nullable=true, name="c_type_pers")
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=1, nullable=true, name="c_etat")
     */
    private $etat;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, name="c_telephone")
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=50, nullable=true, name="l_adresse")
     */
    private $adresse;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, name="c_cpostal")
     */
    private $codePostal;

    /**
     * @ORM\Column(type="string", length=50, nullable=true, name="l_ville")
     */
    private $ville;

    /**
     * @return mixed
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * @param mixed $ville
     */
    public function setVille($ville)
    {
        $this->ville = $ville;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getIdProprietaire()
    {
        return $this->idProprietaire;
    }

    /**
     * @param mixed $idProprietaire
     */
    public function setIdProprietaire($idProprietaire)
    {
        $this->idProprietaire = $idProprietaire;
    }

    /**
     * @return mixed
     */
    public function getCivilite()
    {
        return $this->civilite;
    }

    /**
     * @param mixed $civilite
     */
    public function setCivilite($civilite)
    {
        $this->civilite = $civilite;
    }

    /**
     * @return mixed
     */
    public function getEntreprise()
    {
        return $this->entreprise;
    }

    /**
     * @param mixed $entreprise
     */
    public function setEntreprise($entreprise)
    {
        $this->entreprise = $entreprise;
    }

    /**
     * @return mixed
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param mixed $nom
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    /**
     * @return mixed
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * @param mixed $prenom
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;
    }

    /**
     * @return mixed
     */
    public function getNomMarital()
    {
        return $this->nomMarital;
    }

    /**
     * @param mixed $nomMarital
     */
    public function setNomMarital($nomMarital)
    {
        $this->nomMarital = $nomMarital;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * @param mixed $etat
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;
    }

    /**
     * @return mixed
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * @param mixed $telephone
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;
    }

    /**
     * @return mixed
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * @param mixed $adresse
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;
    }

    /**
     * @return mixed
     */
    public function getCodePostal()
    {
        return $this->codePostal;
    }

    /**
     * @param mixed $codePostal
     */
    public function setCodePostal($codePostal)
    {
        $this->codePostal = $codePostal;
    }
}

