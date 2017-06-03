<?php

namespace Gestime\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * InfosDoc24
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Gestime\CoreBundle\Entity\InfosDoc24Repository")
 * @Vich\Uploadable
 */
class InfosDoc24
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
     * @ORM\Column(name="photoPath", type="string", length=255, nullable=true)
     */
    private $photoPath;

    /**
     * @Assert\File(
     *     maxSize="1M",
     *     mimeTypes={"image/png", "image/jpeg", "image/pjpeg"}
     * )
     * @Vich\UploadableField(mapping="avatar", fileNameProperty="photoPath")
     */
    private $photo;

    /**
     * @var string
     *
     * @ORM\Column(name="presentation", type="string", length=255)
     */
    private $presentation;

    /**
     * @ORM\OneToMany(targetEntity="Tarification", mappedBy="info",  cascade={"persist", "remove"})
     * @Assert\Valid()
     */
    private $tarifications;

    /**
     * @ORM\OneToMany(targetEntity="Transport", mappedBy="info",  cascade={"persist", "remove"})
     * @Assert\Valid()
     */
    private $transports;

    /**
     * @ORM\OneToMany(targetEntity="PhotoCabinet", mappedBy="info",  cascade={"persist", "remove"})
     * @Assert\Valid()
     */
    private $photoCabinets;
  
    /**
     * @var boolean
     *
     * @ORM\Column(name="paiement_cb", type="boolean", nullable=true)
     */
    private $paiement_cb;
    /**
     * @var boolean
     *
     * @ORM\Column(name="paiement_ch", type="boolean", nullable=true)
     */

    private $paiement_ch;
    /**
     * @var boolean
     *
     * @ORM\Column(name="paiement_esp", type="boolean", nullable=true)
     */
    private $paiement_esp;

    /**
     * @var boolean
     *
     * @ORM\Column(name="tiers_payant", type="boolean", nullable=true)
     */
    private $tiers_payant;

    /**
     * @var string
     *
     * @ORM\Column(name="site", type="string", length=255, nullable=true)
     */
    private $site;

    /**
     * @var string
     *
     * @ORM\Column(name="detailTelephone", type="string", length=512, nullable=true))
     */
    private $detailTelephone;

    /**
     * @var string
     *
     * @ORM\Column(name="presentationLongue", type="string", length=1024, nullable=true))
     */
    private $presentationLongue;

    /**
     * @var string
     *
     * @ORM\Column(name="infos_pratiques", type="string", length=255, nullable=true))
     */
    private $infosPratiques;

    /**
     * @var string
     *
     * @ORM\Column(name="detail_acces", type="string", length=255, nullable=true))
     */
    private $detailAcces;

    /**
     * @var boolean
     *
     * @ORM\Column(name="carteVitale", type="boolean", nullable=true))
     */
    private $carteVitale;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true))
     */
    private $email;

    /**
     * @var integer
     *
     * @ORM\Column(name="conventionnement", type="integer", nullable=true))
     */
    private $conventionnement;

    /**
     * @ORM\OneToOne(targetEntity="Gestime\CoreBundle\Entity\Medecin", inversedBy="infosDoc24")
     * @ORM\JoinColumn(name="medecin_id", referencedColumnName="id", nullable=true)
     */
    private $medecin;

    /**
     * @ORM\ManyToMany(targetEntity="Gestime\CoreBundle\Entity\Langue")
     * @ORM\JoinTable(name="langues_parlees",
     *      joinColumns={@ORM\JoinColumn(name="infosMedecin_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="langue_id", referencedColumnName="id")}
     * )
     */
    protected $langues;

    /**
     * @ORM\ManyToMany(targetEntity="Gestime\CoreBundle\Entity\SpecialiteMedicale")
     * @ORM\JoinTable(name="specialite_medecin",
     *      joinColumns={@ORM\JoinColumn(name="infosMedecin_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="specialite_id", referencedColumnName="id")}
     * )
     */
    protected $specialitesMedicales;

    /**
     * [__construct description]
     * @param Site $site
     */
    public function __construct()
    {
      $this->tarifications = new ArrayCollection();
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
     * Set presentation
     *
     * @param string $presentation
     *
     * @return InfosDoc24
     */
    public function setPresentation($presentation)
    {
        $this->presentation = $presentation;

        return $this;
    }

    /**
     * Get presentation
     *
     * @return string
     */
    public function getPresentation()
    {
        return $this->presentation;
    }

    /**
     * Set tarif
     *
     * @param integer $tarif
     *
     * @return InfosDoc24
     */
    public function setTarif($tarif)
    {
        $this->tarif = $tarif;

        return $this;
    }

    /**
     * Get tarif
     *
     * @return integer
     */
    public function getTarif()
    {
        return $this->tarif;
    }

    /**
     * Set site
     *
     * @param string $site
     *
     * @return InfosDoc24
     */
    public function setSite($site)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get site
     *
     * @return string
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Set presentationLongue
     *
     * @param string $presentationLongue
     *
     * @return InfosDoc24
     */
    public function setPresentationLongue($presentationLongue)
    {
        $this->presentationLongue = $presentationLongue;

        return $this;
    }

    /**
     * Get presentationLongue
     *
     * @return string
     */
    public function getPresentationLongue()
    {
        return $this->presentationLongue;
    }

    /**
     * Set carteVitale
     *
     * @param boolean $carteVitale
     *
     * @return InfosDoc24
     */
    public function setCarteVitale($carteVitale)
    {
        $this->carteVitale = $carteVitale;

        return $this;
    }

    /**
     * Get carteVitale
     *
     * @return boolean
     */
    public function getCarteVitale()
    {
        return $this->carteVitale;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return InfosDoc24
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function getMedecin() {
        return $this->medecin;
    }

    /**
     * @param mixed $medecin
     */
    public function setMedecin($medecin) {
        $this->medecin = $medecin;
    }

    /**
     * @return boolean
     */
    public function isPaiementCb() {
        return $this->paiement_cb;
    }

    /**
     * @param boolean $paiement_cb
     */
    public function setPaiementCb($paiement_cb) {
        $this->paiement_cb = $paiement_cb;
    }

    /**
     * @return boolean
     */
    public function isPaiementCh() {
        return $this->paiement_ch;
    }

    /**
     * @param boolean $paiement_ch
     */
    public function setPaiementCh($paiement_ch) {
        $this->paiement_ch = $paiement_ch;
    }

    /**
     * @return boolean
     */
    public function isPaiementEsp() {
        return $this->paiement_esp;
    }

    /**
     * @param boolean $paiement_esp
     */
    public function setPaiementEsp($paiement_esp) {
        $this->paiement_esp = $paiement_esp;
    }

    /**
     * @return mixed
     */
    public function getPhoto() {
        return $this->photo;
    }

    /**
     * @param mixed $photo
     */
    public function setPhoto($photo) {
        $this->photo = $photo;
    }

    /**
     * @return string
     */
    public function getPhotoPath() {
        return $this->photoPath;
    }

    /**
     * @param string $photoPath
     */
    public function setPhotoPath($photoPath) {
        $this->photoPath = $photoPath;
    }

    /**
     * Add Tarification
     *
     * @param \Gestime\CoreBundle\Entity\Tarification $tarifications
     *
     * @return InfosDoc24
     */
    public function addTarification(Tarification $tarifications)
    {
      foreach ($tarifications as $tarification) {
        $tarification->SetInfo($this);
      }

      $this->tarifications[] = $tarifications;

      return $this;
    }

    /**
     * Remove Tarifications
     *
     * @param Tarification $tarifications
     */
    public function removeTarification(Tarification $tarifications)
    {
      $this->tarifications->removeElement($tarifications);
    }

    /**
     * Get Tarifications
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTarification()
    {
      return $this->tarifications;
    }

  /**
   * Add Transport
   *
   * @param \Gestime\CoreBundle\Entity\Transport $transports
   *
   * @return InfosDoc24
   */
  public function addTransport(Transport $transports)
  {
    foreach ($transports as $transport) {
      $transport->SetInfo($this);
    }

    $this->transports[] = $transports;

    return $this;
  }

  /**
   * Remove Transport
   *
   * @param Transport $transports
   */
  public function removeTransport(Transport $transports)
  {
    $this->transports->removeElement($transports);
  }

  /**
   * Get Transports
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getTransport()
  {
    return $this->transports;
  }

  /**
   * @return int
   */
  public function getConventionnement() {
    return $this->conventionnement;
  }

  /**
   * @param int $conventionnement
   */
  public function setConventionnement($conventionnement) {
    $this->conventionnement = $conventionnement;
  }

  /**
   * @return string
   */
  public function getInfosPratiques() {
    return $this->infosPratiques;
  }

  /**
   * @param string $infosPratiques
   */
  public function setInfosPratiques($infosPratiques) {
    $this->infosPratiques = $infosPratiques;
  }

  /**
   * @return string
   */
  public function getDetailAcces() {
    return $this->detailAcces;
  }

  /**
   * @param string $detailAcces
   */
  public function setDetailAcces($detailAcces) {
    $this->detailAcces = $detailAcces;
  }

  /**
   * @return boolean
   */
  public function getTiersPayant() {
    return $this->tiers_payant;
  }

  /**
   * @param boolean $tiers_payant
   */
  public function setTiersPayant($tiers_payant) {
    $this->tiers_payant = $tiers_payant;
  }

  /**
   * Add Langue
   *
   * @param Langue $langues
   */
  public function addLangue(Langue $langues)
  {
    $this->langues[] = $langues;
  }
  /**
   * Remove Langue
   *
   * @param Langue $langues
   */
  public function removeLangue(Langue $langues)
  {
    $this->langues->removeElement($langues);
  }

  /**
   * Get langues
   *
   * @return Doctrine\Common\Collections\Collection
   */
  public function getLangues()
  {
    return $this->langues;
  }

  /**
   * Add SpecialiteMedicale
   *
   * @param SpecialiteMedicale $specialitesMedicales
   */
  public function addSpecialitesMedicale(SpecialiteMedicale $specialitesMedicales)
  {
    $this->specialitesMedicales[] = $specialitesMedicales;
  }

  /**
   * Get SpecialitesMedicale
   *
   * @return Doctrine\Common\Collections\Collection
   */
  public function getSpecialitesMedicales()
  {
    return $this->specialitesMedicales;
  }

  /**
   * Remove SpecialiteMedicale
   *
   * @param SpecialiteMedicale $specialitesMedicales
   */
  public function removeSpecialitesMedicale(SpecialiteMedicale $specialitesMedicales)
  {
    $this->specialitesMedicales->removeElement($specialitesMedicales);
  }


  /**
   * Add PhotoCabinet
   *
   * @param \Gestime\CoreBundle\Entity\PhotoCabinet $photoCabinets
   *
   * @return InfosDoc24
   */
  public function addPhotosCabinet(PhotoCabinet $photoCabinet)
  {
    foreach ($photoCabinet as $photoCabinet) {
      $photoCabinet->SetInfo($this);
    }

    $this->photoCabinets[] = $photoCabinet;

    return $this;
  }

  /**
   * Remove PhotoCabinet
   *
   * @param PhotoCabinet $photoCabinets
   */
  public function removePhotosCabinet(PhotoCabinet $photoCabinets)
  {
    $this->photoCabinets->removeElement($photoCabinets);
  }

  /**
   * Get PhotoCabinets
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getPhotosCabinets()
  {
    return $this->photoCabinets;
  }

    /**
     * @return string
     */
    public function getDetailTelephone()
    {
        return $this->detailTelephone;
    }

    /**
     * @param string $detailTelephone
     */
    public function setDetailTelephone($detailTelephone)
    {
        $this->detailTelephone = $detailTelephone;
    }



}

