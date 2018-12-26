<?php
declare(strict_types=1);

namespace AppBundle\Form;

use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

/**
 * Class ProfileType
 * @package AppBundle\Form
 * @author Plamen Markov <plamen@lynxlake.org>
 */
class ProfileType extends AbstractType
{
    /** @var Security $security */
    private $security;

    /**
     * ProfileType constructor.
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var User $currentUser */
        $currentUser = $options['user'];

        $builder
            ->add('full_name', TextType::class, [
                'label' => 'Име, презиме, фамилия',
                'attr' => [
                    'placeholder' => 'Име, презиме, фамилия',
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'И-мейл',
                'attr' => [
                    'placeholder' => 'И-мейл',
                    'class' => 'form-control-sm'
                ]
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($currentUser) {
            /** @var User $user */
            $user = $event->getData();
            $form = $event->getForm();
            if (!$user || null === $user->getId()) {
                $form->add('password', RepeatedType::class, [
                        'type' => PasswordType::class,
                        'invalid_message' => 'Паролите не съвпадат.',
                        'first_options' => [
                            'label' => 'Парола',
                            'attr' => [
                                'placeholder' => 'Парола',
                                'class' => 'form-control-sm'
                            ],
                        ],
                        'second_options' => [
                            'label' => 'Повтори парола',
                            'attr' => [
                                'placeholder' => 'Повтори парола',
                                'class' => 'form-control-sm'
                            ]
                        ],
                    ]
                );
            } else {
                $form->add('old_password', PasswordType::class, [
                    'mapped' => false,
                    'label' => 'Стара парола',
                    'attr' => [
                        'placeholder' => 'Стара парола',
                        'class' => 'form-control-sm'
                    ]
                ])
                    ->add('new_password', PasswordType::class, [
                        'mapped' => false,
                        'label' => 'Нова парола',
                        'attr' => [
                            'placeholder' => 'Нова парола',
                            'class' => 'form-control-sm'
                        ],
                        'constraints' => [

                        ]
                    ]);
            }

            if ($this->security->isGranted(['ROLE_SUPER_ADMIN', 'ROLE_ADMIN']) && $user->getId() !== $currentUser->getId()) {
                $form->add('profileRoles', EntityType::class, [
                    'label' => 'Роли',
                    'class' => Role::class,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('r')
                            ->where('r.name != :name')
                            ->setParameter('name', 'ROLE_SUPER_ADMIN')
                            ->orderBy('r.position', 'ASC');
                    },
                    'choice_label' => 'title',
                    'multiple' => true,
                    'expanded' => true,
                    'attr' => ['class' => 'checkbox_container']
                ])->add('enabled', CheckboxType::class, ['label' => 'Активен?']);
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'user' => null,
            'validation_groups' => function (FormInterface $form) {
                /** @var User $user */
                $user = $form->getData();
//                $oldPassword = $form->get('old_password')->getData();
//                $newPassword = $form->get('new_password')->getData();
                if (!$user || null === $user->getId()) {
                    return ['registration'];
                }
                return [];
            },
        ]);
    }
}
