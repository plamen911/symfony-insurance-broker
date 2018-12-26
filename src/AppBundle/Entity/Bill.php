<?php
declare(strict_types=1);

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Bill
 * @package AppBundle\Entity
 * @author Plamen Markov <plamen@lynxlake.org>
 *
 * @ORM\Table(name="bills")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BillRepository")
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
}
