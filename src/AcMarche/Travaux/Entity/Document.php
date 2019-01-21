<?php

namespace AcMarche\Travaux\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Travaux\Repository\DocumentRepository")
 * @ORM\Table(name="document")
 *
 */
class Document
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\Travaux\Entity\Intervention", inversedBy="documents")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $intervention;

    /**
     *
     * note This is not a mapped field of entity metadata, just a simple property.
     * @Assert\File(
     *     maxSize = "7M"
     * )
     * @var File $file
     */
    protected $Ofile;

    /**
     * @ORM\Column(type="string", length=255, name="file_name")
     *
     * @var string $fileName
     */
    protected $fileName;

    /**
     * @ORM\Column(type="string")
     * @var string $mime
     */
    protected $mime;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime $updatedAt
     */
    protected $updatedAt;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default"=0} )
     */
    protected $smartphone = false;

    protected $files;

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $file
     */
    public function setOFile(File $file = null)
    {
        $this->Ofile = $file;

        if ($file) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime('now');
        }
    }

    /**
     * @return File
     */
    public function getOFile()
    {
        return $this->Ofile;
    }

    /**
     * @param string $fileName
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    public function __toString()
    {
        return $this->fileName;
    }

    /**
     * @return mixed
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param mixed $files
     */
    public function setFiles($files)
    {
        $this->files = $files;
    }

    /**
     * STOP
     */

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
     * Set mime
     *
     * @param string $mime
     *
     * @return Document
     */
    public function setMime($mime)
    {
        $this->mime = $mime;

        return $this;
    }

    /**
     * Get mime
     *
     * @return string
     */
    public function getMime()
    {
        return $this->mime;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Document
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set smartphone
     *
     * @param boolean $smartphone
     *
     * @return Document
     */
    public function setSmartphone($smartphone)
    {
        $this->smartphone = $smartphone;

        return $this;
    }

    /**
     * Get smartphone
     *
     * @return boolean
     */
    public function getSmartphone()
    {
        return $this->smartphone;
    }

    /**
     * Set intervention
     *
     * @param \AcMarche\Travaux\Entity\Intervention $intervention
     *
     * @return Document
     */
    public function setIntervention(\AcMarche\Travaux\Entity\Intervention $intervention)
    {
        $this->intervention = $intervention;

        return $this;
    }

    /**
     * Get intervention
     *
     * @return \AcMarche\Travaux\Entity\Intervention
     */
    public function getIntervention()
    {
        return $this->intervention;
    }
}
