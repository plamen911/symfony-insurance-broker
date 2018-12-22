<?php
declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\Car;
use AppBundle\Entity\TypeOfPolicy;
use AppBundle\Form\CarType;
use AppBundle\Service\CarServiceInterface;
use AppBundle\Service\FormErrorServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CreatePolicyController
 * @package AppBundle\Controller
 * @author Plamen Markov <plamen@lynxlake.org>
 *
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 */
class CreatePolicyController extends Controller
{
    /** @var FormErrorServiceInterface $formErrorService */
    private $formErrorService;

    /** @var CarServiceInterface $carService */
    private $carService;

    /**
     * PolicyController constructor.
     * @param FormErrorServiceInterface $formErrorsService
     * @param CarServiceInterface $carService
     */
    public function __construct(FormErrorServiceInterface $formErrorsService, CarServiceInterface $carService)
    {
        $this->formErrorService = $formErrorsService;
        $this->carService = $carService;
    }

    /**
     * @Route("/policy/new/type/{typeOfPolicy}/car/", name="policy_add_car", methods={"GET","POST"}, requirements={"typeOfPolicy": "\d+"})
     * @param Request $request
     * @param TypeOfPolicy $typeOfPolicy
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function carAction(Request $request, TypeOfPolicy $typeOfPolicy)
    {
        $car = new Car();
        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);

        $this->formErrorService->checkErrors($form);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->carService->newCar($request, $car);
            $this->addFlash('success', 'МПС бе успешно създадено.');

            return $this->redirectToRoute('car_edit', ['id' => $car->getId()]);
        }

        return $this->render('create-policy/car.html.twig', [
            'typeOfPolicy' => $typeOfPolicy,
            'car' => $car,
            'form' => $form->createView(),
        ]);
    }
}
