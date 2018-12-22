<?php
declare(strict_types=1);

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Payment
 *
 * @ORM\Table(name="payments")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PaymentRepository")
 */
class Payment
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
     * @var int
     *
     * @ORM\Column(name="payment_order", type="integer", nullable=true)
     */
    private $paymentOrder;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="due_at", type="date", nullable=true)
     */
    private $dueAt;

    /**
     * @var float|null
     *
     * @ORM\Column(name="amount_due", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $amountDue;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="paid_at", type="date", nullable=true)
     */
    private $paidAt;

    /**
     * @var float|null
     *
     * @ORM\Column(name="amount_paid", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $amountPaid;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_deferred", type="boolean", nullable=true)
     */
    private $isDeferred;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_reminded", type="boolean", nullable=true)
     */
    private $isReminded;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="reminded_at", type="datetime", nullable=true)
     */
    private $remindedAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="reminders")
     * @ORM\JoinColumn(name="reminded_by", referencedColumnName="id")
     */
    private $reminder;

    /**
     * @var int
     *
     * @ORM\Column(name="policy_id", type="integer", nullable=true)
     */
    private $policyId;

    /**
     * @var Policy
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Policy", inversedBy="payments")
     * @ORM\JoinColumn(name="policy_id", referencedColumnName="id")
     */
    private $policy;

    /**
     * Payment constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->paymentOrder = 1;
        $this->amountDue = 0;
        $this->amountPaid = 0;
        $this->isDeferred = false;
        $this->isReminded = false;
        $this->dueAt = new \DateTime();
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
     * Set dueAt.
     *
     * @param \DateTime|null $dueAt
     *
     * @return Payment
     */
    public function setDueAt($dueAt = null)
    {
        $this->dueAt = $dueAt;

        return $this;
    }

    /**
     * Get dueAt.
     *
     * @return \DateTime|null
     */
    public function getDueAt()
    {
        return $this->dueAt;
    }

    /**
     * Set amountDue.
     *
     * @param float|null $amountDue
     *
     * @return Payment
     */
    public function setAmountDue($amountDue = null)
    {
        $this->amountDue = (float)$amountDue;

        return $this;
    }

    /**
     * Get amountDue.
     *
     * @return float|null
     */
    public function getAmountDue()
    {
        return $this->amountDue;
    }

    /**
     * Set paidAt.
     *
     * @param \DateTime|null $paidAt
     *
     * @return Payment
     */
    public function setPaidAt($paidAt = null)
    {
        $this->paidAt = $paidAt;

        return $this;
    }

    /**
     * Get paidAt.
     *
     * @return \DateTime|null
     */
    public function getPaidAt()
    {
        return $this->paidAt;
    }

    /**
     * Set amountPaid.
     *
     * @param float|null $amountPaid
     *
     * @return Payment
     */
    public function setAmountPaid($amountPaid = null)
    {
        $this->amountPaid = (float)$amountPaid;

        return $this;
    }

    /**
     * Get amountPaid.
     *
     * @return float|null
     */
    public function getAmountPaid()
    {
        return $this->amountPaid;
    }

    /**
     * Set isDiferred.
     *
     * @param bool|null $isDeferred
     *
     * @return Payment
     */
    public function setIsDeferred($isDeferred = null)
    {
        $this->isDeferred = $isDeferred;

        return $this;
    }

    /**
     * Get isDiferred.
     *
     * @return bool|null
     */
    public function getIsDeferred()
    {
        return $this->isDeferred;
    }

    /**
     * @return Policy
     */
    public function getPolicy(): Policy
    {
        return $this->policy;
    }

    /**
     * @param Policy|null $policy
     * @return Payment
     */
    public function setPolicy(?Policy $policy): Payment
    {
        $this->policy = $policy;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsReminded(): ?bool
    {
        return $this->isReminded;
    }

    /**
     * @param bool|null $isReminded
     * @return Payment
     */
    public function setIsReminded(?bool $isReminded): Payment
    {
        $this->isReminded = $isReminded;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getRemindedAt(): ?\DateTime
    {
        return $this->remindedAt;
    }

    /**
     * @param \DateTime|null $remindedAt
     * @return Payment
     */
    public function setRemindedAt(?\DateTime $remindedAt): Payment
    {
        $this->remindedAt = $remindedAt;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getReminder(): ?User
    {
        return $this->reminder;
    }

    /**
     * @param User $user
     * @return Payment
     */
    public function setReminder(?User $user): Payment
    {
        $this->reminder = $user;

        return $this;
    }

    /**
     * @return int
     */
    public function getPaymentOrder(): int
    {
        return (int)$this->paymentOrder;
    }

    /**
     * @param int|null $paymentOrder
     * @return Payment
     */
    public function setPaymentOrder(?int $paymentOrder): Payment
    {
        $this->paymentOrder = $paymentOrder;

        return $this;
    }

    /**
     * @return int
     */
    public function getPolicyId(): int
    {
        return $this->policyId;
    }

    /**
     * @param int $policyId
     * @return Payment
     */
    public function setPolicyId(int $policyId): Payment
    {
        $this->policyId = $policyId;

        return $this;
    }
}
