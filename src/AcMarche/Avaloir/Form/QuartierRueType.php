<?php

namespace AcMarche\Avaloir\Form;

use AcMarche\Avaloir\Entity\Quartier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuartierRueType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'rueids',
                HiddenType::class,
                array(
                    'label' => 'RueIds',
                    'required' => true,
                )
            )
            ->add(
                'tokenfield',
                TextType::class,
                array(
                    'mapped' => false,
                    'required' => true,
                    'label' => 'Rues',
                )
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => Quartier::class,
            )
        );
    }
}
