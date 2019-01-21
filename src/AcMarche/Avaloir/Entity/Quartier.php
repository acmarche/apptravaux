<?php

namespace AcMarche\Avaloir\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo; // gedmo annotations

/**
 * @ORM\Entity(repositoryClass="AcMarche\Avaloir\Repository\QuartierRepository")
 * @ORM\Table(name="quartier")
 *
 */

class Quartier
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Gedmo\Slug(fields={"nom"}, updatable=true)
     * @ORM\Column(length=70, unique=true)
     */
    private $slugname;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @ORM\OrderBy({"nom"="ASC"})
     * @Assert\NotBlank()
     */
    protected $nom;

    /**
     * @ORM\OneToMany(targetEntity="Rue", mappedBy="quartier")
     *
     */
    private $rues;
    private $rueids;

    public function __toString()
    {
        return $this->nom;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->rues = new \Doctrine\Common\Collections\ArrayCollection();
        $this->rueids = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function setRueIds($rues)
    {
        $this->rueids = $rues;

        return $this;
    }

    public function getRueIds()
    {
        return $this->rueids;
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
     * Set slugname
     *
     * @param string $slugname
     * @return Quartier
     */
    public function setSlugname($slugname)
    {
        $this->slugname = $slugname;

        return $this;
    }

    /**
     * Get slugname
     *
     * @return string
     */
    public function getSlugname()
    {
        return $this->slugname;
    }

    /**
     * Set nom
     *
     * @param string $nom
     * @return Quartier
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Add rues
     *
     * @param \AcMarche\Avaloir\Entity\Rue $rues
     * @return Quartier
     */
    public function addRue(\AcMarche\Avaloir\Entity\Rue $rues)
    {
        $this->rues[] = $rues;

        return $this;
    }

    /**
     * Remove rues
     *
     * @param \AcMarche\Avaloir\Entity\Rue $rues
     */
    public function removeRue(\AcMarche\Avaloir\Entity\Rue $rues)
    {
        $this->rues->removeElement($rues);
    }

    /**
     * Get rues
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRues()
    {
        return $this->rues;
    }
}
