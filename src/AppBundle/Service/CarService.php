<?php
declare(strict_types=1);

namespace AppBundle\Service;

use AppBundle\Entity\Car;
use AppBundle\Entity\Document;
use AppBundle\Entity\User;
use AppBundle\Repository\CarRepository;
use AppBundle\Service\Aws\UploadInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class CarService
 * @package AppBundle\Service
 * @author Plamen Markov <plamen@lynxlake.org>
 */
class CarService implements CarServiceInterface
{
    /** @var EntityManagerInterface $em */
    private $em;

    /** @var User $currentUser */
    private $currentUser;

    /** @var CarRepository */
    private $carRepo;

    /** @var UploadInterface $uploadService */
    private $uploadService;

    /**
     * ReportService constructor.
     * @param EntityManagerInterface $em
     * @param TokenStorageInterface $tokenStorage
     * @param CarRepository $carRepo
     * @param UploadInterface $uploadService
     */
    public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage, CarRepository $carRepo, UploadInterface $uploadService)
    {
        $this->em = $em;
        $this->currentUser = $tokenStorage->getToken()->getUser();
        $this->carRepo = $carRepo;
        $this->uploadService = $uploadService;
    }

    /**
     * @param Request $request
     * @param Car $car
     * @return Car
     */
    public function newCar(Request $request, Car $car)
    {
        // upload car documents
        if (null !== $request->files->get('documents')) {
            /** @var UploadedFile $file */
            foreach ($request->files->get('documents') as $file) {
                $fileUrl = $this->uploadService->upload(
                    $file->getPathname(),
                    $this->uploadService->generateUniqueFileName() . '.' . $file->getClientOriginalExtension(),
                    $file->getClientMimeType()
                );

                $document = new Document();
                $document->setFileUrl($fileUrl);
                $document->setFileName($file->getClientOriginalName());
                $document->setMimeType($file->getClientMimeType());
                $car->addDocument($document);
            }
        }

        $car->setAuthor($this->currentUser);
        $car->setUpdater($this->currentUser);
        $this->em->persist($car);
        $this->em->flush();

        return $car;
    }
}