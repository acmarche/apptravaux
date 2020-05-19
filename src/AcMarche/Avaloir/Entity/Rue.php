<?php

namespace AcMarche\Avaloir\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Avaloir\Repository\RueRepository")
 * @ORM\Table(name="rue")
 *
 */
class Rue
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     * @ORM\OrderBy({"nom"="ASC"})
     * @Assert\NotBlank()
     */
    protected $nom;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank()
     */
    protected $village;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $code;

    /**
     * @ORM\ManyToOne(targetEntity="Quartier", inversedBy="rues", cascade={"remove"})
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $quartier;

    public function __construct()
    {
        $this->avaloirs = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->nom;
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

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function setCode(?int $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getQuartier(): ?Quartier
    {
        return $this->quartier;
    }

    public function setQuartier(?Quartier $quartier): self
    {
        $this->quartier = $quartier;

        return $this;
    }

    public function getVillage(): ?string
    {
        return $this->village;
    }

    public function setVillage(string $village): self
    {
        $this->village = $village;

        return $this;
    }

}
