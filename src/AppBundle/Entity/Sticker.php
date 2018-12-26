<?php
declare(strict_types=1);

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Sticker
 * @package AppBundle\Entity
 * @author Plamen Markov <plamen@lynxlake.org>
 *
 * @ORM\Table(name="stickers")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StickerRepository")
 * @UniqueEntity(fields="idNumber", message="Вече има издаден стикер с този номер.")
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
     * @Assert\NotBlank(message="Стикер No е задължително поле.")
     */
    private $idNumber;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_cancelled", type="boolean", nullable=true)
     */
    private $isCancelled;

    /**
     * @var Insurer
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Insurer", inversedBy="stickers")
     * @ORM\JoinColumn(name="insurer_id", referencedColumnName="id")
     * @Assert\NotBlank(message="Застраховател е задължит. поле.")
     */
    private $insurer;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="stickers")
     * @ORM\JoinColumn(name="agent_id", referencedColumnName="id", nullable=true)
     */
    private $agent;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="received_at", type="datetime")
     * @Assert\NotBlank(message="Дата на получаване е задължително поле.")
     */
    private $receivedAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="given_at", type="datetime", nullable=true)
     */
    private $givenAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="createdStickers")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     */
    private $author;

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

    /**
     * @return Insurer
     */
    public function getInsurer(): Insurer
    {
        return $this->insurer;
    }

    /**
     * @param Insurer $insurer
     * @return Sticker
     */
    public function setInsurer(Insurer $insurer): Sticker
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
     * @return Sticker
     */
    public function setAgent(?User $agent): Sticker
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
     * @return Sticker
     */
    public function setReceivedAt(\DateTime $receivedAt): Sticker
    {
        $this->receivedAt = $receivedAt;

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
     * @return Sticker
     */
    public function setGivenAt(?\DateTime $givenAt): Sticker
    {
        $this->givenAt = $givenAt;

        return $this;
    }

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @param User $author
     * @return Sticker
     */
    public function setAuthor(User $author): Sticker
    {
        $this->author = $author;

        return $this;
    }
}
