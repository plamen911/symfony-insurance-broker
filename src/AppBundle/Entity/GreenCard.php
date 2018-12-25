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
}
