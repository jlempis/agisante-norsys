<?php

namespace Gestime\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Message
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Gestime\CoreBundle\Entity\MessageRepository")
 */
class Message
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idMessage;

    /**
     * @var string
     *
     * @ORM\Column(name="sujet", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $sujet;

    /**
     * @var string
     *
     * @ORM\Column(name="objet", type="text")
     * @Assert\NotBlank()
     */
    private $objet;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_envoi", type="datetime")
     */
    private $dateEnvoi;

    /**
     * @var integer
     *
     * @ORM\Column(name="sens", type="integer")
     */
    private $sens;

    /**
     * @var string
     *
     * @ORM\Column(name="etat", type="string", length=1)
     */
    private $etat;

    /**
     * @var boolean
     *
     * @ORM\Column(name="lu", type="boolean")
     */
    private $lu;

    /**
     * @var boolean
     *
     * @ORM\Column(name="sms", type="boolean")
     */
    private $sms;

    /**
     * @var string
     *
     * @ORM\Column(name="numerosms", type="string", length=14, nullable=true)
     */
    private $numerosms;

    /**
     * @ORM\ManyToMany(targetEntity="Categorie", inversedBy="messages")
     * @ORM\JoinTable(name="message2categorie")
     */
    private $categories;

    /**
     * @ORM\ManyToOne(targetEntity="Site")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id", nullable=false)
     */
    private $site;

    /**
     * @ORM\ManyToMany(targetEntity="Utilisateur", inversedBy="messages")
     * @ORM\JoinTable(name="message2utilisateur")
     */
    private $favoris;

    /**
     * @ORM\ManyToMany(targetEntity="Medecin", inversedBy="messages")
     * @ORM\JoinTable(name="message2medecin")
     */
    private $medecins;

    /**
     * @ORM\OneToOne(targetEntity="Gestime\CoreBundle\Entity\Message")
     * @ORM\JoinColumn(name="origine_id", referencedColumnName="id")
     */
    private $msgOrigine;

    /**
     * @var integer
     *
     * @ORM\Column(name="old_msg_id", type="integer", nullable=true)
     */
    private $oldMsgId;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Utilisateur")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     */
    private $createdBy;

    /**
     * @Gedmo\Blameable(on="update")
     * @ORM\ManyToOne(targetEntity="Utilisateur")
     * @ORM\JoinColumn(name="updated_by", referencedColumnName="id")
     */
    private $updatedBy;

    /**
     * [__construct description]
     * @param Utilisateur $user
     * @param string      $sens
     */
    public function __construct(Utilisateur $user, $sens)
    {
        $this->medecins = new ArrayCollection();
        $this->site = $user->getSite();
        $this->etat = 'V';
        $this->lu = false;
        $this->sms = false;
        $this->sens = $sens;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getIdMessage()
    {
        return $this->idMessage;
    }

    /**
     * Set sujet
     *
     * @param string $sujet
     * @return Message
     */
    public function setSujet($sujet)
    {
        $this->sujet = $sujet;

        return $this;
    }

    /**
     * Get sujet
     *
     * @return string
     */
    public function getSujet()
    {
        return $this->sujet;
    }

    /**
     * Set msgOrigine
     *
     * @param message $message
     * @return Message
     */
    public function setMsgOrigine($message)
    {
        $this->msgOrigine = $message;

        return $this;
    }

    /**
     * Get msgOrigine
     *
     * @return message
     */
    public function getMsgOrigine()
    {
        return $this->msgOrigine;
    }

    /**
     * Set Sens
     *
     * @param string $sens
     * @return Message
     */
    public function setSens($sens)
    {
        $this->sens = $sens;

        return $this;
    }

    /**
     * Get Sens
     *
     * @return string
     */
    public function getSens()
    {
        return $this->sens;
    }

    /**
     * Set objet
     *
     * @param string $objet
     * @return Message
     */
    public function setObjet($objet)
    {
        $this->objet = $objet;

        return $this;
    }

    /**
     * Get objet
     *
     * @return string
     */
    public function getObjet()
    {
        return $this->objet;
    }

    /**
     * Set dateEnvoi
     *
     * @param \DateTime $dateEnvoi
     * @return Message
     */
    public function setDateEnvoi($dateEnvoi)
    {
        $this->dateEnvoi = $dateEnvoi;

        return $this;
    }

    /**
     * Get dateEnvoi
     *
     * @return \DateTime
     */
    public function getDateEnvoi()
    {
        return $this->dateEnvoi;
    }

    /**
     * Set etat
     *
     * @param string $etat
     * @return Message
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get etat
     *
     * @return string
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * Set lu
     *
     * @param boolean $lu
     * @return Message
     */
    public function setLu($lu)
    {
        $this->lu = $lu;

        return $this;
    }

    /**
     * Get lu
     *
     * @return boolean
     */
    public function getLu()
    {
        return $this->lu;
    }

    /**
     * Set sms
     *
     * @param boolean $sms
     * @return Message
     */
    public function setSms($sms)
    {
        $this->sms = $sms;

        return $this;
    }

    /**
     * Get sms
     *
     * @return boolean
     */
    public function getSms()
    {
        return $this->sms;
    }

    /**
     * Set Favori
     *
     * @param Utilisateur $utilisateur
     * @return Message
     */
    public function setFavori(Utilisateur $utilisateur)
    {
        $this->favoris[] = $utilisateur;

        return $this;
    }
    /**
     * Remove Favori
     *
     * @param Utilisateur $utilisateur
     */
    public function removeFavori(Utilisateur $utilisateur)
    {
        $this->favoris->removeElement($utilisateur);
    }

    /**
     * [isFavori description]
     * @param string $utilisateur
     * @return boolean
     */
    public function isFavori($utilisateur)
    {
        foreach ($this->favoris as $favori) {
            if ($favori == $utilisateur) {
                return true;
            }
        }

        return false;
    }

    /**
     * Ajout d'un medecin
     *
     * @param Medecin $medecins
     * @return Message
     */
    public function addMedecin(Medecin $medecins)
    {
        $this->medecins[] = $medecins;

        return $this;
    }

    /**
     * Ajout d'un medecin
     *
     * @param Medecin $medecin
     * @return Message
     */
    public function setMedecins($medecin)
    {
        $this->medecins[] = $medecin;

        return $this;
    }

    /**
     * Remove medecins
     *
     * @param Medecin $medecins
     */
    public function removeMedecin(Medecin $medecins)
    {
        $this->medecins->removeElement($medecins);
    }

    /**
     * Get medecins
     *
     * @return Collection
     */
    public function getMedecins()
    {
        return $this->medecins;
    }

    /**
     * Get Site
     *
     * @return string
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @Assert\IsTrue(message="Il faut au moins un destinataire", groups={"Chevauchement"})
     *
     * @return boolean
     */
    public function hasDestinataire()
    {
        return (count($this->medecins) >0);
    }

    /**
     * Add categorie
     *
     * @param Categorie $categories
     *
     * @return Message
     */
    public function addCategory(Categorie $categories)
    {
        $this->categories[] = $categories;

        return $this;
    }

    /**
     * Remove categories
     *
     * @param \Gestime/CoreBundle/Entity\Categorie $categorie
     */
    public function removeCategory(Categorie $categorie)
    {
        $this->categories->removeElement($categorie);
    }

    /**
     * Get categories
     *
     * @return Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * isSupprime
     *
     * @return Boolean
     */
    public function isSupprime()
    {
        if ($this->etat == 'A') {
            return true;
        }

        return false;
    }

    /**
     * canBeModifiedByUser
     *
     * @param utilisateur $user
     * @return Message
     */
    public function canBeModifiedByUser(Utilisateur $user)
    {
        if (($this->createdBy == $user || $user->hasRole('ROLE_MESSAGERIE_SITE'))
            && $this->etat != 'A') {
            return true;
        }

        return false;
    }

    /**
     * isResponse
     *
     * @return Boolean
     */
    public function isResponse()
    {
        if (!is_null($this->msgOrigine)) {
            return true;
        }

        return false;
    }

    /**
     * [getCreated description]
     * @return datetime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * [getUpdated description]
     * @return datetime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * [getCreatedBy description]
     * @return Utilisateur
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * [getUpdatedBy description]
     * @return Utilisateur
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * @return int
     */
    public function getOldMsgId()
    {
        return $this->oldMsgId;
    }

    /**
     * @param int $oldMsgId
     */
    public function setOldMsgId($oldMsgId)
    {
        $this->oldMsgId = $oldMsgId;
    }
}
