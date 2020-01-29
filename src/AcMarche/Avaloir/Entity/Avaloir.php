<?php

namespace AcMarche\Avaloir\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Avaloir\Repository\AvaloirRepository")
 * @ORM\Table(name="avaloir")
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
     * @ORM\Column(type="text", nullable=true)
     */
    protected $descriptif;

    /**
     * @ORM\ManyToOne(targetEntity="Rue", inversedBy="avaloirs")
     * @ORM\JoinColumn(nullable=false)
     *
     */
    protected $rue;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescriptif(): ?string
    {
        return $this->descriptif;
    }

    public function setDescriptif(?string $descriptif): self
    {
        $this->descriptif = $descriptif;

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

    public function getRue(): ?Rue
    {
        return $this->rue;
    }

    public function setRue(?Rue $rue): self
    {
        $this->rue = $rue;

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

}
