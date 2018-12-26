<?php
declare(strict_types=1);

namespace AppBundle\Form;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserType
 * @package AppBundle\Form
 * @author Plamen Markov <plamen@lynxlake.org>
 */
class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('full_name', TextType::class, [
                'label' => 'Full Name',
                'attr' => [
                    'placeholder' => 'First Name',
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-mail',
                'attr' => [
                    'placeholder' => 'E-mail',
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'first_options'  => [
                        'label' => 'Password',
                        'attr' => [
                            'placeholder' => 'Password',
                            'class' => 'form-control-sm'
                        ]
                    ],
                    'second_options' => [
                        'label' => 'Repeat Password',
                        'attr' => [
                            'placeholder' => 'Repeat Password',
                            'class' => 'form-control-sm'
                        ]
                    ]
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class
        ]);
    }
}
