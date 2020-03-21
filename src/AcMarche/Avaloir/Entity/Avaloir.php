<?php

namespace AcMarche\Avaloir\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Avaloir\Repository\AvaloirRepository")
 * @ORM\Table(name="avaloir")
 * @Vich\Uploadable
 *
 */
class Avaloir implements TimestampableInterface
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=8, nullable=false)
     */
    protected $latitude;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=8, nullable=false)
     */
    protected $longitude;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\ManyToOne(targetEntity="Rue", inversedBy="avaloirs")
     * @ORM\JoinColumn(nullable=true)
     *
     */
    protected $rueEntity;

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     *
     */
    protected $rue;

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     *
     */
    protected $localite;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $numero;

    /**
     * @ORM\OneToMany(targetEntity="DateNettoyage", mappedBy="avaloir", cascade={"persist", "remove"}))
     * @ORM\OrderBy({"jour"="DESC"})
     */
    private $dates;

    /**
     * @ORM\Column(type="date", nullable=true, options={"comment" = "date de rappel"})
     * @Assert\DateTime()
     */
    protected $date_rappel;

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

    /**
     * Utilise pour l'ajout d'un avoloir (ajax)
     * @var
     */
    private $rueId;

    public function __construct()
    {
        $this->dates = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getRueId()
    {
        return $this->rueId;
    }

    /**
     * @param mixed $rueId
     */
    public function setRueId($rueId)
    {
        $this->rueId = $rueId;
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

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): self
    {
        $this->longitude = $longitude;

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

    public function getLocalite(): ?string
    {
        return $this->localite;
    }

    public function setLocalite(?string $localite): self
    {
        $this->localite = $localite;

        return $this;
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

    public function getDateRappel(): ?\DateTimeInterface
    {
        return $this->date_rappel;
    }

    public function setDateRappel(?\DateTimeInterface $date_rappel): self
    {
        $this->date_rappel = $date_rappel;

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): self
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getRueEntity(): ?Rue
    {
        return $this->rueEntity;
    }

    public function setRueEntity(?Rue $rueEntity): self
    {
        $this->rueEntity = $rueEntity;

        return $this;
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
            $date->setAvaloir($this);
        }

        return $this;
    }

    public function removeDate(DateNettoyage $date): self
    {
        if ($this->dates->contains($date)) {
            $this->dates->removeElement($date);
            // set the owning side to null (unless already changed)
            if ($date->getAvaloir() === $this) {
                $date->setAvaloir(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }



}
