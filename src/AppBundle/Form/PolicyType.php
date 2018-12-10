<?php

namespace AppBundle\Form;

use AppBundle\Entity\Car;
use AppBundle\Entity\Insurer;
use AppBundle\Entity\Policy;
use AppBundle\Entity\TypeOfPolicy;
use AppBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * https://symfony.com/doc/3.4/form/form_collections.html
 *
 * Class PolicyType
 * @package AppBundle\Form
 * @author Plamen Markov <plamen@lynxlake.org>
 */
class PolicyType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('issuedAt', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker'],
                'format' => 'dd.MM.yyyy',
                'label' => 'Дата на издаване'
            ])
            ->add('startsAt', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker'],
                'format' => 'dd.MM.yyyy',
                'label' => 'Започва от'
            ])
            ->add('expiresAt', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker'],
                'format' => 'dd.MM.yyyy',
                'label' => 'Изтича на'
            ])
            ->add('amount', NumberType::class, ['label' => 'Цена Г.О.'])
            ->add('taxes', NumberType::class, ['label' => 'Данък %'])
            ->add('amountGf', NumberType::class, ['label' => 'Г.Ф.'])
            ->add('officeDiscount', NumberType::class, ['label' => '% на офиса'])
            ->add('clientDiscount', NumberType::class, ['label' => '% на клиента'])
            ->add('total', NumberType::class, ['label' => 'Общо дължима премия'])
            ->add('notes', TextareaType::class, ['label' => 'Бележки'])
            ->add('currency', ChoiceType::class, [
                    'choices' => [
                        'лв.' => 'BGN',
                        '€' => 'EUR',
                        '$' => 'USD',
                        '£' => 'GBP',
                    ],
                    'empty_data' => null,
                    'label' => 'Валута'
                ]
            )
            ->add('payments', CollectionType::class, [
                'entry_type' => PaymentType::class,
                'entry_options' => ['label' => false],
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $policy = $event->getData();
            $form = $event->getForm();
            if (!$policy || null === $policy->getId()) {
               $form
//                   ->add('policyType', EntityType::class, [
//                       'class' => TypeOfPolicy::class,
//                       'choice_label' => 'long_name',
//                       'placeholder' => '- избери -',
//                       'label' => 'Вид полица'
//                   ])
                   ->add('insurer', EntityType::class, [
                       'class' => Insurer::class,
                       'choice_label' => 'long_name',
                       'placeholder' => '- избери -',
                       'label' => 'Застраховател'
                   ])
                   ->add('idNumber', TextType::class, ['label' => 'Застр. полица No'])
                   ->add('agent', EntityType::class, [
                       'class' => User::class,
                       'choice_label' => 'full_name',
                       'placeholder' => '- избери -',
                       'label' => 'Агент'
                   ]);

            } else {
                $form->add('isCancelled', CheckboxType::class, ['label' => 'Анулирана?']);
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Policy::class
        ]);
    }
}
