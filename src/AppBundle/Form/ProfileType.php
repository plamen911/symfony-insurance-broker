<?php
declare(strict_types=1);

namespace AppBundle\Form;

use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
        $builder
            ->add('full_name', TextType::class, [
                'label' => 'Име, презиме, фамилия',
                'attr' => [
                    'placeholder' => 'Име, презиме, фамилия'
                ]
            ])
            ->add('email', EmailType::class, ['label' => 'И-мейл', 'attr' => ['placeholder' => 'И-мейл']])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var User $user */
            $user = $event->getData();
            $form = $event->getForm();
            if ($this->security->isGranted(['ROLE_SUPER_ADMIN', 'ROLE_ADMIN'])) {
                $form->add('profileRoles', EntityType::class, [
                    'label' => 'Права',
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
            'data_class' => User::class
        ]);
    }
}
