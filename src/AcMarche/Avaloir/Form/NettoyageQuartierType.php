<?php

namespace AcMarche\Avaloir\Form;

use AcMarche\Avaloir\Entity\DateNettoyage;
use AcMarche\Avaloir\Form\Type\QuartierHiddenType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NettoyageQuartierType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'jour',
                DateType::class,
                array(
                    'widget' => 'single_text',
                    'label' => 'Date de nettoyage',
                    'format' => 'dd/MM/yyyy',
                    'required' => true,
                    'attr' => array('class' => 'datepicker', 'autocomplete' => 'off'),
                )
            )
            ->add('quartier', QuartierHiddenType::class);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => DateNettoyage::class,
            )
        );
    }
}
