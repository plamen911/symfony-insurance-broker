<?php
declare(strict_types=1);

namespace AppBundle\Service;

use Symfony\Component\Form\FormInterface;

/**
 * Interface FormErrorsServiceInterface
 * @package AppBundle\Service
 */
interface FormErrorServiceInterface
{
    /**
     * @param FormInterface $form
     * @return FormInterface
     */
    public function checkErrors(FormInterface $form);
}
