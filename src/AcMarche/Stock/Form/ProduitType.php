<?php

namespace AcMarche\Stock\Form;

use AcMarche\Stock\Entity\Categorie;
use AcMarche\Stock\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add(
                'categorie',
                EntityType::class,
                [
                    'class' => Categorie::class,
                ]
            )
            ->add(
                'quantite',
                IntegerType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'reference',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'required' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Produit::class,
            ]
        );
    }
}
