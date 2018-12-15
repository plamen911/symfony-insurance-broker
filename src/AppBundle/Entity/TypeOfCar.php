<?php
declare(strict_types=1);

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * TypeOfCar
 *
 * @ORM\Table(name="car_types")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TypeOfCarRepository")
 */
class TypeOfCar
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
     * @ORM\Column(name="name", type="string", length=191, unique=true)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer", nullable=true)
     */
    private $position;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_deleted", type="boolean", nullable=true)
     */
    private $isDeleted;

    /**
     * @var ArrayCollection|Car[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Car", mappedBy="carType")
     */
    private $cars;

    /**
     * TypeOfCar constructor.
     */
    public function __construct()
    {
        $this->isDeleted = false;
        $this->cars = new ArrayCollection();
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
     * Set name.
     *
     * @param string $name
     *
     * @return TypeOfCar
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     * @return TypeOfCar
     */
    public function setPosition(int $position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Set isDeleted.
     *
     * @param bool $isDeleted
     *
     * @return TypeOfCar
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * Get isDeleted.
     *
     * @return bool
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * @return Car[]|ArrayCollection
     */
    public function getCars()
    {
        return $this->cars;
    }

    /**
     * @param Car[]|ArrayCollection $cars
     * @return TypeOfCar
     */
    public function setCars($cars)
    {
        foreach ($cars as $car) {
            $this->addCar($car);
        }

        return $this;
    }

    /**
     * @param Car $car
     * @return TypeOfCar
     */
    public function addCar(Car $car)
    {
        $this->cars->add($car);

        return $this;
    }
}
