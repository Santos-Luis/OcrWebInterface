<?php

namespace App\Form;

use App\Entity\PhotoUrl;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * InputPhotoUrlType
 */
class InputPhotoUrlType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'url',
                TextType::class,
                [
                    'attr' => [
                        'placeholder' => 'Please provide the photo url',
                        'style' => 'width: 200px; height: 20px; padding: 8px 16px; text-align: center; margin: 10px'
                    ],
                    'label' => false
                ]
            )
            ->add('submit', SubmitType::class, [ 'attr' => [ 'style' => 'width: 100px; height: 25px' ] ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PhotoUrl::class,
        ]);
    }
}
