<?php

namespace AcMarche\Travaux\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo; // gedmo annotations

/**
 * @ORM\Entity(repositoryClass="AcMarche\Travaux\Repository\CategorieRepository")
 * @ORM\Table(name="categorie")
 */

class Categorie
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Gedmo\Slug(fields={"intitule"}, updatable=true, separator="_")
     * @ORM\Column(length=62, unique=true)
     */
    private $slugname;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @ORM\OrderBy({"intitule"="ASC"})
     * @Assert\NotBlank()
     */
    protected $intitule;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(name="updated", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updated;

    /**
     * @ORM\OneToMany(targetEntity="AcMarche\Travaux\Entity\Intervention", mappedBy="categorie")
     *
     */
    private $intervention;
    
    public function __toString()
    {
        return $this->intitule;
    }

    /**
     * STOP
     */

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->intervention = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     *
     * @return Categorie
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
     * Set intitule
     *
     * @param string $intitule
     *
     * @return Categorie
     */
    public function setIntitule($intitule)
    {
        $this->intitule = $intitule;

        return $this;
    }

    /**
     * Get intitule
     *
     * @return string
     */
    public function getIntitule()
    {
        return $this->intitule;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Categorie
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return Categorie
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Add intervention
     *
     * @param \AcMarche\Travaux\Entity\Intervention $intervention
     *
     * @return Categorie
     */
    public function addIntervention(\AcMarche\Travaux\Entity\Intervention $intervention)
    {
        $this->intervention[] = $intervention;

        return $this;
    }

    /**
     * Remove intervention
     *
     * @param \AcMarche\Travaux\Entity\Intervention $intervention
     */
    public function removeIntervention(\AcMarche\Travaux\Entity\Intervention $intervention)
    {
        $this->intervention->removeElement($intervention);
    }

    /**
     * Get intervention
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIntervention()
    {
        return $this->intervention;
    }
}
