<?php

namespace AcMarche\Avaloir\Form;

use AcMarche\Avaloir\Data\Localite;
use AcMarche\Avaloir\Entity\Avaloir;
use AcMarche\Avaloir\Entity\Rue;
use AcMarche\Avaloir\Repository\RueRepository;
use AcMarche\Avaloir\Repository\VillageRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AvaloirEditType extends AbstractType
{
    /**
     * @var VillageRepository
     */
    private $villageRepository;

    public function __construct(VillageRepository $villageRepository)
    {
        $this->villageRepository = $villageRepository;
    }

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
                TextType::class,
                [
                    'help' => 'Le nom de la rue a été trouvé suivant les coordonnées gps',
                    'attr' => ['readonly' => true]
                ]
            )
            ->add(
                'localite',
                ChoiceType::class,
                [
                    'required'=>false,
                    'choices' => $this->villageRepository->getForSearch()
                ]
            )
            /*     ->add(
                     'rueEntity',
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
                 )*/
            ->add(
                'numero',
                TextType::class,
                [
                    'label' => 'Numéro de maison',
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
                    'attr' => array('autocomplete' => 'off'),
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
