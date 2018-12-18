<?php
declare(strict_types=1);

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * PolicyType
 *
 * @ORM\Table(name="policy_types")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TypeOfPolicyRepository")
 */
class TypeOfPolicy
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
     * @var int
     *
     * @ORM\Column(name="position", type="integer", nullable=true)
     */
    private $position;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_deleted", type="boolean", nullable=true)
     */
    private $isDeleted;

    /**
     * @var ArrayCollection|Insurer[]
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Insurer", mappedBy="policyTypes")
     */
    private $insurers;

    /**
     * @var ArrayCollection|Policy[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Policy", mappedBy="policyType")
     */
    private $policies;

    /**
     * PolicyType constructor.
     */
    public function __construct()
    {
        $this->isDeleted = false;
        $this->insurers = new ArrayCollection();
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
     * @return TypeOfPolicy
     */
    public function setName(string $name)
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
     * Set name.
     *
     * @param $longName
     * @return TypeOfPolicy
     */
    public function setLongName(string $longName)
    {
        $this->longName = $longName;

        return $this;
    }

    /**
     * Get long name.
     *
     * @return string
     */
    public function getLongName()
    {
        return $this->longName;
    }

    /**
     * Set position.
     *
     * @param int $position
     *
     * @return TypeOfPolicy
     */
    public function setPosition(int $position)
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
     * @return bool
     */
    public function isDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * @param bool $isDeleted
     * @return TypeOfPolicy
     */
    public function setIsDeleted(bool $isDeleted)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * @return Insurer[]|ArrayCollection
     */
    public function getInsurers()
    {
        return $this->insurers;
    }

    /**
     * @param Insurer[]|ArrayCollection $insurers
     * @return TypeOfPolicy
     */
    public function setInsurers($insurers)
    {
        $this->insurers = $insurers;

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
     * @return TypeOfPolicy
     */
    public function setPolicies($policies): TypeOfPolicy
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
        $policy->setPolicyType($this);

        return $this;
    }
}
