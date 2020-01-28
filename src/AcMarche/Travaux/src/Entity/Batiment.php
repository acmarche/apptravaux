<?php

namespace AcMarche\Travaux\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Travaux\Repository\BatimentRepository")
 * @ORM\Table(name="batiment")
 *
 */

class Batiment
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @ORM\OrderBy({"intitule"="ASC"})
     * @Assert\NotBlank()
     */
    protected $intitule;

    /**
     * @ORM\OneToMany(targetEntity="AcMarche\Travaux\Entity\Intervention", mappedBy="batiment")
     *
     */
    private $intervention;

    public function __construct()
    {
        $this->intervention = new ArrayCollection();
    }
    
    public function __toString()
    {
        return $this->intitule;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIntitule(): ?string
    {
        return $this->intitule;
    }

    public function setIntitule(string $intitule): self
    {
        $this->intitule = $intitule;

        return $this;
    }

    /**
     * @return Collection|Intervention[]
     */
    public function getIntervention(): Collection
    {
        return $this->intervention;
    }

    public function addIntervention(Intervention $intervention): self
    {
        if (!$this->intervention->contains($intervention)) {
            $this->intervention[] = $intervention;
            $intervention->setBatiment($this);
        }

        return $this;
    }

    public function removeIntervention(Intervention $intervention): self
    {
        if ($this->intervention->contains($intervention)) {
            $this->intervention->removeElement($intervention);
            // set the owning side to null (unless already changed)
            if ($intervention->getBatiment() === $this) {
                $intervention->setBatiment(null);
            }
        }

        return $this;
    }

    /**
     * STOP
     */

}
