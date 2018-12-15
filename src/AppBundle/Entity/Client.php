<?php
declare(strict_types=1);

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Client
 *
 * @ORM\Table(name="clients")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ClientRepository")
 * @UniqueEntity(fields="idNumber", message="Вече съществува клиент с този ЕГН.")
 */
class Client
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
     * @var string|null
     *
     * @ORM\Column(name="first_name", type="string", length=191, nullable=true)
     * @Assert\NotBlank(message="Име е задължително поле.")
     */
    private $firstName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="middle_name", type="string", length=191, nullable=true)
     */
    private $middleName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="last_name", type="string", length=191, nullable=true)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="id_number", type="string", length=191, unique=true)
     * @Assert\NotBlank(message="ЕГН е задължително поле.")
     */
    private $idNumber;

    /**
     * @var string|null
     *
     * @ORM\Column(name="city", type="string", length=191, nullable=true)
     */
    private $city;

    /**
     * @var string|null
     *
     * @ORM\Column(name="street", type="string", length=191, nullable=true)
     */
    private $street;

    /**
     * @var string|null
     *
     * @ORM\Column(name="phone", type="string", length=191, nullable=true)
     */
    private $phone;

    /**
     * @var string|null
     *
     * @ORM\Column(name="phone2", type="string", length=191, nullable=true)
     */
    private $phone2;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=191, nullable=true)
     * @Assert\NotBlank(message="И-мейл е задължително поле.")
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email2", type="string", length=191, nullable=true)
     */
    private $email2;

    /**
     * @var string|null
     *
     * @ORM\Column(name="notes", type="text", nullable=true)
     */
    private $notes;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_deleted", type="boolean", nullable=true)
     */
    private $isDeleted;

    /**
     * @var ArrayCollection|Policy[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Policy", mappedBy="owner")
     */
    private $ownerPolicies;

    /**
     * @var ArrayCollection|Policy[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Policy", mappedBy="representative")
     */
    private $representativePolicies;

    /**
     * @var ArrayCollection|Car[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Car", mappedBy="owner")
     */
    private $ownerCars;

    /**
     * @var ArrayCollection|Car[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Car", mappedBy="representative")
     */
    private $representativeCars;

    /**
     * Client constructor.
     */
    public function __construct()
    {
        $this->isDeleted = false;
        $this->ownerPolicies = new ArrayCollection();
        $this->representativePolicies = new ArrayCollection();
        $this->ownerCars = new ArrayCollection();
        $this->representativeCars = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
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
     * Set firstName.
     *
     * @param string|null $firstName
     *
     * @return Client
     */
    public function setFirstName($firstName = null)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName.
     *
     * @return string|null
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set middleName.
     *
     * @param string|null $middleName
     *
     * @return Client
     */
    public function setMiddleName($middleName = null)
    {
        $this->middleName = $middleName;

        return $this;
    }

    /**
     * Get middleName.
     *
     * @return string|null
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * Set lastName.
     *
     * @param string|null $lastName
     *
     * @return Client
     */
    public function setLastName($lastName = null)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName.
     *
     * @return string|null
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set idNumber.
     *
     * @param string $idNumber
     *
     * @return Client
     */
    public function setIdNumber($idNumber)
    {
        $this->idNumber = $idNumber;

        return $this;
    }

    /**
     * Get idNumber.
     *
     * @return string
     */
    public function getIdNumber()
    {
        return $this->idNumber;
    }

    /**
     * Set city.
     *
     * @param string|null $city
     *
     * @return Client
     */
    public function setCity($city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city.
     *
     * @return string|null
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set street.
     *
     * @param string|null $street
     *
     * @return Client
     */
    public function setStreet($street = null)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Get street.
     *
     * @return string|null
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set phone.
     *
     * @param string|null $phone
     *
     * @return Client
     */
    public function setPhone($phone = null)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone.
     *
     * @return string|null
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set phone2.
     *
     * @param string|null $phone2
     *
     * @return Client
     */
    public function setPhone2($phone2 = null)
    {
        $this->phone2 = $phone2;

        return $this;
    }

    /**
     * Get phone2.
     *
     * @return string|null
     */
    public function getPhone2()
    {
        return $this->phone2;
    }

    /**
     * Set email.
     *
     * @param string|null $email
     *
     * @return Client
     */
    public function setEmail($email = null)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string|null
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email2.
     *
     * @param string|null $email2
     *
     * @return Client
     */
    public function setEmail2($email2 = null)
    {
        $this->email2 = $email2;

        return $this;
    }

    /**
     * Get email2.
     *
     * @return string|null
     */
    public function getEmail2()
    {
        return $this->email2;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Client
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt.
     *
     * @param \DateTime $updatedAt
     *
     * @return Client
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    /**
     * @param bool $isDeleted
     */
    public function setIsDeleted(bool $isDeleted): void
    {
        $this->isDeleted = $isDeleted;
    }

    /**
     * @return Policy[]|ArrayCollection
     */
    public function getOwnerPolicies()
    {
        return $this->ownerPolicies;
    }

    /**
     * @param Policy[]|ArrayCollection $ownerPolicies
     * @return Client
     */
    public function setOwnerPolicies($ownerPolicies): Client
    {
        foreach ($ownerPolicies as $ownerPolicy) {
            $this->addOwnerPolicy($ownerPolicy);
        }

        return $this;
    }

    /**
     * @param Policy $policy
     * @return $this
     */
    public function addOwnerPolicy(Policy $policy)
    {
        if (!$this->ownerPolicies->contains($policy)) {
            $this->ownerPolicies->add($policy);
            $policy->setOwner($this);
        }

        return $this;
    }

    /**
     * @return Policy[]|ArrayCollection
     */
    public function getRepresentativePolicies()
    {
        return $this->representativePolicies;
    }

    /**
     * @param Policy[]|ArrayCollection $representativePolicies
     * @return Client
     */
    public function setRepresentativePolicies($representativePolicies)
    {
        foreach ($representativePolicies as $representativePolicy) {
            $this->addRepresentativePolicy($representativePolicy);
        }

        return $this;
    }

    /**
     * @param Policy $policy
     * @return Client
     */
    public function addRepresentativePolicy(Policy $policy)
    {
        if (!$this->representativePolicies->contains($policy)) {
            $this->representativePolicies->add($policy);
            $policy->setRepresentative($this);
        }

        return $this;
    }

    /**
     * @return Car[]|ArrayCollection
     */
    public function getOwnerCars()
    {
        return $this->ownerCars;
    }

    /**
     * @param Car[]|ArrayCollection $ownerCars
     * @return Client
     */
    public function setOwnerCars($ownerCars)
    {
        foreach ($ownerCars as $car) {
            $this->addOwnerCar($car);
        }

        return $this;
    }

    /**
     * @param Car $car
     * @return Client
     */
    public function addOwnerCar(Car $car)
    {
        if (!$this->ownerCars->contains($car)) {
            $this->ownerCars->add($car);
            $car->setOwner($this);
        }

        return $this;
    }

    /**
     * @return Car[]|ArrayCollection
     */
    public function getRepresentativeCars()
    {
        return $this->representativeCars;
    }

    /**
     * @param Car[]|ArrayCollection $representativeCars
     * @return Client
     */
    public function setRepresentativeCars($representativeCars)
    {
        foreach ($representativeCars as $car) {
            $this->addRepresentativeCar($car);
        }

        return $this;
    }

    /**
     * @param Car $car
     * @return Client
     */
    public function addRepresentativeCar(Car $car)
    {
        if (!$this->representativeCars->contains($car)) {
            $this->representativeCars->add($car);
            $car->setRepresentative($this);
        }

        return $this;
    }

    /**
     * @param Car $car
     * @return Client
     */
    public function removeRepresentativeCar(Car $car)
    {
        if ($this->representativeCars->contains($car)) {
            $this->representativeCars->removeElement($car);
            $car->setRepresentative(null);
        }

        return $this;
    }

    /**
     * @return null|string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param null|string $notes
     * @return Client
     */
    public function setNotes(?string $notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFullName(): ?string
    {
        return trim($this->getFirstName() . ' ' . $this->getMiddleName() . ' ' . $this->getLastName());
    }
}
