<?php

namespace AcMarche\Avaloir\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo; // gedmo annotations

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
     * @ORM\Column(type="string", nullable=false)
     * @ORM\OrderBy({"nom"="ASC"})
     * @Assert\NotBlank()
     */
    protected $nom;

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\Avaloir\Entity\Village", inversedBy="rue")
     * @Assert\NotBlank()
     */
    protected $village;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $code;

    /**
     * @ORM\OneToMany(targetEntity="Avaloir", mappedBy="rue")
     *
     */
    private $avaloirs;

    /**
     * @ORM\ManyToOne(targetEntity="Quartier", inversedBy="rues", cascade={"remove"})
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $quartier;

    public function __toString()
    {
        return $this->nom;
    }

    /**
     * STOP
     */

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->avaloirs = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set nom
     *
     * @param string $nom
     *
     * @return Rue
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
     * Set code
     *
     * @param integer $code
     *
     * @return Rue
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set village
     *
     * @param \AcMarche\Avaloir\Entity\Village $village
     *
     * @return Rue
     */
    public function setVillage(\AcMarche\Avaloir\Entity\Village $village = null)
    {
        $this->village = $village;

        return $this;
    }

    /**
     * Get village
     *
     * @return \AcMarche\Avaloir\Entity\Village
     */
    public function getVillage()
    {
        return $this->village;
    }

    /**
     * Add avaloir
     *
     * @param \AcMarche\Avaloir\Entity\Avaloir $avaloir
     *
     * @return Rue
     */
    public function addAvaloir(\AcMarche\Avaloir\Entity\Avaloir $avaloir)
    {
        $this->avaloirs[] = $avaloir;

        return $this;
    }

    /**
     * Remove avaloir
     *
     * @param \AcMarche\Avaloir\Entity\Avaloir $avaloir
     */
    public function removeAvaloir(\AcMarche\Avaloir\Entity\Avaloir $avaloir)
    {
        $this->avaloirs->removeElement($avaloir);
    }

    /**
     * Get avaloirs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAvaloirs()
    {
        return $this->avaloirs;
    }

    /**
     * Set quartier
     *
     * @param \AcMarche\Avaloir\Entity\Quartier $quartier
     *
     * @return Rue
     */
    public function setQuartier(\AcMarche\Avaloir\Entity\Quartier $quartier = null)
    {
        $this->quartier = $quartier;

        return $this;
    }

    /**
     * Get quartier
     *
     * @return \AcMarche\Avaloir\Entity\Quartier
     */
    public function getQuartier()
    {
        return $this->quartier;
    }
}
