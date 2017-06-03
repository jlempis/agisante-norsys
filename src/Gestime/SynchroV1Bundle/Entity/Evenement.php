<?php

/**
 * @category Entities
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\SynchroV1Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * V1 : Evenement
 *
 * @ORM\Table(name="EVENEMENT")
 * @ORM\Entity(repositoryClass="Gestime\SynchroV1Bundle\Entity\EvenementRepository")
 */
class Evenement
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="c_id_evt")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true, name="c_id_pers")
     */
    private $idPersonne;

    /**
     * @ORM\Column(type="date", nullable=true, name="date_rdv")
     */
    private $dateRdv;

    /**
     * @ORM\Column(type="integer", nullable=true, name="hdeb")
     */
    private $hdeb;

    /**
     * @ORM\Column(type="integer", nullable=true, name="mdeb")
     */
    private $mdeb;

    /**
     * @ORM\Column(type="integer", nullable=true, name="hfin")
     */
    private $hfin;

    /**
     * @ORM\Column(type="integer", nullable=true, name="mfin")
     */
    private $mfin;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, name="l_objet")
     */
    private $objet;

    /**
     * @ORM\Column(type="string", length=1, nullable=true, name="c_type_evt")
     */
    private $type;

    /**
     * @ORM\Column(type="integer", nullable=true, name="c_id_contact")
     */
    private $id_contact;

    /**
     * @ORM\Column(type="string", length=1, nullable=true, name="c_etat_evt")
     */
    private $etat;

    /**
     * @ORM\Column(type="integer", nullable=true, name="c_id_createur")
     */
    private $createur;

    /**
     * @ORM\Column(type="integer", nullable=true, name="c_id_evt_prec")
     */
    private $id_evt_prec;

    /**
     * @ORM\Column(type="datetime", nullable=true, name="d_modification")
     */
    private $dateModification;

    /**
     * @ORM\Column(type="integer", nullable=true, name="c_rappel")
     */
    private $rappel;

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
    public function getIdPersonne()
    {
        return $this->idPersonne;
    }

    /**
     * @param mixed $idPersonne
     */
    public function setIdPersonne($idPersonne)
    {
        $this->idPersonne = $idPersonne;
    }

    /**
     * @return mixed
     */
    public function getDateRdv()
    {
        return $this->dateRdv;
    }

    /**
     * @param mixed $dateRdv
     */
    public function setDateRdv($dateRdv)
    {
        $this->dateRdv = $dateRdv;
    }

    /**
     * @return mixed
     */
    public function getHdeb()
    {
        return $this->hdeb;
    }

    /**
     * @param mixed $hdeb
     */
    public function setHdeb($hdeb)
    {
        $this->hdeb = $hdeb;
    }

    /**
     * @return mixed
     */
    public function getMdeb()
    {
        return $this->mdeb;
    }

    /**
     * @param mixed $mdeb
     */
    public function setMdeb($mdeb)
    {
        $this->mdeb = $mdeb;
    }

    /**
     * @return mixed
     */
    public function getHfin()
    {
        return $this->hfin;
    }

    /**
     * @param mixed $hfin
     */
    public function setHfin($hfin)
    {
        $this->hfin = $hfin;
    }

    /**
     * @return mixed
     */
    public function getMfin()
    {
        return $this->mfin;
    }

    /**
     * @param mixed $mfin
     */
    public function setMfin($mfin)
    {
        $this->mfin = $mfin;
    }

    /**
     * @return mixed
     */
    public function getObjet()
    {
        return $this->objet;
    }

    /**
     * @param mixed $objet
     */
    public function setObjet($objet)
    {
        $this->objet = $objet;
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
    public function getIdContact()
    {
        return $this->id_contact;
    }

    /**
     * @param mixed $id_contact
     */
    public function setIdContact($id_contact)
    {
        $this->id_contact = $id_contact;
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
    public function getCreateur()
    {
        return $this->createur;
    }

    /**
     * @param mixed $createur
     */
    public function setCreateur($createur)
    {
        $this->createur = $createur;
    }

    /**
     * @return mixed
     */
    public function getIdEvtPrec()
    {
        return $this->id_evt_prec;
    }

    /**
     * @param mixed $id_evt_prec
     */
    public function setIdEvtPrec($id_evt_prec)
    {
        $this->id_evt_prec = $id_evt_prec;
    }

    /**
     * @return mixed
     */
    public function getDateModification()
    {
        return $this->dateModification;
    }

    /**
     * @param mixed $dateModification
     */
    public function setDateModification($dateModification)
    {
        $this->dateModification = $dateModification;
    }

    /**
     * @return mixed
     */
    public function getRappel()
    {
        return $this->rappel;
    }

    /**
     * @param mixed $rappel
     */
    public function setRappel($rappel)
    {
        $this->rappel = $rappel;
    }

}
