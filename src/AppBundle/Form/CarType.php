<?php
declare(strict_types=1);

namespace AppBundle\Form;

use AppBundle\Entity\Car;
use AppBundle\Entity\TypeOfCar;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CarType
 * @package AppBundle\Form
 * @author Plamen Markov <plamen@lynxlake.org>
 */
class CarType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('carType', EntityType::class, [
                'class' => TypeOfCar::class,
                'choice_label' => 'name',
                'placeholder' => '- избери -',
                'label' => 'Вид МПС',
                'attr' => [
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('idNumber', TextType::class, [
                'label' => 'Рег. No',
                'attr' => [
                    'placeholder' => 'Рег. No',
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('carMake', TextType::class, [
                'label' => 'Марка',
                'attr' => [
                    'placeholder' => 'Марка',
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('carModel', TextType::class, [
                'label' => 'Модел',
                'attr' => [
                    'placeholder' => 'Модел',
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('idFrame', TextType::class, [
                'label' => 'Рама No',
                'attr' => [
                    'placeholder' => 'Рама No',
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('isRightSteeringWheel', null, ['label' => 'Десен волан?'])
            ->add('engineVol', TextType::class, [
                'label' => 'Двиг. обем (см3)',
                'attr' => [
                    'placeholder' => 'Двиг. обем (куб.см)',
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('newWeight', NumberType::class, [
                'label' => 'Полезен товар (тон)',
                'attr' => [
                    'placeholder' => 'Полезен товар (тон)',
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('grossWeight', NumberType::class, [
                'label' => 'Общо тегло (тон)',
                'attr' => [
                    'placeholder' => 'Общо тегло (тон)',
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('color', TextType::class, [
                'label' => 'Цвят',
                'attr' => [
                    'placeholder' => 'Цвят',
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('yearMade', TextType::class, [
                'label' => 'Год. на произв.',
                'attr' => [
                    'placeholder' => 'Год. на произв.',
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('notes', TextareaType::class, [
                'label' => 'Бележки',
                'attr' => [
                    'placeholder' => 'Бележки',
                    'class' => 'form-control-sm'
                ]
            ]);

        if (null === $options['block_name']) {
            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                /** @var Car $car */
                $car = $event->getData();
                $form = $event->getForm();
                if ($car && null !== $car->getId()) {
                    if (null !== $car->getOwner()) {
                        $form->add('owner', ClientType::class);
                    }
                    if (null !== $car->getRepresentative()) {
                        $form->add('representative', ClientType::class);
                    }
                }
            });
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Car::class
        ));
    }
}
