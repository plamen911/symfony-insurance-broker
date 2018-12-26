<?php
declare(strict_types=1);

namespace AppBundle\Form;

use AppBundle\Entity\Bill;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BillType
 * @package AppBundle\Form
 * @author Plamen Markov <plamen@lynxlake.org>
 */
class BillType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idNumber', TextType::class, [
                'label' => 'Сметка No',
                'label_attr' => [
                    'class' => 'sr-only'
                ],
                'attr' => [
                    'class' => 'form-control-sm mr-2',
                    'placeholder' => 'Сметка No',
                ]
            ])
            ->add('price', NumberType::class, [
                'label' => 'Цена сметка',
                'label_attr' => [
                    'class' => 'sr-only'
                ],
                'attr' => [
                    'class' => 'form-control-sm mr-2',
                    'placeholder' => 'Цена сметка',
                ]
            ]);
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Bill::class
        ));
    }
}
