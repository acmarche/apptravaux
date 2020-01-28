<?php

namespace AcMarche\Travaux\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Travaux\Repository\DocumentRepository")
 * @ORM\Table(name="document")
 *
 */
class Document implements TimestampableInterface
{
    use TimestampableTrait;

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

    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getFileName(): ?string
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMime(): ?string
    {
        return $this->mime;
    }

    public function setMime(string $mime): self
    {
        $this->mime = $mime;

        return $this;
    }

    public function getSmartphone(): ?bool
    {
        return $this->smartphone;
    }

    public function setSmartphone(bool $smartphone): self
    {
        $this->smartphone = $smartphone;

        return $this;
    }

    public function getIntervention(): ?Intervention
    {
        return $this->intervention;
    }

    public function setIntervention(?Intervention $intervention): self
    {
        $this->intervention = $intervention;

        return $this;
    }

    /**
     * STOP
     */

}
