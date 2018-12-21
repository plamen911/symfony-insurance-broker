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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserProfileType
 * @package AppBundle\Form
 * @author Plamen Markov <plamen@lynxlake.org>
 */
class UserProfileType extends AbstractType
{
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
            ->add('enabled', CheckboxType::class, ['label' => 'Активен?'])
            ->add('profileRoles', EntityType::class, [
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
                ])
        ;
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
