<?php

namespace Gestime\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * PhotoCabinet
 *
 * @ORM\Table()
 * @ORM\Entity
 * @Vich\Uploadable
 */

class PhotoCabinet
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
     * @ORM\Column(name="photoPath", type="string", length=255)
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
     * @ORM\ManyToOne(targetEntity="Gestime\CoreBundle\Entity\InfosDoc24", inversedBy="photosCabinet")
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
     * Set photoPath
     *
     * @param string $photoPath
     *
     * @return PhotoCabinet
     */
    public function setPhotoPath($photoPath)
    {
        $this->photoPath = $photoPath;

        return $this;
    }

    /**
     * Get photoPath
     *
     * @return string
     */
    public function getPhotoPath()
    {
        return $this->photoPath;
    }

    /**
     * Set photo
     *
     * @param string $photo
     *
     * @return PhotoCabinet
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return string
     */
    public function getPhoto()
    {
        return $this->photo;
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

