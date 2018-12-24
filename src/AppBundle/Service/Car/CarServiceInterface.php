<?php
declare(strict_types=1);

namespace AppBundle\Service\Car;

use AppBundle\Entity\Car;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface CarServiceInterface
 * @package AppBundle\Service\Car
 */
interface CarServiceInterface
{
    /**
     * @param string $keyword
     * @return Car[]|null
     */
    public function findByKeyword(string $keyword);

    /**
     * @param Request $request
     * @param Car $car
     * @return Car
     */
    public function newCar(Request $request, Car $car);

    /**
     * @param Request $request
     * @param Car $car
     * @return Car
     * @throws \Exception
     */
    public function editCar(Request $request, Car $car);

    /**
     * @param Car $car
     */
    public function deleteCar(Car $car);

    /**
     * @param Car $car
     * @return bool
     */
    public function canDelete(Car $car);
}
