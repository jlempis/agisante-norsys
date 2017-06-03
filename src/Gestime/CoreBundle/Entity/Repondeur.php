<?php

/**
 * @category Entities
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\CoreBundle\Entity;

use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Repondeur
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Gestime\CoreBundle\Entity\RepondeurRepository")
 * @Vich\Uploadable
 * @UniqueEntity("tag")
 */
class Repondeur
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $idRepondeur;

    /**
     * @var string
     *
     * @ORM\Column(name="tag", type="string", length=50, unique = true)
     */
    private $tag;

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire", type="string", length=255)
     */
    private $commentaire;

    /**
     * @Vich\UploadableField(mapping="repondeur", fileNameProperty="name")
     *
     * @var File $file
     */
    protected $file;

    /**
     * @ORM\Column(type="string", length=255, name="name")
     * @var string $name
     */
    protected $name;

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
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime $updatedAt
     */
    protected $updatedAt;

    /**
     * Get id
     *
     * @return integer
     */
    public function getIdRepondeur()
    {
        return $this->idRepondeur;
    }

    /**
     * Set tag
     *
     * @param string $tag
     * @return Repondeur
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set commentaire
     *
     * @param string $commentaire
     * @return Repondeur
     */
    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    /**
     * Get commentaire
     *
     * @return string
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Repondeur
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getNameWithoutExt()
    {
        return preg_replace('/\\.[^.\\s]{3,4}$/', '', $this->name);
    }

    /**
     * Set file
     *
     * @param string $uploadedFile
     * @return Repondeur
     */
    public function setFile($uploadedFile)
    {
        $this->file = $uploadedFile;

        if ($this->file instanceof file) {
            $this->updatedAt = new \DateTime('now');
        }
    }

    /**
     * [getFile description]
     * @return File
     */
    public function getFile()
    {
        return $this->file;
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
     * [getUpdatedBy]
     * @return Utilisateur
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }
}
