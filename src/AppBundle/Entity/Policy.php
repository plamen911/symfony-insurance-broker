<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Policy
 *
 * @ORM\Table(name="policies")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PolicyRepository")
 */
class Policy
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
     * @ORM\Column(name="id_number", type="string", length=191, unique=true)
     */
    private $idNumber;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_cancelled", type="boolean")
     */
    private $isCancelled;

    /**
     * @var string|null
     *
     * @ORM\Column(name="notes", type="text", nullable=true)
     */
    private $notes;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="issued_at", type="datetime")
     */
    private $issuedAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="starts_at", type="datetime", nullable=true)
     */
    private $startsAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="expires_at", type="datetime", nullable=true)
     */
    private $expiresAt;

    /**
     * @var float|null
     *
     * @ORM\Column(name="amount", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $amount;

    /**
     * @var float|null
     *
     * @ORM\Column(name="taxes", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $taxes;

    /**
     * @var float|null
     *
     * @ORM\Column(name="amount_gf", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $amountGf;

    /**
     * @var float|null
     *
     * @ORM\Column(name="office_discount", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $officeDiscount;

    /**
     * @var float|null
     *
     * @ORM\Column(name="client_discount", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $clientDiscount;

    /**
     * @var float|null
     *
     * @ORM\Column(name="total", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $total;

    /**
     * @var string|null
     *
     * @ORM\Column(name="currency", type="string", length=191, nullable=true)
     */
    private $currency;

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
     * @var bool|null
     *
     * @ORM\Column(name="is_deleted", type="boolean", nullable=true)
     */
    private $isDeleted;

    /**
     * @var TypeOfPolicy
     *
     * @ORM\ManyToOne(targetEntity="TypeOfPolicy", inversedBy="policies")
     * @ORM\JoinColumn(name="policy_type_id", referencedColumnName="id")
     */
    private $policyType;

    /**
     * @var Insurer
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Insurer", inversedBy="policies")
     * @ORM\JoinColumn(name="insurer_id", referencedColumnName="id")
     */
    private $insurer;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="assignedPolicies")
     * @ORM\JoinColumn(name="agent_id", referencedColumnName="id")
     */
    private $agent;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="createdPolicies")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     */
    private $author;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="updatedPolicies")
     * @ORM\JoinColumn(name="updated_by", referencedColumnName="id")
     */
    private $updater;

    /**
     * @var Car
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Car", inversedBy="policies")
     * @ORM\JoinColumn(name="car_id", referencedColumnName="id")
     */
    private $car;

    /**
     * @var Client
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Client", inversedBy="ownerPolicies")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", nullable=true)
     */
    private $owner;

    /**
     * @var Client
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Client", inversedBy="representativePolicies")
     * @ORM\JoinColumn(name="representative_id", referencedColumnName="id", nullable=true)
     */
    private $representative;

    /**
     * @var ArrayCollection|Payment[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Payment", mappedBy="policy", cascade={"persist"})
     */
    private $payments;

    /**
     * Policy constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->isCancelled = false;
        $this->issuedAt = new \DateTime();
        $this->startsAt = new \DateTime();
        $this->expiresAt = (new \DateTime())->add(new \DateInterval('P1Y'));
        $this->amount = 0;
        $this->taxes = 0;
        $this->amountGf = 0;
        $this->officeDiscount = 0;
        $this->clientDiscount = 0;
        $this->total = 0;
        $this->currency = 'BGN';
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->isDeleted = false;
        $this->payments = new ArrayCollection();
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
     * Set idNumber.
     *
     * @param string $idNumber
     *
     * @return Policy
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
     * Set isCancelled.
     *
     * @param bool $isCancelled
     *
     * @return Policy
     */
    public function setIsCancelled($isCancelled)
    {
        $this->isCancelled = $isCancelled;

        return $this;
    }

    /**
     * Get isCancelled.
     *
     * @return bool
     */
    public function getIsCancelled()
    {
        return $this->isCancelled;
    }

    /**
     * Set notes.
     *
     * @param string|null $notes
     *
     * @return Policy
     */
    public function setNotes($notes = null)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes.
     *
     * @return string|null
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set issuedAt.
     *
     * @param \DateTime $issuedAt
     *
     * @return Policy
     */
    public function setIssuedAt($issuedAt)
    {
        $this->issuedAt = $issuedAt;

        return $this;
    }

    /**
     * Get issuedAt.
     *
     * @return \DateTime
     */
    public function getIssuedAt()
    {
        return $this->issuedAt;
    }

    /**
     * Set startDate.
     *
     * @param \DateTime|null $startsAt
     *
     * @return Policy
     */
    public function setStartsAt($startsAt = null)
    {
        $this->startsAt = $startsAt;

        return $this;
    }

    /**
     * Get startDate.
     *
     * @return \DateTime|null
     */
    public function getStartsAt()
    {
        return $this->startsAt;
    }

    /**
     * Set endDate.
     *
     * @param \DateTime|null $expiresAt
     *
     * @return Policy
     */
    public function setExpiresAt($expiresAt = null)
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    /**
     * Get endDate.
     *
     * @return \DateTime|null
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * Set amount.
     *
     * @param float|null $amount
     *
     * @return Policy
     */
    public function setAmount($amount = null)
    {
        $this->amount = (float)$amount;

        return $this;
    }

    /**
     * Get amount.
     *
     * @return float|null
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set taxes.
     *
     * @param float|null $taxes
     *
     * @return Policy
     */
    public function setTaxes($taxes = null)
    {
        $this->taxes = (float)$taxes;

        return $this;
    }

    /**
     * Get taxes.
     *
     * @return float|null
     */
    public function getTaxes()
    {
        return $this->taxes;
    }

    /**
     * Set amountGf.
     *
     * @param float|null $amountGf
     *
     * @return Policy
     */
    public function setAmountGf($amountGf = null)
    {
        $this->amountGf = (float)$amountGf;

        return $this;
    }

    /**
     * Get amountGf.
     *
     * @return float|null
     */
    public function getAmountGf()
    {
        return $this->amountGf;
    }

    /**
     * Set total.
     *
     * @param float|null $total
     *
     * @return Policy
     */
    public function setTotal($total = null)
    {
        $this->total = (float)$total;

        return $this;
    }

    /**
     * Get total.
     *
     * @return float|null
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set currency.
     *
     * @param string|null $currency
     *
     * @return Policy
     */
    public function setCurrency($currency = null)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency.
     *
     * @return string|null
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return Policy
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
     * @return Policy
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
     * Set isDeleted.
     *
     * @param bool|null $isDeleted
     *
     * @return Policy
     */
    public function setIsDeleted($isDeleted = null)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * Get isDeleted.
     *
     * @return bool|null
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * @return Insurer
     */
    public function getInsurer()
    {
        return $this->insurer;
    }

    /**
     * @param Insurer $insurer
     * @return Policy
     */
    public function setInsurer(Insurer $insurer)
    {
        $this->insurer = $insurer;

        return $this;
    }

    /**
     * @return TypeOfPolicy
     */
    public function getPolicyType()
    {
        return $this->policyType;
    }

    /**
     * @param TypeOfPolicy $policyType
     * @return Policy
     */
    public function setPolicyType(TypeOfPolicy $policyType)
    {
        $this->policyType = $policyType;

        return $this;
    }

    /**
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param User $author
     * @return Policy
     */
    public function setAuthor(User $author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return User
     */
    public function getUpdater()
    {
        return $this->updater;
    }

    /**
     * @param User $updater
     * @return Policy
     */
    public function setUpdater(User $updater)
    {
        $this->updater = $updater;

        return $this;
    }

    /**
     * @return User
     */
    public function getAgent()
    {
        return $this->agent;
    }

    /**
     * @param User $agent
     * @return Policy
     */
    public function setAgent(User $agent)
    {
        $this->agent = $agent;

        return $this;
    }

    /**
     * @return Client
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param Client $owner
     * @return Policy
     */
    public function setOwner(Client $owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Client
     */
    public function getRepresentative()
    {
        return $this->representative;
    }

    /**
     * @param Client|null $representative
     * @return Policy
     */
    public function setRepresentative(?Client $representative)
    {
        $this->representative = $representative;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getOfficeDiscount()
    {
        return $this->officeDiscount;
    }

    /**
     * @param float|null $officeDiscount
     * @return Policy
     */
    public function setOfficeDiscount(?float $officeDiscount)
    {
        $this->officeDiscount = $officeDiscount;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getClientDiscount()
    {
        return $this->clientDiscount;
    }

    /**
     * @param float|null $clientDiscount
     * @return Policy
     */
    public function setClientDiscount(?float $clientDiscount)
    {
        $this->clientDiscount = $clientDiscount;

        return $this;
    }

    /**
     * @return Payment[]|ArrayCollection
     */
    public function getPayments()
    {
        return $this->payments;
    }

    /**
     * @param Payment[]|ArrayCollection $payments
     * @return Policy
     */
    public function setPayments($payments)
    {
        foreach ($payments as $payment) {
            $this->addPayment($payment);
        }

        return $this;
    }

    /**
     * @param Payment $payment
     * @return Policy
     */
    public function addPayment(Payment $payment)
    {
        $payment->setPolicy($this);
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
        }

        return $this;
    }

    /**
     * @param Payment $payment
     * @return Policy
     */
    public function removePayment(Payment $payment)
    {
        if ($this->payments->contains($payment)) {
            $this->payments->removeElement($payment);
            $payment->setPolicy(null);
        }

        return $this;
    }

    /**
     * @return Car
     */
    public function getCar()
    {
        return $this->car;
    }

    /**
     * @param Car $car
     * @return Policy
     */
    public function setCar(Car $car)
    {
        $this->car = $car;

        return $this;
    }
}
