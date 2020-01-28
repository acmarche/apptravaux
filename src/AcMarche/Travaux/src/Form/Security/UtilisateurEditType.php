<?php

namespace AcMarche\Travaux\Form\Security;

use AcMarche\Travaux\Entity\Security\Group;
use AcMarche\Travaux\Entity\Security\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UtilisateurEditType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->remove("plainPassword")
            ->add(
                "groups",
                EntityType::class,
                [
                    'class' => Group::class,
                    'multiple' => true,
                    'expanded' => true,
                ]
            );
    }

    public function getParent()
    {
        return UtilisateurType::class;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => User::class,
            )
        );
    }
}
