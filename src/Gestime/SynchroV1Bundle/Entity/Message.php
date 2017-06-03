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
 * @ORM\Table(name="MESSAGES")
 * @ORM\Entity(repositoryClass="Gestime\SynchroV1Bundle\Entity\MessageRepository")
 */
class Message
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="c_id_msg")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true, name="date_msg")
     */
    private $dateMessage;

    /**
     * @ORM\Column(type="integer", nullable=true, name="c_id_emetteur")
     */
    private $emetteur;

    /**
     * @ORM\Column(type="integer", nullable=true, name="c_id_ref")
     */
    private $reference;

    /**
     * @ORM\Column(type="string", length=1, nullable=true, name="c_rep")
     */
    private $reponse;

    /**
     * @ORM\Column(type="integer", nullable=true, name="c_id_dest")
     */
    private $destinataire;

    /**
     * @ORM\Column(type="integer", nullable=true, name="c_id_regroup")
     */
    private $regroupement;

    /**
     * @ORM\Column(type="string", length=60, nullable=true, name="l_sujet")
     */
    private $sujet;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, name="l_objet")
     */
    private $objet;

    /**
     * @ORM\Column(type="string", length=1, nullable=true, name="c_type_msg")
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=1, nullable=true, name="c_statut")
     */
    private $statut;

    /**
     * @ORM\Column(type="string", length=1, nullable=true, name="c_supp")
     */
    private $suppr;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, name="l_demande")
     */
    private $demande;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, name="l_reponse")
     */
    private $lreponse;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, name="l_sujetobjet")
     */
    private $sujetObjet;

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
    public function getDateMessage()
    {
        return $this->dateMessage;
    }

    /**
     * @param mixed $dateMessage
     */
    public function setDateMessage($dateMessage)
    {
        $this->dateMessage = $dateMessage;
    }

    /**
     * @return mixed
     */
    public function getEmetteur()
    {
        return $this->emetteur;
    }

    /**
     * @param mixed $emetteur
     */
    public function setEmetteur($emetteur)
    {
        $this->emetteur = $emetteur;
    }

    /**
     * @return mixed
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param mixed $reference
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    /**
     * @return mixed
     */
    public function getDestinataire()
    {
        return $this->destinataire;
    }

    /**
     * @param mixed $destinataire
     */
    public function setDestinataire($destinataire)
    {
        $this->destinataire = $destinataire;
    }

    /**
     * @return mixed
     */
    public function getRegroupement()
    {
        return $this->regroupement;
    }

    /**
     * @param mixed $regroupement
     */
    public function setRegroupement($regroupement)
    {
        $this->regroupement = $regroupement;
    }

    /**
     * @return mixed
     */
    public function getSujet()
    {
        return $this->sujet;
    }

    /**
     * @param mixed $sujet
     */
    public function setSujet($sujet)
    {
        $this->sujet = $sujet;
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
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * @param mixed $statut
     */
    public function setStatut($statut)
    {
        $this->statut = $statut;
    }

    /**
     * @return mixed
     */
    public function getSuppr()
    {
        return $this->suppr;
    }

    /**
     * @param mixed $suppr
     */
    public function setSuppr($suppr)
    {
        $this->suppr = $suppr;
    }

    /**
     * @return mixed
     */
    public function getDemande()
    {
        return $this->demande;
    }

    /**
     * @param mixed $demande
     */
    public function setDemande($demande)
    {
        $this->demande = $demande;
    }

    /**
     * @return mixed
     */
    public function getReponse()
    {
        return $this->reponse;
    }

    /**
     * @param mixed $reponse
     */
    public function setReponse($reponse)
    {
        $this->reponse = $reponse;
    }

    /**
     * @return mixed
     */
    public function getSujetObjet()
    {
        return $this->sujetObjet;
    }

    /**
     * @param mixed $sujetObjet
     */
    public function setSujetObjet($sujetObjet)
    {
        $this->sujetObjet = $sujetObjet;
    }

    /**
     * @return mixed
     */
    public function getLreponse()
    {
        return $this->lreponse;
    }

    /**
     * @param mixed $lreponse
     */
    public function setLreponse($lreponse)
    {
        $this->lreponse = $lreponse;
    }
}
