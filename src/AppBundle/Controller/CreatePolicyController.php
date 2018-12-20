<?php
declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\Car;
use AppBundle\Entity\TypeOfPolicy;
use AppBundle\Form\CarType;
use AppBundle\Service\Aws\UploadInterface;
use AppBundle\Service\FormErrorServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CreatePolicyController
 * @package AppBundle\Controller
 * @author Plamen Markov <plamen@lynxlake.org>
 */
class CreatePolicyController extends Controller
{
    /** @var EntityManagerInterface $em */
    private $em;
    /** @var UploadInterface $uploadService */
    private $uploadService;
    /** @var FormErrorServiceInterface $formErrorService */
    private $formErrorService;

    /**
     * PolicyController constructor.
     * @param EntityManagerInterface $em
     * @param UploadInterface $uploadService
     * @param FormErrorServiceInterface $formErrorsService
     */
    public function __construct(EntityManagerInterface $em, UploadInterface $uploadService, FormErrorServiceInterface $formErrorsService)
    {
        $this->em = $em;
        $this->uploadService = $uploadService;
        $this->formErrorService = $formErrorsService;
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
            $em = $this->getDoctrine()->getManager();
            $em->persist($car);
            $em->flush();

            return $this->redirectToRoute('car_edit', ['id' => $car->getId()]);
        }

        return $this->render('create-policy/car.html.twig', [
            'typeOfPolicy' => $typeOfPolicy,
            'car' => $car,
            'form' => $form->createView(),
        ]);
    }
}
