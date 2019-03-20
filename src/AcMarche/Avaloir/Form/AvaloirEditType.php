<?php

namespace AcMarche\Avaloir\Form;

use AcMarche\Avaloir\Entity\Avaloir;
use AcMarche\Avaloir\Entity\Rue;
use AcMarche\Avaloir\Repository\RueRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AvaloirEditType extends AbstractType
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
                    'mapped' => false,
                )
            )
            ->add(
                'rue',
                EntityType::class,
                array(
                    'class' => Rue::class,
                    'required' => true,
                    'placeholder' => 'Sélectionnez une rue',
                    'group_by' => 'village',
                    'query_builder' => function (RueRepository $er) {
                        return $er->getForList();
                    },
                )
            )
            ->add(
                'numero',
                TextType::class,
                [
                    'help' => 'Emplacement approximatif dans la rue',
                    'required' => false,
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
                    'attr' => array('class' => 'datepicker', 'autocomplete' => 'off'),
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
