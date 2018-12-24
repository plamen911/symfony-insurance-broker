<?php
declare(strict_types=1);

namespace AppBundle\Service\FormError;

use Symfony\Component\Form\FormInterface;

/**
 * Interface FormErrorsServiceInterface
 * @package AppBundle\Service\FormError
 */
interface FormErrorServiceInterface
{
    /**
     * @param FormInterface $form
     * @return FormInterface
     */
    public function checkErrors(FormInterface $form);
}
