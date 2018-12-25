<?php
declare(strict_types=1);

namespace AppBundle\Form;

use AppBundle\Entity\Sticker;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class StickerType
 * @package AppBundle\Form
 * @author Plamen Markov <plamen@lynxlake.org>
 */
class StickerType extends AbstractType
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
                    'class' => 'mr-2',
                    'placeholder' => 'Стикер No',
                ]
            ])
            ->add('isCancelled', CheckboxType::class, ['label' => 'Анулиран?']);
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Sticker::class
        ));
    }
}
