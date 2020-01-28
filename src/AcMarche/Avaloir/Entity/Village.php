<?php

namespace AcMarche\Avaloir\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Avaloir\Repository\VillageRepository")
 * @ORM\Table(name="village")
 *
 */
class Village
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @ORM\OrderBy({"nom"="ASC"})
     * @Assert\NotBlank()
     */
    protected $nom;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     *
     */
    private $couleur;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=30, nullable=true)
     *
     */
    private $icone;

    /**
     * @ORM\OneToMany(targetEntity="AcMarche\Avaloir\Entity\Rue", mappedBy="village")
     *
     */
    private $rue;

    public function __construct()
    {
        $this->rue = new ArrayCollection();
    }
    
    public function __toString()
    {
        return $this->getNom();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getCouleur(): ?string
    {
        return $this->couleur;
    }

    public function setCouleur(?string $couleur): self
    {
        $this->couleur = $couleur;

        return $this;
    }

    public function getIcone(): ?string
    {
        return $this->icone;
    }

    public function setIcone(?string $icone): self
    {
        $this->icone = $icone;

        return $this;
    }

    /**
     * @return Collection|Rue[]
     */
    public function getRue(): Collection
    {
        return $this->rue;
    }

    public function addRue(Rue $rue): self
    {
        if (!$this->rue->contains($rue)) {
            $this->rue[] = $rue;
            $rue->setVillage($this);
        }

        return $this;
    }

    public function removeRue(Rue $rue): self
    {
        if ($this->rue->contains($rue)) {
            $this->rue->removeElement($rue);
            // set the owning side to null (unless already changed)
            if ($rue->getVillage() === $this) {
                $rue->setVillage(null);
            }
        }

        return $this;
    }

    /**
     * STOP
     */

}
