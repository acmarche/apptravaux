<?php

namespace AcMarche\Travaux\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo; // gedmo annotations

/**
 * @ORM\Entity(repositoryClass="AcMarche\Travaux\Repository\InterventionRepository")
 * @ORM\Table(name="intervention")
 *
 */
class Intervention
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $old_id;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @ORM\OrderBy({"intitule"="ASC"})
     * @Assert\NotBlank()
     */
    protected $intitule;

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\Travaux\Entity\Etat", inversedBy="interventions")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $etat;

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\Travaux\Entity\Priorite", inversedBy="interventions")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $priorite;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default"=0} )
     */
    protected $transmis = false;

    /**
     * @ORM\Column(type="date", nullable=false)
     */
    protected $date_introduction;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $date_rappel;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $date_execution;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    protected $descriptif;

    /********************
     * AFFECTATION
     * ******************
     */

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $affectation;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default"=0} )
     */
    protected $affecte_prive = false;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $soumis_le;

    /********************
     * SOLUTION
     * ******************
     */

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $solution;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    protected $date_solution;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default"=0} )
     */
    protected $archive = false;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2, nullable=true)
     */
    protected $cout_main;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=2, nullable=true)
     */
    protected $cout_materiel;

    /**
     *
     * @ORM\Column(type="date", nullable=true)
     */
    protected $date_validation;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default"=0} )
     */
    protected $smartphone = false;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $user_add;

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
     * @ORM\ManyToOne(targetEntity="Domaine", inversedBy="intervention")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    protected $domaine;

    /**
     * @ORM\ManyToOne(targetEntity="Batiment", inversedBy="intervention")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    protected $batiment;

    /**
     * @ORM\ManyToOne(targetEntity="Service", inversedBy="intervention")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    protected $service;

    /**
     * @ORM\ManyToOne(targetEntity="Categorie", inversedBy="intervention")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $categorie;

    /**
     * @ORM\OneToMany(targetEntity="Document", mappedBy="intervention", cascade="remove")
     *
     */
    protected $documents;

    /**
     * @ORM\OneToMany(targetEntity="AcMarche\Travaux\Entity\Suivi", mappedBy="intervention", cascade="remove")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $suivis;

    /**
     * This property is used by the marking store
     * @ORM\Column(type="json_array", nullable=true)
     */
    public $currentPlace;

    public function __toString()
    {
        return $this->intitule;
    }

    protected $lastSuivi;

    /**
     * @return mixed
     */
    public function getLastSuivi()
    {
        return $this->lastSuivi;
    }

    /**
     * @param mixed $lastSuivis
     */
    public function setLastSuivi(Suivi $lastSuivi)
    {
        $this->lastSuivi = $lastSuivi;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->date_introduction = new \DateTime();
        $this->documents = new \Doctrine\Common\Collections\ArrayCollection();
        $this->suivis = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /***
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
     * Set oldId
     *
     * @param integer $oldId
     *
     * @return Intervention
     */
    public function setOldId($oldId)
    {
        $this->old_id = $oldId;

        return $this;
    }

    /**
     * Get oldId
     *
     * @return integer
     */
    public function getOldId()
    {
        return $this->old_id;
    }

    /**
     * Set intitule
     *
     * @param string $intitule
     *
     * @return Intervention
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
     * Set transmis
     *
     * @param boolean $transmis
     *
     * @return Intervention
     */
    public function setTransmis($transmis)
    {
        $this->transmis = $transmis;

        return $this;
    }

    /**
     * Get transmis
     *
     * @return boolean
     */
    public function getTransmis()
    {
        return $this->transmis;
    }

    /**
     * Set dateIntroduction
     *
     * @param \DateTime $dateIntroduction
     *
     * @return Intervention
     */
    public function setDateIntroduction($dateIntroduction)
    {
        $this->date_introduction = $dateIntroduction;

        return $this;
    }

    /**
     * Get dateIntroduction
     *
     * @return \DateTime
     */
    public function getDateIntroduction()
    {
        return $this->date_introduction;
    }

    /**
     * Set dateRappel
     *
     * @param \DateTime $dateRappel
     *
     * @return Intervention
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
     * Set dateExecution
     *
     * @param \DateTime $dateExecution
     *
     * @return Intervention
     */
    public function setDateExecution($dateExecution)
    {
        $this->date_execution = $dateExecution;

        return $this;
    }

    /**
     * Get dateExecution
     *
     * @return \DateTime
     */
    public function getDateExecution()
    {
        return $this->date_execution;
    }

    /**
     * Set descriptif
     *
     * @param string $descriptif
     *
     * @return Intervention
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
     * Set affectation
     *
     * @param string $affectation
     *
     * @return Intervention
     */
    public function setAffectation($affectation)
    {
        $this->affectation = $affectation;

        return $this;
    }

    /**
     * Get affectation
     *
     * @return string
     */
    public function getAffectation()
    {
        return $this->affectation;
    }

    /**
     * Set affectePrive
     *
     * @param boolean $affectePrive
     *
     * @return Intervention
     */
    public function setAffectePrive($affectePrive)
    {
        $this->affecte_prive = $affectePrive;

        return $this;
    }

    /**
     * Get affectePrive
     *
     * @return boolean
     */
    public function getAffectePrive()
    {
        return $this->affecte_prive;
    }

    /**
     * Set soumisLe
     *
     * @param \DateTime $soumisLe
     *
     * @return Intervention
     */
    public function setSoumisLe($soumisLe)
    {
        $this->soumis_le = $soumisLe;

        return $this;
    }

    /**
     * Get soumisLe
     *
     * @return \DateTime
     */
    public function getSoumisLe()
    {
        return $this->soumis_le;
    }

    /**
     * Set solution
     *
     * @param string $solution
     *
     * @return Intervention
     */
    public function setSolution($solution)
    {
        $this->solution = $solution;

        return $this;
    }

    /**
     * Get solution
     *
     * @return string
     */
    public function getSolution()
    {
        return $this->solution;
    }

    /**
     * Set dateSolution
     *
     * @param \DateTime $dateSolution
     *
     * @return Intervention
     */
    public function setDateSolution($dateSolution)
    {
        $this->date_solution = $dateSolution;

        return $this;
    }

    /**
     * Get dateSolution
     *
     * @return \DateTime
     */
    public function getDateSolution()
    {
        return $this->date_solution;
    }

    /**
     * Set archive
     *
     * @param boolean $archive
     *
     * @return Intervention
     */
    public function setArchive($archive)
    {
        $this->archive = $archive;

        return $this;
    }

    /**
     * Get archive
     *
     * @return boolean
     */
    public function getArchive()
    {
        return $this->archive;
    }

    /**
     * Set coutMain
     *
     * @param string $coutMain
     *
     * @return Intervention
     */
    public function setCoutMain($coutMain)
    {
        $this->cout_main = $coutMain;

        return $this;
    }

    /**
     * Get coutMain
     *
     * @return string
     */
    public function getCoutMain()
    {
        return $this->cout_main;
    }

    /**
     * Set coutMateriel
     *
     * @param string $coutMateriel
     *
     * @return Intervention
     */
    public function setCoutMateriel($coutMateriel)
    {
        $this->cout_materiel = $coutMateriel;

        return $this;
    }

    /**
     * Get coutMateriel
     *
     * @return string
     */
    public function getCoutMateriel()
    {
        return $this->cout_materiel;
    }

    /**
     * Set dateValidation
     *
     * @param \DateTime $dateValidation
     *
     * @return Intervention
     */
    public function setDateValidation($dateValidation)
    {
        $this->date_validation = $dateValidation;

        return $this;
    }

    /**
     * Get dateValidation
     *
     * @return \DateTime
     */
    public function getDateValidation()
    {
        return $this->date_validation;
    }

    /**
     * Set smartphone
     *
     * @param boolean $smartphone
     *
     * @return Intervention
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
     * Set userAdd
     *
     * @param string $userAdd
     *
     * @return Intervention
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
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Intervention
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
     * @return Intervention
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
     * Set currentPlace
     *
     * @param array $currentPlace
     *
     * @return Intervention
     */
    public function setCurrentPlace($currentPlace)
    {
        $this->currentPlace = $currentPlace;

        return $this;
    }

    /**
     * Get currentPlace
     *
     * @return array
     */
    public function getCurrentPlace()
    {
        return $this->currentPlace;
    }

    /**
     * Set etat
     *
     * @param \AcMarche\Travaux\Entity\Etat $etat
     *
     * @return Intervention
     */
    public function setEtat(\AcMarche\Travaux\Entity\Etat $etat)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get etat
     *
     * @return \AcMarche\Travaux\Entity\Etat
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * Set priorite
     *
     * @param \AcMarche\Travaux\Entity\Priorite $priorite
     *
     * @return Intervention
     */
    public function setPriorite(\AcMarche\Travaux\Entity\Priorite $priorite)
    {
        $this->priorite = $priorite;

        return $this;
    }

    /**
     * Get priorite
     *
     * @return \AcMarche\Travaux\Entity\Priorite
     */
    public function getPriorite()
    {
        return $this->priorite;
    }

    /**
     * Set domaine
     *
     * @param \AcMarche\Travaux\Entity\Domaine $domaine
     *
     * @return Intervention
     */
    public function setDomaine(\AcMarche\Travaux\Entity\Domaine $domaine = null)
    {
        $this->domaine = $domaine;

        return $this;
    }

    /**
     * Get domaine
     *
     * @return \AcMarche\Travaux\Entity\Domaine
     */
    public function getDomaine()
    {
        return $this->domaine;
    }

    /**
     * Set batiment
     *
     * @param \AcMarche\Travaux\Entity\Batiment $batiment
     *
     * @return Intervention
     */
    public function setBatiment(\AcMarche\Travaux\Entity\Batiment $batiment = null)
    {
        $this->batiment = $batiment;

        return $this;
    }

    /**
     * Get batiment
     *
     * @return \AcMarche\Travaux\Entity\Batiment
     */
    public function getBatiment()
    {
        return $this->batiment;
    }

    /**
     * Set service
     *
     * @param \AcMarche\Travaux\Entity\Service $service
     *
     * @return Intervention
     */
    public function setService(\AcMarche\Travaux\Entity\Service $service = null)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * Get service
     *
     * @return \AcMarche\Travaux\Entity\Service
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Set categorie
     *
     * @param \AcMarche\Travaux\Entity\Categorie $categorie
     *
     * @return Intervention
     */
    public function setCategorie(\AcMarche\Travaux\Entity\Categorie $categorie)
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Get categorie
     *
     * @return \AcMarche\Travaux\Entity\Categorie
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * Add document
     *
     * @param \AcMarche\Travaux\Entity\Document $document
     *
     * @return Intervention
     */
    public function addDocument(\AcMarche\Travaux\Entity\Document $document)
    {
        $this->documents[] = $document;

        return $this;
    }

    /**
     * Remove document
     *
     * @param \AcMarche\Travaux\Entity\Document $document
     */
    public function removeDocument(\AcMarche\Travaux\Entity\Document $document)
    {
        $this->documents->removeElement($document);
    }

    /**
     * Get documents
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * Add suivi
     *
     * @param \AcMarche\Travaux\Entity\Suivi $suivi
     *
     * @return Intervention
     */
    public function addSuivi(\AcMarche\Travaux\Entity\Suivi $suivi)
    {
        $this->suivis[] = $suivi;

        return $this;
    }

    /**
     * Remove suivi
     *
     * @param \AcMarche\Travaux\Entity\Suivi $suivi
     */
    public function removeSuivi(\AcMarche\Travaux\Entity\Suivi $suivi)
    {
        $this->suivis->removeElement($suivi);
    }

    /**
     * Get suivis
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSuivis()
    {
        return $this->suivis;
    }
}
