<?php
declare(strict_types=1);

namespace AppBundle\Form;

use AppBundle\Entity\Insurer;
use AppBundle\Entity\Sticker;
use AppBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class StickerFormType
 * @package AppBundle\Form
 * @author Plamen Markov <plamen@lynxlake.org>
 */
class StickerFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idNumber', TextType::class, [
                'label' => 'Стикер No',
                'label_attr' => [
                    'class' => 'sr-only'
                ],
                'attr' => [
                    'class' => 'form-control-sm mr-2',
                    'placeholder' => 'Стикер No',
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
                'label' => 'Дата на получаване от застрахователя'
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
            ])
            ->add('isCancelled', CheckboxType::class, ['label' => 'Анулиран?']);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Sticker::class
        ));
    }
}
