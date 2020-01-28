<?php

namespace AcMarche\Avaloir\Form;

use AcMarche\Avaloir\Entity\Avaloir;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AvaloirType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'rueId',
                HiddenType::class,
                array(
                    'required' => true,
                )
            )
            ->add(
                'dates',
                CollectionType::class,
                array(
                    'entry_type' => DateNettoyageType::class,
                    'required' => false,
                    'allow_add' => false,
                    'label' => ' ',
                    'prototype' => true,
                    'allow_delete' => false,
                )
            )
            ->add(
                'rue',
                TextType::class,
                array(
                    'required' => true,
                    'mapped' => false,
                    'attr' => array('class' => 'typeahead'),
                )
            )
            ->add(
                'numero',
                TextType::class,
                [
                    'required' => false,
                    'help' => 'Emplacement approximatif dans la rue'
                ]
            )
            ->add(
                'descriptif',
                TextareaType::class,
                array(
                    'required' => false,
                    'attr' => array('rows' => 5),
                )
            )
            ->add(
                'date_rappel',
                DateType::class,
                array(
                    'widget' => 'single_text',
                    'required' => false,
                    'label' => 'Date de rappel',
                    'format' => 'dd/MM/yyyy',
                    'attr' => array( 'autocomplete' => 'off'),
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
                'data_class' => Avaloir::class,
            )
        );
    }
}
