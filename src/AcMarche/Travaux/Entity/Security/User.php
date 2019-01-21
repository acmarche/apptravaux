<?php

namespace AcMarche\Travaux\Entity\Security;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Travaux\Repository\UserRepository")
 * @ORM\Table(name="users")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="AcMarche\Travaux\Entity\Security\Group", inversedBy="users")
     * @ORM\JoinTable(name="fos_user_group")
     *
     */
    protected $groups;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @ORM\OrderBy({"intitule"="ASC"})
     * @Assert\Length(
     *     min=3
     * )
     * @var string
     */
    protected $nom;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\Length(
     *     min=3
     * )
     * @var string
     */
    protected $prenom;

    /**
     * @var bool|null
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $accord;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $accord_date;

    /**
     * @return string
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * @param string $nom
     */
    public function setNom(string $nom)
    {
        $this->nom = $nom;
    }

    /**
     * @return string
     */
    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    /**
     * @param string $prenom
     */
    public function setPrenom(string $prenom)
    {
        $this->prenom = $prenom;
    }

    /**
     * @return bool|null
     */
    public function getAccord(): ?bool
    {
        return $this->accord;
    }

    /**
     * @param bool|null $accord
     */
    public function setAccord(?bool $accord): void
    {
        $this->accord = $accord;
    }

    /**
     * @return \DateTime|null
     */
    public function getAccordDate(): ?\DateTime
    {
        return $this->accord_date;
    }

    /**
     * @param \DateTime|null $accord_date
     */
    public function setAccordDate(?\DateTime $accord_date): void
    {
        $this->accord_date = $accord_date;
    }

}
