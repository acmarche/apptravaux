<?php

namespace AcMarche\Travaux\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo; // gedmo annotations

/**
 * @ORM\Entity(repositoryClass="AcMarche\Travaux\Repository\EtatRepository")
 * @ORM\Table(name="etat")
 *
 */
class Etat
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
     * @ORM\OneToMany(targetEntity="AcMarche\Travaux\Entity\Intervention", mappedBy="etat")
     *
     */
    private $interventions;

    public function __toString()
    {
        return $this->getIntitule();
    }

    /**
     * STOP
     */

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->interventions = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set intitule
     *
     * @param string $intitule
     *
     * @return Etat
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
     * Set couleur
     *
     * @param string $couleur
     *
     * @return Etat
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
     * @return Etat
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
     * @return Etat
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
     * @return Etat
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
     * @return Etat
     */
    public function addIntervention(\AcMarche\Travaux\Entity\Intervention $intervention)
    {
        $this->interventions[] = $intervention;

        return $this;
    }

    /**
     * Remove intervention
     *
     * @param \AcMarche\Travaux\Entity\Intervention $intervention
     */
    public function removeIntervention(\AcMarche\Travaux\Entity\Intervention $intervention)
    {
        $this->interventions->removeElement($intervention);
    }

    /**
     * Get interventions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInterventions()
    {
        return $this->interventions;
    }
}
