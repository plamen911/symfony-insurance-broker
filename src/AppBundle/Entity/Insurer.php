<?php
declare(strict_types=1);

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Insurer
 *
 * @ORM\Table(name="insurers")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\InsurerRepository")
 */
class Insurer
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=191, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="long_name", type="string", length=191, unique=true)
     */
    private $longName;

    /**
     * @var string
     *
     * @ORM\Column(name="logo", type="string", length=191, nullable=true)
     */
    private $logo;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_deleted", type="boolean")
     */
    private $isDeleted;

    /**
     * @var ArrayCollection|Policy[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Policy", mappedBy="insurer")
     */
    private $policies;

    /**
     * @var ArrayCollection|TypeOfPolicy[]
     *
     * @ORM\ManyToMany(targetEntity="TypeOfPolicy", inversedBy="insurers")
     * @ORM\JoinTable(name="insurers_policy_types",
     *     joinColumns={@ORM\JoinColumn(name="insurer_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="policy_type_id", referencedColumnName="id")}
     * )
     */
    private $policyTypes;

    /**
     * Insurer constructor.
     */
    public function __construct()
    {
        $this->isDeleted = false;
        $this->policyTypes = new ArrayCollection();
        $this->policies = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Insurer
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set position.
     *
     * @param int $position
     *
     * @return Insurer
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position.
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set isDeleted.
     *
     * @param bool $isDeleted
     *
     * @return Insurer
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * Get isDeleted.
     *
     * @return bool
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @param string $logo
     * @return Insurer
     */
    public function setLogo(string $logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * @return ArrayCollection|TypeOfPolicy[]
     */
    public function getPolicyTypes()
    {
        return $this->policyTypes;
    }

    /**
     * @param TypeOfPolicy $policyType
     * @return Insurer
     */
    public function addPolicyType(TypeOfPolicy $policyType)
    {
        $this->policyTypes[] = $policyType;

        return $this;
    }

    /**
     * @param ArrayCollection|TypeOfPolicy[] $policyTypes
     * @return Insurer
     */
    public function setPolicyTypes($policyTypes)
    {
        $this->policyTypes = $policyTypes;

        return $this;
    }

    /**
     * @return string
     */
    public function getLongName()
    {
        return $this->longName;
    }

    /**
     * @param string $longName
     * @return Insurer
     */
    public function setLongName(string $longName)
    {
        $this->longName = $longName;

        return $this;
    }

    /**
     * @return Policy[]|ArrayCollection
     */
    public function getPolicies()
    {
        return $this->policies;
    }

    /**
     * @param Policy[]|ArrayCollection $policies
     * @return Insurer
     */
    public function setPolicies($policies)
    {
        foreach ($policies as $policy) {
            $this->addPolicy($policy);
        }

        return $this;
    }

    /**
     * @param Policy $policy
     * @return $this
     */
    public function addPolicy(Policy $policy)
    {
        $this->policies->add($policy);
        $policy->setIdNumber($this);

        return $this;
    }
}
