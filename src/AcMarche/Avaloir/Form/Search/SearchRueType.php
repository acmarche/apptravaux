<?php

namespace AcMarche\Avaloir\Form\Search;

use AcMarche\Avaloir\Entity\Quartier;
use AcMarche\Avaloir\Entity\Village;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManager;

class SearchRueType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $options['em'];

        $villages = $em->getRepository(Village::class)->getForSearch();
        $quartiers = $em->getRepository(Quartier::class)->getForSearch();

        $builder
            ->add('village', ChoiceType::class, array(
                'choices' => $villages,
                'required' => false,
                'placeholder' => 'Choisissez un village',
            ))
            ->add('quartier', ChoiceType::class, array(
                'choices' => $quartiers,
                'required' => false,
                'placeholder' => 'Choisissez un quartier',
            ))
            ->add('nom', SearchType::class, array(
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Rue',
                )))
            ->add('submit', SubmitType::class, array(
                'label' => 'Rechercher'))
            ->add('raz', SubmitType::class, array(
                'label' => 'Raz', 'attr' => array(
                    'class' => 'btn-sm btn-info',
                    'title' => 'Réinitialiser la recherche')
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array());
        $resolver->setRequired(array(
            'em',
        ));

        $resolver->setAllowedTypes('em', ObjectManager::class);
    }
}
