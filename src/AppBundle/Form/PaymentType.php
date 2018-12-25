<?php
declare(strict_types=1);

namespace AppBundle\Form;

use AppBundle\Entity\Payment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PaymentType
 * @package AppBundle\Form
 * @author Plamen Markov <plamen@lynxlake.org>
 */
class PaymentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dueAt', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'class' => 'form-control-sm js-datepicker mr-2',
                    'placeholder' => 'Дата на падежа',
                ],
                'format' => 'dd.MM.yyyy',
                'label' => 'Дата на падежа',
                'label_attr' => [
                    'class' => 'sr-only'
                ]
            ])
            ->add('amountDue', NumberType::class, [
                'label' => 'Вноска',
                'label_attr' => [
                    'class' => 'sr-only'
                ],
                'attr' => [
                    'class' => 'form-control-sm mr-2',
                    'placeholder' => 'Вноска',
                ]
            ])
            ->add('paidAt', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'class' => 'form-control-sm js-datepicker mr-2',
                    'placeholder' => 'Дата на плащане',
                ],
                'format' => 'dd.MM.yyyy',
                'label' => 'Дата на плащане',
                'label_attr' => [
                    'class' => 'sr-only'
                ],
                'required' => false
            ])
            ->add('amountPaid', NumberType::class, [
                'label' => 'Платена сума',
                'label_attr' => [
                    'class' => 'sr-only'
                ],
                'attr' => [
                    'class' => 'form-control-sm mr-2',
                    'placeholder' => 'Платена сума',
                    'required' => false
                ]
            ])
            ->add('isDeferred', CheckboxType::class, [
                'label' => 'Отложено плащане?',
                'attr' => [
                    'class' => 'mr-2'
                ]
            ])
            ->add('paymentOrder', HiddenType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Payment::class
        ));
    }
}
