<?php
declare(strict_types=1);

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Bill
 * @package AppBundle\Entity
 * @author Plamen Markov <plamen@lynxlake.org>
 *
 * @ORM\Table(name="bills")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BillRepository")
 * @UniqueEntity(fields="idNumber", message="Вече има издадена сметка с този номер.")
 */
class Bill
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
     * @Assert\NotBlank(message="Сметка No е задължително поле.")
     */
    private $idNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=10, scale=2)
     */
    private $price;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var Insurer
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Insurer", inversedBy="bills")
     * @ORM\JoinColumn(name="insurer_id", referencedColumnName="id")
     * @Assert\NotBlank(message="Застраховател е задължит. поле.")
     */
    private $insurer;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="bills")
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="createdBills")
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Policy", inversedBy="bills")
     * @ORM\JoinColumn(name="policy_id", referencedColumnName="id")
     */
    private $policy;

    /**
     * Bill constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->price = 0;
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
     * @return Bill
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
     * @param string $price
     *
     * @return Bill
     */
    public function setPrice($price)
    {
        $this->price = (float)$price;

        return $this;
    }

    /**
     * Get price.
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
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
     */
    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
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
     */
    public function setPolicy(?Policy $policy): void
    {
        $this->policy = $policy;
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
     * @return Bill
     */
    public function setPolicyId(?int $policyId): Bill
    {
        $this->policyId = $policyId;

        return $this;
    }

    /**
     * @return Insurer
     */
    public function getInsurer(): Insurer
    {
        return $this->insurer;
    }

    /**
     * @param Insurer $insurer
     * @return Bill
     */
    public function setInsurer(Insurer $insurer): Bill
    {
        $this->insurer = $insurer;

        return $this;
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
     * @return Bill
     */
    public function setAgent(?User $agent): Bill
    {
        $this->agent = $agent;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getReceivedAt(): \DateTime
    {
        return $this->receivedAt;
    }

    /**
     * @param \DateTime $receivedAt
     * @return Bill
     */
    public function setReceivedAt(\DateTime $receivedAt): Bill
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
     * @return Bill
     */
    public function setAuthor(?User $author): Bill
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
     * @return Bill
     */
    public function setGivenAt(?\DateTime $givenAt): Bill
    {
        $this->givenAt = $givenAt;

        return $this;
    }
}
