<?php

namespace AcMarche\Avaloir\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Avaloir\Repository\DateNettoyageRepository")
 * @ORM\Table(name="dates")
 *
 */
class DateNettoyage implements TimestampableInterface
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="date", nullable=false)
     */
    protected $jour;

    /**
     * @ORM\ManyToOne(targetEntity="Avaloir", inversedBy="dates")
     * @ORM\JoinColumn(nullable=false)
     *
     */
    protected $avaloir;

    /**
     * pour ajouter une date a chaque rue
     * @var
     */
    protected $quartier;

    public function __toString()
    {
        $txt = $this->getJour()->format('d-m-Y');
        return $txt;
    }

    /**
     * @return mixed
     */
    public function getQuartier()
    {
        return $this->quartier;
    }

    /**
     * @param mixed $quartier
     */
    public function setQuartier($quartier)
    {
        $this->quartier = $quartier;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJour(): ?\DateTimeInterface
    {
        return $this->jour;
    }

    public function setJour(\DateTimeInterface $jour): self
    {
        $this->jour = $jour;

        return $this;
    }

    public function getAvaloir(): ?Avaloir
    {
        return $this->avaloir;
    }

    public function setAvaloir(?Avaloir $avaloir): self
    {
        $this->avaloir = $avaloir;

        return $this;
    }

    /**
     * STOP
     */

}
