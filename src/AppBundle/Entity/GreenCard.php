<?php
declare(strict_types=1);

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class GreenCard
 * @package AppBundle\Entity
 * @author Plamen Markov <plamen@lynxlake.org>
 *
 * @ORM\Table(name="green_cards")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GreenCardRepository")
 * @UniqueEntity(fields="idNumber", message="Вече има издадена зелена карта с този номер.")
 */
class GreenCard
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
     * @Assert\NotBlank(message="Зелена карта No е задължително поле.")
     */
    private $idNumber;

    /**
     * @var string|null
     *
     * @ORM\Column(name="price", type="decimal", precision=10, scale=2)
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column(name="tax", type="decimal", precision=10, scale=2)
     */
    private $tax;

    /**
     * @var string
     *
     * @ORM\Column(name="amount_due", type="decimal", precision=10, scale=2)
     */
    private $amountDue;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var Insurer
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Insurer", inversedBy="greenCards")
     * @ORM\JoinColumn(name="insurer_id", referencedColumnName="id")
     * @Assert\NotBlank(message="Застраховател е задължит. поле.")
     */
    private $insurer;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="greenCards")
     * @ORM\JoinColumn(name="agent_id", referencedColumnName="id", nullable=true)
     */
    private $agent;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="received_at", type="datetime")
     * @Assert\NotBlank(message="Дата на получаване е задължителна.")
     */
    private $receivedAt;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="createdGreenCards")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     */
    private $author;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="given_at", type="datetime", nullable=true)
     */
    private $givenAt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="policy_id", type="integer", nullable=true)
     */
    private $policyId;

    /**
     * @var Policy|null
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Policy", inversedBy="greenCards")
     * @ORM\JoinColumn(name="policy_id", referencedColumnName="id")
     */
    private $policy;

    /**
     * GreenCard constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->price = 0;
        $this->tax = 0;
        $this->amountDue = 0;
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
     * @return GreenCard
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
     * Set price.
     *
     * @param string|null $price
     *
     * @return GreenCard
     */
    public function setPrice($price = null)
    {
        $this->price = (float)$price;

        return $this;
    }

    /**
     * Get price.
     *
     * @return string|null
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set tax.
     *
     * @param string $tax
     *
     * @return GreenCard
     */
    public function setTax($tax)
    {
        $this->tax = (float)$tax;

        return $this;
    }

    /**
     * Get tax.
     *
     * @return string
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * Set amountDue.
     *
     * @param string $amountDue
     *
     * @return GreenCard
     */
    public function setAmountDue($amountDue)
    {
        $this->amountDue = (float)$amountDue;

        return $this;
    }

    /**
     * Get amountDue.
     *
     * @return string
     */
    public function getAmountDue()
    {
        return $this->amountDue;
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
     * @return GreenCard
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Policy|null
     */
    public function getPolicy(): ?Policy
    {
        return $this->policy;
    }

    /**
     * @param Policy|null $policy
     * @return GreenCard
     */
    public function setPolicy(?Policy $policy)
    {
        $this->policy = $policy;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPolicyId(): ?int
    {
        return $this->policyId;
    }

    /**
     * @param int|null $policyId
     * @return GreenCard
     */
    public function setPolicyId(?int $policyId): GreenCard
    {
        $this->policyId = $policyId;

        return $this;
    }

    /**
     * @return Insurer|null
     */
    public function getInsurer(): ?Insurer
    {
        return $this->insurer;
    }

    /**
     * @param Insurer $insurer
     */
    public function setInsurer(Insurer $insurer): void
    {
        $this->insurer = $insurer;
    }

    /**
     * @return User|null
     */
    public function getAgent(): ?User
    {
        return $this->agent;
    }

    /**
     * @param User|null $agent
     */
    public function setAgent(?User $agent): void
    {
        $this->agent = $agent;
    }

    /**
     * @return \DateTime|null
     */
    public function getReceivedAt(): ?\DateTime
    {
        return $this->receivedAt;
    }

    /**
     * @param \DateTime|null $receivedAt
     * @return GreenCard
     */
    public function setReceivedAt(?\DateTime $receivedAt): GreenCard
    {
        $this->receivedAt = $receivedAt;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * @param User|null $author
     * @return GreenCard
     */
    public function setAuthor(?User $author): GreenCard
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getGivenAt(): ?\DateTime
    {
        return $this->givenAt;
    }

    /**
     * @param \DateTime|null $givenAt
     * @return GreenCard
     */
    public function setGivenAt(?\DateTime $givenAt): GreenCard
    {
        $this->givenAt = $givenAt;

        return $this;
    }
}
