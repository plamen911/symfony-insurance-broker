<?php
declare(strict_types=1);

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Document
 *
 * @ORM\Table(name="documents")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DocumentRepository")
 */
class Document
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
     * @var string|null
     *
     * @ORM\Column(name="file_name", type="string", length=191, nullable=true)
     */
    private $fileName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="file_url", type="string", length=191, nullable=true)
     */
    private $fileUrl;

    /**
     * @var string|null
     *
     * @ORM\Column(name="mime_type", type="string", length=191, nullable=true)
     */
    private $mimeType;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="uploaded_at", type="datetime")
     */
    private $uploadedAt;

    /**
     * @var Car
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Car", inversedBy="documents")
     * @ORM\JoinColumn(name="car_id", referencedColumnName="id", nullable=true)
     */
    private $car;

    /**
     * Document constructor.
     */
    public function __construct()
    {
        $this->uploadedAt = new \DateTime();
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
     * @return null|string
     */
    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    /**
     * @param null|string $fileName
     * @return Document
     */
    public function setFileName(?string $fileName): Document
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getFileUrl(): ?string
    {
        return $this->fileUrl;
    }

    /**
     * @param null|string $fileUrl
     * @return Document
     */
    public function setFileUrl(?string $fileUrl): Document
    {
        $this->fileUrl = $fileUrl;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    /**
     * @param null|string $mimeType
     * @return Document
     */
    public function setMimeType(?string $mimeType): Document
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * Set updatedAt.
     *
     * @param \DateTime $uploadedAt
     *
     * @return Document
     */
    public function setUploadedAt($uploadedAt)
    {
        $this->uploadedAt = $uploadedAt;

        return $this;
    }

    /**
     * Get updatedAt.
     *
     * @return \DateTime
     */
    public function getUploadedAt()
    {
        return $this->uploadedAt;
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
     * @return Document
     */
    public function setCar(Car $car)
    {
        $this->car = $car;

        return $this;
    }
}
