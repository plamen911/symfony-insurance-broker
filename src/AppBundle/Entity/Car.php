<?php
declare(strict_types=1);

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class Car
 * @package AppBundle\Entity
 * @author Plamen Markov <plamen@lynxlake.org>
 *
 * @ORM\Table(name="cars")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CarRepository")
 * @UniqueEntity(fields="idNumber", message="Вече съществува МПС с този рег. номер.")
 */
class Car
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
     * @Assert\NotBlank(message="Рег. номер е задължително поле.")
     * @Assert\Length(
     *      min = 7,
     *      max = 8,
     *      minMessage = "Рег. номер трябва да съдържа поне {{ limit }} символа",
     *      maxMessage = "Рег. номер не трябва да е по-дълъг от {{ limit }} символа"
     * )
     * @Assert\Regex(
     *     pattern="/^[A-Z]{1,2}[0-9]{4}[A-Z]{1,2}$/",
     *     message="Рег. номер трябва да съдържа само главни латински букви и числа"
     * )
     */
    private $idNumber;

    /**
     * @var string|null
     *
     * @ORM\Column(name="id_frame", type="string", length=191, nullable=true)
     * @Assert\Regex(
     *     pattern="/^[A-Z0-9]*$/",
     *     message="Номер на рама трябва да съдържа само главни латински букви и числа"
     * )
     */
    private $idFrame;

    /**
     * @var string
     *
     * @ORM\Column(name="car_make", type="string", length=191, nullable=true)
     * @Assert\NotBlank(message="Марка на МПС е задължително поле.")
     */
    private $carMake;

    /**
     * @var string
     *
     * @ORM\Column(name="car_model", type="string", length=191, nullable=true)
     */
    private $carModel;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_right_steering_wheel", type="boolean")
     */
    private $isRightSteeringWheel;

    /**
     * @var string|null
     *
     * @ORM\Column(name="engine_vol", type="string", length=191, nullable=true)
     */
    private $engineVol;

    /**
     * @var int|null
     *
     * @ORM\Column(name="pow_kw", type="integer", nullable=true)
     */
    private $powKw;

    /**
     * @var int|null
     *
     * @ORM\Column(name="pow_hp", type="integer", nullable=true)
     */
    private $powHp;

    /**
     * @var string|null
     *
     * @ORM\Column(name="new_weight", type="string", length=191, nullable=true)
     */
    private $newWeight;

    /**
     * @var string|null
     *
     * @ORM\Column(name="gross_weight", type="string", length=191, nullable=true)
     */
    private $grossWeight;

    /**
     * @var string|null
     *
     * @ORM\Column(name="color", type="string", length=191, nullable=true)
     */
    private $color;

    /**
     * @var string|null
     *
     * @ORM\Column(name="year_made", type="string", length=191, nullable=true)
     */
    private $yearMade;

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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="createdCars")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     */
    private $author;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="updatedCars")
     * @ORM\JoinColumn(name="updated_by", referencedColumnName="id")
     */
    private $updater;

    /**
     * @var string|null
     *
     * @ORM\Column(name="notes", type="text", nullable=true)
     */
    private $notes;

    /**
     * @var Client
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Client", inversedBy="ownerCars", cascade={"persist"})
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", nullable=true)
     */
    private $owner;

    /**
     * @var Client
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Client", inversedBy="representativeCars", cascade={"persist"})
     * @ORM\JoinColumn(name="representative_id", referencedColumnName="id", nullable=true)
     */
    private $representative;

    /**
     * @var ArrayCollection|Policy[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Policy", mappedBy="car")
     */
    private $policies;

    /**
     * @var TypeOfCar
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TypeOfCar", inversedBy="cars")
     * @ORM\JoinColumn(name="car_type_id", referencedColumnName="id")
     * @Assert\NotBlank(message="Вид МПС е задължително поле.")
     */
    private $carType;

    /**
     * @var ArrayCollection|Document[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Document", mappedBy="car", cascade={"persist", "remove"})
     */
    private $documents;

    /**
     * Car constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->isRightSteeringWheel = false;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->policies = new ArrayCollection();
        $this->documents = new ArrayCollection();
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
     * @return Car
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
     * Set idFrame.
     *
     * @param string|null $idFrame
     *
     * @return Car
     */
    public function setIdFrame($idFrame = null)
    {
        $this->idFrame = $idFrame;

        return $this;
    }

    /**
     * Get idFrame.
     *
     * @return string|null
     */
    public function getIdFrame()
    {
        return $this->idFrame;
    }

    /**
     * Set isRightSteeringWheel.
     *
     * @param bool $isRightSteeringWheel
     *
     * @return Car
     */
    public function setIsRightSteeringWheel($isRightSteeringWheel)
    {
        $this->isRightSteeringWheel = $isRightSteeringWheel;

        return $this;
    }

    /**
     * Get isRightSteeringWheel.
     *
     * @return bool
     */
    public function getIsRightSteeringWheel()
    {
        return $this->isRightSteeringWheel;
    }

    /**
     * Set engineVol.
     *
     * @param string|null $engineVol
     *
     * @return Car
     */
    public function setEngineVol($engineVol = null)
    {
        $this->engineVol = $engineVol;

        return $this;
    }

    /**
     * Get engineVol.
     *
     * @return string|null
     */
    public function getEngineVol()
    {
        return $this->engineVol;
    }

    /**
     * Set powKw.
     *
     * @param int|null $powKw
     *
     * @return Car
     */
    public function setPowKw($powKw = null)
    {
        $this->powKw = $powKw;

        return $this;
    }

    /**
     * Get powKw.
     *
     * @return int|null
     */
    public function getPowKw()
    {
        return $this->powKw;
    }

    /**
     * Set powHp.
     *
     * @param int|null $powHp
     *
     * @return Car
     */
    public function setPowHp($powHp = null)
    {
        $this->powHp = $powHp;

        return $this;
    }

    /**
     * Get powHp.
     *
     * @return int|null
     */
    public function getPowHp()
    {
        return $this->powHp;
    }

    /**
     * Set newWeight.
     *
     * @param string|null $newWeight
     *
     * @return Car
     */
    public function setNewWeight($newWeight = null)
    {
        $this->newWeight = $newWeight;

        return $this;
    }

    /**
     * Get newWeight.
     *
     * @return string|null
     */
    public function getNewWeight()
    {
        return $this->newWeight;
    }

    /**
     * Set grossWeight.
     *
     * @param string|null $grossWeight
     *
     * @return Car
     */
    public function setGrossWeight($grossWeight = null)
    {
        $this->grossWeight = $grossWeight;

        return $this;
    }

    /**
     * Get grossWeight.
     *
     * @return string|null
     */
    public function getGrossWeight()
    {
        return $this->grossWeight;
    }

    /**
     * Set color.
     *
     * @param string|null $color
     *
     * @return Car
     */
    public function setColor($color = null)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color.
     *
     * @return string|null
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set madeAt.
     *
     * @param string|null $yearMade
     *
     * @return Car
     */
    public function setYearMade($yearMade = null)
    {
        $this->yearMade = $yearMade;

        return $this;
    }

    /**
     * Get madeAt.
     *
     * @return string|null
     */
    public function getYearMade()
    {
        return $this->yearMade;
    }

    /**
     * @return string
     */
    public function getCarMake()
    {
        return $this->carMake;
    }

    /**
     * @param string $carMake
     * @return Car
     */
    public function setCarMake(string $carMake)
    {
        $this->carMake = $carMake;

        return $this;
    }

    /**
     * @return string
     */
    public function getCarModel()
    {
        return $this->carModel;
    }

    /**
     * @param string $carModel
     * @return Car
     */
    public function setCarModel(string $carModel)
    {
        $this->carModel = $carModel;

        return $this;
    }

    /**
     * @return Client|null
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param Client $owner
     * @return Car
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
     * @param null|Client $representative
     * @return Car
     */
    public function setRepresentative(?Client $representative)
    {
        $this->representative = $representative;

        return $this;
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
     * @return Car
     */
    public function setCreatedAt(\DateTime $createdAt): Car
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     * @return Car
     */
    public function setUpdatedAt(\DateTime $updatedAt): Car
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getNotes(): ?string
    {
        return $this->notes;
    }

    /**
     * @param null|string $notes
     * @return Car
     */
    public function setNotes(?string $notes): Car
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * @return Policy[]|ArrayCollection
     */
    public function getPolicies()
    {
        return $this->policies;
    }

    /**
     * @param Policy[]|ArrayCollection $policies
     * @return Car
     */
    public function setPolicies($policies)
    {
        foreach ($policies as $policy) {
            $this->addPolicy($policy);
        }

        return $this;
    }

    /**
     * @param Policy $policy
     * @return $this
     */
    public function addPolicy(Policy $policy)
    {
        $this->policies->add($policy);
        $policy->setCar($this);

        return $this;
    }

    /**
     * @return TypeOfCar
     */
    public function getCarType()
    {
        return $this->carType;
    }

    /**
     * @param TypeOfCar $carType
     * @return Car
     */
    public function setCarType(TypeOfCar $carType)
    {
        $this->carType = $carType;

        return $this;
    }

    /**
     * @return Document[]|ArrayCollection
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * @param Document[]|ArrayCollection $documents
     * @return Car
     */
    public function setDocuments($documents)
    {
        foreach ($documents as $document) {
            $this->addDocument($document);
        }

        return $this;
    }

    /**
     * @param Document $document
     * @return $this
     */
    public function addDocument(Document $document)
    {
        if (!$this->documents->contains($document)) {
            $this->documents->add($document);
            $document->setCar($this);
        }

        return $this;
    }

    /**
     * @param Document $document
     * @return $this
     */
    public function removeDocument(Document $document)
    {
        if ($this->documents->contains($document)) {
            $this->documents->removeElement($document);
            $document->setCar(null);
        }

        return $this;
    }

    /**
     * @return null|User
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * @param User $author
     * @return Car
     */
    public function setAuthor(User $author): Car
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return null|User
     */
    public function getUpdater(): ?User
    {
        return $this->updater;
    }

    /**
     * @param User $updater
     * @return Car
     */
    public function setUpdater(User $updater): Car
    {
        $this->updater = $updater;

        return $this;
    }
}
