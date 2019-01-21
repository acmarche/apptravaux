<?php

namespace AcMarche\Avaloir\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo; // gedmo annotations

/**
 * @ORM\Entity(repositoryClass="AcMarche\Avaloir\Repository\AvaloirRepository")
 * @ORM\Table(name="avaloir")
 *
 */
class Avaloir
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $descriptif;

    /**
     * @ORM\ManyToOne(targetEntity="Rue", inversedBy="avaloirs")
     * @ORM\JoinColumn(nullable=false)
     *
     */
    protected $rue;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $numero;

    /**
     * @ORM\OneToMany(targetEntity="DateNettoyage", mappedBy="avaloir", cascade={"persist", "remove"}))
     * @ORM\OrderBy({"jour"="DESC"})
     */
    private $dates;

    /**
     * @ORM\Column(type="date", nullable=true, options={"comment" = "date de rappel"})
     * @Assert\DateTime()
     */
    protected $date_rappel;

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
     * Utilise pour l'ajout d'un avoloir (ajax)
     * @var
     */
    private $rueId;

    /**
     * @return mixed
     */
    public function getRueId()
    {
        return $this->rueId;
    }

    /**
     * @param mixed $rueId
     */
    public function setRueId($rueId)
    {
        $this->rueId = $rueId;
    }

    public function __toString()
    {
        return $this->rue . " " . $this->numero;
    }

    /**
     * STOP
     */

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->dates = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set descriptif
     *
     * @param string $descriptif
     *
     * @return Avaloir
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
     * Set numero
     *
     * @param string $numero
     *
     * @return Avaloir
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set dateRappel
     *
     * @param \DateTime $dateRappel
     *
     * @return Avaloir
     */
    public function setDateRappel($dateRappel)
    {
        $this->date_rappel = $dateRappel;

        return $this;
    }

    /**
     * Get dateRappel
     *
     * @return \DateTime
     */
    public function getDateRappel()
    {
        return $this->date_rappel;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Avaloir
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
     * @return Avaloir
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
     * Set rue
     *
     * @param \AcMarche\Avaloir\Entity\Rue $rue
     *
     * @return Avaloir
     */
    public function setRue(\AcMarche\Avaloir\Entity\Rue $rue)
    {
        $this->rue = $rue;

        return $this;
    }

    /**
     * Get rue
     *
     * @return \AcMarche\Avaloir\Entity\Rue
     */
    public function getRue()
    {
        return $this->rue;
    }

    /**
     * Add date
     *
     * @param \AcMarche\Avaloir\Entity\DateNettoyage $date
     *
     * @return Avaloir
     */
    public function addDate(\AcMarche\Avaloir\Entity\DateNettoyage $date)
    {
        $this->dates[] = $date;

        return $this;
    }

    /**
     * Remove date
     *
     * @param \AcMarche\Avaloir\Entity\DateNettoyage $date
     */
    public function removeDate(\AcMarche\Avaloir\Entity\DateNettoyage $date)
    {
        $this->dates->removeElement($date);
    }

    /**
     * Get dates
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDates()
    {
        return $this->dates;
    }
}
