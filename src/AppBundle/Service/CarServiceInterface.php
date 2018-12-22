<?php
declare(strict_types=1);

namespace AppBundle\Service;

use AppBundle\Entity\Car;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface CarServiceInterface
 * @package AppBundle\Service
 */
interface CarServiceInterface
{
    /**
     * @param Request $request
     * @param Car $car
     * @return Car
     */
    public function newCar(Request $request, Car $car);
}
