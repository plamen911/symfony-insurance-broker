<?php
declare(strict_types=1);

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @UniqueEntity(fields="email", message="Sorry, this e-mail address is already used.")
 */
class User implements AdvancedUserInterface
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
     * @ORM\Column(name="email", type="string", length=191, unique=true)
     * @Assert\NotBlank(message="This field is required.")
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=191)
     * @Assert\NotBlank(message="Password field is required.")
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="full_name", type="string", length=191, nullable=true)
     */
    private $fullName;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=true)
     */
    private $enabled;

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
     * @var ArrayCollection|Role[]
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Role", inversedBy="users")
     * @ORM\JoinTable(name="roles_users",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     * )
     */
    private $roles;

    /**
     * @var ArrayCollection|Policy[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Policy", mappedBy="agent")
     */
    private $assignedPolicies;

    /**
     * @var ArrayCollection|Policy[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Policy", mappedBy="author")
     */
    private $createdPolicies;

    /**
     * @var ArrayCollection|Policy[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Policy", mappedBy="updater")
     */
    private $updatedPolicies;

    /**
     * @var ArrayCollection|Car[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Car", mappedBy="author")
     */
    private $createdCars;

    /**
     * @var ArrayCollection|Car[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Car", mappedBy="updater")
     */
    private $updatedCars;

    /**
     * User constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->assignedPolicies = new ArrayCollection();
        $this->createdPolicies = new ArrayCollection();
        $this->updatedPolicies = new ArrayCollection();
        $this->createdCars = new ArrayCollection();
        $this->updatedCars = new ArrayCollection();
        $this->setEnabled(true);
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set fullName
     *
     * @param string $fullName
     *
     * @return User
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * Get fullName
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * Checks whether the user's account has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw an AccountExpiredException and prevent login.
     *
     * @return bool true if the user's account is non expired, false otherwise
     *
     * @see AccountExpiredException
     */
    public function isAccountNonExpired()
    {
        // TODO: Implement isAccountNonExpired() method.
        return true;
    }

    /**
     * Checks whether the user is locked.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a LockedException and prevent login.
     *
     * @return bool true if the user is not locked, false otherwise
     *
     * @see LockedException
     */
    public function isAccountNonLocked()
    {
        // TODO: Implement isAccountNonLocked() method.
        return true;
    }

    /**
     * Checks whether the user's credentials (password) has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a CredentialsExpiredException and prevent login.
     *
     * @return bool true if the user's credentials are non expired, false otherwise
     *
     * @see CredentialsExpiredException
     */
    public function isCredentialsNonExpired()
    {
        // TODO: Implement isCredentialsNonExpired() method.
        return true;
    }

    /**
     * Checks whether the user is enabled.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a DisabledException and prevent login.
     *
     * @return bool true if the user is enabled, false otherwise
     *
     * @see DisabledException
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return array('ROLE_USER');
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return array (Role|string)[] The user roles
     */
    public function getRoles()
    {
        $stringRoles = [];
        /** @var Role $role */
        foreach ($this->roles as $role) {
            $stringRoles[] = $role->getRole();
        }

        return $stringRoles;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function __toString()
    {
        return !empty($this->getFullName()) ? $this->getFullName() : $this->getUsername();
    }

    /**
     * @param Role $role
     *
     * @return User
     */
    public function addRole(Role $role)
    {
        $this->roles[] = $role;

        return $this;
    }

    /**
     * @param array $roles
     * @return $this
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSuperAdmin()
    {
        return in_array('ROLE_SUPER_ADMIN', $this->getRoles());
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return $this->isSuperAdmin() || in_array('ROLE_ADMIN', $this->getRoles());
    }

    /**
     * @return bool
     */
    public function isOffice()
    {
        return in_array('ROLE_OFFICE', $this->getRoles());
    }

    /**
     * @return bool
     */
    public function isUser()
    {
        return in_array('ROLE_USER', $this->getRoles());
    }

    /**
     * @return string
     */
    public function getAvatar()
    {
        return 'https://ui-avatars.com/api/?name=' . rawurlencode($this->getFullName()) . '&rounded=true&background=a0a0a0';
    }

    /**
     * @return Policy[]|ArrayCollection
     */
    public function getCreatedPolicies()
    {
        return $this->createdPolicies;
    }

    /**
     * @param Policy[]|ArrayCollection $createdPolicies
     * @return User
     */
    public function setCreatedPolicies($createdPolicies): User
    {
        foreach ($createdPolicies as $createdPolicy) {
            $this->addCreatedPolicy($createdPolicy);
        }

        return $this;
    }

    /**
     * @param Policy $policy
     * @return $this
     */
    public function addCreatedPolicy(Policy $policy)
    {
        $this->createdPolicies->add($policy);
        $policy->setAuthor($this);

        return $this;
    }

    /**
     * @return Policy[]|ArrayCollection
     */
    public function getUpdatedPolicies()
    {
        return $this->updatedPolicies;
    }

    /**
     * @param Policy[]|ArrayCollection $policies
     * @return User
     */
    public function setUpdatedPolicies($policies): User
    {
        foreach ($policies as $policy) {
            $this->addUpdatedPolicy($policy);
        }

        return $this;
    }

    /**
     * @param Policy $policy
     * @return User
     */
    public function addUpdatedPolicy(Policy $policy)
    {
        if (!$this->updatedPolicies->contains($policy)) {
            $this->updatedPolicies->add($policy);
            $policy->setUpdater($this);
        }

        return $this;
    }

    /**
     * @return Car[]|ArrayCollection
     */
    public function getCreatedCars()
    {
        return $this->createdCars;
    }

    /**
     * @param Car[]|ArrayCollection $cars
     * @return User
     */
    public function setCreatedCars($cars): User
    {
        foreach ($cars as $car) {
            $this->addCreatedCar($car);
        }

        return $this;
    }

    /**
     * @param Car $car
     * @return User
     */
    public function addCreatedCar(Car $car)
    {
        if (!$this->createdCars->contains($car)) {
            $this->createdCars->add($car);
            $car->setAuthor($this);
        }

        return $this;
    }

    /**
     * @return Car[]|ArrayCollection
     */
    public function getUpdatedCars()
    {
        return $this->updatedCars;
    }

    /**
     * @param Car[]|ArrayCollection $cars
     * @return User
     */
    public function setUpdatedCars($cars): User
    {
        foreach ($cars as $car) {
            $this->addUpdatedCar($car);
        }

        return $this;
    }

    /**
     * @param Car $car
     * @return User
     */
    public function addUpdatedCar(Car $car)
    {
        if (!$this->updatedCars->contains($car)) {
            $this->updatedCars->add($car);
            $car->setUpdater($this);
        }

        return $this;
    }

    /**
     * @return Policy[]|ArrayCollection
     */
    public function getAssignedPolicies()
    {
        return $this->assignedPolicies;
    }

    /**
     * @param Policy[]|ArrayCollection $policies
     * @return User
     */
    public function setAssignedPolicies($policies): User
    {
        foreach ($policies as $policy) {
            $this->addAssignedPolicy($policy);
        }

        return $this;
    }

    /**
     * @param Policy $policy
     * @return $this
     */
    public function addAssignedPolicy(Policy $policy)
    {
        $this->assignedPolicies->add($policy);
        $policy->setAgent($this);

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     * @return User
     */
    public function setCreatedAt(\DateTime $createdAt): User
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     * @return User
     */
    public function setUpdatedAt(\DateTime $updatedAt): User
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}

