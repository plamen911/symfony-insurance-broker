<?php
declare(strict_types=1);

namespace AppBundle\Service\Car;

use AppBundle\Entity\Car;
use AppBundle\Entity\Document;
use AppBundle\Entity\User;
use AppBundle\Repository\CarRepository;
use AppBundle\Service\Aws\UploadInterface;
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
    /** @var User $currentUser */
    private $currentUser;

    /** @var CarRepository */
    private $carRepo;

    /** @var UploadInterface $uploadService */
    private $uploadService;

    /**
     * ReportService constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param CarRepository $carRepo
     * @param UploadInterface $uploadService
     */
    public function __construct(TokenStorageInterface $tokenStorage, CarRepository $carRepo, UploadInterface $uploadService)
    {
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
        $this->processUpload($request, $car);

        $car->setAuthor($this->currentUser);
        $car->setUpdater($this->currentUser);
        $this->carRepo->save($car);

        return $car;
    }

    /**
     * @param Request $request
     * @param Car $car
     * @return Car
     * @throws \Exception
     */
    public function editCar(Request $request, Car $car)
    {
        $this->processUpload($request, $car);

        $car->setUpdatedAt(new \DateTime());
        $car->setUpdater($this->currentUser);
        $this->carRepo->save($car);

        return $car;
    }

    /**
     * @param Car $car
     */
    public function deleteCar(Car $car)
    {
        // delete uploaded files from S3 cloud
        if ($car->getDocuments()->count() > 0) {
            foreach ($car->getDocuments() as $document) {
                $this->uploadService->delete(basename($document->getFileUrl()));
            }
        }

        $this->carRepo->delete($car);
    }

    /**
     * @param Car $car
     * @return bool
     */
    public function canDelete(Car $car)
    {
        return $this->currentUser->isAdmin() || (null !== $car->getAuthor() && $this->currentUser->getId() === $car->getAuthor()->getId());
    }

    /**
     * Upload car documents
     *
     * @param Request $request
     * @param Car $car
     */
    private function processUpload(Request $request, Car $car): void
    {
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
    }
}