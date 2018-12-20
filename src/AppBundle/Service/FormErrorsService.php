<?php
declare(strict_types=1);

namespace AppBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class ErrorMessageService
 * @package AppBundle\Service
 * @author Plamen Markov <plamen@lynxlake.org>
 */
class FormErrorsService implements FormErrorsServiceInterface
{
    private $container;

    /**
     * FormErrorsService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param FormInterface $form
     * @return FormInterface
     * @throws \Exception
     */
    public function checkErrors(FormInterface $form)
    {
        $errors = $form->getErrors(true, true);
        if (!empty($errors)) {
            foreach ($errors as $error) {
                // dump($error->getMessage());
                $this->container->get('session')->getFlashBag()->add('danger', $error->getMessage());
            }
        }

        return $form;
    }
}
