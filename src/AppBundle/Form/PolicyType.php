<?php
declare(strict_types=1);

namespace AppBundle\Form;

use AppBundle\Entity\Insurer;
use AppBundle\Entity\Policy;
use AppBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;

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
                'attr' => ['class' => 'form-control-sm js-datepicker'],
                'format' => 'dd.MM.yyyy',
                'label' => 'Дата на издаване'
            ])
            ->add('startsAt', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'form-control-sm js-datepicker'],
                'format' => 'dd.MM.yyyy',
                'label' => 'Валидна от'
            ])
            ->add('expiresAt', DateType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'form-control-sm js-datepicker'],
                'format' => 'dd.MM.yyyy',
                'label' => 'Изтича на'
            ])
            ->add('amount', NumberType::class, [
                'label' => 'Цена Г.О.',
                'attr' => ['class' => 'form-control-sm']
            ])
            ->add('taxes', HiddenType::class, [
                'label' => 'Данък %',
                'attr' => [
                    'readonly' => true,
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('amountGf', HiddenType::class, [
                'label' => 'Г.Ф.',
                'attr' => [
                    'readonly' => true,
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('officeCommission', NumberType::class, [
                'label' => '% на офиса',
                'attr' => [
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('clientCommission', NumberType::class, [
                'label' => '% на клиента',
                'attr' => [
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('billTotal', HiddenType::class, [
                'label' => 'Дължимо сметки',
                'attr' => [
                    'readonly' => true,
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('greenCardTotal', HiddenType::class, [
                'label' => 'Дължимо З.К.',
                'attr' => [
                    'readonly' => true,
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('total', HiddenType::class, [
                'label' => 'Дължима премия',
                'attr' => [
                    'readonly' => true,
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('notes', TextareaType::class, [
                'label' => 'Бележки',
                'attr' => [
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('currency', ChoiceType::class, [
                    'choices' => [
                        'лв.' => 'BGN',
                        '€' => 'EUR',
                        '$' => 'USD',
                        '£' => 'GBP',
                    ],
                    'empty_data' => null,
                    'label' => 'Валута',
                    'attr' => [
                        'class' => 'form-control-sm'
                    ]
                ]
            )
            ->add('payments', CollectionType::class, [
                'entry_type' => PaymentType::class,
                'entry_options' => ['label' => false],
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'label' => false
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var Policy $policy */
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
                       'label' => 'Застраховател',
                       'attr' => [
                           'class' => 'form-control-sm'
                       ]
                   ])
                   ->add('idNumber', TextType::class, [
                       'label' => 'Застр. полица No',
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
                   ]);

            } else {
                $form
                    ->add('isCancelled', CheckboxType::class, ['label' => 'Анулирана?'])
                    ->add('greenCards', CollectionType::class, [
                        'entry_type' => GreenCardType::class,
                        'entry_options' => ['label' => false],
                        'by_reference' => false,
                        'allow_add' => true,
                        'allow_delete' => true,
                        'label' => false
                    ])
                    ->add('stickers', CollectionType::class, [
                        'entry_type' => StickerType::class,
                        'entry_options' => ['label' => false],
                        'by_reference' => false,
                        'allow_add' => true,
                        'allow_delete' => true,
                        'label' => false
                    ])
                    ->add('bills', CollectionType::class, [
                        'entry_type' => BillType::class,
                        'entry_options' => ['label' => false],
                        'by_reference' => false,
                        'allow_add' => true,
                        'allow_delete' => true,
                        'label' => false
                    ]);
            }

            if (null !== $policy->getCar()) {
                $form->add('car', CarType::class, [
                    'block_name' => 'policy_form',
                    'constraints' => [new Valid()]
                ]);
            }
            if (null !== $policy->getOwner()) {
                $form->add('owner', ClientType::class, [
                    'block_name' => 'policy_form',
                    'constraints' => [new Valid()]
                ]);
            }
            if (null !== $policy->getRepresentative()) {
                $form->add('representative', ClientType::class, [
                    'block_name' => 'policy_form',
                    'constraints' => [new Valid()],
                ]);
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
