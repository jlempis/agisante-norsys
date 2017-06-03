<?php

/**
 * @category Entities
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use FOS\UserBundle\Entity\User as BaseUser;

/**
 * Utilisateur
 * Un utilisateur peut se logger et accéder à un, plusieurs out tous les agendas
 * Un utilisateur a des droits
 *
 */

/**
 * @ORM\Entity(repositoryClass="Gestime\CoreBundle\Entity\UtilisateurRepository")
 * @UniqueEntity("email")
 */

class Utilisateur  extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Site")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id",nullable=true)
     */
    protected $site;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, name="nom")
     */
    protected $nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, name="prenom")
     */
    protected $prenom;

    /**
     * @ORM\ManyToMany(targetEntity="Gestime\CoreBundle\Entity\Group")
     * @ORM\JoinTable(name="fos_user_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;

    /**
     * @ORM\ManyToMany(targetEntity="Medecin", inversedBy="utilisateurs")
     * @ORM\JoinTable(
     *     name="Utilisateur2medecin",
     *     joinColumns={@ORM\JoinColumn(name="utilisateur_id", referencedColumnName="id", nullable=true)},
     *     inverseJoinColumns={@ORM\JoinColumn(name="medecin_id", referencedColumnName="id", nullable=true)}
     * )
     */

    protected $medecins;

    /**
     * @ORM\ManyToMany(targetEntity="Message", mappedBy="favoris")
     * @ORM\JoinTable(name="message2utilisateur")
     */
    private $messages;

    /**
     * @ORM\ManyToOne(targetEntity="Medecin")
     * @ORM\JoinColumn(name="medecin_default_id", referencedColumnName="id", nullable=true)
     * @Assert\NotNull(message="Le medecin par défaut est obligatoire", groups={"Utilisateur"})
     */
    protected $medecindefault;

    /**
     * @var string
     *
     * @ORM\Column(name="phone_number", type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="Le numero de téléphone est bligatoire", groups={"Web"})
     */
    protected $phoneNumber;

    /**
     * @ORM\Column(name="auth_code", type="string", nullable=true)
     */
    private $authCode;

    /**
     * @ORM\Column(name="code_expires_at", type="datetime", nullable=true)
     */
    private $authCodeExpiresAt;

    /**
     * @ORM\Column(name="date_naissance", type="date", nullable=true)
     */
    private $dateNaissance;

    /**
     * @ORM\Column(name="sexe", type="string", length=1, nullable=true)
     */
    private $sexe;

    /**
     * @ORM\Column(name="trusted", type="json_array", nullable=true)
     */
    private $trusted;

    /**
     * @ORM\Column(name="userWeb", type="boolean", nullable=true)
     */
    private $userWeb;

    /**
     * @ORM\Column(name="notifications", type="integer", nullable=true)
     */
    private $notifications;


    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->medecins = new ArrayCollection();
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->roles = array();
    }

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
     * Set nom
     *
     * @param string $nom
     * @return Utilisateur
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     * @return Utilisateur
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Get Nom complet
     *
     * @return string
     */
    public function getNomComplet()
    {
        return $this->prenom.' '.$this->nom;
    }

    /**
     * Get Medecin
     *
     * @return string
     */
    public function getMedecindefault()
    {
        return $this->medecindefault;
    }

    /**
     * Set Medecin
     *
     * @param Medecin $medecin
     * @return Utilisateur
     */
    public function setMedecindefault(Medecin $medecin)
    {
        $this->medecindefault = $medecin;

        return $this;
    }
    /**
     * Ajout d'un medecin
     *
     * @param Medecin $medecins
     * @return Utilisateur
     */
    public function addMedecin(Medecin $medecins)
    {
        $this->medecins[] = $medecins;

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
     * Add groups
     *
     * @param Group $groups
     */
    public function addGroups(Group $groups)
    {
        $this->groups[] = $groups;
    }

    /**
     * Get groups
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getGroups()
    {
        return $this->groups;
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
     * [setSite description]
     * @param Site $site
     * @return string
     */
    public function setSite($site)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * getListRoles
     *
     * [getListRoles description]
     * @return array liste des roles de l'utilisateur
     */
    public function getListRoles()
    {
        $roles = array();

        foreach ($this->getGroups() as $key => $groupe) {
            foreach ($groupe->getRoles() as $key => $role) {
                $roles[] = $role;
            }
        }

        return $roles;
    }

    /**
     * [hasRole description]
     * @param  Role $userRole
     * @return boolean        vrai si l'utilisateur a un role spécifique
     */
    public function hasRole($userRole)
    {
        foreach ($this->getListRoles() as $key => $role) {
            if ($role == $userRole) {
                return true;
            }
        }

        return false;
    }

    /**
     * [hasGroup description]
     * @param  Role $userGroup
     * @return boolean        vrai si l'utilisateur a un groupe spécifique
     */
    public function hasGroup($userGroup)
    {
        foreach ($this->getGroups() as $key => $groupe) {
            if (strcmp($groupe, $userGroup) == 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * @Assert\IsTrue(message="Le medecin par défaut doit faire partie des médecins gérés.", groups={"Utilisateur"})
     * @return boolean
     */
    public function isMedecinDefaultOK()
    {
        if ($this->hasGroup('SECRETAIRE-SITE') || $this->hasGroup('SECRETAIRE-CABINET')) {
            return true;
        }
        foreach ($this->getMedecins() as $key => $medecin) {
            if ($medecin == $this->getMedecindefault()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @Assert\IsTrue(message="Les rôles messagerie_site et messagerie_medecin sont incompatibles")
     * @return boolean
     */
    public function isRolesMessagerieOK()
    {
        if ($this->hasRole('ROLE_MESSAGERIE_SITE') &&
            $this->hasRole('ROLE_MESSAGERIE_MEDECIN')) {
            return false;
        }

        return true;
    }


    /**
     * setPhoneNumber
     * @param string $phoneNumber Numero de téléphone de l'utilisateur Doc24
     * @return Utilisateur
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * getPhoneNumber
     * @return string Numero de téléphone de l'utilisateur Doc24
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * setUserWeb
     * @param boolean $userWeb
     * @return Utilisateur
     */
    public function setUserWeb($userWeb)
    {
        $this->userWeb = $userWeb;

        return $this;
    }

    /**
     * isUserWeb
     * @return boolean
     */
    public function isUserWeb()
    {
        return $this->userWeb;
    }

    /*
     * Implement the TwoFactorInterface
     */

    /**
     * [isEmailAuthEnabled
     * @return boolean
     */
    public function isEmailAuthEnabled()
    {
        return true; // This can also be a persisted field but it is enabled by default for now
    }

    /**
     * getEmailAuthCode
     * @return boolean
     */
    public function getEmailAuthCode()
    {
        return $this->authCode;
    }


    /**
     * setEmailAuthCode
     * @param [type] $authCode
     */
    public function setEmailAuthCode($authCode)
    {
        $this->authCode = $authCode;
    }

    /*
     * Implement the TrustedComputerInterface
     */

    /**
     * addTrustedComputer
     * @param string    $token
     * @param \DateTime $validUntil
     */
    public function addTrustedComputer($token, \DateTime $validUntil)
    {
        $this->trusted[$token] = $validUntil->format('r');
    }

    /**
     * [isTrustedComputer
     * @param  string $token
     * @return boolean
     */
    public function isTrustedComputer($token)
    {
        if (isset($this->trusted[$token])) {
            $now = new \DateTime();
            $validUntil = new \DateTime($this->trusted[$token]);

            return $now < $validUntil;
        }

        return false;
    }

  /**
   * @return mixed
   */
  public function getAuthCode() {
    return $this->authCode;
  }

  /**
   * @param mixed $authCode
   */
  public function setAuthCode($authCode) {
    $this->authCode = $authCode;
  }

  /**
   * @return mixed
   */
  public function getAuthCodeExpiresAt() {
    return $this->authCodeExpiresAt;
  }

  /**
   * @param mixed $authCodeExpiresAt
   */
  public function setAuthCodeExpiresAt($authCodeExpiresAt) {
    $this->authCodeExpiresAt = $authCodeExpiresAt;
  }

  /**
   * @return mixed
   */
  public function getDateNaissance() {
    return $this->dateNaissance;
  }

  /**
   * @param mixed $dateNaissance
   */
  public function setDateNaissance($dateNaissance) {
    $this->dateNaissance = $dateNaissance;
  }

  /**
   * @return mixed
   */
  public function getSexe() {
    return $this->sexe;
  }

  /**
   * @param mixed $sexe
   */
  public function setSexe($sexe) {
    $this->sexe = $sexe;
  }

  /**
   * @return mixed
   */
  public function getTrusted() {
    return $this->trusted;
  }

  /**
   * @param mixed $trusted
   */
  public function setTrusted($trusted) {
    $this->trusted = $trusted;
  }

  /**
   * @return mixed
   */
  public function getUserWeb() {
    return $this->userWeb;
  }

    /**
     * @return mixed
     */
    public function getNotifications() {
        return $this->notifications;
    }

    /**
     * @param mixed $notifications
     */
    public function setNotifications($notifications) {
        $this->notifications = $notifications;
    }



}
