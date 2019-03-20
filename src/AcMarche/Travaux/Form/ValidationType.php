<?php

namespace AcMarche\Travaux\Form;

use AcMarche\Travaux\Entity\Intervention;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ValidationType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'accepter',
                SubmitType::class,
                array(
                    'label' => 'Accepter',
                    'attr' => array('class' => 'btn-success mr-1'),
                )
            )
            ->add(
                'refuser',
                SubmitType::class,
                array(
                    'label' => 'Refuser',
                    'attr' => array('class' => 'btn-danger ml-1'),
                )
            )
            ->add(
                'message',
                TextareaType::class,
                array(
                    'required' => false,
                    'label' => 'Laissez un message',
                    'mapped' => false,
                    'attr' => array('cols' => 50, 'rows' => 5),
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
                'data_class' => Intervention::class,
            )
        );
    }
}
