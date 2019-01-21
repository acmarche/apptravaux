<?php

namespace AcMarche\Avaloir\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo; // gedmo annotations

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
     * @Gedmo\Slug(fields={"nom"}, updatable=true)
     * @ORM\Column(length=62, unique=true)
     */
    private $slugname;

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
     * @ORM\OneToMany(targetEntity="AcMarche\Avaloir\Entity\Rue", mappedBy="village")
     *
     */
    private $rue;
    
    public function __toString()
    {
        return $this->getNom();
    }

    /**
     * STOP
     */

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->rue = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Village
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
     *
     * @return Village
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
     * Set couleur
     *
     * @param string $couleur
     *
     * @return Village
     */
    public function setCouleur($couleur)
    {
        $this->couleur = $couleur;

        return $this;
    }

    /**
     * Get couleur
     *
     * @return string
     */
    public function getCouleur()
    {
        return $this->couleur;
    }

    /**
     * Set icone
     *
     * @param string $icone
     *
     * @return Village
     */
    public function setIcone($icone)
    {
        $this->icone = $icone;

        return $this;
    }

    /**
     * Get icone
     *
     * @return string
     */
    public function getIcone()
    {
        return $this->icone;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Village
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
     * @return Village
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
     * Add rue
     *
     * @param \AcMarche\Avaloir\Entity\Rue $rue
     *
     * @return Village
     */
    public function addRue(\AcMarche\Avaloir\Entity\Rue $rue)
    {
        $this->rue[] = $rue;

        return $this;
    }

    /**
     * Remove rue
     *
     * @param \AcMarche\Avaloir\Entity\Rue $rue
     */
    public function removeRue(\AcMarche\Avaloir\Entity\Rue $rue)
    {
        $this->rue->removeElement($rue);
    }

    /**
     * Get rue
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRue()
    {
        return $this->rue;
    }
}
