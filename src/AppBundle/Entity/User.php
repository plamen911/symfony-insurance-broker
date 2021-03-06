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
 * @UniqueEntity(fields="email", message="Вече има регистриран профил с този и-мейл.")
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
     * @Assert\NotBlank(message="И-мейлът е задължителен.")
     * @Assert\Email()
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=191)
     * @Assert\NotBlank(message="Паролата е задължителна.", groups={"registration"})
     * @Assert\Length(
     *     min="6",
     *     max="12",
     *     minMessage="Паролата трябва да е дълга поне {{ limit }} символа.",
     *     maxMessage="Паролата трябва да съдържа не повече от {{ limit }} символа.",
     *     groups={"registration"}
     * )
     * @Assert\Regex(
     *     pattern="/^[A-Za-z0-9]+$/",
     *     message="Паролата трябва се състои само от малки и главни букви и цифри.",
     *     groups={"registration"}
     * )
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
     * @var ArrayCollection|Payment[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Payment", mappedBy="reminder")
     */
    private $reminders;

    /**
     * @var ArrayCollection|Sticker[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Sticker", mappedBy="agent")
     */
    private $stickers;

    /**
     * @var ArrayCollection|Sticker[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Sticker", mappedBy="author")
     */
    private $createdStickers;

    /**
     * @var ArrayCollection|GreenCard[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\GreenCard", mappedBy="agent")
     */
    private $greenCards;

    /**
     * @var ArrayCollection|GreenCard[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\GreenCard", mappedBy="author")
     */
    private $createdGreenCards;

    /**
     * @var ArrayCollection|Sticker[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Bill", mappedBy="agent")
     */
    private $bills;

    /**
     * @var ArrayCollection|Bill[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Bill", mappedBy="author")
     */
    private $createdBills;

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
        $this->reminders = new ArrayCollection();
        $this->createdStickers = new ArrayCollection();
        $this->stickers = new ArrayCollection();
        $this->createdGreenCards = new ArrayCollection();
        $this->greenCards = new ArrayCollection();
        $this->createdBills = new ArrayCollection();
        $this->bills = new ArrayCollection();
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
     * @return Role[]|ArrayCollection
     */
    public function getProfileRoles()
    {
        return $this->roles;
    }

    /**
     * @param Role[]|ArrayCollection $roles
     * @return User
     */
    public function setProfileRoles($roles)
    {
        foreach ($roles as $role) {
            $this->addProfileRole($role);
        }

        return $this;
    }

    /**
     * @param Role $role
     * @return User
     */
    public function addProfileRole(Role $role)
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
        }

        return $this;
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

    /**
     * @return Payment[]|ArrayCollection
     */
    public function getReminders()
    {
        return $this->reminders;
    }

    /**
     * @param Payment[]|ArrayCollection $payments
     * @return User
     */
    public function setReminders($payments): User
    {
        foreach ($payments as $payment) {
            $this->addReminder($payment);
        }

        return $this;
    }

    /**
     * @param Payment $payment
     * @return $this
     */
    public function addReminder(Payment $payment)
    {
        if (!$this->reminders->contains($payment)) {
            $this->reminders->add($payment);
            $payment->setReminder($this);
        }

        return $this;
    }

    /**
     * @return Sticker[]|ArrayCollection
     */
    public function getStickers()
    {
        return $this->stickers;
    }

    /**
     * @param Sticker[]|ArrayCollection $stickers
     * @return User
     */
    public function setStickers($stickers): User
    {
        foreach ($stickers as $sticker) {
            $this->addSticker($sticker);
        }

        return $this;
    }

    /**
     * @param Sticker $sticker
     * @return $this
     */
    public function addSticker(Sticker $sticker)
    {
        if (!$this->stickers->contains($sticker)) {
            $this->stickers->add($sticker);
            $sticker->setAgent($this);
        }

        return $this;
    }

    /**
     * @return Sticker[]|ArrayCollection
     */
    public function getCreatedStickers()
    {
        return $this->createdStickers;
    }

    /**
     * @param Sticker[]|ArrayCollection $createdStickers
     * @return User
     */
    public function setCreatedStickers($createdStickers): User
    {
        foreach ($createdStickers as $sticker) {
            $this->addSticker($sticker);
        }

        return $this;
    }

    /**
     * @param Sticker $sticker
     * @return $this
     */
    public function addCreatedSticker(Sticker $sticker)
    {
        if (!$this->createdStickers->contains($sticker)) {
            $this->createdStickers->add($sticker);
            $sticker->setAuthor($this);
        }

        return $this;
    }

    /**
     * @return GreenCard[]|ArrayCollection
     */
    public function getCreatedGreenCards()
    {
        return $this->createdGreenCards;
    }

    /**
     * @param GreenCard[]|ArrayCollection $createdGreenCards
     * @return User
     */
    public function setCreatedGreenCards($createdGreenCards): User
    {
        foreach ($createdGreenCards as $greenCard) {
            $this->addCreatedGreenCard($greenCard);
        }

        return $this;
    }

    /**
     * @param GreenCard $greenCard
     * @return User
     */
    public function addCreatedGreenCard(GreenCard $greenCard)
    {
        if (!$this->createdGreenCards->contains($greenCard)) {
            $this->createdGreenCards->add($greenCard);
            $greenCard->setAuthor($this);
        }

        return $this;
    }

    /**
     * @return GreenCard[]|ArrayCollection
     */
    public function getGreenCards()
    {
        return $this->greenCards;
    }

    /**
     * @param GreenCard[]|ArrayCollection $greenCards
     * @return User
     */
    public function setGreenCards($greenCards): User
    {
        foreach ($greenCards as $greenCard) {
            $this->addGreenCard($greenCard);
        }

        return $this;
    }

    /**
     * @param GreenCard $greenCard
     * @return User
     */
    public function addGreenCard(GreenCard $greenCard)
    {
        if (!$this->greenCards->contains($greenCard)) {
            $this->greenCards->add($greenCard);
            $greenCard->setAgent($this);
        }

        return $this;
    }

    /**
     * @return Sticker[]|ArrayCollection
     */
    public function getBills()
    {
        return $this->bills;
    }

    /**
     * @param Sticker[]|ArrayCollection $bills
     * @return User
     */
    public function setBills($bills): User
    {
        foreach ($bills as $bill) {
            $this->addBill($bill);
        }

        return $this;
    }

    /**
     * @param Bill $bill
     * @return User
     */
    public function addBill(Bill $bill)
    {
        if (!$this->bills->contains($bill)) {
            $this->bills->add($bill);
            $bill->setAuthor($this);
        }

        return $this;
    }

    /**
     * @return Bill[]|ArrayCollection
     */
    public function getCreatedBills()
    {
        return $this->createdBills;
    }

    /**
     * @param Bill[]|ArrayCollection $createdBills
     * @return User
     */
    public function setCreatedBills($createdBills): User
    {
        foreach ($createdBills as $createdBill) {
            $this->addCreatedBill($createdBill);
        }

        return $this;
    }

    /**
     * @param Bill $bill
     * @return User
     */
    private function addCreatedBill(Bill $bill)
    {
        if (!$this->createdBills->contains($bill)) {
            $this->createdBills->add($bill);
            $bill->setAuthor($this);
        }

        return $this;
    }
}
