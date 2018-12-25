<?php
declare(strict_types=1);

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Sticker
 * @package AppBundle\Entity
 * @author Plamen Markov <plamen@lynxlake.org>
 *
 * @ORM\Table(name="stickers")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StickerRepository")
 */
class Sticker
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
     * @var bool|null
     *
     * @ORM\Column(name="is_cancelled", type="boolean", nullable=true)
     */
    private $isCancelled;

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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Policy", inversedBy="stickers")
     * @ORM\JoinColumn(name="policy_id", referencedColumnName="id")
     */
    private $policy;

    /**
     * Sticker constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
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
     * @return Sticker
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
     * @param bool|null $isCancelled
     *
     * @return Sticker
     */
    public function setIsCancelled($isCancelled = null)
    {
        $this->isCancelled = $isCancelled;

        return $this;
    }

    /**
     * Get isCancelled.
     *
     * @return bool|null
     */
    public function getIsCancelled()
    {
        return $this->isCancelled;
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
     * @return Sticker
     */
    public function setPolicyId(?int $policyId): Sticker
    {
        $this->policyId = $policyId;

        return $this;
    }
}
