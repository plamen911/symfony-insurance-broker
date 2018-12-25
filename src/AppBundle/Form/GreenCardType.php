<?php
declare(strict_types=1);

namespace AppBundle\Form;

use AppBundle\Entity\GreenCard;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class GreenCardType
 * @package AppBundle\Form
 * @author Plamen Markov <plamen@lynxlake.org>
 */
class GreenCardType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idNumber', TextType::class, [
                'label' => 'Зелена карта No',
                'label_attr' => [
                    'class' => 'sr-only'
                ],
                'attr' => [
                    'class' => 'mr-2',
                    'placeholder' => 'Зелена карта No',
                ]
            ])
            ->add('price', NumberType::class, [
                'label' => 'Цена З.К.',
                'label_attr' => [
                    'class' => 'sr-only'
                ],
                'attr' => [
                    'class' => 'mr-2',
                    'placeholder' => 'Цена З.К.',
                ]
            ])
            ->add('tax', NumberType::class, [
                'label' => 'Данък',
                'label_attr' => [
                    'class' => 'sr-only'
                ],
                'attr' => [
                    'class' => 'mr-2',
                    'placeholder' => 'Данък',
                ]
            ])
            ->add('amountDue', NumberType::class, [
                'label' => 'Дължимо',
                'label_attr' => [
                    'class' => 'sr-only'
                ],
                'attr' => [
                    'class' => 'mr-2',
                    'placeholder' => 'Дължимо',
                ]
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => GreenCard::class
        ));
    }
}
