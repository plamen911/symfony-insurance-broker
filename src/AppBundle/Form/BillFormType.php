<?php
declare(strict_types=1);

namespace AppBundle\Form;

use AppBundle\Entity\Bill;
use AppBundle\Entity\Insurer;
use AppBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BillFormType
 * @package AppBundle\Form
 * @author Plamen Markov <plamen@lynxlake.org>
 */
class BillFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idNumber', TextType::class, [
                'label' => 'Сметка No',
                'attr' => [
                    'class' => 'form-control-sm mr-2',
                    'placeholder' => 'Сметка No',
                ]
            ])
            ->add('insurer', EntityType::class, [
                'class' => Insurer::class,
                'choice_label' => 'long_name',
                'placeholder' => '- избери -',
                'label' => 'Застраховател',
                'attr' => [
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('agent', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'full_name',
                'placeholder' => '- избери -',
                'label' => 'Агент',
                'attr' => [
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('receivedAt', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'class' => 'form-control-sm js-datepicker',
                    'placeholder' => 'Дата на получаване'
                ],
                'format' => 'dd.MM.yyyy',
                'label' => 'Дата на получаване от застр.'
            ])
            ->add('givenAt', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'class' => 'form-control-sm js-datepicker',
                    'placeholder' => 'Дата на предаване'
                ],
                'format' => 'dd.MM.yyyy',
                'label' => 'Дата на предаване на агента'
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Bill::class
        ]);
    }
}
