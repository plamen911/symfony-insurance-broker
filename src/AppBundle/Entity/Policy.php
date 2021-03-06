<?php
declare(strict_types=1);

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class Policy
 * @package AppBundle\Entity
 * @author Plamen Markov <plamen@lynxlake.org>
 *
 * @ORM\Table(name="policies")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PolicyRepository")
 * @UniqueEntity(fields="idNumber", message="Вече има издадена полица с този номер.")
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
     * @Assert\NotBlank(message="Полица No е задължително поле.")
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
     * @Assert\NotBlank(message="Дата на издаване е задължително поле.")
     */
    private $issuedAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="starts_at", type="datetime", nullable=true)
     * @Assert\NotBlank(message="Начална дата е задължително поле.")
     */
    private $startsAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="expires_at", type="datetime", nullable=true)
     * @Assert\NotBlank(message="Крайна дата е задължително поле.")
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
     * @ORM\Column(name="office_commission", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $officeCommission;

    /**
     * @var float|null
     *
     * @ORM\Column(name="client_commission", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $clientCommission;

    /**
     * @var float|null
     *
     * @ORM\Column(name="green_card_total", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $greenCardTotal;

    /**
     * @var float|null
     *
     * @ORM\Column(name="bill_total", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $billTotal;

    /**
     * @var float|null
     *
     * @ORM\Column(name="total", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $total;

    /**
     * @var float|null
     *
     * @ORM\Column(name="paid", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $paid;

    /**
     * @var float|null
     *
     * @ORM\Column(name="balance", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $balance;

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
     * @Assert\NotBlank(message="Застраховател е задължит. поле.")
     */
    private $insurer;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="assignedPolicies")
     * @ORM\JoinColumn(name="agent_id", referencedColumnName="id")
     * @Assert\NotBlank(message="Агент е задължително поле.")
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
     * @Assert\NotBlank(message="МПС е задължително поле.")
     */
    private $car;

    /**
     * @var Client
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Client", inversedBy="ownerPolicies")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", nullable=true)
     * @Assert\NotBlank(message="Собственик на МПС е задължително поле.")
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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Payment", mappedBy="policy", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $payments;

    /**
     * @var ArrayCollection|GreenCard[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\GreenCard", mappedBy="policy", cascade={"persist", "remove"}, orphanRemoval=false)
     */
    private $greenCards;

    /**
     * @var ArrayCollection|Bill[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Bill", mappedBy="policy", cascade={"persist", "remove"}, orphanRemoval=false)
     */
    private $bills;

    /**
     * @var ArrayCollection|Sticker[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Sticker", mappedBy="policy", cascade={"persist", "remove"}, orphanRemoval=false)
     */
    private $stickers;

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
        $this->taxes = 2;
        $this->amountGf = 11.5;
        $this->officeCommission = 0;
        $this->clientCommission = 0;
        $this->greenCardTotal = 0;
        $this->billTotal = 0;
        $this->total = 0;
        $this->paid = 0;
        $this->balance = 0;
        $this->currency = 'BGN';
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->payments = new ArrayCollection();
        $this->greenCards = new ArrayCollection();
        $this->bills = new ArrayCollection();
        $this->stickers = new ArrayCollection();
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
        return (float)$this->amount;
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
        return (float)$this->taxes;
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
        return (float)$this->amountGf;
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
        return (float)$this->total;
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
    public function getOfficeCommission()
    {
        return $this->officeCommission;
    }

    /**
     * @param float|null $officeCommission
     * @return Policy
     */
    public function setOfficeCommission(?float $officeCommission)
    {
        $this->officeCommission = $officeCommission;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getClientCommission()
    {
        return $this->clientCommission;
    }

    /**
     * @param float|null $clientCommission
     * @return Policy
     */
    public function setClientCommission(?float $clientCommission)
    {
        $this->clientCommission = $clientCommission;

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
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setPolicy($this);
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
     * @return GreenCard[]|ArrayCollection
     */
    public function getGreenCards()
    {
        return $this->greenCards;
    }

    /**
     * @param GreenCard[]|ArrayCollection $greenCards
     * @return Policy
     */
    public function setGreenCards($greenCards): Policy
    {
        foreach ($greenCards as $greenCard) {
            $this->addGreenCard($greenCard);
        }

        return $this;
    }

    /**
     * @param GreenCard $greenCard
     * @return $this
     */
    public function addGreenCard(GreenCard $greenCard)
    {
        if (!$this->greenCards->contains($greenCard)) {
            $this->greenCards->add($greenCard);
            $greenCard->setPolicy($this);
        }

        return $this;
    }

    /**
     * @param GreenCard $greenCard
     * @return Policy
     */
    public function removeGreenCard(GreenCard $greenCard)
    {
        if ($this->greenCards->contains($greenCard)) {
            $this->greenCards->removeElement($greenCard);
            $greenCard->setPolicy(null);
        }

        return $this;
    }

    /**
     * @return Bill[]|ArrayCollection
     */
    public function getBills()
    {
        return $this->bills;
    }

    /**
     * @param Bill[]|ArrayCollection $bills
     * @return Policy
     */
    public function setBills($bills): Policy
    {
        foreach ($bills as $bill) {
            $this->addBill($bill);
        }

        return $this;
    }

    /**
     * @param Bill $bill
     * @return Policy
     */
    public function addBill(Bill $bill)
    {
        if (!$this->bills->contains($bill)) {
            $this->bills->add($bill);
            $bill->setPolicy($this);
        }

        return $this;
    }

    /**
     * @param Bill $bill
     * @return Policy
     */
    public function removeBill(Bill $bill)
    {
        if ($this->bills->contains($bill)) {
            $this->bills->removeElement($bill);
            $bill->setPolicy(null);
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
     * @return Policy
     */
    public function setStickers($stickers): Policy
    {
        foreach ($stickers as $sticker) {
            $this->addSticker($sticker);
        }

        return $this;
    }

    /**
     * @param Sticker $sticker
     * @return Policy
     */
    public function addSticker(Sticker $sticker)
    {
        if (!$this->stickers->contains($sticker)) {
            $this->stickers->add($sticker);
            $sticker->setPolicy($this);
        }

        return $this;
    }

    /**
     * @param Sticker $sticker
     * @return Policy
     */
    public function removeSticker(Sticker $sticker)
    {
        if ($this->stickers->contains($sticker)) {
            $this->stickers->removeElement($sticker);
            $sticker->setPolicy(null);
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

    /**
     * @return float|null
     */
    public function getPaid(): ?float
    {
        return (float)$this->paid;
    }

    /**
     * @param float|null $paid
     * @return Policy
     */
    public function setPaid(?float $paid): Policy
    {
        $this->paid = $paid;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getBalance(): ?float
    {
        return (float)$this->balance;
    }

    /**
     * @param float|null $balance
     * @return Policy
     */
    public function setBalance(?float $balance): Policy
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * @return float
     */
    public function getPaidTotal()
    {
        $paid = 0.0;
        foreach ($this->getPayments() as $payment) {
            $paid += (float)$payment->getAmountPaid();
        }

        return $paid;
    }

    /**
     * @return float
     */
    public function getBalanceTotal()
    {
        return $this->getTotal() - $this->getPaidTotal();
    }

    /**
     * @return float|null
     */
    public function getGreenCardTotal(): ?float
    {
        return (float)$this->greenCardTotal;
    }

    /**
     * @param float|null $greenCardTotal
     * @return Policy
     */
    public function setGreenCardTotal(?float $greenCardTotal): Policy
    {
        $this->greenCardTotal = (float)$greenCardTotal;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getBillTotal(): ?float
    {
        return (float)$this->billTotal;
    }

    /**
     * @param float|null $billTotal
     * @return Policy
     */
    public function setBillTotal(?float $billTotal): Policy
    {
        $this->billTotal = (float)$billTotal;

        return $this;
    }

    /**
     * @return Policy
     */
    public function calculate()
    {
        $greenCardTotal = 0.0;
        foreach ($this->getGreenCards() as $greenCard) {
            $amountDue = sprintf('%.2f', $greenCard->getPrice() + ($greenCard->getPrice() * $greenCard->getTax() / 100));
            $greenCard->setAmountDue($amountDue);
            $greenCardTotal += $greenCard->getAmountDue();
        }
        $this->setGreenCardTotal($greenCardTotal);

        $billTotal = 0.0;
        foreach ($this->getBills() as $bill) {
            $billTotal += $bill->getPrice();
        }
        $this->setBillTotal($billTotal);

        $total = sprintf('%.2f', $this->getAmount() + ($this->getTaxes() * $this->getAmount() / 100) + $this->getAmountGf() + $this->getGreenCardTotal() + $this->getBillTotal());
        $this->setTotal($total);

        return $this;
    }
}
