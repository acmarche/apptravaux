<?php

namespace AcMarche\Avaloir\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo; // gedmo annotations

/**
 * @ORM\Entity(repositoryClass="AcMarche\Avaloir\Repository\DateNettoyageRepository")
 * @ORM\Table(name="dates")
 *
 */

class DateNettoyage
{

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
     * Set jour
     *
     * @param \DateTime $jour
     * @return DateNettoyage
     */
    public function setJour($jour)
    {
        $this->jour = $jour;

        return $this;
    }

    /**
     * Get jour
     *
     * @return \DateTime
     */
    public function getJour()
    {
        return $this->jour;
    }

    /**
     * Set avaloir
     *
     * @param string $avaloir
     * @return DateNettoyage
     */
    public function setAvaloir($avaloir)
    {
        $this->avaloir = $avaloir;

        return $this;
    }

    /**
     * Get avaloir
     *
     * @return string
     */
    public function getAvaloir()
    {
        return $this->avaloir;
    }
}
