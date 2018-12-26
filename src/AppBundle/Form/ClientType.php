<?php
declare(strict_types=1);

namespace AppBundle\Form;

use AppBundle\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ClientType
 * @package AppBundle\Form
 * @author Plamen Markov <plamen@lynxlake.org>
 */
class ClientType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Име',
                'attr' => [
                    'placeholder' => 'Име',
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('middleName', TextType::class, [
                'label' => 'Презиме',
                'attr' => [
                    'placeholder' => 'Презиме',
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Фамилия',
                'attr' => [
                    'placeholder' => 'Фамилия',
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('idNumber', TextType::class, [
                'label' => 'ЕГН/ЕИК/БУЛСТАТ',
                'attr' => [
                    'placeholder' => 'ЕГН/ЕИК/БУЛСТАТ',
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'Населено място (гр./с.)',
                'attr' => [
                    'placeholder' => 'Населено място (гр./с.)',
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('street', TextType::class, [
                'label' => 'Адрес (ул./жк.)',
                'attr' => [
                    'placeholder' => 'Адрес (ул./жк.)',
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('phone', TelType::class, [
                'label' => 'Телефон',
                'attr' => [
                    'placeholder' => 'Телефон',
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('phone2', TelType::class, [
                'label' => 'Телефон 2',
                'attr' => [
                    'placeholder' => 'Телефон 2',
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'И-мейл',
                'attr' => [
                    'placeholder' => 'И-мейл',
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('email2', EmailType::class, [
                'label' => 'И-мейл 2',
                'attr' => [
                    'placeholder' => 'И-мейл 2',
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
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Client::class
        ));
    }
}
