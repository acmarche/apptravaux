<?php

namespace AcMarche\Travaux\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo; // gedmo annotations

/**
 * @ORM\Entity(repositoryClass="AcMarche\Travaux\Repository\SuiviRepository")
 * @ORM\Table(name="suivis")
 */
class Suivi
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    protected $descriptif;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default"=0} )
     */
    protected $smartphone = false;

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
     * @ORM\ManyToOne(targetEntity="AcMarche\Travaux\Entity\Intervention", inversedBy="suivis")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $intervention;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $user_add;

    public function __toString()
    {
        return $this->descriptif;
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
     * Set descriptif
     *
     * @param string $descriptif
     *
     * @return Suivi
     */
    public function setDescriptif($descriptif)
    {
        $this->descriptif = $descriptif;

        return $this;
    }

    /**
     * Get descriptif
     *
     * @return string
     */
    public function getDescriptif()
    {
        return $this->descriptif;
    }

    /**
     * Set smartphone
     *
     * @param boolean $smartphone
     *
     * @return Suivi
     */
    public function setSmartphone($smartphone)
    {
        $this->smartphone = $smartphone;

        return $this;
    }

    /**
     * Get smartphone
     *
     * @return boolean
     */
    public function getSmartphone()
    {
        return $this->smartphone;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Suivi
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
     * @return Suivi
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
     * Set userAdd
     *
     * @param string $userAdd
     *
     * @return Suivi
     */
    public function setUserAdd($userAdd)
    {
        $this->user_add = $userAdd;

        return $this;
    }

    /**
     * Get userAdd
     *
     * @return string
     */
    public function getUserAdd()
    {
        return $this->user_add;
    }

    /**
     * Set intervention
     *
     * @param \AcMarche\Travaux\Entity\Intervention $intervention
     *
     * @return Suivi
     */
    public function setIntervention(\AcMarche\Travaux\Entity\Intervention $intervention)
    {
        $this->intervention = $intervention;

        return $this;
    }

    /**
     * Get intervention
     *
     * @return \AcMarche\Travaux\Entity\Intervention
     */
    public function getIntervention()
    {
        return $this->intervention;
    }
}
