<?php

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
     * @var Policy
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Policy", inversedBy="payments")
     * @ORM\JoinColumn(name="policy_id", referencedColumnName="id")
     */
    private $policy;

    /**
     * Payment constructor.
     */
    public function __construct()
    {
        $this->amountDue = 0;
        $this->amountPaid = 0;
        $this->isDeferred = false;
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

}
