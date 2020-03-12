<?php

namespace AcMarche\Avaloir\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Avaloir\Repository\AvaloirNewRepository")
 * @ORM\Table(name="avaloir_new")
 * @Vich\Uploadable
 *
 */
class AvaloirNew implements TimestampableInterface
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="float", nullable=false)
     */
    protected $latitude;

    /**
     * @ORM\Column(type="float", nullable=false)
     */
    protected $longitude;

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     */
    private $reference;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     *
     */
    protected $rue;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $numero;

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     *
     */
    protected $localite;

    /**
     * @ORM\OneToMany(targetEntity="DateNettoyage", mappedBy="avaloirNew", cascade={"persist", "remove"}))
     * @ORM\OrderBy({"jour"="DESC"})
     */
    private $dates;

    /**
     *
     * @Vich\UploadableField(mapping="avaloir_image", fileNameProperty="imageName")
     *
     * @var File|null
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     */
    private $imageName;

    public function __construct()
    {
        $this->dates = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->rue . " " . $this->numero;
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     * @throws \Exception
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(?string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getRue(): ?string
    {
        return $this->rue;
    }

    public function setRue(?string $rue): self
    {
        $this->rue = $rue;

        return $this;
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
    public function setReference($reference): void
    {
        $this->reference = $reference;
    }

    /**
     * @return mixed
     */
    public function getLocalite()
    {
        return $this->localite;
    }

    /**
     * @param mixed $localite
     */
    public function setLocalite($localite): void
    {
        $this->localite = $localite;
    }

    /**
     * @return mixed
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param mixed $latitude
     */
    public function setLatitude($latitude): void
    {
        $this->latitude = $latitude;
    }

    /**
     * @return mixed
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param mixed $longitude
     */
    public function setLongitude($longitude): void
    {
        $this->longitude = $longitude;
    }

    /**
     * @return ?string
     */
    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    /**
     * @param mixed $imageName
     */
    public function setImageName($imageName): void
    {
        $this->imageName = $imageName;
    }

    /**
     * @return Collection|DateNettoyage[]
     */
    public function getDates(): Collection
    {
        return $this->dates;
    }

    public function addDate(DateNettoyage $date): self
    {
        if (!$this->dates->contains($date)) {
            $this->dates[] = $date;
            $date->setAvaloirNew($this);
        }

        return $this;
    }

    public function removeDate(DateNettoyage $date): self
    {
        if ($this->dates->contains($date)) {
            $this->dates->removeElement($date);
            // set the owning side to null (unless already changed)
            if ($date->getAvaloirNew() === $this) {
                $date->setAvaloirNew(null);
            }
        }

        return $this;
    }
}